<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerJourneyStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'order_index',
        'color',
        'expected_actions',
        'success_criteria',
        'is_active'
    ];

    protected $casts = [
        'expected_actions' => 'array',
        'success_criteria' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get the customer journeys in this stage.
     */
    public function customerJourneys(): HasMany
    {
        return $this->hasMany(CustomerJourney::class, 'current_stage_id');
    }

    /**
     * Scope for active stages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered stages.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index');
    }

    /**
     * Get the next stage in the journey.
     */
    public function getNextStageAttribute(): ?self
    {
        return static::where('order_index', '>', $this->order_index)
                     ->where('is_active', true)
                     ->orderBy('order_index')
                     ->first();
    }

    /**
     * Get the previous stage in the journey.
     */
    public function getPreviousStageAttribute(): ?self
    {
        return static::where('order_index', '<', $this->order_index)
                     ->where('is_active', true)
                     ->orderBy('order_index', 'desc')
                     ->first();
    }

    /**
     * Get count of customers in this stage.
     */
    public function getCustomerCountAttribute(): int
    {
        return $this->customerJourneys()->count();
    }
}