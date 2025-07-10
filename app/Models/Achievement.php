<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'badge_image',
        'category',
        'type',
        'criteria',
        'points',
        'rarity',
        'is_repeatable',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'criteria' => 'array',
        'points' => 'integer',
        'is_repeatable' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    // Category helpers
    public function isSales(): bool
    {
        return $this->category === 'sales';
    }

    public function isActivity(): bool
    {
        return $this->category === 'activity';
    }

    public function isSocial(): bool
    {
        return $this->category === 'social';
    }

    public function isLearning(): bool
    {
        return $this->category === 'learning';
    }

    public function isMilestone(): bool
    {
        return $this->category === 'milestone';
    }

    // Type helpers
    public function isNumeric(): bool
    {
        return $this->type === 'numeric';
    }

    public function isBoolean(): bool
    {
        return $this->type === 'boolean';
    }

    public function isStreak(): bool
    {
        return $this->type === 'streak';
    }

    public function isPercentage(): bool
    {
        return $this->type === 'percentage';
    }

    // Rarity helpers
    public function isCommon(): bool
    {
        return $this->rarity === 'common';
    }

    public function isUncommon(): bool
    {
        return $this->rarity === 'uncommon';
    }

    public function isRare(): bool
    {
        return $this->rarity === 'rare';
    }

    public function isEpic(): bool
    {
        return $this->rarity === 'epic';
    }

    public function isLegendary(): bool
    {
        return $this->rarity === 'legendary';
    }

    public function getRarityColor(): string
    {
        return match ($this->rarity) {
            'common' => 'gray',
            'uncommon' => 'green',
            'rare' => 'blue',
            'epic' => 'purple',
            'legendary' => 'yellow',
            default => 'gray'
        };
    }

    public function getCategoryIcon(): string
    {
        return match ($this->category) {
            'sales' => 'currency-dollar',
            'activity' => 'lightning-bolt',
            'social' => 'users',
            'learning' => 'academic-cap',
            'milestone' => 'star',
            default => 'trophy'
        };
    }

    // Achievement checking
    public function checkCriteria(User $user): bool
    {
        $criteria = $this->criteria;
        
        // This is a simplified example - in a real implementation,
        // you'd have more sophisticated criteria checking based on the type
        foreach ($criteria as $key => $value) {
            switch ($key) {
                case 'deals_closed':
                    $dealsCount = $user->deals()->where('status', 'won')->count();
                    if ($dealsCount < $value) return false;
                    break;
                    
                case 'revenue_generated':
                    $revenue = $user->deals()->where('status', 'won')->sum('value');
                    if ($revenue < $value) return false;
                    break;
                    
                case 'calls_made':
                    $callsCount = $user->communications()->where('type', 'call')->count();
                    if ($callsCount < $value) return false;
                    break;
                    
                case 'emails_sent':
                    $emailsCount = $user->emails()->count();
                    if ($emailsCount < $value) return false;
                    break;
                    
                case 'playbooks_completed':
                    $playbooksCount = $user->playbookExecutions()->where('status', 'completed')->count();
                    if ($playbooksCount < $value) return false;
                    break;
            }
        }
        
        return true;
    }

    public function awardToUser(User $user, array $criteriaValues = []): UserAchievement
    {
        return $this->userAchievements()->create([
            'user_id' => $user->id,
            'criteria_met' => $criteriaValues,
            'points_earned' => $this->points,
            'earned_at' => now(),
        ]);
    }

    public function getBadgeUrl(): ?string
    {
        return $this->badge_image ? asset('storage/' . $this->badge_image) : null;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($achievement) {
            if (empty($achievement->slug)) {
                $achievement->slug = Str::slug($achievement->name);
            }
        });

        static::updating(function ($achievement) {
            if ($achievement->isDirty('name') && empty($achievement->slug)) {
                $achievement->slug = Str::slug($achievement->name);
            }
        });
    }
}