<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldEncryptionSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'model_type',
        'field_name',
        'is_encrypted',
        'encryption_algorithm',
        'sensitivity_level',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeEncrypted($query)
    {
        return $query->where('is_encrypted', true);
    }

    public function scopeForModel($query, string $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    public function scopeHighSensitivity($query)
    {
        return $query->whereIn('sensitivity_level', ['high', 'critical']);
    }

    public static function isFieldEncrypted(string $modelType, string $fieldName): bool
    {
        return static::where('model_type', $modelType)
                    ->where('field_name', $fieldName)
                    ->where('is_encrypted', true)
                    ->where('is_active', true)
                    ->exists();
    }

    public static function getEncryptedFields(string $modelType): array
    {
        return static::where('model_type', $modelType)
                    ->where('is_encrypted', true)
                    ->where('is_active', true)
                    ->pluck('field_name')
                    ->toArray();
    }
}