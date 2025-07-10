<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'contact_id',
        'responses',
        'nps_score',
        'csat_score',
        'ces_score',
        'feedback',
        'is_completed',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'responses' => 'array',
        'is_completed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'nps_score' => 'decimal:1',
        'csat_score' => 'decimal:1',
        'ces_score' => 'decimal:1'
    ];

    /**
     * Get the survey this response belongs to.
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Get the contact who submitted the response.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Scope for completed responses.
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Mark response as completed.
     */
    public function markCompleted(): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now()
        ]);
    }

    /**
     * Get NPS category (Promoter, Passive, Detractor).
     */
    public function getNpsCategoryAttribute(): ?string
    {
        if (!$this->nps_score) {
            return null;
        }

        if ($this->nps_score >= 9) {
            return 'Promoter';
        } elseif ($this->nps_score >= 7) {
            return 'Passive';
        } else {
            return 'Detractor';
        }
    }

    /**
     * Get response time in minutes.
     */
    public function getResponseTimeAttribute(): ?int
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        return $this->started_at->diffInMinutes($this->completed_at);
    }
}