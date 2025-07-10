<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CadenceSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'trigger_conditions',
        'total_steps',
        'duration_days',
        'is_active',
        'exit_conditions',
        'success_metrics',
        'created_by_user_id',
        'tenant_id',
    ];

    protected $casts = [
        'trigger_conditions' => 'array',
        'is_active' => 'boolean',
        'exit_conditions' => 'array',
        'success_metrics' => 'array',
    ];

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function cadenceSteps(): HasMany
    {
        return $this->hasMany(CadenceStep::class)->orderBy('step_number');
    }

    public function cadenceEnrollments(): HasMany
    {
        return $this->hasMany(CadenceEnrollment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function canEnrollContact($contact): bool
    {
        // Check if contact already enrolled in this sequence
        if ($this->cadenceEnrollments()->where('contact_id', $contact->id)->where('status', 'active')->exists()) {
            return false;
        }

        // Check trigger conditions
        return $this->evaluateTriggerConditions($contact);
    }

    public function enrollContact($contact, $enrolledByUser): CadenceEnrollment
    {
        return $this->cadenceEnrollments()->create([
            'contact_id' => $contact->id,
            'enrolled_by_user_id' => $enrolledByUser->id,
            'status' => 'active',
            'current_step' => 0,
            'enrolled_at' => now(),
            'next_action_at' => $this->calculateNextActionTime(0),
            'tenant_id' => $this->tenant_id,
        ]);
    }

    public function getCompletionRate(): float
    {
        $totalEnrollments = $this->cadenceEnrollments()->count();
        if ($totalEnrollments === 0) return 0;

        $completedEnrollments = $this->cadenceEnrollments()->where('status', 'completed')->count();
        return ($completedEnrollments / $totalEnrollments) * 100;
    }

    public function getAverageCompletionTime(): ?int
    {
        $completedEnrollments = $this->cadenceEnrollments()
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->get();

        if ($completedEnrollments->isEmpty()) return null;

        $totalDays = $completedEnrollments->sum(function ($enrollment) {
            return $enrollment->enrolled_at->diffInDays($enrollment->completed_at);
        });

        return round($totalDays / $completedEnrollments->count());
    }

    public function getEngagementMetrics(): array
    {
        $enrollments = $this->cadenceEnrollments()->with('stepHistory')->get();
        
        $totalActions = 0;
        $successfulActions = 0;
        
        foreach ($enrollments as $enrollment) {
            $stepHistory = $enrollment->step_history ?? [];
            foreach ($stepHistory as $step) {
                $totalActions++;
                if ($step['status'] === 'completed' || $step['status'] === 'success') {
                    $successfulActions++;
                }
            }
        }

        return [
            'total_enrollments' => $enrollments->count(),
            'active_enrollments' => $enrollments->where('status', 'active')->count(),
            'completed_enrollments' => $enrollments->where('status', 'completed')->count(),
            'total_actions' => $totalActions,
            'successful_actions' => $successfulActions,
            'success_rate' => $totalActions > 0 ? ($successfulActions / $totalActions) * 100 : 0,
            'completion_rate' => $this->getCompletionRate(),
            'average_completion_time' => $this->getAverageCompletionTime(),
        ];
    }

    private function evaluateTriggerConditions($contact): bool
    {
        // Evaluate trigger conditions similar to LeadRoutingRule
        foreach ($this->trigger_conditions as $condition) {
            $field = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? '=';
            $value = $condition['value'] ?? null;

            if (!$field || $value === null) continue;

            $contactValue = data_get($contact, $field);
            
            if (!$this->evaluateCondition($contactValue, $operator, $value)) {
                return false;
            }
        }

        return true;
    }

    private function evaluateCondition($contactValue, $operator, $expectedValue): bool
    {
        // Same logic as LeadRoutingRule
        switch ($operator) {
            case '=':
                return $contactValue == $expectedValue;
            case '!=':
                return $contactValue != $expectedValue;
            case '>':
                return $contactValue > $expectedValue;
            case '>=':
                return $contactValue >= $expectedValue;
            case '<':
                return $contactValue < $expectedValue;
            case '<=':
                return $contactValue <= $expectedValue;
            case 'contains':
                return str_contains(strtolower($contactValue), strtolower($expectedValue));
            default:
                return false;
        }
    }

    private function calculateNextActionTime($stepNumber): ?\Carbon\Carbon
    {
        $nextStep = $this->cadenceSteps()->where('step_number', $stepNumber + 1)->first();
        if (!$nextStep) return null;

        return now()->addDays($nextStep->delay_days)->addHours($nextStep->delay_hours);
    }
}