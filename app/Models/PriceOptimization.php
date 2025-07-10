<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceOptimization extends Model
{
    use HasFactory;

    protected $fillable = [
        'deal_id',
        'contact_id',
        'company_id',
        'suggested_price',
        'confidence_score',
        'price_factors',
        'market_analysis',
        'competitor_pricing',
        'historical_comparison',
        'discount_recommendations',
        'pricing_strategy',
        'margin_analysis',
        'model_version',
        'last_calculated_at',
        'ai_model_id',
    ];

    protected $casts = [
        'suggested_price' => 'decimal:10,2',
        'confidence_score' => 'decimal:4',
        'price_factors' => 'array',
        'market_analysis' => 'array',
        'competitor_pricing' => 'array',
        'historical_comparison' => 'array',
        'discount_recommendations' => 'array',
        'margin_analysis' => 'array',
        'last_calculated_at' => 'datetime',
    ];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function aiModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class);
    }

    public function getConfidenceColor(): string
    {
        return match (true) {
            $this->confidence_score >= 0.8 => 'green',
            $this->confidence_score >= 0.6 => 'yellow',
            $this->confidence_score >= 0.4 => 'orange',
            default => 'red'
        };
    }

    public function getConfidenceLevel(): string
    {
        return match (true) {
            $this->confidence_score >= 0.8 => 'high',
            $this->confidence_score >= 0.6 => 'medium',
            $this->confidence_score >= 0.4 => 'low',
            default => 'very_low'
        };
    }

    public function isStale(int $hoursThreshold = 72): bool
    {
        return $this->last_calculated_at->diffInHours(now()) > $hoursThreshold;
    }
}