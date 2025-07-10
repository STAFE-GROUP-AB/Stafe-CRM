<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GdprConsent extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'consentable_type',
        'consentable_id',
        'purpose',
        'status',
        'description',
        'legal_basis',
        'ip_address',
        'user_agent',
        'granted_at',
        'withdrawn_at',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
        'withdrawn_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function consentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'granted')
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    public function scopeForPurpose($query, string $purpose)
    {
        return $query->where('purpose', $purpose);
    }

    public function isActive(): bool
    {
        return $this->status === 'granted' && 
               (is_null($this->expires_at) || $this->expires_at->isFuture());
    }

    public function withdraw(): void
    {
        $this->update([
            'status' => 'withdrawn',
            'withdrawn_at' => now(),
        ]);
    }
}