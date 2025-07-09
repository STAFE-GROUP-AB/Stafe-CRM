<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'is_internal',
        'mentions',
        'commentable_type',
        'commentable_id',
        'user_id',
        'parent_id',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'mentions' => 'array',
    ];

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeForEntity($query, $entity)
    {
        return $query->where('commentable_type', get_class($entity))
                    ->where('commentable_id', $entity->id);
    }

    /**
     * Get mentioned users
     */
    public function getMentionedUsers()
    {
        if (empty($this->mentions)) {
            return collect();
        }

        return User::whereIn('id', $this->mentions)->get();
    }

    /**
     * Extract mentions from content
     */
    public static function extractMentions(string $content): array
    {
        preg_match_all('/@(\w+)/', $content, $matches);
        
        if (empty($matches[1])) {
            return [];
        }

        // Get user IDs by username/email
        $users = User::whereIn('email', $matches[1])
            ->orWhereIn('name', $matches[1])
            ->pluck('id')
            ->toArray();

        return $users;
    }

    /**
     * Create comment with mention processing
     */
    public static function createWithMentions(array $data): self
    {
        $mentions = self::extractMentions($data['content']);
        $data['mentions'] = $mentions;

        $comment = self::create($data);

        // Send notifications to mentioned users
        foreach ($mentions as $userId) {
            Notification::create([
                'type' => 'mention',
                'title' => 'You were mentioned in a comment',
                'message' => "You were mentioned by {$comment->user->name}",
                'user_id' => $userId,
                'triggered_by_user_id' => $comment->user_id,
                'notifiable_type' => get_class($comment),
                'notifiable_id' => $comment->id,
                'data' => [
                    'comment_id' => $comment->id,
                    'entity_type' => $comment->commentable_type,
                    'entity_id' => $comment->commentable_id,
                ],
            ]);
        }

        return $comment;
    }
}