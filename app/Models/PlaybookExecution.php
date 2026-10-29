<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function stepExecutions(): HasMany
    {
        return $this->hasMany(PlaybookStepExecution::class, 'execution_id');
    }

    public function completedStepExecutions(): HasMany
    {
        return $this->hasMany(PlaybookStepExecution::class, 'execution_id')
            ->where('status', 'completed');
    }

    public function skippedStepExecutions(): HasMany
    {
        return $this->hasMany(PlaybookStepExecution::class, 'execution_id')
            ->where('status', 'skipped');
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
        return $this->stepExecutions()
            ->whereIn('status', ['completed', 'skipped'])
            ->count();
    }

    public function getCompletedOnlySteps(): int
    {
        return $this->completedStepExecutions()->count();
    }

    public function getSkippedSteps(): int
    {
        return $this->skippedStepExecutions()->count();
    }

    public function getTotalSteps(): int
    {
        return $this->playbook->steps()->where('is_active', true)->count();
    }

    public function getProgressPercentage(): int
    {
        $totalSteps = $this->getTotalSteps();

        if ($totalSteps === 0) {
            return 0;
        }

        return round(($this->getCompletedSteps() / $totalSteps) * 100);
    }

    public function getTotalDurationSeconds(): int
    {
        return $this->stepExecutions()
            ->whereNotNull('duration_seconds')
            ->sum('duration_seconds');
    }

    public function getFormattedTotalDuration(): string
    {
        $totalSeconds = $this->getTotalDurationSeconds();

        if ($totalSeconds === 0) {
            return '-';
        }

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m {$seconds}s";
        }

        if ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        }

        return "{$seconds}s";
    }

    public function initializeStepExecutions(): void
    {
        $steps = $this->playbook->steps()->where('is_active', true)->get();

        foreach ($steps as $step) {
            $this->stepExecutions()->firstOrCreate(
                ['step_id' => $step->id],
                [
                    'user_id' => $this->user_id,
                    'status' => 'pending',
                ]
            );
        }
    }

    public function getStepExecution(PlaybookStep $step): ?PlaybookStepExecution
    {
        return $this->stepExecutions()->where('step_id', $step->id)->first();
    }

    public function startStep(PlaybookStep $step): PlaybookStepExecution
    {
        $stepExecution = $this->getStepExecution($step);

        if (! $stepExecution) {
            $stepExecution = $this->stepExecutions()->create([
                'step_id' => $step->id,
                'user_id' => $this->user_id,
                'status' => 'pending',
            ]);
        }

        $stepExecution->start();

        return $stepExecution;
    }

    public function completeStep(PlaybookStep $step, array $result = [], ?string $notes = null): void
    {
        $stepExecution = $this->getStepExecution($step);

        if (! $stepExecution) {
            $stepExecution = $this->stepExecutions()->create([
                'step_id' => $step->id,
                'user_id' => $this->user_id,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        $stepExecution->complete($result, $notes);

        // Update step_results for backward compatibility
        $stepResults = $this->step_results ?: [];
        $stepResults[$step->id] = [
            'step_id' => $step->id,
            'completed' => true,
            'completed_at' => now()->toISOString(),
            'result' => $result,
            'notes' => $notes,
        ];

        $this->update([
            'step_results' => $stepResults,
            'current_step_order' => $this->getNextStep()?->sort_order ?? $this->current_step_order,
        ]);

        // Check if all steps are completed
        if (! $this->getNextStep() && $this->isInProgress()) {
            $this->complete();
        }
    }

    public function skipStep(PlaybookStep $step, string $reason, ?string $notes = null): void
    {
        $stepExecution = $this->getStepExecution($step);

        if (! $stepExecution) {
            $stepExecution = $this->stepExecutions()->create([
                'step_id' => $step->id,
                'user_id' => $this->user_id,
                'status' => 'pending',
            ]);
        }

        $stepExecution->skip($reason, $notes);

        // Update step_results for backward compatibility
        $stepResults = $this->step_results ?: [];
        $stepResults[$step->id] = [
            'step_id' => $step->id,
            'completed' => false,
            'skipped' => true,
            'skipped_at' => now()->toISOString(),
            'skipped_reason' => $reason,
            'notes' => $notes,
        ];

        $this->update([
            'step_results' => $stepResults,
            'current_step_order' => $this->getNextStep()?->sort_order ?? $this->current_step_order,
        ]);

        // Check if all steps are completed or skipped
        if (! $this->getNextStep() && $this->isInProgress()) {
            $this->complete('partial');
        }
    }

    public function complete(string $outcome = 'successful', ?string $notes = null): void
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

    public function abandon(?string $notes = null): void
    {
        $this->update([
            'status' => 'abandoned',
            'notes' => $notes,
        ]);
    }

    public function getStepsWithExecutions(): array
    {
        $steps = $this->playbook->steps()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $stepExecutionsMap = $this->stepExecutions()
            ->get()
            ->keyBy('step_id');

        return $steps->map(function ($step) use ($stepExecutionsMap) {
            return [
                'step' => $step,
                'execution' => $stepExecutionsMap->get($step->id),
            ];
        })->toArray();
    }
}
