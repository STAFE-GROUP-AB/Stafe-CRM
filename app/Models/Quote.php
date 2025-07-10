<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_number',
        'title',
        'description',
        'deal_id',
        'company_id',
        'contact_id',
        'created_by',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'status',
        'valid_until',
        'terms_and_conditions',
        'custom_fields',
        'sent_at',
        'viewed_at',
        'accepted_at',
        'rejected_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'valid_until' => 'date',
        'terms_and_conditions' => 'array',
        'custom_fields' => 'array',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(QuoteApproval::class);
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(QuoteSignature::class);
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    // Status helpers
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || ($this->valid_until && $this->valid_until->isPast());
    }

    // Approval helpers
    public function needsApproval(): bool
    {
        return $this->approvals()->where('status', 'pending')->exists();
    }

    public function isApproved(): bool
    {
        return $this->approvals()->where('status', 'approved')->exists() && 
               !$this->approvals()->where('status', 'pending')->exists();
    }

    public function isApprovalRejected(): bool
    {
        return $this->approvals()->where('status', 'rejected')->exists();
    }

    // Financial calculations
    public function calculateSubtotal(): float
    {
        return $this->items->sum('line_total');
    }

    public function calculateTotal(): float
    {
        return $this->subtotal + $this->tax_amount - $this->discount_amount;
    }

    public function updateTotals(): void
    {
        $this->subtotal = $this->calculateSubtotal();
        $this->total_amount = $this->calculateTotal();
        $this->save();
    }

    // Generate quote number
    public static function generateQuoteNumber(): string
    {
        $year = now()->year;
        $month = now()->format('m');
        $lastQuote = static::whereYear('created_at', $year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastQuote ? (intval(substr($lastQuote->quote_number, -4)) + 1) : 1;

        return sprintf('QUO-%s%s-%04d', $year, $month, $sequence);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quote) {
            if (empty($quote->quote_number)) {
                $quote->quote_number = static::generateQuoteNumber();
            }
        });
    }
}