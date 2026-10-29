<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerHealthScoreHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'overall_score',
        'score_breakdown',
        'health_status',
        'previous_score',
        'previous_status',
        'score_change',
        'trigger',
    ];

    protected $casts = [
        'score_breakdown' => 'array',
        'overall_score' => 'decimal:2',
        'previous_score' => 'decimal:2',
        'score_change' => 'decimal:2',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Scope for recent history.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for specific trigger types.
     */
    public function scopeByTrigger($query, string $trigger)
    {
        return $query->where('trigger', $trigger);
    }

    /**
     * Get health status color for UI display.
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
     * Get trend direction based on score change.
     */
    public function getTrendDirectionAttribute(): string
    {
        if ($this->score_change === null || abs($this->score_change) < 2) {
            return 'stable';
        }

        return $this->score_change > 0 ? 'improving' : 'declining';
    }
}
