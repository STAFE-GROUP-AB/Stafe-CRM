<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'tiers',
        'point_rules',
        'rewards_catalog',
        'is_active',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'tiers' => 'array',
        'point_rules' => 'array',
        'rewards_catalog' => 'array',
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    /**
     * Get the customer loyalty points for this program.
     */
    public function customerPoints(): HasMany
    {
        return $this->hasMany(CustomerLoyaltyPoints::class);
    }

    /**
     * Scope for active programs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function($q) {
                         $q->whereNull('start_date')->orWhere('start_date', '<=', now());
                     })
                     ->where(function($q) {
                         $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                     });
    }

    /**
     * Get total enrolled customers.
     */
    public function getTotalCustomersAttribute(): int
    {
        return $this->customerPoints()->count();
    }

    /**
     * Get tier by name.
     */
    public function getTier(string $tierName): ?array
    {
        foreach ($this->tiers as $tier) {
            if ($tier['name'] === $tierName) {
                return $tier;
            }
        }
        return null;
    }

    /**
     * Calculate tier for given points.
     */
    public function calculateTier(int $totalPoints): string
    {
        $currentTier = 'Bronze'; // Default tier
        
        foreach ($this->tiers as $tier) {
            if ($totalPoints >= $tier['points_required']) {
                $currentTier = $tier['name'];
            }
        }
        
        return $currentTier;
    }

    /**
     * Get reward by ID.
     */
    public function getReward(string $rewardId): ?array
    {
        foreach ($this->rewards_catalog as $reward) {
            if ($reward['id'] === $rewardId) {
                return $reward;
            }
        }
        return null;
    }

    /**
     * Calculate points for action.
     */
    public function calculatePointsForAction(string $action, array $data = []): int
    {
        $rules = $this->point_rules['earning'] ?? [];
        
        foreach ($rules as $rule) {
            if ($rule['action'] === $action) {
                $basePoints = $rule['points'];
                
                // Apply multipliers if any
                if (isset($rule['multiplier_field']) && isset($data[$rule['multiplier_field']])) {
                    $basePoints *= $data[$rule['multiplier_field']];
                }
                
                return $basePoints;
            }
        }
        
        return 0;
    }

    /**
     * Check if program is currently active.
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        if ($this->start_date && $this->start_date > $now) {
            return false;
        }

        if ($this->end_date && $this->end_date < $now) {
            return false;
        }

        return true;
    }
}