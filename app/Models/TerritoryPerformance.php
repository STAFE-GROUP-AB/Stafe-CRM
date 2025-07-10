<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TerritoryPerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'territory_name',
        'territory_description',
        'performance_score',
        'revenue_target',
        'revenue_actual',
        'revenue_percentage',
        'deal_count_target',
        'deal_count_actual',
        'activity_score',
        'optimization_recommendations',
        'market_potential',
        'competition_density',
        'territory_balance_score',
        'period_start',
        'period_end',
        'last_calculated_at',
    ];

    protected $casts = [
        'performance_score' => 'decimal:4',
        'revenue_target' => 'decimal:10,2',
        'revenue_actual' => 'decimal:10,2',
        'revenue_percentage' => 'decimal:5,2',
        'activity_score' => 'decimal:4',
        'optimization_recommendations' => 'array',
        'market_potential' => 'array',
        'competition_density' => 'decimal:4',
        'territory_balance_score' => 'decimal:4',
        'period_start' => 'date',
        'period_end' => 'date',
        'last_calculated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'assigned_to', 'user_id')
            ->whereBetween('created_at', [$this->period_start, $this->period_end]);
    }

    public function getPerformanceColor(): string
    {
        return match (true) {
            $this->revenue_percentage >= 100 => 'green',
            $this->revenue_percentage >= 80 => 'yellow',
            $this->revenue_percentage >= 60 => 'orange',
            default => 'red'
        };
    }

    public function getPerformanceLevel(): string
    {
        return match (true) {
            $this->revenue_percentage >= 120 => 'excellent',
            $this->revenue_percentage >= 100 => 'on_target',
            $this->revenue_percentage >= 80 => 'below_target',
            default => 'underperforming'
        };
    }

    public function scopeCurrentPeriod($query)
    {
        $now = now();
        return $query->where('period_start', '<=', $now)
                    ->where('period_end', '>=', $now);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}