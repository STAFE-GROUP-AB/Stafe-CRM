<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerLoyaltyPoints extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'loyalty_program_id',
        'total_points',
        'available_points',
        'redeemed_points',
        'current_tier',
        'tier_achieved_at',
        'tier_benefits'
    ];

    protected $casts = [
        'tier_benefits' => 'array',
        'tier_achieved_at' => 'datetime'
    ];

    /**
     * Get the contact these points belong to.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the loyalty program.
     */
    public function loyaltyProgram(): BelongsTo
    {
        return $this->belongsTo(LoyaltyProgram::class);
    }

    /**
     * Add points to the customer's account.
     */
    public function addPoints(int $points, string $reason = null): void
    {
        $oldTier = $this->current_tier;
        
        $this->increment('total_points', $points);
        $this->increment('available_points', $points);
        
        // Check for tier upgrade
        $newTier = $this->loyaltyProgram->calculateTier($this->total_points);
        
        if ($newTier !== $oldTier) {
            $this->upgradeTier($newTier);
        }
        
        // Log the transaction (you could create a separate model for this)
        $this->logPointTransaction('earned', $points, $reason);
    }

    /**
     * Redeem points from the customer's account.
     */
    public function redeemPoints(int $points, string $rewardId = null): bool
    {
        if ($this->available_points < $points) {
            return false;
        }
        
        $this->decrement('available_points', $points);
        $this->increment('redeemed_points', $points);
        
        // Log the transaction
        $this->logPointTransaction('redeemed', $points, $rewardId);
        
        return true;
    }

    /**
     * Upgrade customer to new tier.
     */
    private function upgradeTier(string $newTier): void
    {
        $tierData = $this->loyaltyProgram->getTier($newTier);
        
        $this->update([
            'current_tier' => $newTier,
            'tier_achieved_at' => now(),
            'tier_benefits' => $tierData['benefits'] ?? []
        ]);
    }

    /**
     * Log point transaction.
     */
    private function logPointTransaction(string $type, int $points, string $reason = null): void
    {
        // In a real implementation, you might want to create a separate model for this
        // For now, we'll just add it to the activity log
        activity()
            ->performedOn($this->contact)
            ->withProperties([
                'loyalty_program_id' => $this->loyalty_program_id,
                'points' => $points,
                'type' => $type,
                'reason' => $reason,
                'balance_after' => $this->available_points
            ])
            ->log("Customer {$type} {$points} loyalty points");
    }

    /**
     * Get points needed for next tier.
     */
    public function getPointsToNextTierAttribute(): ?int
    {
        $currentTierIndex = null;
        $tiers = $this->loyaltyProgram->tiers;
        
        // Find current tier index
        foreach ($tiers as $index => $tier) {
            if ($tier['name'] === $this->current_tier) {
                $currentTierIndex = $index;
                break;
            }
        }
        
        // Get next tier
        if ($currentTierIndex !== null && isset($tiers[$currentTierIndex + 1])) {
            $nextTier = $tiers[$currentTierIndex + 1];
            return max(0, $nextTier['points_required'] - $this->total_points);
        }
        
        return null; // Already at highest tier
    }

    /**
     * Get tier progress percentage.
     */
    public function getTierProgressAttribute(): float
    {
        $pointsToNext = $this->points_to_next_tier;
        
        if ($pointsToNext === null) {
            return 100; // Max tier reached
        }
        
        $currentTierData = $this->loyaltyProgram->getTier($this->current_tier);
        $currentTierPoints = $currentTierData['points_required'] ?? 0;
        
        $tiers = $this->loyaltyProgram->tiers;
        $nextTierPoints = null;
        
        foreach ($tiers as $tier) {
            if ($tier['points_required'] > $currentTierPoints) {
                $nextTierPoints = $tier['points_required'];
                break;
            }
        }
        
        if ($nextTierPoints === null) {
            return 100;
        }
        
        $progress = ($this->total_points - $currentTierPoints) / ($nextTierPoints - $currentTierPoints);
        return round($progress * 100, 1);
    }

    /**
     * Check if customer can redeem a specific reward.
     */
    public function canRedeemReward(string $rewardId): bool
    {
        $reward = $this->loyaltyProgram->getReward($rewardId);
        
        if (!$reward) {
            return false;
        }
        
        return $this->available_points >= $reward['points_cost'];
    }
}