<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImportJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'filename',
        'original_filename',
        'status',
        'total_rows',
        'processed_rows',
        'successful_rows',
        'failed_rows',
        'mapping',
        'errors',
        'options',
        'started_at',
        'completed_at',
        'user_id',
    ];

    protected $casts = [
        'mapping' => 'array',
        'errors' => 'array',
        'options' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute(): int
    {
        if ($this->total_rows === 0) {
            return 0;
        }
        
        return (int) (($this->processed_rows / $this->total_rows) * 100);
    }

    /**
     * Get success rate percentage
     */
    public function getSuccessRateAttribute(): float
    {
        if ($this->processed_rows === 0) {
            return 0;
        }
        
        return round(($this->successful_rows / $this->processed_rows) * 100, 2);
    }

    /**
     * Check if import is in progress
     */
    public function isInProgress(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Check if import is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if import failed
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Mark import as started
     */
    public function markAsStarted(): void
    {
        $this->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);
    }

    /**
     * Mark import as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark import as failed
     */
    public function markAsFailed(array $errors = []): void
    {
        $this->update([
            'status' => 'failed',
            'errors' => $errors,
            'completed_at' => now(),
        ]);
    }

    /**
     * Update progress
     */
    public function updateProgress(int $processed, int $successful, int $failed): void
    {
        $this->update([
            'processed_rows' => $processed,
            'successful_rows' => $successful,
            'failed_rows' => $failed,
        ]);
    }
}