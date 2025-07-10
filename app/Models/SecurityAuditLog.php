<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SecurityAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'event_type',
        'event_category',
        'risk_level',
        'auditable_type',
        'auditable_id',
        'action',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'session_id',
        'description',
        'metadata',
        'occurred_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeHighRisk($query)
    {
        return $query->whereIn('risk_level', ['high', 'critical']);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('occurred_at', '>=', now()->subDays($days));
    }

    public static function logEvent(array $data): self
    {
        return static::create(array_merge($data, [
            'occurred_at' => $data['occurred_at'] ?? now(),
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'session_id' => $data['session_id'] ?? session()->getId(),
        ]));
    }
}