<?php

namespace App\Models;

use App\Services\CustomerHealthService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerHealthScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'overall_score',
        'previous_score',
        'score_breakdown',
        'health_status',
        'trend',
        'trend_percentage',
        'risk_factors',
        'improvement_suggestions',
        'last_calculated_at',
        'alert_sent_at',
    ];

    protected $casts = [
        'score_breakdown' => 'array',
        'risk_factors' => 'array',
        'improvement_suggestions' => 'array',
        'overall_score' => 'decimal:2',
        'previous_score' => 'decimal:2',
        'trend_percentage' => 'decimal:2',
        'last_calculated_at' => 'datetime',
        'alert_sent_at' => 'datetime',
    ];

    /**
     * Get the contact this health score belongs to.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the score history for this contact.
     */
    public function history(): HasMany
    {
        return $this->hasMany(CustomerHealthScoreHistory::class, 'contact_id', 'contact_id');
    }

    /**
     * Scope for at-risk customers.
     */
    public function scopeAtRisk($query)
    {
        return $query->whereIn('health_status', ['at_risk', 'critical']);
    }

    /**
     * Scope for healthy customers.
     */
    public function scopeHealthy($query)
    {
        return $query->whereIn('health_status', ['excellent', 'good']);
    }

    /**
     * Scope for critical customers only.
     */
    public function scopeCritical($query)
    {
        return $query->where('health_status', 'critical');
    }

    /**
     * Scope for declining scores.
     */
    public function scopeDeclining($query)
    {
        return $query->where('trend', 'declining');
    }

    /**
     * Scope for improving scores.
     */
    public function scopeImproving($query)
    {
        return $query->where('trend', 'improving');
    }

    /**
     * Get the health status color for UI display.
     */
    public function getHealthStatusColorAttribute(): string
    {
        return match ($this->health_status) {
            'excellent' => 'green',
            'good' => 'blue',
            'at_risk' => 'yellow',
            'critical' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get the trend color for UI display.
     */
    public function getTrendColorAttribute(): string
    {
        return match ($this->trend) {
            'improving' => 'green',
            'declining' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get the trend icon for UI display.
     */
    public function getTrendIconAttribute(): string
    {
        return match ($this->trend) {
            'improving' => 'arrow-up',
            'declining' => 'arrow-down',
            default => 'minus'
        };
    }

    /**
     * Get the score as a percentage.
     */
    public function getScorePercentageAttribute(): float
    {
        return round($this->overall_score, 1);
    }

    /**
     * Get formatted score change.
     */
    public function getFormattedScoreChangeAttribute(): string
    {
        if ($this->previous_score === null) {
            return '-';
        }

        $change = $this->overall_score - $this->previous_score;
        $prefix = $change >= 0 ? '+' : '';

        return "{$prefix}" . round($change, 1);
    }

    /**
     * Check if the score needs updating (older than 24 hours).
     */
    public function needsUpdate(): bool
    {
        if (!$this->last_calculated_at) {
            return true;
        }

        return $this->last_calculated_at->lt(now()->subDays(1));
    }

    /**
     * Calculate and update health score using the CustomerHealthService.
     */
    public function recalculate(string $trigger = 'manual'): self
    {
        $service = app(CustomerHealthService::class);

        return $service->calculateForContact($this->contact, $trigger);
    }

    /**
     * Get the highest risk factor.
     */
    public function getHighestRiskAttribute(): ?array
    {
        if (empty($this->risk_factors)) {
            return null;
        }

        return collect($this->risk_factors)
            ->sortBy('score')
            ->first();
    }

    /**
     * Get the top improvement suggestion.
     */
    public function getTopSuggestionAttribute(): ?array
    {
        if (empty($this->improvement_suggestions)) {
            return null;
        }

        return collect($this->improvement_suggestions)
            ->first();
    }

    /**
     * Check if the customer is in a critical state.
     */
    public function isCritical(): bool
    {
        return $this->health_status === 'critical';
    }

    /**
     * Check if the customer is at risk.
     */
    public function isAtRisk(): bool
    {
        return in_array($this->health_status, ['at_risk', 'critical']);
    }

    /**
     * Check if the customer is healthy.
     */
    public function isHealthy(): bool
    {
        return in_array($this->health_status, ['excellent', 'good']);
    }

    /**
     * Get the score breakdown as a formatted array for display.
     */
    public function getFormattedBreakdownAttribute(): array
    {
        $labels = [
            'engagement' => 'Engagement',
            'support_satisfaction' => 'Support Satisfaction',
            'product_usage' => 'Product Usage',
            'payment_history' => 'Payment History',
            'communication_frequency' => 'Communication',
            'nps_csat' => 'NPS/CSAT',
            'deal_activity' => 'Deal Activity',
            'recency' => 'Recency',
        ];

        $breakdown = [];
        foreach ($this->score_breakdown ?? [] as $factor => $score) {
            $breakdown[] = [
                'factor' => $factor,
                'label' => $labels[$factor] ?? ucfirst(str_replace('_', ' ', $factor)),
                'score' => round($score, 1),
                'color' => $this->getFactorColor($score),
            ];
        }

        return $breakdown;
    }

    /**
     * Get color based on factor score.
     */
    protected function getFactorColor(float $score): string
    {
        if ($score >= 70) {
            return 'green';
        }

        if ($score >= 50) {
            return 'yellow';
        }

        return 'red';
    }
}