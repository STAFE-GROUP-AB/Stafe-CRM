<?php

namespace App\Services\Security;

use App\Models\FieldEncryptionSetting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;

class FieldEncryptionService
{
    protected array $encryptedFields = [];

    public function __construct()
    {
        $this->loadEncryptionSettings();
    }

    public function encryptField(string $modelType, string $fieldName, $value): string
    {
        if (!$this->shouldEncryptField($modelType, $fieldName)) {
            return $value;
        }

        if (is_null($value) || $value === '') {
            return $value;
        }

        try {
            return Crypt::encryptString((string) $value);
        } catch (\Exception $e) {
            // Log the error but don't throw - fall back to original value
            logger()->error('Field encryption failed', [
                'model' => $modelType,
                'field' => $fieldName,
                'error' => $e->getMessage(),
            ]);
            
            return $value;
        }
    }

    public function decryptField(string $modelType, string $fieldName, $value): string
    {
        if (!$this->shouldEncryptField($modelType, $fieldName)) {
            return $value;
        }

        if (is_null($value) || $value === '') {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // Log the error - this might indicate corrupted data or wrong encryption key
            logger()->error('Field decryption failed', [
                'model' => $modelType,
                'field' => $fieldName,
                'error' => $e->getMessage(),
            ]);
            
            // Return the original value as fallback
            return $value;
        }
    }

    public function encryptModelData(Model $model): array
    {
        $modelType = get_class($model);
        $data = $model->getAttributes();
        
        foreach ($data as $field => $value) {
            if ($this->shouldEncryptField($modelType, $field)) {
                $data[$field] = $this->encryptField($modelType, $field, $value);
            }
        }

        return $data;
    }

    public function decryptModelData(Model $model): array
    {
        $modelType = get_class($model);
        $data = $model->getAttributes();
        
        foreach ($data as $field => $value) {
            if ($this->shouldEncryptField($modelType, $field)) {
                $data[$field] = $this->decryptField($modelType, $field, $value);
            }
        }

        return $data;
    }

    public function shouldEncryptField(string $modelType, string $fieldName): bool
    {
        $key = "{$modelType}.{$fieldName}";
        return $this->encryptedFields[$key] ?? false;
    }

    public function addEncryptionRule(
        string $modelType,
        string $fieldName,
        string $sensitivityLevel = 'medium',
        string $description = null
    ): FieldEncryptionSetting {
        $setting = FieldEncryptionSetting::updateOrCreate(
            [
                'tenant_id' => auth()->user()?->tenant_id ?? 1,
                'model_type' => $modelType,
                'field_name' => $fieldName,
            ],
            [
                'is_encrypted' => true,
                'sensitivity_level' => $sensitivityLevel,
                'description' => $description,
                'is_active' => true,
            ]
        );

        $this->loadEncryptionSettings(); // Refresh cache
        return $setting;
    }

    public function removeEncryptionRule(string $modelType, string $fieldName): bool
    {
        $deleted = FieldEncryptionSetting::where('model_type', $modelType)
            ->where('field_name', $fieldName)
            ->where('tenant_id', auth()->user()?->tenant_id ?? 1)
            ->delete();

        if ($deleted) {
            $this->loadEncryptionSettings(); // Refresh cache
        }

        return $deleted > 0;
    }

    public function getEncryptedFields(string $modelType): array
    {
        return FieldEncryptionSetting::getEncryptedFields($modelType);
    }

    public function getAllEncryptionSettings(): array
    {
        return FieldEncryptionSetting::active()
            ->where('tenant_id', auth()->user()?->tenant_id ?? 1)
            ->get()
            ->groupBy('model_type')
            ->toArray();
    }

    protected function loadEncryptionSettings(): void
    {
        $settings = FieldEncryptionSetting::active()
            ->encrypted()
            ->get(['model_type', 'field_name']);

        $this->encryptedFields = [];
        foreach ($settings as $setting) {
            $key = "{$setting->model_type}.{$setting->field_name}";
            $this->encryptedFields[$key] = true;
        }
    }

    public function migrateFieldEncryption(string $modelType, string $fieldName, bool $encrypt = true): array
    {
        $modelClass = $modelType;
        if (!class_exists($modelClass)) {
            throw new \InvalidArgumentException("Model class {$modelClass} does not exist");
        }

        $results = [
            'processed' => 0,
            'errors' => 0,
            'skipped' => 0,
        ];

        $query = $modelClass::whereNotNull($fieldName);
        
        $query->chunk(100, function ($models) use ($fieldName, $encrypt, &$results) {
            foreach ($models as $model) {
                try {
                    $currentValue = $model->getAttribute($fieldName);
                    
                    if (empty($currentValue)) {
                        $results['skipped']++;
                        continue;
                    }

                    if ($encrypt) {
                        // Encrypt the field
                        $newValue = $this->encryptField(get_class($model), $fieldName, $currentValue);
                    } else {
                        // Decrypt the field
                        $newValue = $this->decryptField(get_class($model), $fieldName, $currentValue);
                    }

                    $model->update([$fieldName => $newValue]);
                    $results['processed']++;

                } catch (\Exception $e) {
                    logger()->error('Field encryption migration failed', [
                        'model_id' => $model->id,
                        'field' => $fieldName,
                        'error' => $e->getMessage(),
                    ]);
                    $results['errors']++;
                }
            }
        });

        return $results;
    }

    public function validateEncryptionIntegrity(string $modelType): array
    {
        $modelClass = $modelType;
        $encryptedFields = $this->getEncryptedFields($modelType);
        
        if (empty($encryptedFields)) {
            return ['status' => 'success', 'message' => 'No encrypted fields to validate'];
        }

        $results = [
            'total_records' => 0,
            'valid_records' => 0,
            'invalid_records' => 0,
            'errors' => [],
        ];

        $modelClass::chunk(100, function ($models) use ($encryptedFields, &$results) {
            foreach ($models as $model) {
                $results['total_records']++;
                $isValid = true;

                foreach ($encryptedFields as $field) {
                    $value = $model->getAttribute($field);
                    
                    if (!empty($value)) {
                        try {
                            $this->decryptField(get_class($model), $field, $value);
                        } catch (\Exception $e) {
                            $isValid = false;
                            $results['errors'][] = [
                                'model_id' => $model->id,
                                'field' => $field,
                                'error' => 'Decryption failed',
                            ];
                        }
                    }
                }

                if ($isValid) {
                    $results['valid_records']++;
                } else {
                    $results['invalid_records']++;
                }
            }
        });

        return $results;
    }
}