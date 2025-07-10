<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealRiskAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'deal_id',
        'risk_score',
        'risk_level',
        'risk_factors',
        'intervention_recommendations',
        'probability_to_close',
        'predicted_close_date',
        'confidence_score',
        'model_version',
        'last_analyzed_at',
        'ai_model_id',
    ];

    protected $casts = [
        'risk_factors' => 'array',
        'intervention_recommendations' => 'array',
        'probability_to_close' => 'float',
        'confidence_score' => 'float',
        'predicted_close_date' => 'date',
        'last_analyzed_at' => 'datetime',
    ];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function aiModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class);
    }

    public function getRiskColor(): string
    {
        return match ($this->risk_level) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray'
        };
    }

    public function scopeHighRisk($query)
    {
        return $query->whereIn('risk_level', ['high', 'critical']);
    }

    public function scopeByRiskLevel($query, $level)
    {
        return $query->where('risk_level', $level);
    }

    public function isStale(int $hoursThreshold = 24): bool
    {
        return $this->last_analyzed_at->diffInHours(now()) > $hoursThreshold;
    }
}