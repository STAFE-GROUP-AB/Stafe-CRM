<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Deal;
use App\Models\PlaybookExecution;
use App\Models\PlaybookStep;
use App\Models\PlaybookStepExecution;
use App\Models\SalesPlaybook;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlaybookExecutionService
{
    public function startExecution(
        SalesPlaybook $playbook,
        User $user,
        ?Deal $deal = null,
        ?Contact $contact = null
    ): PlaybookExecution {
        return DB::transaction(function () use ($playbook, $user, $deal, $contact) {
            $execution = $playbook->startExecution($user, $deal, $contact);
            $execution->initializeStepExecutions();

            return $execution;
        });
    }

    public function startStep(PlaybookExecution $execution, PlaybookStep $step): PlaybookStepExecution
    {
        return $execution->startStep($step);
    }

    public function completeStep(
        PlaybookExecution $execution,
        PlaybookStep $step,
        array $resultData = [],
        ?string $notes = null
    ): void {
        $execution->completeStep($step, $resultData, $notes);
    }

    public function skipStep(
        PlaybookExecution $execution,
        PlaybookStep $step,
        string $reason,
        ?string $notes = null
    ): void {
        $execution->skipStep($step, $reason, $notes);
    }

    public function completeExecution(
        PlaybookExecution $execution,
        string $outcome = 'successful',
        ?string $notes = null
    ): void {
        $execution->complete($outcome, $notes);
    }

    public function abandonExecution(PlaybookExecution $execution, ?string $notes = null): void
    {
        $execution->abandon($notes);
    }

    public function rateExecution(PlaybookExecution $execution, int $rating, ?string $feedback = null): void
    {
        $execution->playbook->addRating($execution, $rating, $feedback);
    }

    public function getAnalytics(SalesPlaybook $playbook): array
    {
        $executions = $playbook->executions()->with('stepExecutions')->get();

        return [
            'total_executions' => $executions->count(),
            'completed_executions' => $executions->where('status', 'completed')->count(),
            'abandoned_executions' => $executions->where('status', 'abandoned')->count(),
            'in_progress_executions' => $executions->where('status', 'in_progress')->count(),
            'average_completion_time_minutes' => $this->getAverageCompletionTime($playbook),
            'step_completion_rates' => $this->getStepCompletionRates($playbook),
            'most_skipped_steps' => $this->getMostSkippedSteps($playbook),
            'success_rate_with_completed_playbooks' => $this->getSuccessRateWithCompletedPlaybooks($playbook),
        ];
    }

    public function getAverageCompletionTime(SalesPlaybook $playbook): float
    {
        return (float) $playbook->completedExecutions()
            ->whereNotNull('duration_minutes')
            ->avg('duration_minutes') ?? 0;
    }

    public function getStepCompletionRates(SalesPlaybook $playbook): Collection
    {
        return $playbook->steps()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function (PlaybookStep $step) {
                return [
                    'step_id' => $step->id,
                    'title' => $step->title,
                    'completion_rate' => $step->getCompletionRate(),
                    'skip_rate' => $step->getSkipRate(),
                    'average_duration_seconds' => $step->getAverageDurationSeconds(),
                ];
            });
    }

    public function getMostSkippedSteps(SalesPlaybook $playbook, int $limit = 5): Collection
    {
        return PlaybookStepExecution::whereHas('step', function ($query) use ($playbook) {
            $query->where('playbook_id', $playbook->id);
        })
            ->where('status', 'skipped')
            ->select('step_id', DB::raw('COUNT(*) as skip_count'))
            ->groupBy('step_id')
            ->orderByDesc('skip_count')
            ->limit($limit)
            ->with('step:id,title')
            ->get()
            ->map(function ($item) {
                return [
                    'step_id' => $item->step_id,
                    'title' => $item->step?->title,
                    'skip_count' => $item->skip_count,
                ];
            });
    }

    public function getSkipReasons(PlaybookStep $step): Collection
    {
        return PlaybookStepExecution::where('step_id', $step->id)
            ->where('status', 'skipped')
            ->whereNotNull('skipped_reason')
            ->select('skipped_reason', DB::raw('COUNT(*) as count'))
            ->groupBy('skipped_reason')
            ->orderByDesc('count')
            ->get();
    }

    public function getSuccessRateWithCompletedPlaybooks(SalesPlaybook $playbook): float
    {
        $completedExecutions = $playbook->completedExecutions()
            ->whereNotNull('deal_id')
            ->with('deal')
            ->get();

        if ($completedExecutions->isEmpty()) {
            return 0;
        }

        $wonDeals = $completedExecutions->filter(function ($execution) {
            return $execution->deal?->isWon();
        })->count();

        return round(($wonDeals / $completedExecutions->count()) * 100, 2);
    }

    public function getGlobalAnalytics(): array
    {
        $totalExecutions = PlaybookExecution::count();
        $completedExecutions = PlaybookExecution::where('status', 'completed')->count();
        $successfulExecutions = PlaybookExecution::where('outcome', 'successful')->count();

        return [
            'total_executions' => $totalExecutions,
            'completed_executions' => $completedExecutions,
            'completion_rate' => $totalExecutions > 0
                ? round(($completedExecutions / $totalExecutions) * 100, 2)
                : 0,
            'success_rate' => $completedExecutions > 0
                ? round(($successfulExecutions / $completedExecutions) * 100, 2)
                : 0,
            'average_completion_time_minutes' => PlaybookExecution::where('status', 'completed')
                ->whereNotNull('duration_minutes')
                ->avg('duration_minutes') ?? 0,
            'top_performing_playbooks' => $this->getTopPerformingPlaybooks(),
            'playbooks_by_usage' => $this->getPlaybooksByUsage(),
        ];
    }

    public function getTopPerformingPlaybooks(int $limit = 5): Collection
    {
        return SalesPlaybook::where('status', 'published')
            ->orderByDesc('success_rate')
            ->where('usage_count', '>', 0)
            ->limit($limit)
            ->get(['id', 'title', 'success_rate', 'usage_count', 'average_rating']);
    }

    public function getPlaybooksByUsage(int $limit = 5): Collection
    {
        return SalesPlaybook::where('status', 'published')
            ->orderByDesc('usage_count')
            ->limit($limit)
            ->get(['id', 'title', 'usage_count', 'success_rate']);
    }

    public function getUserExecutions(User $user, ?string $status = null): Collection
    {
        $query = PlaybookExecution::where('user_id', $user->id)
            ->with(['playbook', 'deal', 'stepExecutions']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderByDesc('created_at')->get();
    }

    public function getDealExecutions(Deal $deal): Collection
    {
        return PlaybookExecution::where('deal_id', $deal->id)
            ->with(['playbook', 'user', 'stepExecutions'])
            ->orderByDesc('created_at')
            ->get();
    }
}
