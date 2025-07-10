<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaybookExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'playbook_id',
        'user_id',
        'deal_id',
        'contact_id',
        'status',
        'step_results',
        'current_step_order',
        'started_at',
        'completed_at',
        'duration_minutes',
        'outcome',
        'notes',
        'rating',
        'feedback',
    ];

    protected $casts = [
        'step_results' => 'array',
        'current_step_order' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'duration_minutes' => 'integer',
        'rating' => 'integer',
    ];

    public function playbook(): BelongsTo
    {
        return $this->belongsTo(SalesPlaybook::class, 'playbook_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    // Status helpers
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isAbandoned(): bool
    {
        return $this->status === 'abandoned';
    }

    // Outcome helpers
    public function wasSuccessful(): bool
    {
        return $this->outcome === 'successful';
    }

    public function wasUnsuccessful(): bool
    {
        return $this->outcome === 'unsuccessful';
    }

    public function wasPartial(): bool
    {
        return $this->outcome === 'partial';
    }

    // Step management
    public function getCurrentStep(): ?PlaybookStep
    {
        return $this->playbook->steps()
            ->where('sort_order', $this->current_step_order)
            ->where('is_active', true)
            ->first();
    }

    public function getNextStep(): ?PlaybookStep
    {
        return $this->playbook->steps()
            ->where('sort_order', '>', $this->current_step_order)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first();
    }

    public function getPreviousStep(): ?PlaybookStep
    {
        return $this->playbook->steps()
            ->where('sort_order', '<', $this->current_step_order)
            ->where('is_active', true)
            ->orderBy('sort_order', 'desc')
            ->first();
    }

    public function getCompletedSteps(): int
    {
        $stepResults = $this->step_results ?: [];
        return count(array_filter($stepResults, fn($result) => $result['completed'] ?? false));
    }

    public function getTotalSteps(): int
    {
        return $this->playbook->steps()->where('is_active', true)->count();
    }

    public function getProgressPercentage(): int
    {
        $totalSteps = $this->getTotalSteps();
        if ($totalSteps === 0) return 0;
        
        return round(($this->getCompletedSteps() / $totalSteps) * 100);
    }

    // Step result management
    public function completeStep(PlaybookStep $step, array $result = []): void
    {
        $stepResults = $this->step_results ?: [];
        $stepResults[$step->id] = [
            'step_id' => $step->id,
            'completed' => true,
            'completed_at' => now()->toISOString(),
            'result' => $result,
        ];

        $this->update([
            'step_results' => $stepResults,
            'current_step_order' => $this->getNextStep()?->sort_order ?? $this->current_step_order,
        ]);

        // Check if all steps are completed
        if (!$this->getNextStep() && $this->isInProgress()) {
            $this->complete();
        }
    }

    public function complete(string $outcome = 'successful', string $notes = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'duration_minutes' => now()->diffInMinutes($this->started_at),
            'outcome' => $outcome,
            'notes' => $notes,
        ]);

        // Update playbook success rate
        $this->playbook->updateSuccessRate();
    }

    public function abandon(string $notes = null): void
    {
        $this->update([
            'status' => 'abandoned',
            'notes' => $notes,
        ]);
    }
}