<?php

namespace App\Traits;

use App\Models\SecurityAuditLog;
use App\Services\Security\AuditTrailService;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasAuditLog
{
    protected static function bootHasAuditLog()
    {
        static::created(function ($model) {
            $model->logAuditEvent('create', [], $model->getAttributes());
        });

        static::updated(function ($model) {
            $model->logAuditEvent('update', $model->getOriginal(), $model->getAttributes());
        });

        static::deleted(function ($model) {
            $model->logAuditEvent('delete', $model->getAttributes(), []);
        });
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(SecurityAuditLog::class, 'auditable');
    }

    public function logAuditEvent(string $action, array $oldValues = [], array $newValues = []): SecurityAuditLog
    {
        $auditService = app(AuditTrailService::class);
        
        return $auditService->logDataModification(
            $this,
            $action,
            $this->filterSensitiveData($oldValues),
            $this->filterSensitiveData($newValues)
        );
    }

    public function getAuditHistory(): \Illuminate\Support\Collection
    {
        return $this->auditLogs()
            ->with('user')
            ->orderBy('occurred_at', 'desc')
            ->get();
    }

    protected function filterSensitiveData(array $data): array
    {
        $sensitiveFields = $this->getSensitiveAuditFields();
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[REDACTED]';
            }
        }

        return $data;
    }

    protected function getSensitiveAuditFields(): array
    {
        // Override this method in your models to specify which fields
        // should be redacted in audit logs
        return [
            'password',
            'password_hash',
            'remember_token',
            'api_token',
            'ssn',
            'credit_card',
            'bank_account',
        ];
    }

    public function getAuditableFields(): array
    {
        // Override this method to specify which fields should be audited
        // By default, audit all fillable fields except sensitive ones
        return array_diff(
            $this->getFillable(),
            $this->getSensitiveAuditFields()
        );
    }

    public function shouldAuditField(string $field): bool
    {
        return in_array($field, $this->getAuditableFields());
    }

    // Manually log access events
    public function logAccess(string $description = null): SecurityAuditLog
    {
        $auditService = app(AuditTrailService::class);
        
        return $auditService->logDataAccess(
            $this,
            'read',
            auth()->id()
        );
    }

    // Get audit summary for this model
    public function getAuditSummary(): array
    {
        $logs = $this->auditLogs()->get();
        
        return [
            'total_events' => $logs->count(),
            'created_at' => $this->created_at,
            'last_modified' => $logs->where('action', 'update')->max('occurred_at'),
            'last_accessed' => $logs->where('action', 'read')->max('occurred_at'),
            'modification_count' => $logs->where('action', 'update')->count(),
            'access_count' => $logs->where('action', 'read')->count(),
            'unique_users' => $logs->pluck('user_id')->unique()->count(),
        ];
    }
}