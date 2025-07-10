<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitiveIntelligence extends Model
{
    use HasFactory;

    protected $fillable = [
        'deal_id',
        'competitor_name',
        'competitor_strength',
        'competitor_weaknesses',
        'win_loss_probability',
        'competitive_factors',
        'battle_card_recommendations',
        'pricing_comparison',
        'feature_comparison',
        'source',
        'confidence_score',
        'last_updated_at',
    ];

    protected $casts = [
        'competitor_strength' => 'array',
        'competitor_weaknesses' => 'array',
        'competitive_factors' => 'array',
        'battle_card_recommendations' => 'array',
        'pricing_comparison' => 'array',
        'feature_comparison' => 'array',
        'win_loss_probability' => 'decimal:4',
        'confidence_score' => 'decimal:4',
        'last_updated_at' => 'datetime',
    ];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function scopeByCompetitor($query, $competitor)
    {
        return $query->where('competitor_name', $competitor);
    }

    public function scopeHighThreat($query, $threshold = 0.7)
    {
        return $query->where('win_loss_probability', '<=', $threshold);
    }

    public function getThreatLevel(): string
    {
        return match (true) {
            $this->win_loss_probability <= 0.3 => 'critical',
            $this->win_loss_probability <= 0.5 => 'high',
            $this->win_loss_probability <= 0.7 => 'medium',
            default => 'low'
        };
    }

    public function getThreatColor(): string
    {
        return match ($this->getThreatLevel()) {
            'critical' => 'red',
            'high' => 'orange', 
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray'
        };
    }
}