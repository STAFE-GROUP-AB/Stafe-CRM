<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbTestParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'ab_test_id',
        'participant_type',
        'participant_id',
        'variant',
        'assigned_at',
        'context',
        'conversions',
        'engagement_score',
        'last_interaction_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'context' => 'array',
        'conversions' => 'array',
        'engagement_score' => 'decimal:2',
        'last_interaction_at' => 'datetime',
    ];

    public function abTest(): BelongsTo
    {
        return $this->belongsTo(AbTest::class);
    }

    public function participant(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeVariantA($query)
    {
        return $query->where('variant', 'a');
    }

    public function scopeVariantB($query)
    {
        return $query->where('variant', 'b');
    }

    public function scopeConverted($query, string $metricName = null)
    {
        if ($metricName) {
            return $query->whereJsonContainsKey('conversions', $metricName);
        }

        return $query->whereNotNull('conversions');
    }

    /**
     * Record a conversion for this participant
     */
    public function recordConversion(string $metricName, $value = 1, array $metadata = []): void
    {
        $conversions = $this->conversions ?? [];
        
        $conversions[$metricName] = [
            'value' => $value,
            'converted_at' => now()->toISOString(),
            'metadata' => $metadata,
        ];

        $this->update([
            'conversions' => $conversions,
            'last_interaction_at' => now(),
        ]);

        // Update engagement score
        $this->updateEngagementScore();
    }

    /**
     * Record multiple conversions at once
     */
    public function recordConversions(array $conversions, array $metadata = []): void
    {
        $existingConversions = $this->conversions ?? [];

        foreach ($conversions as $metricName => $value) {
            $existingConversions[$metricName] = [
                'value' => $value,
                'converted_at' => now()->toISOString(),
                'metadata' => $metadata,
            ];
        }

        $this->update([
            'conversions' => $existingConversions,
            'last_interaction_at' => now(),
        ]);

        $this->updateEngagementScore();
    }

    /**
     * Check if participant has converted for a metric
     */
    public function hasConverted(string $metricName): bool
    {
        return isset($this->conversions[$metricName]);
    }

    /**
     * Get conversion value for a metric
     */
    public function getConversionValue(string $metricName): mixed
    {
        return $this->conversions[$metricName]['value'] ?? null;
    }

    /**
     * Get conversion date for a metric
     */
    public function getConversionDate(string $metricName): ?\Carbon\Carbon
    {
        $dateString = $this->conversions[$metricName]['converted_at'] ?? null;
        
        return $dateString ? \Carbon\Carbon::parse($dateString) : null;
    }

    /**
     * Get time to conversion for a metric
     */
    public function getTimeToConversion(string $metricName): ?int
    {
        $conversionDate = $this->getConversionDate($metricName);
        
        return $conversionDate ? $this->assigned_at->diffInSeconds($conversionDate) : null;
    }

    /**
     * Record interaction (view, click, etc.)
     */
    public function recordInteraction(string $interactionType, array $data = []): void
    {
        $interactions = $this->context['interactions'] ?? [];
        
        $interactions[] = [
            'type' => $interactionType,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ];

        $context = $this->context ?? [];
        $context['interactions'] = $interactions;

        $this->update([
            'context' => $context,
            'last_interaction_at' => now(),
        ]);

        $this->updateEngagementScore();
    }

    /**
     * Update engagement score based on interactions and conversions
     */
    private function updateEngagementScore(): void
    {
        $score = 0;

        // Base score for participation
        $score += 1;

        // Score for interactions
        $interactions = $this->context['interactions'] ?? [];
        $score += count($interactions) * 0.1;

        // Score for conversions
        $conversions = $this->conversions ?? [];
        $score += count($conversions) * 2;

        // Bonus for recent activity
        if ($this->last_interaction_at && $this->last_interaction_at->isAfter(now()->subDays(7))) {
            $score += 1;
        }

        // Normalize to 0-10 scale
        $score = min($score, 10);

        $this->update(['engagement_score' => $score]);
    }

    /**
     * Get all interactions
     */
    public function getInteractions(): array
    {
        return $this->context['interactions'] ?? [];
    }

    /**
     * Get interactions of a specific type
     */
    public function getInteractionsByType(string $type): array
    {
        $interactions = $this->getInteractions();
        
        return array_filter($interactions, fn($interaction) => $interaction['type'] === $type);
    }

    /**
     * Count interactions of a specific type
     */
    public function countInteractions(string $type = null): int
    {
        if ($type === null) {
            return count($this->getInteractions());
        }

        return count($this->getInteractionsByType($type));
    }

    /**
     * Get first interaction date
     */
    public function getFirstInteractionDate(): ?\Carbon\Carbon
    {
        $interactions = $this->getInteractions();
        
        if (empty($interactions)) {
            return null;
        }

        $firstTimestamp = min(array_column($interactions, 'timestamp'));
        
        return \Carbon\Carbon::parse($firstTimestamp);
    }

    /**
     * Get last interaction date
     */
    public function getLastInteractionDate(): ?\Carbon\Carbon
    {
        return $this->last_interaction_at;
    }

    /**
     * Get participation duration in days
     */
    public function getParticipationDuration(): int
    {
        $endDate = $this->last_interaction_at ?? now();
        
        return $this->assigned_at->diffInDays($endDate);
    }

    /**
     * Check if participant is still active
     */
    public function isActive(): bool
    {
        // Consider participant active if they've interacted in the last 30 days
        return $this->last_interaction_at && $this->last_interaction_at->isAfter(now()->subDays(30));
    }

    /**
     * Get participant summary
     */
    public function getSummary(): array
    {
        return [
            'variant' => $this->variant,
            'assigned_at' => $this->assigned_at->toDateString(),
            'participation_duration_days' => $this->getParticipationDuration(),
            'total_interactions' => $this->countInteractions(),
            'total_conversions' => count($this->conversions ?? []),
            'engagement_score' => $this->engagement_score,
            'is_active' => $this->isActive(),
            'conversions' => array_keys($this->conversions ?? []),
        ];
    }

    /**
     * Export participant data for analysis
     */
    public function exportData(): array
    {
        return [
            'ab_test_id' => $this->ab_test_id,
            'participant_type' => $this->participant_type,
            'participant_id' => $this->participant_id,
            'variant' => $this->variant,
            'assigned_at' => $this->assigned_at->toISOString(),
            'context' => $this->context,
            'conversions' => $this->conversions,
            'engagement_score' => $this->engagement_score,
            'last_interaction_at' => $this->last_interaction_at?->toISOString(),
            'interactions_count' => $this->countInteractions(),
            'conversions_count' => count($this->conversions ?? []),
            'participation_duration_days' => $this->getParticipationDuration(),
            'is_active' => $this->isActive(),
        ];
    }
}