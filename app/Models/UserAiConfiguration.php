<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class UserAiConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ai_provider_id',
        'name',
        'credentials',
        'default_models',
        'settings',
        'is_active',
        'is_default',
        'last_used_at',
        'usage_stats',
    ];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'default_models' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'last_used_at' => 'datetime',
        'usage_stats' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function aiProvider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function markAsUsed(): void
    {
        $this->update([
            'last_used_at' => now(),
            'usage_stats' => array_merge($this->usage_stats ?? [], [
                'last_used' => now()->toISOString(),
                'usage_count' => ($this->usage_stats['usage_count'] ?? 0) + 1,
            ])
        ]);
    }

    public function hasCredential(string $key): bool
    {
        return isset($this->credentials[$key]) && !empty($this->credentials[$key]);
    }

    public function getCredential(string $key): ?string
    {
        return $this->credentials[$key] ?? null;
    }

    public function setCredential(string $key, string $value): void
    {
        $credentials = $this->credentials ?? [];
        $credentials[$key] = $value;
        $this->credentials = $credentials;
    }

    public function getDefaultModel(string $useCase = 'general'): ?string
    {
        return $this->default_models[$useCase] ?? null;
    }

    public function setDefaultModel(string $useCase, string $modelId): void
    {
        $models = $this->default_models ?? [];
        $models[$useCase] = $modelId;
        $this->default_models = $models;
    }
}
