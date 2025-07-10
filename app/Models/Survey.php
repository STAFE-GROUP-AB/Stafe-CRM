<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'questions',
        'is_active',
        'trigger_conditions',
        'start_date',
        'end_date',
        'created_by'
    ];

    protected $casts = [
        'questions' => 'array',
        'trigger_conditions' => 'array',
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    /**
     * Get the user who created the survey.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the survey responses.
     */
    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    /**
     * Get completed responses only.
     */
    public function completedResponses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class)->where('is_completed', true);
    }

    /**
     * Scope for active surveys.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function($q) {
                         $q->whereNull('start_date')->orWhere('start_date', '<=', now());
                     })
                     ->where(function($q) {
                         $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                     });
    }

    /**
     * Get the completion rate.
     */
    public function getCompletionRateAttribute(): float
    {
        $total = $this->responses()->count();
        $completed = $this->completedResponses()->count();
        return $total > 0 ? round(($completed / $total) * 100, 1) : 0;
    }

    /**
     * Get average NPS score.
     */
    public function getAverageNpsAttribute(): ?float
    {
        if ($this->type !== 'nps') {
            return null;
        }

        return $this->completedResponses()
                    ->whereNotNull('nps_score')
                    ->avg('nps_score');
    }

    /**
     * Get average CSAT score.
     */
    public function getAverageCsatAttribute(): ?float
    {
        if ($this->type !== 'csat') {
            return null;
        }

        return $this->completedResponses()
                    ->whereNotNull('csat_score')
                    ->avg('csat_score');
    }

    /**
     * Check if survey is currently active.
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        if ($this->start_date && $this->start_date > $now) {
            return false;
        }

        if ($this->end_date && $this->end_date < $now) {
            return false;
        }

        return true;
    }
}