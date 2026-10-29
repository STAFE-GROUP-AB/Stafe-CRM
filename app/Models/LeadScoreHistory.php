<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadScoreHistory extends Model
{
    use HasFactory;

    protected $table = 'lead_score_history';

    protected $fillable = [
        'lead_score_id',
        'contact_id',
        'previous_score',
        'new_score',
        'change_reason',
        'factors_snapshot',
        'changed_by',
    ];

    protected $casts = [
        'factors_snapshot' => 'array',
        'previous_score' => 'integer',
        'new_score' => 'integer',
    ];

    public function leadScore(): BelongsTo
    {
        return $this->belongsTo(LeadScore::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Get the score change (positive or negative)
     */
    public function getScoreChangeAttribute(): int
    {
        if ($this->previous_score === null) {
            return $this->new_score;
        }

        return $this->new_score - $this->previous_score;
    }

    /**
     * Get formatted score change with direction indicator
     */
    public function getFormattedChangeAttribute(): string
    {
        $change = $this->score_change;

        if ($change > 0) {
            return "+{$change}";
        }

        return (string) $change;
    }

    /**
     * Check if score increased
     */
    public function isIncrease(): bool
    {
        return $this->score_change > 0;
    }

    /**
     * Check if score decreased
     */
    public function isDecrease(): bool
    {
        return $this->score_change < 0;
    }

    /**
     * Scope for recent changes
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for significant changes (more than X points)
     */
    public function scopeSignificant($query, int $threshold = 10)
    {
        return $query->whereRaw('ABS(new_score - COALESCE(previous_score, 0)) >= ?', [$threshold]);
    }

    /**
     * Scope for manual adjustments
     */
    public function scopeManualAdjustments($query)
    {
        return $query->where('change_reason', 'like', 'Manual adjustment:%');
    }
}
