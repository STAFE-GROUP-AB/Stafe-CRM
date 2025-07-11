<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'subscription_plan_id',
        'status',
        'amount',
        'currency',
        'quantity',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'canceled_at',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'canceled_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function activeUserSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class)->where('status', 'active');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiring($query, $days = 30)
    {
        return $query->whereDate('current_period_end', '<=', now()->addDays($days));
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
               ($this->current_period_end && $this->current_period_end->isPast());
    }

    public function isInTrial(): bool
    {
        return $this->status === 'trial' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    public function hasTrialExpired(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    public function getRemainingTrialDaysAttribute(): int
    {
        if (!$this->trial_ends_at) {
            return 0;
        }
        
        return max(0, $this->trial_ends_at->diffInDays(now()));
    }

    public function canAddUsers(): bool
    {
        return $this->activeUserSubscriptions()->count() < $this->quantity;
    }

    public function getActiveUserCountAttribute(): int
    {
        return $this->activeUserSubscriptions()->count();
    }

    public function getTotalCostAttribute(): float
    {
        return $this->amount * $this->quantity;
    }
}
