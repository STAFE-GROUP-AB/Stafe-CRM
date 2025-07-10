<?php

namespace App\Services\Security;

use App\Models\SecurityAuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class AuditTrailService
{
    public function logAuthentication(string $action, ?int $userId = null, array $metadata = []): SecurityAuditLog
    {
        return $this->logEvent([
            'event_type' => $action,
            'event_category' => 'authentication',
            'user_id' => $userId,
            'risk_level' => $this->determineAuthRiskLevel($action, $metadata),
            'description' => "User {$action}",
            'metadata' => $metadata,
        ]);
    }

    public function logDataAccess(Model $model, string $action, ?int $userId = null): SecurityAuditLog
    {
        return $this->logEvent([
            'event_type' => 'data_access',
            'event_category' => 'data',
            'user_id' => $userId,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'action' => $action,
            'risk_level' => $this->determineDataRiskLevel($model, $action),
            'description' => "Data {$action} on " . class_basename($model),
        ]);
    }

    public function logDataModification(
        Model $model,
        string $action,
        array $oldValues = [],
        array $newValues = [],
        ?int $userId = null
    ): SecurityAuditLog {
        return $this->logEvent([
            'event_type' => 'data_modification',
            'event_category' => 'data',
            'user_id' => $userId,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'risk_level' => $this->determineDataRiskLevel($model, $action),
            'description' => "Data {$action} on " . class_basename($model),
        ]);
    }

    public function logSecurityEvent(string $eventType, string $description, array $metadata = []): SecurityAuditLog
    {
        return $this->logEvent([
            'event_type' => $eventType,
            'event_category' => 'security',
            'risk_level' => $this->determineSecurityRiskLevel($eventType, $metadata),
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    public function logSystemEvent(string $eventType, string $description, array $metadata = []): SecurityAuditLog
    {
        return $this->logEvent([
            'event_type' => $eventType,
            'event_category' => 'system',
            'risk_level' => 'low',
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    protected function logEvent(array $data): SecurityAuditLog
    {
        return SecurityAuditLog::logEvent(array_merge([
            'tenant_id' => auth()->user()?->tenant_id ?? 1,
            'user_id' => auth()->id(),
        ], $data));
    }

    protected function determineAuthRiskLevel(string $action, array $metadata): string
    {
        // High risk for suspicious login activities
        if ($action === 'login_failed' && ($metadata['attempts'] ?? 0) > 3) {
            return 'high';
        }

        if ($action === 'login_success' && ($metadata['suspicious_ip'] ?? false)) {
            return 'medium';
        }

        if (in_array($action, ['login_failed', 'logout_forced'])) {
            return 'medium';
        }

        return 'low';
    }

    protected function determineDataRiskLevel(Model $model, string $action): string
    {
        // Critical for deletion of important data
        if ($action === 'delete' && $this->isCriticalModel($model)) {
            return 'critical';
        }

        // High for modifications to sensitive data
        if (in_array($action, ['update', 'create']) && $this->isSensitiveModel($model)) {
            return 'high';
        }

        // Medium for access to sensitive data
        if ($action === 'read' && $this->isSensitiveModel($model)) {
            return 'medium';
        }

        return 'low';
    }

    protected function determineSecurityRiskLevel(string $eventType, array $metadata): string
    {
        $highRiskEvents = [
            'suspicious_activity',
            'unauthorized_access_attempt',
            'privilege_escalation',
            'data_breach_detected',
        ];

        $criticalRiskEvents = [
            'security_breach',
            'data_exfiltration',
            'system_compromise',
        ];

        if (in_array($eventType, $criticalRiskEvents)) {
            return 'critical';
        }

        if (in_array($eventType, $highRiskEvents)) {
            return 'high';
        }

        return 'medium';
    }

    protected function isCriticalModel(Model $model): bool
    {
        $criticalModels = [
            'App\Models\User',
            'App\Models\Deal',
            'App\Models\Company',
        ];

        return in_array(get_class($model), $criticalModels);
    }

    protected function isSensitiveModel(Model $model): bool
    {
        $sensitiveModels = [
            'App\Models\Contact',
            'App\Models\Company',
            'App\Models\Deal',
            'App\Models\User',
            'App\Models\GdprConsent',
            'App\Models\GdprDataRequest',
        ];

        return in_array(get_class($model), $sensitiveModels);
    }

    public function getRecentHighRiskEvents(int $days = 7): Collection
    {
        return SecurityAuditLog::highRisk()
            ->recent($days)
            ->orderBy('occurred_at', 'desc')
            ->get();
    }

    public function getUserActivityReport(int $userId, int $days = 30): array
    {
        $logs = SecurityAuditLog::forUser($userId)
            ->recent($days)
            ->orderBy('occurred_at', 'desc')
            ->get();

        return [
            'total_events' => $logs->count(),
            'event_types' => $logs->groupBy('event_type')->map->count(),
            'risk_levels' => $logs->groupBy('risk_level')->map->count(),
            'daily_activity' => $logs->groupBy(fn($log) => $log->occurred_at->format('Y-m-d'))->map->count(),
            'recent_events' => $logs->take(20)->toArray(),
        ];
    }

    public function getComplianceReport(int $days = 30): array
    {
        $logs = SecurityAuditLog::recent($days)->get();

        return [
            'total_events' => $logs->count(),
            'by_category' => $logs->groupBy('event_category')->map->count(),
            'by_risk_level' => $logs->groupBy('risk_level')->map->count(),
            'high_risk_events' => $logs->where('risk_level', 'high')->count(),
            'critical_events' => $logs->where('risk_level', 'critical')->count(),
            'failed_logins' => $logs->where('event_type', 'login_failed')->count(),
            'data_modifications' => $logs->where('event_category', 'data')->count(),
        ];
    }
}