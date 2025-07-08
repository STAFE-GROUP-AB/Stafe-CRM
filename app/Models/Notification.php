<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
        'notifiable_type',
        'notifiable_id',
        'user_id',
        'triggered_by_user_id',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by_user_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Create notification for user
     */
    public static function notify(
        int $userId,
        string $type,
        string $title,
        string $message,
        $notifiable = null,
        ?int $triggeredByUserId = null,
        array $data = []
    ): self {
        return self::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'user_id' => $userId,
            'triggered_by_user_id' => $triggeredByUserId ?? auth()->id(),
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id' => $notifiable?->id,
        ]);
    }

    /**
     * Create assignment notification
     */
    public static function notifyAssignment(
        int $userId,
        $entity,
        ?int $assignedByUserId = null
    ): self {
        $entityName = class_basename($entity);
        
        return self::notify(
            $userId,
            'assignment',
            "New {$entityName} Assignment",
            "You have been assigned to {$entityName}: {$entity->name ?? $entity->title ?? $entity->id}",
            $entity,
            $assignedByUserId,
            [
                'entity_type' => get_class($entity),
                'entity_id' => $entity->id,
                'entity_name' => $entity->name ?? $entity->title ?? null,
            ]
        );
    }

    /**
     * Create deadline notification
     */
    public static function notifyDeadline(
        int $userId,
        $entity,
        string $deadline
    ): self {
        $entityName = class_basename($entity);
        
        return self::notify(
            $userId,
            'deadline',
            "{$entityName} Deadline Approaching",
            "Deadline for {$entityName} '{$entity->name ?? $entity->title}' is approaching: {$deadline}",
            $entity,
            null,
            [
                'entity_type' => get_class($entity),
                'entity_id' => $entity->id,
                'deadline' => $deadline,
            ]
        );
    }
}