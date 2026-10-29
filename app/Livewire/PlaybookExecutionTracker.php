<?php

namespace App\Livewire;

use App\Models\PlaybookExecution;
use App\Models\PlaybookStep;
use App\Services\PlaybookExecutionService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Playbook Execution')]
class PlaybookExecutionTracker extends Component
{
    public PlaybookExecution $execution;

    public ?PlaybookStep $currentStep = null;

    public int $progress = 0;

    public bool $showCompleteModal = false;

    public bool $showSkipModal = false;

    public bool $showAbandonModal = false;

    public bool $showRatingModal = false;

    public string $stepNotes = '';

    public string $skipReason = '';

    public string $abandonReason = '';

    public int $rating = 5;

    public string $feedback = '';

    public string $completionOutcome = 'successful';

    protected PlaybookExecutionService $service;

    public function boot(PlaybookExecutionService $service): void
    {
        $this->service = $service;
    }

    public function mount(PlaybookExecution $execution): void
    {
        if ($execution->user_id !== auth()->id()) {
            abort(403, 'You are not authorized to view this execution.');
        }

        $this->execution = $execution;
        $this->execution->load([
            'playbook.steps' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            },
            'deal',
            'contact',
            'stepExecutions',
        ]);

        $this->refreshCurrentStep();
    }

    public function render()
    {
        $stepsWithExecutions = $this->execution->getStepsWithExecutions();

        return view('livewire.playbook-execution-tracker', [
            'playbook' => $this->execution->playbook,
            'stepsWithExecutions' => $stepsWithExecutions,
            'deal' => $this->execution->deal,
            'contact' => $this->execution->contact,
        ]);
    }

    public function startStep(int $stepId): void
    {
        $step = PlaybookStep::findOrFail($stepId);

        if ($step->playbook_id !== $this->execution->playbook_id) {
            return;
        }

        $this->service->startStep($this->execution, $step);
        $this->execution->refresh();
        $this->refreshCurrentStep();
    }

    public function openCompleteModal(int $stepId): void
    {
        $this->currentStep = PlaybookStep::findOrFail($stepId);
        $this->stepNotes = '';
        $this->showCompleteModal = true;
    }

    public function completeStep(): void
    {
        if (! $this->currentStep) {
            return;
        }

        $this->service->completeStep(
            $this->execution,
            $this->currentStep,
            [],
            $this->stepNotes ?: null
        );

        $this->execution->refresh();
        $this->showCompleteModal = false;
        $this->stepNotes = '';
        $this->refreshCurrentStep();

        if ($this->execution->isCompleted()) {
            $this->showRatingModal = true;
        }
    }

    public function openSkipModal(int $stepId): void
    {
        $this->currentStep = PlaybookStep::findOrFail($stepId);
        $this->skipReason = '';
        $this->stepNotes = '';
        $this->showSkipModal = true;
    }

    public function skipStep(): void
    {
        $this->validate([
            'skipReason' => 'required|string|min:3',
        ]);

        if (! $this->currentStep) {
            return;
        }

        $this->service->skipStep(
            $this->execution,
            $this->currentStep,
            $this->skipReason,
            $this->stepNotes ?: null
        );

        $this->execution->refresh();
        $this->showSkipModal = false;
        $this->skipReason = '';
        $this->stepNotes = '';
        $this->refreshCurrentStep();

        if ($this->execution->isCompleted()) {
            $this->showRatingModal = true;
        }
    }

    public function openAbandonModal(): void
    {
        $this->abandonReason = '';
        $this->showAbandonModal = true;
    }

    public function abandonExecution()
    {
        $this->service->abandonExecution($this->execution, $this->abandonReason ?: null);
        $this->execution->refresh();
        $this->showAbandonModal = false;

        return redirect()->route('sales-enablement.playbooks')
            ->with('message', 'Playbook execution abandoned.');
    }

    public function submitRating()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string',
        ]);

        $this->service->rateExecution($this->execution, $this->rating, $this->feedback ?: null);
        $this->showRatingModal = false;

        return redirect()->route('sales-enablement.playbooks')
            ->with('message', 'Playbook completed successfully!');
    }

    public function skipRating()
    {
        $this->showRatingModal = false;

        return redirect()->route('sales-enablement.playbooks')
            ->with('message', 'Playbook completed successfully!');
    }

    public function goBack()
    {
        return redirect()->route('sales-enablement.playbooks');
    }

    private function refreshCurrentStep(): void
    {
        $this->currentStep = $this->execution->getCurrentStep();
        $this->progress = $this->execution->getProgressPercentage();
    }
}
