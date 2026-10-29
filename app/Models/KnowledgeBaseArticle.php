<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class KnowledgeBaseArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_description',
        'category',
        'status',
        'tags',
        'view_count',
        'helpful_votes',
        'unhelpful_votes',
        'ai_keywords',
        'ai_relevance_score',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tags' => 'array',
        'ai_keywords' => 'array',
        'ai_relevance_score' => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = static::generateUniqueSlug($article->title);
            }
        });

        static::updating(function ($article) {
            if ($article->isDirty('title') && ! $article->isDirty('slug')) {
                $article->slug = static::generateUniqueSlug($article->title, $article->id);
            }
        });
    }

    public static function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)
            ->when($excludeId, fn ($query) => $query->where('id', '!=', $excludeId))
            ->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('content', 'like', "%{$term}%")
                ->orWhere('meta_description', 'like', "%{$term}%")
                ->orWhereJsonContains('tags', $term)
                ->orWhereJsonContains('ai_keywords', $term);
        });
    }

    public function scopeInCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function getHelpfulnessRatioAttribute(): float
    {
        $total = $this->helpful_votes + $this->unhelpful_votes;

        return $total > 0 ? round(($this->helpful_votes / $total) * 100, 1) : 0;
    }

    public function getTotalVotesAttribute(): int
    {
        return $this->helpful_votes + $this->unhelpful_votes;
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'published' => 'green',
            'draft' => 'yellow',
            'archived' => 'gray',
            default => 'gray',
        };
    }

    public function getExcerptAttribute(): string
    {
        if ($this->meta_description) {
            return $this->meta_description;
        }

        return Str::limit(strip_tags($this->content), 150);
    }

    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    public function addHelpfulVote(): void
    {
        $this->increment('helpful_votes');
    }

    public function addUnhelpfulVote(): void
    {
        $this->increment('unhelpful_votes');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    public function publish(): void
    {
        $this->update(['status' => 'published']);
    }

    public function archive(): void
    {
        $this->update(['status' => 'archived']);
    }

    public function toDraft(): void
    {
        $this->update(['status' => 'draft']);
    }
}
