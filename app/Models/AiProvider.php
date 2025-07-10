<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'api_base_url',
        'supported_features',
        'authentication_methods',
        'configuration_schema',
        'status',
        'logo_url',
        'rate_limits',
    ];

    protected $casts = [
        'supported_features' => 'array',
        'authentication_methods' => 'array',
        'configuration_schema' => 'array',
        'rate_limits' => 'array',
    ];

    public function aiModels(): HasMany
    {
        return $this->hasMany(AiModel::class);
    }

    public function userConfigurations(): HasMany
    {
        return $this->hasMany(UserAiConfiguration::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function supportsFeature(string $feature): bool
    {
        return in_array($feature, $this->supported_features ?? []);
    }

    public function supportsAuthentication(string $method): bool
    {
        return in_array($method, $this->authentication_methods ?? []);
    }
}
