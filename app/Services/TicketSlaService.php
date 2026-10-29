<?php

namespace App\Services;

use App\Models\CustomerTicket;
use App\Models\TicketResponse;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Config;

class TicketSlaService
{
    protected array $config;

    public function __construct()
    {
        $this->config = Config::get('ticket-sla', []);
    }

    /**
     * Calculate business minutes between two timestamps.
     */
    public function calculateBusinessMinutes(Carbon $start, Carbon $end): int
    {
        if (! $this->isBusinessHoursEnabled()) {
            return (int) $start->diffInMinutes($end);
        }

        $timezone = $this->getTimezone();
        $start = $start->copy()->setTimezone($timezone);
        $end = $end->copy()->setTimezone($timezone);

        if ($start->greaterThanOrEqualTo($end)) {
            return 0;
        }

        $totalMinutes = 0;
        $current = $start->copy();

        while ($current->lessThan($end)) {
            if ($this->isWorkingDay($current) && ! $this->isHoliday($current)) {
                $dayStart = $this->getDayStart($current);
                $dayEnd = $this->getDayEnd($current);

                if ($dayStart && $dayEnd) {
                    $workStart = $current->copy()->max($dayStart);
                    $workEnd = $end->copy()->min($dayEnd);

                    if ($workStart->lessThan($workEnd)) {
                        $totalMinutes += (int) $workStart->diffInMinutes($workEnd);
                    }
                }
            }

            $current->addDay()->startOfDay();
        }

        return $totalMinutes;
    }

    /**
     * Calculate the SLA due timestamp based on business hours.
     */
    public function calculateSlaDueAt(Carbon $start, int $targetMinutes): Carbon
    {
        if (! $this->isBusinessHoursEnabled()) {
            return $start->copy()->addMinutes($targetMinutes);
        }

        $timezone = $this->getTimezone();
        $current = $start->copy()->setTimezone($timezone);
        $remainingMinutes = $targetMinutes;

        $maxIterations = 365;
        $iteration = 0;

        while ($remainingMinutes > 0 && $iteration < $maxIterations) {
            $iteration++;

            if (! $this->isWorkingDay($current) || $this->isHoliday($current)) {
                $current->addDay()->startOfDay();
                continue;
            }

            $dayStart = $this->getDayStart($current);
            $dayEnd = $this->getDayEnd($current);

            if (! $dayStart || ! $dayEnd) {
                $current->addDay()->startOfDay();
                continue;
            }

            if ($current->lessThan($dayStart)) {
                $current = $dayStart->copy();
            }

            if ($current->greaterThanOrEqualTo($dayEnd)) {
                $current->addDay()->startOfDay();
                continue;
            }

            $availableMinutes = (int) $current->diffInMinutes($dayEnd);

            if ($availableMinutes >= $remainingMinutes) {
                return $current->addMinutes($remainingMinutes);
            }

            $remainingMinutes -= $availableMinutes;
            $current->addDay()->startOfDay();
        }

        return $current;
    }

    /**
     * Get the first response SLA target in minutes for a priority.
     */
    public function getFirstResponseTarget(string $priority): int
    {
        return $this->config['targets']['first_response'][$priority] ?? 480;
    }

    /**
     * Get the resolution SLA target in minutes for a priority.
     */
    public function getResolutionTarget(string $priority): int
    {
        return $this->config['targets']['resolution'][$priority] ?? 2880;
    }

    /**
     * Check if SLA is breached for first response.
     */
    public function isFirstResponseBreached(CustomerTicket $ticket): bool
    {
        if ($ticket->first_response_at) {
            $businessMinutes = $this->calculateBusinessMinutes(
                $ticket->created_at,
                $ticket->first_response_at
            );

            return $businessMinutes > $this->getFirstResponseTarget($ticket->priority);
        }

        if ($this->isSlaPaused($ticket)) {
            return false;
        }

        $businessMinutes = $this->calculateBusinessMinutes(
            $ticket->created_at,
            now()
        );

        return $businessMinutes > $this->getFirstResponseTarget($ticket->priority);
    }

    /**
     * Check if SLA is breached for resolution.
     */
    public function isResolutionBreached(CustomerTicket $ticket): bool
    {
        if ($ticket->resolved_at) {
            $businessMinutes = $this->calculateBusinessMinutes(
                $ticket->created_at,
                $ticket->resolved_at
            );

            return $businessMinutes > $this->getResolutionTarget($ticket->priority);
        }

        if ($this->isSlaPaused($ticket)) {
            return false;
        }

        $businessMinutes = $this->calculateBusinessMinutes(
            $ticket->created_at,
            now()
        );

        return $businessMinutes > $this->getResolutionTarget($ticket->priority);
    }

    /**
     * Get SLA status for a ticket.
     */
    public function getSlaStatus(CustomerTicket $ticket): array
    {
        $firstResponseStatus = $this->getFirstResponseStatus($ticket);
        $resolutionStatus = $this->getResolutionStatus($ticket);

        return [
            'first_response' => $firstResponseStatus,
            'resolution' => $resolutionStatus,
            'overall' => $this->determineOverallStatus($firstResponseStatus, $resolutionStatus),
        ];
    }

