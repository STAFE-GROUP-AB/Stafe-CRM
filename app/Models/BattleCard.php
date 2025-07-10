<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class BattleCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'competitor_name',
        'competitor_logo',
        'overview',
        'our_strengths',
        'our_weaknesses',
        'competitor_strengths',
        'competitor_weaknesses',
        'key_differentiators',
        'pricing_comparison',
        'feature_comparison',
        'objection_handling',
        'winning_strategies',
        'recent_wins',
        'recent_losses',
        'win_rate',
        'sales_notes',
        'resources',
        'threat_level',
        'status',
        'created_by',
        'updated_by',
        'last_updated_at',
        'view_count',
        'usage_count',
    ];

    protected $casts = [
        'our_strengths' => 'array',
        'our_weaknesses' => 'array',
        'competitor_strengths' => 'array',
        'competitor_weaknesses' => 'array',
        'key_differentiators' => 'array',
        'pricing_comparison' => 'array',
        'feature_comparison' => 'array',
        'objection_handling' => 'array',
        'winning_strategies' => 'array',
        'recent_wins' => 'array',
        'recent_losses' => 'array',
        'win_rate' => 'decimal:2',
        'resources' => 'array',
        'last_updated_at' => 'datetime',
        'view_count' => 'integer',
        'usage_count' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
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

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    // Threat level helpers
    public function isLowThreat(): bool
    {
        return $this->threat_level === 'low';
    }

    public function isMediumThreat(): bool
    {
        return $this->threat_level === 'medium';
    }

    public function isHighThreat(): bool
    {
        return $this->threat_level === 'high';
    }

    public function isCriticalThreat(): bool
    {
        return $this->threat_level === 'critical';
    }

    public function getThreatColor(): string
    {
        return match ($this->threat_level) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray'
        };
    }

    // Analytics methods
    public function recordView(User $user): void
    {
        $this->increment('view_count');
        $this->update(['last_updated_at' => now()]);

        $this->activityLogs()->create([
            'action' => 'viewed',
            'description' => "Battle card viewed by {$user->name}",
            'user_id' => $user->id,
        ]);
    }

    public function recordUsage(User $user, Deal $deal): void
    {
        $this->increment('usage_count');

        $this->activityLogs()->create([
            'action' => 'used_in_deal',
            'description' => "Battle card used in deal: {$deal->name}",
            'user_id' => $user->id,
            'properties' => [
                'deal_id' => $deal->id,
                'deal_name' => $deal->name,
            ],
        ]);
    }

    // Win rate calculation
    public function updateWinRate(): void
    {
        $competitiveIntelligence = CompetitiveIntelligence::where('competitor_name', $this->competitor_name)->get();
        
        if ($competitiveIntelligence->isNotEmpty()) {
            $winRate = $competitiveIntelligence->avg('win_loss_probability') * 100;
            $this->update(['win_rate' => $winRate]);
        }
    }

    // Get logo URL
    public function getLogoUrl(): ?string
    {
        return $this->competitor_logo ? asset('storage/' . $this->competitor_logo) : null;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($battleCard) {
            if (empty($battleCard->slug)) {
                $battleCard->slug = Str::slug($battleCard->title);
            }
            $battleCard->last_updated_at = now();
        });

        static::updating(function ($battleCard) {
            if ($battleCard->isDirty('title') && empty($battleCard->slug)) {
                $battleCard->slug = Str::slug($battleCard->title);
            }
            $battleCard->last_updated_at = now();
        });
    }
}