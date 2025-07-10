<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SsoProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'provider_type',
        'client_id',
        'client_secret',
        'certificate',
        'entity_id',
        'sso_url',
        'sls_url',
        'attribute_mapping',
        'scopes',
        'is_active',
        'auto_provision',
        'default_role',
        'metadata',
    ];

    protected $casts = [
        'attribute_mapping' => 'array',
        'scopes' => 'array',
        'is_active' => 'boolean',
        'auto_provision' => 'boolean',
        'metadata' => 'array',
    ];

    protected $hidden = [
        'client_secret',
        'certificate',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForType($query, string $type)
    {
        return $query->where('provider_type', $type);
    }

    public function setClientSecretAttribute($value)
    {
        $this->attributes['client_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getClientSecretAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setCertificateAttribute($value)
    {
        $this->attributes['certificate'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getCertificateAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function getRedirectUrl(): string
    {
        return route('sso.callback', ['provider' => $this->id]);
    }

    public function mapAttributes(array $providerUser): array
    {
        $mapping = $this->attribute_mapping ?? [];
        $result = [];

        foreach ($mapping as $localField => $providerField) {
            $result[$localField] = data_get($providerUser, $providerField);
        }

        return $result;
    }
}