    /**
     * Get first response SLA status details.
     */
    public function getFirstResponseStatus(CustomerTicket $ticket): array
    {
        $targetMinutes = $this->getFirstResponseTarget($ticket->priority);

        if ($ticket->first_response_at) {
            $actualMinutes = $this->calculateBusinessMinutes(
                $ticket->created_at,
                $ticket->first_response_at
            );

            return [
                'status' => $actualMinutes <= $targetMinutes ? 'met' : 'breached',
                'target_minutes' => $targetMinutes,
                'actual_minutes' => $actualMinutes,
                'remaining_minutes' => null,
                'due_at' => null,
                'formatted_actual' => $this->formatMinutes($actualMinutes),
                'formatted_target' => $this->formatMinutes($targetMinutes),
            ];
        }

        if ($this->isSlaPaused($ticket)) {
            return [
                'status' => 'paused',
                'target_minutes' => $targetMinutes,
                'actual_minutes' => null,
                'remaining_minutes' => null,
                'due_at' => null,
                'formatted_target' => $this->formatMinutes($targetMinutes),
            ];
        }

        $elapsedMinutes = $this->calculateBusinessMinutes($ticket->created_at, now());
        $remainingMinutes = max(0, $targetMinutes - $elapsedMinutes);
        $dueAt = $this->calculateSlaDueAt($ticket->created_at, $targetMinutes);

        $status = 'on_track';
        if ($remainingMinutes <= 0) {
            $status = 'breached';
        } elseif ($remainingMinutes <= ($targetMinutes * $this->getWarningThreshold())) {
            $status = 'warning';
        }

        return [
            'status' => $status,
            'target_minutes' => $targetMinutes,
            'actual_minutes' => $elapsedMinutes,
            'remaining_minutes' => $remainingMinutes,
            'due_at' => $dueAt,
            'formatted_remaining' => $this->formatMinutes($remainingMinutes),
            'formatted_target' => $this->formatMinutes($targetMinutes),
            'formatted_elapsed' => $this->formatMinutes($elapsedMinutes),
        ];
    }

    /**
     * Get resolution SLA status details.
     */
    public function getResolutionStatus(CustomerTicket $ticket): array
    {
        $targetMinutes = $this->getResolutionTarget($ticket->priority);

        if (in_array($ticket->status, ['resolved', 'closed'])) {
            $resolvedAt = $ticket->resolved_at ?? $ticket->updated_at;
            $actualMinutes = $this->calculateBusinessMinutes($ticket->created_at, $resolvedAt);

            return [
                'status' => $actualMinutes <= $targetMinutes ? 'met' : 'breached',
                'target_minutes' => $targetMinutes,
                'actual_minutes' => $actualMinutes,
                'remaining_minutes' => null,
                'due_at' => null,
                'formatted_actual' => $this->formatMinutes($actualMinutes),
                'formatted_target' => $this->formatMinutes($targetMinutes),
            ];
        }

        if ($this->isSlaPaused($ticket)) {
            return [
                'status' => 'paused',
                'target_minutes' => $targetMinutes,
                'actual_minutes' => null,
                'remaining_minutes' => null,
                'due_at' => null,
                'formatted_target' => $this->formatMinutes($targetMinutes),
            ];
        }

        $elapsedMinutes = $this->calculateBusinessMinutes($ticket->created_at, now());
        $remainingMinutes = max(0, $targetMinutes - $elapsedMinutes);
        $dueAt = $this->calculateSlaDueAt($ticket->created_at, $targetMinutes);

        $status = 'on_track';
        if ($remainingMinutes <= 0) {
            $status = 'breached';
        } elseif ($remainingMinutes <= ($targetMinutes * $this->getWarningThreshold())) {
            $status = 'warning';
        }

        return [
            'status' => $status,
            'target_minutes' => $targetMinutes,
            'actual_minutes' => $elapsedMinutes,
            'remaining_minutes' => $remainingMinutes,
            'due_at' => $dueAt,
            'formatted_remaining' => $this->formatMinutes($remainingMinutes),
            'formatted_target' => $this->formatMinutes($targetMinutes),
            'formatted_elapsed' => $this->formatMinutes($elapsedMinutes),
        ];
    }

