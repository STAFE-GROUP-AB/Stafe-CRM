<?php

namespace App\Services;

use App\Models\CustomerTicket;
use App\Models\TicketResponse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TicketSlaReportingService
{
    protected TicketSlaService $slaService;

    public function __construct(TicketSlaService $slaService)
    {
        $this->slaService = $slaService;
    }

    /**
     * Get comprehensive SLA performance metrics.
     */
    public function getSlaPerformanceMetrics(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->subMonth();
        $endDate = $endDate ?? now();

        $tickets = CustomerTicket::whereBetween('created_at', [$startDate, $endDate]);

        $totalTickets = $tickets->count();
        $resolvedTickets = (clone $tickets)->whereNotNull('resolved_at')->count();
        $firstResponseBreached = (clone $tickets)->where('first_response_sla_breached', true)->count();
        $resolutionBreached = (clone $tickets)->where('resolution_sla_breached', true)->count();

        $avgFirstResponse = (clone $tickets)
            ->whereNotNull('total_response_time_minutes')
            ->avg('total_response_time_minutes');

        $avgResolution = (clone $tickets)
            ->whereNotNull('total_resolution_time_minutes')
            ->avg('total_resolution_time_minutes');

        $firstResponseCompliance = $totalTickets > 0
            ? round((($totalTickets - $firstResponseBreached) / $totalTickets) * 100, 1)
            : 100;

        $resolutionCompliance = $resolvedTickets > 0
            ? round((($resolvedTickets - $resolutionBreached) / $resolvedTickets) * 100, 1)
            : 100;

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'totals' => [
                'tickets_created' => $totalTickets,
                'tickets_resolved' => $resolvedTickets,
                'first_response_breaches' => $firstResponseBreached,
                'resolution_breaches' => $resolutionBreached,
            ],
            'averages' => [
                'first_response_minutes' => $avgFirstResponse ? round($avgFirstResponse) : null,
                'first_response_formatted' => $avgFirstResponse
                    ? $this->slaService->formatMinutes((int) $avgFirstResponse)
                    : '-',
                'resolution_minutes' => $avgResolution ? round($avgResolution) : null,
                'resolution_formatted' => $avgResolution
                    ? $this->slaService->formatMinutes((int) $avgResolution)
                    : '-',
            ],
            'compliance' => [
                'first_response_percentage' => $firstResponseCompliance,
                'resolution_percentage' => $resolutionCompliance,
                'overall_percentage' => round(($firstResponseCompliance + $resolutionCompliance) / 2, 1),
            ],
        ];
    }

    /**
     * Get response time statistics by priority.
     */
    public function getResponseTimesByPriority(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->subMonth();
        $endDate = $endDate ?? now();

        $priorities = ['urgent', 'high', 'medium', 'low'];
        $results = [];

        foreach ($priorities as $priority) {
            $tickets = CustomerTicket::whereBetween('created_at', [$startDate, $endDate])
                ->where('priority', $priority);

            $total = $tickets->count();
            $avgFirstResponse = (clone $tickets)
                ->whereNotNull('total_response_time_minutes')
                ->avg('total_response_time_minutes');
            $avgResolution = (clone $tickets)
                ->whereNotNull('total_resolution_time_minutes')
                ->avg('total_resolution_time_minutes');
            $breached = (clone $tickets)->slaBreached()->count();

            $results[$priority] = [
                'total_tickets' => $total,
                'avg_first_response_minutes' => $avgFirstResponse ? round($avgFirstResponse) : null,
                'avg_first_response_formatted' => $avgFirstResponse
                    ? $this->slaService->formatMinutes((int) $avgFirstResponse)
                    : '-',
                'avg_resolution_minutes' => $avgResolution ? round($avgResolution) : null,
                'avg_resolution_formatted' => $avgResolution
                    ? $this->slaService->formatMinutes((int) $avgResolution)
                    : '-',
                'sla_breaches' => $breached,
                'compliance_percentage' => $total > 0
                    ? round((($total - $breached) / $total) * 100, 1)
                    : 100,
                'target_first_response' => $this->slaService->getFirstResponseTarget($priority),
                'target_resolution' => $this->slaService->getResolutionTarget($priority),
            ];
        }

        return $results;
    }

    /**
     * Get agent performance metrics.
     */
    public function getAgentPerformance(?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $startDate = $startDate ?? now()->subMonth();
        $endDate = $endDate ?? now();

        return User::select('users.id', 'users.name')
            ->join('customer_tickets', 'users.id', '=', 'customer_tickets.assigned_to')
            ->whereBetween('customer_tickets.created_at', [$startDate, $endDate])
            ->groupBy('users.id', 'users.name')
            ->selectRaw('
                COUNT(customer_tickets.id) as total_tickets,
                SUM(CASE WHEN customer_tickets.status IN ("resolved", "closed") THEN 1 ELSE 0 END) as resolved_tickets,
                AVG(customer_tickets.total_response_time_minutes) as avg_first_response,
                AVG(customer_tickets.total_resolution_time_minutes) as avg_resolution,
                SUM(CASE WHEN customer_tickets.first_response_sla_breached = 1 THEN 1 ELSE 0 END) as first_response_breaches,
                SUM(CASE WHEN customer_tickets.resolution_sla_breached = 1 THEN 1 ELSE 0 END) as resolution_breaches
            ')
            ->get()
            ->map(function ($agent) {
                $agent->avg_first_response_formatted = $agent->avg_first_response
                    ? $this->slaService->formatMinutes((int) $agent->avg_first_response)
                    : '-';
                $agent->avg_resolution_formatted = $agent->avg_resolution
                    ? $this->slaService->formatMinutes((int) $agent->avg_resolution)
                    : '-';
                $agent->sla_compliance = $agent->total_tickets > 0
                    ? round((($agent->total_tickets - $agent->first_response_breaches - $agent->resolution_breaches) / $agent->total_tickets) * 100, 1)
                    : 100;

                return $agent;
            });
    }

    /**
     * Get daily SLA trend data for charts.
     */
    public function getDailySlaTrend(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $data = CustomerTicket::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total'),
            DB::raw('AVG(total_response_time_minutes) as avg_first_response'),
            DB::raw('AVG(total_resolution_time_minutes) as avg_resolution'),
            DB::raw('SUM(CASE WHEN first_response_sla_breached = 1 THEN 1 ELSE 0 END) as breaches')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->toArray(),
            'datasets' => [
                'total_tickets' => $data->pluck('total')->toArray(),
                'avg_first_response' => $data->pluck('avg_first_response')->map(fn ($v) => $v ? round($v) : null)->toArray(),
                'avg_resolution' => $data->pluck('avg_resolution')->map(fn ($v) => $v ? round($v) : null)->toArray(),
                'breaches' => $data->pluck('breaches')->toArray(),
            ],
        ];
    }

    /**
     * Get tickets at risk of SLA breach.
     */
    public function getTicketsAtRisk(int $minutesThreshold = 60): Collection
    {
        return CustomerTicket::with(['contact', 'assignedUser'])
            ->open()
            ->slaDueSoon($minutesThreshold)
            ->orderBy('sla_due_at')
            ->get()
            ->map(function ($ticket) {
                $ticket->sla_details = $ticket->sla_status;
                $ticket->time_remaining = $ticket->time_to_sla_due;

                return $ticket;
            });
    }

    /**
     * Get current SLA breach statistics.
     */
    public function getCurrentBreachStats(): array
    {
        $openTickets = CustomerTicket::open();

        $awaitingFirstResponse = (clone $openTickets)->awaitingFirstResponse()->count();

        $firstResponseBreached = CustomerTicket::open()
            ->whereNull('first_response_at')
            ->get()
            ->filter(fn ($ticket) => $this->slaService->isFirstResponseBreached($ticket))
            ->count();

        $resolutionAtRisk = CustomerTicket::open()
            ->slaDueSoon(60)
            ->count();

        $totalBreached = CustomerTicket::open()->slaBreached()->count();

        return [
            'awaiting_first_response' => $awaitingFirstResponse,
            'first_response_breached' => $firstResponseBreached,
            'resolution_at_risk' => $resolutionAtRisk,
            'total_breached' => $totalBreached,
        ];
    }

    /**
     * Get response distribution data.
     */
    public function getResponseDistribution(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->subMonth();
        $endDate = $endDate ?? now();

        $responses = TicketResponse::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('response_time_minutes')
            ->where('response_type', 'agent')
            ->where('is_internal_note', false);

        $distribution = [
            'under_1h' => (clone $responses)->where('response_time_minutes', '<', 60)->count(),
            '1h_4h' => (clone $responses)->whereBetween('response_time_minutes', [60, 240])->count(),
            '4h_8h' => (clone $responses)->whereBetween('response_time_minutes', [241, 480])->count(),
            '8h_24h' => (clone $responses)->whereBetween('response_time_minutes', [481, 1440])->count(),
            'over_24h' => (clone $responses)->where('response_time_minutes', '>', 1440)->count(),
        ];

        $total = array_sum($distribution);

        return [
            'counts' => $distribution,
            'percentages' => $total > 0 ? array_map(
                fn ($count) => round(($count / $total) * 100, 1),
                $distribution
            ) : $distribution,
            'total' => $total,
        ];
    }

    /**
     * Get category breakdown with SLA stats.
     */
    public function getCategoryBreakdown(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->subMonth();
        $endDate = $endDate ?? now();

        $categories = ['technical', 'billing', 'general', 'feature_request', 'bug_report'];
        $results = [];

        foreach ($categories as $category) {
            $tickets = CustomerTicket::whereBetween('created_at', [$startDate, $endDate])
                ->where('category', $category);

            $total = $tickets->count();
            $resolved = (clone $tickets)->whereNotNull('resolved_at')->count();
            $breached = (clone $tickets)->slaBreached()->count();
            $avgResolution = (clone $tickets)
                ->whereNotNull('total_resolution_time_minutes')
                ->avg('total_resolution_time_minutes');

            $results[$category] = [
                'total' => $total,
                'resolved' => $resolved,
                'open' => $total - $resolved,
                'breached' => $breached,
                'compliance' => $total > 0 ? round((($total - $breached) / $total) * 100, 1) : 100,
                'avg_resolution_formatted' => $avgResolution
                    ? $this->slaService->formatMinutes((int) $avgResolution)
                    : '-',
            ];
        }

        return $results;
    }

    /**
     * Generate a summary report for a date range.
     */
    public function generateSummaryReport(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        return [
            'metrics' => $this->getSlaPerformanceMetrics($startDate, $endDate),
            'by_priority' => $this->getResponseTimesByPriority($startDate, $endDate),
            'by_category' => $this->getCategoryBreakdown($startDate, $endDate),
            'agent_performance' => $this->getAgentPerformance($startDate, $endDate)->toArray(),
            'response_distribution' => $this->getResponseDistribution($startDate, $endDate),
            'daily_trend' => $this->getDailySlaTrend($startDate, $endDate),
            'current_breach_stats' => $this->getCurrentBreachStats(),
            'tickets_at_risk' => $this->getTicketsAtRisk()->take(10)->toArray(),
        ];
    }
}
