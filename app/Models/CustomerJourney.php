<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerJourney extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'current_stage_id',
        'stage_entered_at',
        'stage_history',
        'touchpoints',
        'progression_score'
    ];

    protected $casts = [
        'stage_history' => 'array',
        'touchpoints' => 'array',
        'stage_entered_at' => 'datetime',
        'progression_score' => 'decimal:2'
    ];

    /**
     * Get the contact this journey belongs to.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the current stage.
     */
    public function currentStage(): BelongsTo
    {
        return $this->belongsTo(CustomerJourneyStage::class, 'current_stage_id');
    }

    /**
     * Move to the next stage.
     */
    public function moveToNextStage(): bool
    {
        $nextStage = $this->currentStage->next_stage;
        
        if (!$nextStage) {
            return false;
        }

        return $this->moveToStage($nextStage->id);
    }

    /**
     * Move to a specific stage.
     */
    public function moveToStage(int $stageId): bool
    {
        $stage = CustomerJourneyStage::find($stageId);
        
        if (!$stage || !$stage->is_active) {
            return false;
        }

        // Update stage history
        $history = $this->stage_history ?? [];
        $history[] = [
            'stage_id' => $this->current_stage_id,
            'stage_name' => $this->currentStage->name,
            'entered_at' => $this->stage_entered_at,
            'exited_at' => now(),
            'duration_days' => $this->stage_entered_at->diffInDays(now())
        ];

        $this->update([
            'current_stage_id' => $stageId,
            'stage_entered_at' => now(),
            'stage_history' => $history
        ]);

        return true;
    }

    /**
     * Add a touchpoint to the journey.
     */
    public function addTouchpoint(string $type, array $data = []): void
    {
        $touchpoints = $this->touchpoints ?? [];
        $touchpoints[] = array_merge([
            'type' => $type,
            'timestamp' => now()->toISOString(),
            'stage_id' => $this->current_stage_id,
            'stage_name' => $this->currentStage->name
        ], $data);

        $this->update(['touchpoints' => $touchpoints]);
    }

    /**
     * Get time spent in current stage.
     */
    public function getTimeInCurrentStageAttribute(): int
    {
        return $this->stage_entered_at->diffInDays(now());
    }

    /**
     * Get total journey duration.
     */
    public function getTotalJourneyDurationAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Calculate progression score.
     */
    public function calculateProgressionScore(): float
    {
        // Simplified scoring - in reality, this would use ML models
        $factors = [
            'stage_progression' => $this->calculateStageProgression(),
            'touchpoint_frequency' => $this->calculateTouchpointFrequency(),
            'engagement_quality' => $this->calculateEngagementQuality()
        ];

        $weights = [
            'stage_progression' => 0.4,
            'touchpoint_frequency' => 0.3,
            'engagement_quality' => 0.3
        ];

        $score = 0;
        foreach ($factors as $factor => $value) {
            $score += $value * $weights[$factor];
        }

        $this->update(['progression_score' => round($score, 2)]);
        
        return $score;
    }

    /**
     * Calculate stage progression factor.
     */
    private function calculateStageProgression(): float
    {
        $totalStages = CustomerJourneyStage::active()->count();
        $currentStageIndex = $this->currentStage->order_index;
        
        return ($currentStageIndex / $totalStages) * 100;
    }

    /**
     * Calculate touchpoint frequency factor.
     */
    private function calculateTouchpointFrequency(): float
    {
        $touchpointCount = count($this->touchpoints ?? []);
        $daysSinceStart = max(1, $this->created_at->diffInDays(now()));
        
        $frequency = $touchpointCount / $daysSinceStart;
        
        // Normalize to 0-100 scale (assuming 1 touchpoint per day is optimal)
        return min(100, $frequency * 100);
    }

    /**
     * Calculate engagement quality factor.
     */
    private function calculateEngagementQuality(): float
    {
        // This would analyze touchpoint types and quality
        // For now, return a random score between 60-100
        return rand(60, 100);
    }
}