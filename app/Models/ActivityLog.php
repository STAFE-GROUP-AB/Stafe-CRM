<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'description',
        'properties',
        'loggable_type',
        'loggable_id',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForEntity($query, $entity)
    {
        return $query->where('loggable_type', get_class($entity))
                    ->where('loggable_id', $entity->id);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Log an activity
     */
    public static function logActivity(
        string $action,
        string $description,
        $loggable,
        ?User $user = null,
        array $properties = []
    ): self {
        return self::create([
            'action' => $action,
            'description' => $description,
            'loggable_type' => get_class($loggable),
            'loggable_id' => $loggable->id,
            'user_id' => $user?->id ?? auth()->id(),
            'properties' => $properties,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}