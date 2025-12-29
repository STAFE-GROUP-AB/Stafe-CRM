<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    protected $fillable = [
        'identifier',
        'otp_hash',
        'purpose',
        'expires_at',
        'attempts',
        'ip_address',
        'user_agent',
        'verified_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    protected $hidden = [
        'otp_hash',
    ];

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    public function scopeForIdentifier($query, string $identifier, string $purpose)
    {
        return $query->where('identifier', $identifier)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now());
    }
}
