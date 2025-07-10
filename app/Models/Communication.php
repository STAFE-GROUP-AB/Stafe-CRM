<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Communication extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'direction',
        'status',
        'communicable_type',
        'communicable_id',
        'user_id',
        'content',
        'metadata',
        'duration_seconds',
        'recording_url',
        'transcript',
        'sentiment_score',
        'external_id',
        'provider',
        'provider_data',
        'from_number',
        'to_number',
        'from_email',
        'to_email',
        'ai_analysis',
        'follow_up_suggestions',
    ];

    protected $casts = [
        'metadata' => 'array',
        'provider_data' => 'array',
        'ai_analysis' => 'array',
        'follow_up_suggestions' => 'array',
        'sentiment_score' => 'decimal:2',
        'duration_seconds' => 'integer',
    ];

    /**
     * Get the entity this communication belongs to
     */
    public function communicable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who handled this communication
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for specific communication types
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for specific direction
     */
    public function scopeDirection($query, string $direction)
    {
        return $query->where('direction', $direction);
    }

    /**
     * Scope for calls only
     */
    public function scopeCalls($query)
    {
        return $query->whereIn('type', ['call', 'video']);
    }

    /**
     * Scope for messages only
     */
    public function scopeMessages($query)
    {
        return $query->whereIn('type', ['sms', 'whatsapp', 'chat']);
    }

    /**
     * Check if this communication has a recording
     */
    public function hasRecording(): bool
    {
        return !empty($this->recording_url);
    }

    /**
     * Check if this communication has a transcript
     */
    public function hasTranscript(): bool
    {
        return !empty($this->transcript);
    }

    /**
     * Get sentiment as human readable
     */
    public function getSentimentAttribute(): string
    {
        if ($this->sentiment_score === null) {
            return 'neutral';
        }

        return match (true) {
            $this->sentiment_score >= 0.3 => 'positive',
            $this->sentiment_score <= -0.3 => 'negative',
            default => 'neutral'
        };
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration_seconds) {
            return '0:00';
        }

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    /**
     * Mark as completed
     */
    public function markCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }

    /**
     * Mark as failed
     */
    public function markFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    /**
     * Add AI analysis
     */
    public function addAiAnalysis(array $analysis): void
    {
        $this->update(['ai_analysis' => array_merge($this->ai_analysis ?? [], $analysis)]);
    }

    /**
     * Add follow-up suggestions
     */
    public function addFollowUpSuggestions(array $suggestions): void
    {
        $this->update(['follow_up_suggestions' => $suggestions]);
    }
}