<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CadenceEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'cadence_sequence_id',
        'contact_id',
        'enrolled_by_user_id',
        'status',
        'current_step',
        'enrolled_at',
        'next_action_at',
        'completed_at',
        'enrollment_data',
        'step_history',
        'exit_reason',
        'tenant_id',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'next_action_at' => 'datetime',
        'completed_at' => 'datetime',
        'enrollment_data' => 'array',
        'step_history' => 'array',
    ];

    public function cadenceSequence(): BelongsTo
    {
        return $this->belongsTo(CadenceSequence::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function enrolledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enrolled_by_user_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeDueForAction($query)
    {
        return $query->where('status', 'active')
                     ->where('next_action_at', '<=', now());
    }

    public function processNextStep(): array
    {
        if ($this->status !== 'active') {
            return ['status' => 'error', 'message' => 'Enrollment is not active'];
        }

        $nextStep = $this->cadenceSequence->cadenceSteps()
            ->where('step_number', $this->current_step + 1)
            ->first();

        if (!$nextStep) {
            $this->complete();
            return ['status' => 'completed', 'message' => 'Sequence completed'];
        }

        $result = $nextStep->execute($this->contact, $this);
        $this->recordStepExecution($nextStep, $result);

        if ($result['status'] === 'completed') {
            $this->current_step = $nextStep->step_number;
            $this->calculateNextAction();
        } elseif ($result['status'] === 'failed') {
            $this->status = 'failed';
            $this->exit_reason = $result['reason'] ?? 'Step execution failed';
        }

        $this->save();

        return $result;
    }

    public function pause($reason = null): void
    {
        $this->status = 'paused';
        $this->exit_reason = $reason;
        $this->save();
    }

    public function resume(): void
    {
        if ($this->status === 'paused') {
            $this->status = 'active';
            $this->exit_reason = null;
            $this->calculateNextAction();
            $this->save();
        }
    }

    public function exit($reason): void
    {
        $this->status = 'exited';
        $this->exit_reason = $reason;
        $this->next_action_at = null;
        $this->save();
    }

    public function complete(): void
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->next_action_at = null;
        $this->save();
    }

    public function getProgress(): array
    {
        $totalSteps = $this->cadenceSequence->total_steps;
        $completedSteps = $this->current_step;
        
        return [
            'total_steps' => $totalSteps,
            'completed_steps' => $completedSteps,
            'remaining_steps' => $totalSteps - $completedSteps,
            'progress_percentage' => $totalSteps > 0 ? ($completedSteps / $totalSteps) * 100 : 0,
        ];
    }

    public function getDuration(): ?int
    {
        if (!$this->completed_at) {
            return $this->enrolled_at->diffInDays(now());
        }
        
        return $this->enrolled_at->diffInDays($this->completed_at);
    }

    public function getEngagementScore(): float
    {
        $stepHistory = $this->step_history ?? [];
        if (empty($stepHistory)) return 0;

        $totalSteps = count($stepHistory);
        $successfulSteps = collect($stepHistory)->where('status', 'completed')->count();
        
        return $totalSteps > 0 ? ($successfulSteps / $totalSteps) * 100 : 0;
    }

    private function recordStepExecution($step, $result): void
    {
        $stepHistory = $this->step_history ?? [];
        
        $stepHistory[] = [
            'step_id' => $step->id,
            'step_number' => $step->step_number,
            'step_name' => $step->name,
            'action_type' => $step->action_type,
            'status' => $result['status'],
            'reason' => $result['reason'] ?? null,
            'data' => $result['data'] ?? null,
            'executed_at' => now()->toISOString(),
        ];

        $this->step_history = $stepHistory;
    }

    private function calculateNextAction(): void
    {
        $nextStep = $this->cadenceSequence->cadenceSteps()
            ->where('step_number', $this->current_step + 1)
            ->first();

        if ($nextStep) {
            $this->next_action_at = now()
                ->addDays($nextStep->delay_days)
                ->addHours($nextStep->delay_hours);
        } else {
            $this->next_action_at = null;
        }
    }
}