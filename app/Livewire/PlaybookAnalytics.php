<?php

namespace App\Livewire;

use App\Models\PlaybookExecution;
use App\Models\PlaybookStepExecution;
use App\Models\SalesPlaybook;
use App\Services\PlaybookExecutionService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Playbook Analytics')]
class PlaybookAnalytics extends Component
{
    public ?int $selectedPlaybookId = null;

    public string $dateRange = '30';

    protected PlaybookExecutionService $service;

    public function boot(PlaybookExecutionService $service): void
    {
        $this->service = $service;
    }

    public function render()
    {
        $globalAnalytics = $this->getGlobalAnalytics();
        $playbooks = SalesPlaybook::where('status', 'published')
            ->orderBy('title')
            ->get(['id', 'title', 'usage_count', 'success_rate', 'average_rating']);

        $selectedPlaybookAnalytics = null;
        if ($this->selectedPlaybookId) {
            $playbook = SalesPlaybook::find($this->selectedPlaybookId);
            if ($playbook) {
                $selectedPlaybookAnalytics = $this->service->getAnalytics($playbook);
            }
        }

        return view('livewire.playbook-analytics', [
            'globalAnalytics' => $globalAnalytics,
            'playbooks' => $playbooks,
            'selectedPlaybookAnalytics' => $selectedPlaybookAnalytics,
            'topPerformers' => $this->getTopPerformers(),
            'mostSkippedSteps' => $this->getMostSkippedSteps(),
            'completionTrend' => $this->getCompletionTrend(),
        ]);
    }

    private function getGlobalAnalytics(): array
    {
        $startDate = now()->subDays((int) $this->dateRange);

        $totalExecutions = PlaybookExecution::where('created_at', '>=', $startDate)->count();
        $completedExecutions = PlaybookExecution::where('created_at', '>=', $startDate)
            ->where('status', 'completed')
            ->count();
        $abandonedExecutions = PlaybookExecution::where('created_at', '>=', $startDate)
            ->where('status', 'abandoned')
            ->count();
        $inProgressExecutions = PlaybookExecution::where('status', 'in_progress')->count();
        $successfulExecutions = PlaybookExecution::where('created_at', '>=', $startDate)
            ->where('outcome', 'successful')
            ->count();

        $avgCompletionTime = PlaybookExecution::where('created_at', '>=', $startDate)
            ->where('status', 'completed')
            ->whereNotNull('duration_minutes')
            ->avg('duration_minutes');

        $avgRating = PlaybookExecution::where('created_at', '>=', $startDate)
            ->whereNotNull('rating')
            ->avg('rating');

        return [
            'total_executions' => $totalExecutions,
            'completed_executions' => $completedExecutions,
            'abandoned_executions' => $abandonedExecutions,
            'in_progress_executions' => $inProgressExecutions,
            'successful_executions' => $successfulExecutions,
            'completion_rate' => $totalExecutions > 0
                ? round(($completedExecutions / $totalExecutions) * 100, 1)
                : 0,
            'success_rate' => $completedExecutions > 0
                ? round(($successfulExecutions / $completedExecutions) * 100, 1)
                : 0,
            'avg_completion_time' => round($avgCompletionTime ?? 0, 1),
            'avg_rating' => round($avgRating ?? 0, 1),
        ];
    }

    private function getTopPerformers(): array
    {
        return SalesPlaybook::where('status', 'published')
            ->where('usage_count', '>', 0)
            ->orderByDesc('success_rate')
            ->limit(5)
            ->get(['id', 'title', 'usage_count', 'success_rate', 'average_rating'])
            ->toArray();
    }

    private function getMostSkippedSteps(): array
    {
        return PlaybookStepExecution::where('status', 'skipped')
            ->select('step_id', DB::raw('COUNT(*) as skip_count'))
            ->groupBy('step_id')
            ->orderByDesc('skip_count')
            ->limit(10)
            ->with('step:id,title,playbook_id', 'step.playbook:id,title')
            ->get()
            ->map(function ($item) {
                return [
                    'step_id' => $item->step_id,
                    'step_title' => $item->step?->title ?? 'Unknown',
                    'playbook_title' => $item->step?->playbook?->title ?? 'Unknown',
                    'skip_count' => $item->skip_count,
                ];
            })
            ->toArray();
    }

    private function getCompletionTrend(): array
    {
        $days = (int) $this->dateRange;
        $trend = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $completed = PlaybookExecution::whereDate('completed_at', $date)
                ->where('status', 'completed')
                ->count();

            $trend[] = [
                'date' => $date,
                'completed' => $completed,
            ];
        }

        return $trend;
    }

    public function selectPlaybook(int $playbookId): void
    {
        $this->selectedPlaybookId = $playbookId;
    }

    public function clearSelection(): void
    {
        $this->selectedPlaybookId = null;
    }

    public function updatedDateRange(): void
    {
        // Re-render with new date range
    }
}
