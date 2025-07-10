<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'approver_id',
        'status',
        'comments',
        'approved_amount',
        'discount_limit',
        'requested_at',
        'responded_at',
    ];

    protected $casts = [
        'approved_amount' => 'decimal:2',
        'discount_limit' => 'decimal:2',
        'requested_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}