<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'ai_provider_id',
        'name',
        'model_id',
        'description',
        'capabilities',
        'pricing_info',
        'max_tokens',
        'context_length',
        'supports_streaming',
        'supports_function_calling',
        'status',
        'configuration_options',
    ];

    protected $casts = [
        'capabilities' => 'array',
        'pricing_info' => 'array',
        'supports_streaming' => 'boolean',
        'supports_function_calling' => 'boolean',
        'configuration_options' => 'array',
    ];

    public function aiProvider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class);
    }

    public function leadScores(): HasMany
    {
        return $this->hasMany(LeadScore::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByProvider($query, $providerId)
    {
        return $query->where('ai_provider_id', $providerId);
    }

    public function hasCapability(string $capability): bool
    {
        return in_array($capability, $this->capabilities ?? []);
    }

    public function getFullName(): string
    {
        return $this->aiProvider->name . ' - ' . $this->name;
    }
}
