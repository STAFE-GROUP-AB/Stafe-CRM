<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'deal_id',
        'commission_amount',
        'commission_rate',
        'commission_type',
        'tier_level',
        'base_amount',
        'bonus_amount',
        'calculation_rules',
        'payment_status',
        'payment_date',
        'payment_period_start',
        'payment_period_end',
        'dispute_status',
        'dispute_reason',
        'approved_by',
        'approved_at',
        'notes',
        'last_calculated_at',
    ];

    protected $casts = [
        'commission_amount' => 'decimal:10,2',
        'commission_rate' => 'decimal:5,4',
        'base_amount' => 'decimal:10,2',
        'bonus_amount' => 'decimal:10,2',
        'calculation_rules' => 'array',
        'payment_date' => 'date',
        'payment_period_start' => 'date',
        'payment_period_end' => 'date',
        'approved_at' => 'datetime',
        'last_calculated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusColor(): string
    {
        return match ($this->payment_status) {
            'paid' => 'green',
            'approved' => 'blue',
            'pending' => 'yellow',
            'disputed' => 'orange',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeDisputed($query)
    {
        return $query->where('dispute_status', 'open');
    }

    public function scopeByPeriod($query, $start, $end)
    {
        return $query->whereBetween('payment_period_start', [$start, $end]);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getTotalCommission(): float
    {
        return $this->base_amount + $this->bonus_amount;
    }

    public static function getCommissionTypes(): array
    {
        return [
            'percentage' => 'Percentage',
            'flat_rate' => 'Flat Rate',
            'tiered' => 'Tiered',
            'accelerator' => 'Accelerator',
        ];
    }

    public static function getPaymentStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'paid' => 'Paid',
            'disputed' => 'Disputed',
            'cancelled' => 'Cancelled',
        ];
    }
}