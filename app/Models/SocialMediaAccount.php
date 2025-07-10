<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialMediaAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'platform',
        'platform_user_id',
        'username',
        'display_name',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'platform_data',
        'monitoring_keywords',
        'is_active',
        'last_sync_at',
    ];

    protected $casts = [
        'platform_data' => 'array',
        'monitoring_keywords' => 'array',
        'is_active' => 'boolean',
        'token_expires_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
    ];

    /**
     * Get the user that owns this social media account
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active accounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific platform
     */
    public function scopePlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Check if token is expired
     */
    public function isTokenExpired(): bool
    {
        if (!$this->token_expires_at) {
            return false;
        }

        return now()->isAfter($this->token_expires_at);
    }

    /**
     * Check if account needs refresh
     */
    public function needsRefresh(): bool
    {
        return $this->isTokenExpired() && !empty($this->refresh_token);
    }

    /**
     * Mark as synced
     */
    public function markAsSynced(): void
    {
        $this->update(['last_sync_at' => now()]);
    }

    /**
     * Get platform display name
     */
    public function getPlatformDisplayNameAttribute(): string
    {
        return match ($this->platform) {
            'linkedin' => 'LinkedIn',
            'twitter' => 'Twitter (X)',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
            default => ucfirst($this->platform)
        };
    }

    /**
     * Get platform icon
     */
    public function getPlatformIconAttribute(): string
    {
        return match ($this->platform) {
            'linkedin' => 'fab fa-linkedin',
            'twitter' => 'fab fa-twitter',
            'facebook' => 'fab fa-facebook',
            'instagram' => 'fab fa-instagram',
            'youtube' => 'fab fa-youtube',
            default => 'fas fa-share-alt'
        };
    }

    /**
     * Get platform color
     */
    public function getPlatformColorAttribute(): string
    {
        return match ($this->platform) {
            'linkedin' => 'bg-blue-600',
            'twitter' => 'bg-sky-500',
            'facebook' => 'bg-blue-700',
            'instagram' => 'bg-pink-600',
            'youtube' => 'bg-red-600',
            default => 'bg-gray-600'
        };
    }

    /**
     * Get profile URL
     */
    public function getProfileUrlAttribute(): ?string
    {
        if (!$this->username) {
            return null;
        }

        return match ($this->platform) {
            'linkedin' => "https://linkedin.com/in/{$this->username}",
            'twitter' => "https://twitter.com/{$this->username}",
            'facebook' => "https://facebook.com/{$this->username}",
            'instagram' => "https://instagram.com/{$this->username}",
            'youtube' => "https://youtube.com/@{$this->username}",
            default => null
        };
    }

    /**
     * Update platform data
     */
    public function updatePlatformData(array $data): void
    {
        $currentData = $this->platform_data ?? [];
        $this->update([
            'platform_data' => array_merge($currentData, $data)
        ]);
    }

    /**
     * Add monitoring keyword
     */
    public function addMonitoringKeyword(string $keyword): void
    {
        $keywords = $this->monitoring_keywords ?? [];
        if (!in_array($keyword, $keywords)) {
            $keywords[] = $keyword;
            $this->update(['monitoring_keywords' => $keywords]);
        }
    }

    /**
     * Remove monitoring keyword
     */
    public function removeMonitoringKeyword(string $keyword): void
    {
        $keywords = $this->monitoring_keywords ?? [];
        $keywords = array_values(array_filter($keywords, fn($k) => $k !== $keyword));
        $this->update(['monitoring_keywords' => $keywords]);
    }
}