<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class SalesContent extends Model
{
    use HasFactory;

    protected $table = 'sales_content';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'content',
        'metadata',
        'tags',
        'status',
        'created_by',
        'category_id',
        'download_count',
        'view_count',
        'average_rating',
        'rating_count',
        'last_accessed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'tags' => 'array',
        'download_count' => 'integer',
        'view_count' => 'integer',
        'average_rating' => 'decimal:2',
        'rating_count' => 'integer',
        'last_accessed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SalesContentCategory::class, 'category_id');
    }

    public function usageAnalytics(): HasMany
    {
        return $this->hasMany(ContentUsageAnalytic::class, 'content_id');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
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
    public function isDocument(): bool
    {
        return $this->type === 'document';
    }

    public function isPresentation(): bool
    {
        return $this->type === 'presentation';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isBattleCard(): bool
    {
        return $this->type === 'battle_card';
    }

    // Analytics methods
    public function recordView(User $user, $dealId = null, $contactId = null): void
    {
        $this->usageAnalytics()->create([
            'user_id' => $user->id,
            'deal_id' => $dealId,
            'contact_id' => $contactId,
            'action' => 'view',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->increment('view_count');
        $this->update(['last_accessed_at' => now()]);
    }

    public function recordDownload(User $user, $dealId = null, $contactId = null): void
    {
        $this->usageAnalytics()->create([
            'user_id' => $user->id,
            'deal_id' => $dealId,
            'contact_id' => $contactId,
            'action' => 'download',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->increment('download_count');
        $this->update(['last_accessed_at' => now()]);
    }

    public function addRating(User $user, int $rating, $comment = null): void
    {
        $this->usageAnalytics()->create([
            'user_id' => $user->id,
            'action' => 'rate',
            'metadata' => [
                'rating' => $rating,
                'comment' => $comment,
            ],
        ]);

        // Recalculate average rating
        $ratings = $this->usageAnalytics()
            ->where('action', 'rate')
            ->get()
            ->pluck('metadata.rating')
            ->filter();

        $this->update([
            'average_rating' => $ratings->avg(),
            'rating_count' => $ratings->count(),
        ]);
    }

    // File helpers
    public function getFileUrl(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function getFormattedFileSize(): string
    {
        if (!$this->file_size) return '';

        $size = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($content) {
            if (empty($content->slug)) {
                $content->slug = Str::slug($content->title);
            }
        });

        static::updating(function ($content) {
            if ($content->isDirty('title') && empty($content->slug)) {
                $content->slug = Str::slug($content->title);
            }
        });
    }
}