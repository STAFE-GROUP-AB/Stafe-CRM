<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class SalesPlaybook extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'type',
        'target_personas',
        'deal_stages',
        'overview',
        'objectives',
        'prerequisites',
        'estimated_duration',
        'difficulty_level',
        'status',
        'created_by',
        'updated_by',
        'usage_count',
        'success_rate',
        'average_rating',
        'rating_count',
        'last_used_at',
    ];

    protected $casts = [
        'target_personas' => 'array',
        'deal_stages' => 'array',
        'objectives' => 'array',
        'prerequisites' => 'array',
        'usage_count' => 'integer',
        'success_rate' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'rating_count' => 'integer',
        'last_used_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(PlaybookStep::class, 'playbook_id')->orderBy('sort_order');
    }

    public function executions(): HasMany
    {
        return $this->hasMany(PlaybookExecution::class, 'playbook_id');
    }

    public function activeExecutions(): HasMany
    {
        return $this->hasMany(PlaybookExecution::class, 'playbook_id')
            ->where('status', 'in_progress');
    }

    public function completedExecutions(): HasMany
    {
        return $this->hasMany(PlaybookExecution::class, 'playbook_id')
            ->where('status', 'completed');
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

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    // Type helpers
    public function isDiscovery(): bool
    {
        return $this->type === 'discovery';
    }

    public function isDemo(): bool
    {
        return $this->type === 'demo';
    }

    public function isObjectionHandling(): bool
    {
        return $this->type === 'objection_handling';
    }

    public function isClosing(): bool
    {
        return $this->type === 'closing';
    }

    // Difficulty helpers
    public function isBeginner(): bool
    {
        return $this->difficulty_level === 'beginner';
    }

    public function isIntermediate(): bool
    {
        return $this->difficulty_level === 'intermediate';
    }

    public function isAdvanced(): bool
    {
        return $this->difficulty_level === 'advanced';
    }

    public function getDifficultyColor(): string
    {
        return match ($this->difficulty_level) {
            'beginner' => 'green',
            'intermediate' => 'yellow',
            'advanced' => 'red',
            default => 'gray'
        };
    }

    // Analytics methods
    public function startExecution(User $user, Deal $deal = null, Contact $contact = null): PlaybookExecution
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);

        return $this->executions()->create([
            'user_id' => $user->id,
            'deal_id' => $deal?->id,
            'contact_id' => $contact?->id,
            'started_at' => now(),
        ]);
    }

    public function updateSuccessRate(): void
    {
        $completedExecutions = $this->completedExecutions;
        
        if ($completedExecutions->isNotEmpty()) {
            $successfulExecutions = $completedExecutions->where('outcome', 'successful')->count();
            $successRate = ($successfulExecutions / $completedExecutions->count()) * 100;
            $this->update(['success_rate' => $successRate]);
        }
    }

    public function addRating(PlaybookExecution $execution, int $rating, string $feedback = null): void
    {
        $execution->update([
            'rating' => $rating,
            'feedback' => $feedback,
        ]);

        // Recalculate average rating
        $ratings = $this->executions()
            ->whereNotNull('rating')
            ->pluck('rating');

        if ($ratings->isNotEmpty()) {
            $this->update([
                'average_rating' => $ratings->avg(),
                'rating_count' => $ratings->count(),
            ]);
        }
    }

    // Step management
    public function getTotalSteps(): int
    {
        return $this->steps()->where('is_active', true)->count();
    }

    public function getRequiredSteps(): int
    {
        return $this->steps()->where('is_active', true)->where('is_required', true)->count();
    }

    public function getEstimatedDurationMinutes(): int
    {
        return $this->steps()->where('is_active', true)->sum('estimated_duration_minutes') ?: 0;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($playbook) {
            if (empty($playbook->slug)) {
                $playbook->slug = Str::slug($playbook->title);
            }
        });

        static::updating(function ($playbook) {
            if ($playbook->isDirty('title') && empty($playbook->slug)) {
                $playbook->slug = Str::slug($playbook->title);
            }
        });
    }
}