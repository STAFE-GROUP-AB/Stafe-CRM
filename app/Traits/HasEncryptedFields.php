<?php

namespace App\Traits;

use App\Services\Security\FieldEncryptionService;

trait HasEncryptedFields
{
    protected static function bootHasEncryptedFields()
    {
        // Encrypt fields when creating/updating
        static::saving(function ($model) {
            $model->encryptSensitiveFields();
        });

        // Decrypt fields when retrieving
        static::retrieved(function ($model) {
            $model->decryptSensitiveFields();
        });
    }

    public function encryptSensitiveFields(): void
    {
        $encryptionService = app(FieldEncryptionService::class);
        $modelType = get_class($this);

        foreach ($this->getAttributes() as $field => $value) {
            if ($encryptionService->shouldEncryptField($modelType, $field)) {
                $this->attributes[$field] = $encryptionService->encryptField($modelType, $field, $value);
            }
        }
    }

    public function decryptSensitiveFields(): void
    {
        $encryptionService = app(FieldEncryptionService::class);
        $modelType = get_class($this);

        foreach ($this->getAttributes() as $field => $value) {
            if ($encryptionService->shouldEncryptField($modelType, $field)) {
                $this->attributes[$field] = $encryptionService->decryptField($modelType, $field, $value);
            }
        }
    }

    public function getEncryptedFields(): array
    {
        $encryptionService = app(FieldEncryptionService::class);
        return $encryptionService->getEncryptedFields(get_class($this));
    }

    public function isFieldEncrypted(string $fieldName): bool
    {
        $encryptionService = app(FieldEncryptionService::class);
        return $encryptionService->shouldEncryptField(get_class($this), $fieldName);
    }

    // Override the setAttribute method to encrypt on the fly
    public function setAttribute($key, $value)
    {
        // Only encrypt if not already processing to avoid double encryption
        if (!$this->isEncryptionInProgress() && $this->shouldEncryptAttribute($key)) {
            $encryptionService = app(FieldEncryptionService::class);
            $value = $encryptionService->encryptField(get_class($this), $key, $value);
        }

        return parent::setAttribute($key, $value);
    }

    // Override the getAttribute method to decrypt on the fly
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($value !== null && $this->shouldDecryptAttribute($key)) {
            $encryptionService = app(FieldEncryptionService::class);
            $value = $encryptionService->decryptField(get_class($this), $key, $value);
        }

        return $value;
    }

    protected function shouldEncryptAttribute(string $key): bool
    {
        $encryptionService = app(FieldEncryptionService::class);
        return $encryptionService->shouldEncryptField(get_class($this), $key);
    }

    protected function shouldDecryptAttribute(string $key): bool
    {
        // Same logic as encryption for now
        return $this->shouldEncryptAttribute($key);
    }

    protected function isEncryptionInProgress(): bool
    {
        // Simple flag to prevent recursive encryption/decryption
        return property_exists($this, '_encryptionInProgress') && $this->_encryptionInProgress;
    }

    public function toArrayDecrypted(): array
    {
        // Get array representation with decrypted values
        $this->decryptSensitiveFields();
        return $this->toArray();
    }

    public function toArrayEncrypted(): array
    {
        // Get array representation with encrypted values (raw database format)
        $encryptionService = app(FieldEncryptionService::class);
        $data = $this->getAttributes();
        $modelType = get_class($this);

        foreach ($data as $field => $value) {
            if ($encryptionService->shouldEncryptField($modelType, $field) && $value !== null) {
                $data[$field] = '[ENCRYPTED]'; // Mask encrypted fields in output
            }
        }

        return $data;
    }

    // Scope to search encrypted fields (this is complex and may require special handling)
    public function scopeWhereEncrypted($query, string $field, $value)
    {
        if ($this->shouldEncryptAttribute($field)) {
            $encryptionService = app(FieldEncryptionService::class);
            $encryptedValue = $encryptionService->encryptField(get_class($this), $field, $value);
            return $query->where($field, $encryptedValue);
        }

        return $query->where($field, $value);
    }
}