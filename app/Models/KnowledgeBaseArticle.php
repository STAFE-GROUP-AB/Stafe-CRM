<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeBaseArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'category',
        'tags',
        'is_published',
        'view_count',
        'helpful_votes',
        'unhelpful_votes',
        'ai_keywords',
        'ai_relevance_score',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tags' => 'array',
        'ai_keywords' => 'array',
        'is_published' => 'boolean',
        'ai_relevance_score' => 'decimal:2'
    ];

    /**
     * Get the user who created the article.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the article.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for published articles.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for searching articles.
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('content', 'like', "%{$term}%")
              ->orWhereJsonContains('tags', $term)
              ->orWhereJsonContains('ai_keywords', $term);
        });
    }

    /**
     * Get the helpfulness ratio.
     */
    public function getHelpfulnessRatioAttribute(): float
    {
        $total = $this->helpful_votes + $this->unhelpful_votes;
        return $total > 0 ? round(($this->helpful_votes / $total) * 100, 1) : 0;
    }

    /**
     * Increment view count.
     */
    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    /**
     * Add helpful vote.
     */
    public function addHelpfulVote(): void
    {
        $this->increment('helpful_votes');
    }

    /**
     * Add unhelpful vote.
     */
    public function addUnhelpfulVote(): void
    {
        $this->increment('unhelpful_votes');
    }
}