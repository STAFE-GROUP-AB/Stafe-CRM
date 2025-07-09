<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'domain',
        'subdomain',
        'settings',
        'status',
        'max_users',
        'storage_limit',
        'features',
        'trial_ends_at',
        'suspended_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'features' => 'array',
        'trial_ends_at' => 'datetime',
        'suspended_at' => 'datetime',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_users')
                    ->withPivot(['role', 'permissions', 'is_active', 'joined_at'])
                    ->withTimestamps();
    }

    public function tenantUsers(): HasMany
    {
        return $this->hasMany(TenantUser::class);
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeBySubdomain($query, string $subdomain)
    {
        return $query->where('subdomain', $subdomain);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Check if tenant is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if tenant is suspended
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Check if tenant is in trial
     */
    public function isInTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if trial has expired
     */
    public function hasTrialExpired(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    /**
     * Get tenant setting
     */
    public function getSetting(string $key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Set tenant setting
     */
    public function setSetting(string $key, $value): void
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->update(['settings' => $settings]);
    }

    /**
     * Check if feature is enabled
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    /**
     * Enable feature
     */
    public function enableFeature(string $feature): void
    {
        $features = $this->features ?? [];
        if (!in_array($feature, $features)) {
            $features[] = $feature;
            $this->update(['features' => $features]);
        }
    }

    /**
     * Disable feature
     */
    public function disableFeature(string $feature): void
    {
        $features = $this->features ?? [];
        $features = array_diff($features, [$feature]);
        $this->update(['features' => array_values($features)]);
    }

    /**
     * Check if user limit is reached
     */
    public function hasReachedUserLimit(): bool
    {
        return $this->users()->count() >= $this->max_users;
    }

    /**
     * Suspend tenant
     */
    public function suspend(string $reason = null): void
    {
        $this->update([
            'status' => 'suspended',
            'suspended_at' => now(),
        ]);

        if ($reason) {
            $this->setSetting('suspension_reason', $reason);
        }
    }

    /**
     * Activate tenant
     */
    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'suspended_at' => null,
        ]);
    }

    /**
     * Get full domain for tenant
     */
    public function getFullDomain(): string
    {
        return $this->domain ?? "{$this->subdomain}." . config('app.domain');
    }
}