<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventTriggerExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_trigger_id',
        'entity_type',
        'entity_id',
        'context',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'error_message',
        'retry_count',
        'parent_execution_id',
    ];

    protected $casts = [
        'context' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function eventTrigger(): BelongsTo
    {
        return $this->belongsTo(EventTrigger::class);
    }

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    public function parentExecution(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_execution_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeScheduled($query)
    {
        return $query->where('scheduled_at', '<=', now());
    }

    /**
     * Mark execution as started
     */
    public function markAsStarted(): void
    {
        $this->update([
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    /**
     * Mark execution as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark execution as failed
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'completed_at' => now(),
        ]);
    }

    /**
     * Get execution duration in seconds
     */
    public function getDurationAttribute(): ?int
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        return $this->completed_at->diffInSeconds($this->started_at);
    }

    /**
     * Check if execution is ready to run
     */
    public function isReadyToRun(): bool
    {
        return $this->status === 'pending' && $this->scheduled_at <= now();
    }

    /**
     * Check if execution has failed
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if execution is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if this is a retry execution
     */
    public function isRetry(): bool
    {
        return $this->retry_count > 0 || $this->parent_execution_id !== null;
    }
}