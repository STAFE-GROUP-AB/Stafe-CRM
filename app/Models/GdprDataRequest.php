<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GdprDataRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'requestable_type',
        'requestable_id',
        'type',
        'status',
        'description',
        'requester_email',
        'requester_name',
        'verification_details',
        'verified_at',
        'deadline',
        'completed_at',
        'processed_by',
        'processing_notes',
        'exported_data',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'deadline' => 'datetime',
        'completed_at' => 'datetime',
        'exported_data' => 'array',
    ];

    public function requestable(): MorphTo
    {
        return $this->morphTo();
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                    ->whereNotIn('status', ['completed', 'rejected']);
    }

    public function isOverdue(): bool
    {
        return $this->deadline && 
               $this->deadline->isPast() && 
               !in_array($this->status, ['completed', 'rejected']);
    }

    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function reject(string $reason = null): void
    {
        $this->update([
            'status' => 'rejected',
            'processing_notes' => $reason,
            'completed_at' => now(),
        ]);
    }
}