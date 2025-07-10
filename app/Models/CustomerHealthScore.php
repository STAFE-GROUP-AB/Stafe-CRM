<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerHealthScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'overall_score',
        'score_breakdown',
        'health_status',
        'risk_factors',
        'improvement_suggestions',
        'last_calculated_at'
    ];

    protected $casts = [
        'score_breakdown' => 'array',
        'risk_factors' => 'array',
        'improvement_suggestions' => 'array',
        'overall_score' => 'decimal:2',
        'last_calculated_at' => 'datetime'
    ];

    /**
     * Get the contact this health score belongs to.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
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
     * Get the health status color for UI display.
     */
    public function getHealthStatusColorAttribute(): string
    {
        return match($this->health_status) {
            'excellent' => 'green',
            'good' => 'blue',
            'at_risk' => 'yellow',
            'critical' => 'red',
            default => 'gray'
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
     * Check if the score needs updating.
     */
    public function needsUpdate(): bool
    {
        return $this->last_calculated_at->lt(now()->subDays(1));
    }

    /**
     * Calculate and update health score.
     */
    public function recalculate(): void
    {
        // This would typically integrate with AI/ML services
        $factors = $this->calculateFactors();
        $overallScore = $this->calculateOverallScore($factors);
        $status = $this->determineHealthStatus($overallScore);

        $this->update([
            'overall_score' => $overallScore,
            'score_breakdown' => $factors,
            'health_status' => $status,
            'risk_factors' => $this->identifyRiskFactors($factors),
            'improvement_suggestions' => $this->generateSuggestions($factors),
            'last_calculated_at' => now()
        ]);
    }

    /**
     * Calculate individual health factors.
     */
    private function calculateFactors(): array
    {
        // Simplified calculation - in real implementation, this would use ML models
        return [
            'engagement' => rand(60, 100),
            'support_satisfaction' => rand(70, 100),
            'product_usage' => rand(50, 100),
            'payment_history' => rand(80, 100),
            'communication_frequency' => rand(40, 100)
        ];
    }

    /**
     * Calculate overall score from factors.
     */
    private function calculateOverallScore(array $factors): float
    {
        $weights = [
            'engagement' => 0.3,
            'support_satisfaction' => 0.2,
            'product_usage' => 0.25,
            'payment_history' => 0.15,
            'communication_frequency' => 0.1
        ];

        $weightedSum = 0;
        foreach ($factors as $factor => $score) {
            $weightedSum += $score * ($weights[$factor] ?? 0.1);
        }

        return round($weightedSum, 2);
    }

    /**
     * Determine health status based on score.
     */
    private function determineHealthStatus(float $score): string
    {
        if ($score >= 85) return 'excellent';
        if ($score >= 70) return 'good';
        if ($score >= 50) return 'at_risk';
        return 'critical';
    }

    /**
     * Identify risk factors.
     */
    private function identifyRiskFactors(array $factors): array
    {
        $risks = [];
        foreach ($factors as $factor => $score) {
            if ($score < 60) {
                $risks[] = ucfirst(str_replace('_', ' ', $factor));
            }
        }
        return $risks;
    }

    /**
     * Generate improvement suggestions.
     */
    private function generateSuggestions(array $factors): array
    {
        $suggestions = [];
        
        if ($factors['engagement'] < 70) {
            $suggestions[] = 'Increase engagement through personalized communication';
        }
        
        if ($factors['support_satisfaction'] < 80) {
            $suggestions[] = 'Follow up on recent support interactions';
        }
        
        if ($factors['product_usage'] < 60) {
            $suggestions[] = 'Provide product training or onboarding assistance';
        }

        return $suggestions;
    }
}