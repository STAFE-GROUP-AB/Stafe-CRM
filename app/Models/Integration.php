<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Integration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'category_id',
        'provider',
        'version',
        'config_schema',
        'webhook_endpoints',
        'api_endpoints',
        'auth_type',
        'auth_config',
        'is_active',
        'is_featured',
        'install_count',
    ];

    protected $casts = [
        'config_schema' => 'array',
        'webhook_endpoints' => 'array',
        'api_endpoints' => 'array',
        'auth_config' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(IntegrationCategory::class, 'category_id');
    }

    public function connections(): HasMany
    {
        return $this->hasMany(ApiConnection::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Increment install count
     */
    public function incrementInstallCount(): void
    {
        $this->increment('install_count');
    }

    /**
     * Check if integration supports webhooks
     */
    public function supportsWebhooks(): bool
    {
        return !empty($this->webhook_endpoints);
    }

    /**
     * Check if integration supports API calls
     */
    public function supportsApi(): bool
    {
        return !empty($this->api_endpoints);
    }

    /**
     * Get available webhook endpoints
     */
    public function getWebhookEndpoints(): array
    {
        return $this->webhook_endpoints ?? [];
    }

    /**
     * Get available API endpoints
     */
    public function getApiEndpoints(): array
    {
        return $this->api_endpoints ?? [];
    }

    /**
     * Validate configuration against schema
     */
    public function validateConfig(array $config): array
    {
        // Implement JSON schema validation
        return ['valid' => true, 'errors' => []];
    }
}