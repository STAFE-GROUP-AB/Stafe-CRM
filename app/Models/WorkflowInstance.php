<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkflowInstance extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_template_id',
        'status',
        'entity_type',
        'entity_id',
        'context',
        'started_at',
        'completed_at',
        'error_message',
    ];

    protected $casts = [
        'context' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function workflowTemplate(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplate::class);
    }

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
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

    /**
     * Mark instance as started
     */
    public function markAsStarted(): void
    {
        $this->update([
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    /**
     * Mark instance as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark instance as failed
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
     * Check if instance is in progress
     */
    public function isInProgress(): bool
    {
        return in_array($this->status, ['pending', 'running']);
    }

    /**
     * Check if instance is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if instance has failed
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Execute the workflow instance
     */
    public function execute(): void
    {
        $this->markAsStarted();

        try {
            $steps = $this->workflowTemplate->steps()->active()->get();
            $context = $this->context ?? [];

            foreach ($steps as $step) {
                $result = $step->execute($this, $context);
                
                if (!$result['success']) {
                    $this->markAsFailed($result['message'] ?? 'Step execution failed');
                    return;
                }

                // Update context with step results
                $context = array_merge($context, $result);
            }

            $this->markAsCompleted();
        } catch (\Exception $e) {
            $this->markAsFailed($e->getMessage());
        }
    }
}