    /**
     * Record a response and calculate response time.
     */
    public function recordResponse(CustomerTicket $ticket, TicketResponse $response): void
    {
        $previousResponse = $ticket->responses()
            ->where('id', '!=', $response->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $referenceTime = $previousResponse
            ? $previousResponse->created_at
            : $ticket->created_at;

        $responseTimeMinutes = $this->calculateBusinessMinutes(
            $referenceTime,
            $response->created_at
        );

        $response->update(['response_time_minutes' => $responseTimeMinutes]);

        if ($response->isFromAgent() && ! $response->is_internal_note && ! $ticket->first_response_at) {
            $firstResponseMinutes = $this->calculateBusinessMinutes(
                $ticket->created_at,
                $response->created_at
            );

            $ticket->update([
                'first_response_at' => $response->created_at,
                'total_response_time_minutes' => $firstResponseMinutes,
                'first_response_sla_breached' => $firstResponseMinutes > $this->getFirstResponseTarget($ticket->priority),
            ]);
        }

        $this->updateResponseTimesArray($ticket);
    }

    /**
     * Mark ticket as resolved and calculate resolution time.
     */
    public function markResolved(CustomerTicket $ticket): void
    {
        $resolutionMinutes = $this->calculateBusinessMinutes(
            $ticket->created_at,
            now()
        );

        $ticket->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'total_resolution_time_minutes' => $resolutionMinutes,
            'resolution_sla_breached' => $resolutionMinutes > $this->getResolutionTarget($ticket->priority),
        ]);
    }

    /**
     * Update the SLA due timestamp for a ticket.
     */
    public function updateSlaDueAt(CustomerTicket $ticket): void
    {
        if (in_array($ticket->status, ['resolved', 'closed'])) {
            $ticket->update(['sla_due_at' => null]);

            return;
        }

        $targetMinutes = $ticket->first_response_at
            ? $this->getResolutionTarget($ticket->priority)
            : $this->getFirstResponseTarget($ticket->priority);

        $dueAt = $this->calculateSlaDueAt($ticket->created_at, $targetMinutes);
        $ticket->update(['sla_due_at' => $dueAt]);
    }

    /**
     * Format minutes into human-readable string.
     */
    public function formatMinutes(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes} min";
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours < 24) {
            return $remainingMinutes > 0 ? "{$hours}t {$remainingMinutes}m" : "{$hours}t";
        }

        $days = floor($hours / 24);
        $remainingHours = $hours % 24;

        if ($remainingHours > 0) {
            return "{$days}d {$remainingHours}t";
        }

        return "{$days}d";
    }

    /**
     * Get SLA color class for UI display.
     */
    public function getSlaColorClass(string $status): string
    {
        return match ($status) {
            'met', 'on_track' => 'green',
            'warning' => 'yellow',
            'breached' => 'red',
            'paused' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Check if SLA timer should be paused.
     */
    public function isSlaPaused(CustomerTicket $ticket): bool
    {
        $pauseStatuses = $this->config['pause_statuses'] ?? ['waiting_customer'];

        return in_array($ticket->status, $pauseStatuses);
    }

    protected function isBusinessHoursEnabled(): bool
    {
        return $this->config['business_hours']['enabled'] ?? false;
    }

    protected function getTimezone(): string
    {
        return $this->config['business_hours']['timezone'] ?? 'Europe/Stockholm';
    }

    protected function getWarningThreshold(): float
    {
        return $this->config['warning_threshold'] ?? 0.25;
    }

    protected function isWorkingDay(Carbon $date): bool
    {
        $dayName = strtolower($date->format('l'));
        $schedule = $this->config['business_hours']['schedule'][$dayName] ?? null;

        return $schedule !== null;
    }

    protected function isHoliday(Carbon $date): bool
    {
        $holidays = $this->config['holidays'] ?? [];
        $dateFormatted = $date->format('Y-m-d');
        $dateRecurring = $date->format('m-d');

        return in_array($dateFormatted, $holidays) || in_array($dateRecurring, $holidays);
    }

    protected function getDayStart(Carbon $date): ?Carbon
    {
        $dayName = strtolower($date->format('l'));
        $schedule = $this->config['business_hours']['schedule'][$dayName] ?? null;

        if (! $schedule || ! isset($schedule['start'])) {
            return null;
        }

        return $date->copy()->setTimeFromTimeString($schedule['start']);
    }

    protected function getDayEnd(Carbon $date): ?Carbon
    {
        $dayName = strtolower($date->format('l'));
        $schedule = $this->config['business_hours']['schedule'][$dayName] ?? null;

        if (! $schedule || ! isset($schedule['end'])) {
            return null;
        }

        return $date->copy()->setTimeFromTimeString($schedule['end']);
    }

    protected function determineOverallStatus(array $firstResponse, array $resolution): string
    {
        if ($firstResponse['status'] === 'breached' || $resolution['status'] === 'breached') {
            return 'breached';
        }

        if ($firstResponse['status'] === 'warning' || $resolution['status'] === 'warning') {
            return 'warning';
        }

        if ($firstResponse['status'] === 'paused' || $resolution['status'] === 'paused') {
            return 'paused';
        }

        if ($firstResponse['status'] === 'met' && $resolution['status'] === 'met') {
            return 'met';
        }

        return 'on_track';
    }

    protected function updateResponseTimesArray(CustomerTicket $ticket): void
    {
        $responseTimes = $ticket->responses()
            ->whereNotNull('response_time_minutes')
            ->orderBy('created_at')
            ->pluck('response_time_minutes')
            ->toArray();

        $ticket->update(['response_times' => $responseTimes]);
    }
}
