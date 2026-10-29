<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaybookStepExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'execution_id',
        'step_id',
        'user_id',
        'status',
        'started_at',
        'completed_at',
        'duration_seconds',
        'notes',
        'skipped_reason',
        'result_data',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'duration_seconds' => 'integer',
        'result_data' => 'array',
    ];

    public function execution(): BelongsTo
    {
        return $this->belongsTo(PlaybookExecution::class, 'execution_id');
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(PlaybookStep::class, 'step_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isSkipped(): bool
    {
        return $this->status === 'skipped';
    }

    public function start(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function complete(array $resultData = [], ?string $notes = null): void
    {
        $startedAt = $this->started_at ?? now();
        $completedAt = now();

        $this->update([
            'status' => 'completed',
            'completed_at' => $completedAt,
            'duration_seconds' => $completedAt->diffInSeconds($startedAt),
            'notes' => $notes,
            'result_data' => $resultData,
        ]);
    }

    public function skip(string $reason, ?string $notes = null): void
    {
        $this->update([
            'status' => 'skipped',
            'completed_at' => now(),
            'skipped_reason' => $reason,
            'notes' => $notes,
        ]);
    }

    public function getFormattedDuration(): string
    {
        if (! $this->duration_seconds) {
            return '-';
        }

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        if ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        }

        return "{$seconds}s";
    }
}
