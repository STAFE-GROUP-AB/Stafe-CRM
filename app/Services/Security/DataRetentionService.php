<?php

namespace App\Services\Security;

use App\Models\DataRetentionPolicy;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DataRetentionService
{
    public function createPolicy(array $data): DataRetentionPolicy
    {
        return DataRetentionPolicy::create(array_merge($data, [
            'tenant_id' => auth()->user()?->tenant_id ?? 1,
        ]));
    }

    public function executePolicy(DataRetentionPolicy $policy): array
    {
        if (!$policy->is_active) {
            return [
                'success' => false,
                'error' => 'Policy is not active',
            ];
        }

        try {
            $results = $policy->execute();
            
            // Log the execution
            logger()->info('Data retention policy executed', [
                'policy_id' => $policy->id,
                'policy_name' => $policy->name,
                'results' => $results,
            ]);

            return [
                'success' => true,
                'results' => $results,
            ];

        } catch (\Exception $e) {
            logger()->error('Data retention policy execution failed', [
                'policy_id' => $policy->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function executeAllPolicies(): array
    {
        $policies = DataRetentionPolicy::dueForExecution()->get();
        $results = [];

        foreach ($policies as $policy) {
            $results[$policy->id] = $this->executePolicy($policy);
        }

        return $results;
    }

    public function previewPolicyExecution(DataRetentionPolicy $policy): array
    {
        $modelClass = $policy->model_type;
        $cutoffDate = $policy->getRetentionCutoffDate();
        
        if (!class_exists($modelClass)) {
            return [
                'error' => "Model class {$modelClass} does not exist",
            ];
        }

        $query = $modelClass::where($policy->date_field, '<', $cutoffDate);
        
        // Apply additional conditions
        if ($policy->conditions) {
            foreach ($policy->conditions as $field => $value) {
                $query->where($field, $value);
            }
        }

        $affectedRecords = $query->count();
        $sampleRecords = $query->take(10)->get()->toArray();

        return [
            'policy_name' => $policy->name,
            'model_type' => $policy->model_type,
            'cutoff_date' => $cutoffDate->toDateTimeString(),
            'action' => $policy->action_after_retention,
            'affected_records' => $affectedRecords,
            'sample_records' => $sampleRecords,
        ];
    }

    public function getRecordsNearingRetention(int $warningDays = 30): Collection
    {
        $policies = DataRetentionPolicy::active()->get();
        $nearingRetention = collect();

        foreach ($policies as $policy) {
            $warningDate = $policy->getWarningCutoffDate();
            $retentionDate = $policy->getRetentionCutoffDate();
            
            $modelClass = $policy->model_type;
            
            if (!class_exists($modelClass)) {
                continue;
            }

            $query = $modelClass::whereBetween($policy->date_field, [$retentionDate, $warningDate]);
            
            // Apply additional conditions
            if ($policy->conditions) {
                foreach ($policy->conditions as $field => $value) {
                    $query->where($field, $value);
                }
            }

            $records = $query->get();
            
            foreach ($records as $record) {
                $nearingRetention->push([
                    'policy' => $policy,
                    'record' => $record,
                    'retention_date' => $retentionDate,
                    'days_until_retention' => now()->diffInDays($retentionDate),
                ]);
            }
        }

        return $nearingRetention->sortBy('retention_date');
    }

    public function getRetentionReport(): array
    {
        $policies = DataRetentionPolicy::active()->get();
        $report = [
            'total_policies' => $policies->count(),
            'policies_by_action' => $policies->groupBy('action_after_retention')->map->count(),
            'policies_by_model' => $policies->groupBy('model_type')->map->count(),
            'recent_executions' => [],
            'upcoming_retentions' => [],
        ];

        // Recent executions
        $recentExecutions = DataRetentionPolicy::whereNotNull('last_executed_at')
            ->orderBy('last_executed_at', 'desc')
            ->take(10)
            ->get(['name', 'last_executed_at', 'execution_results']);

        $report['recent_executions'] = $recentExecutions->toArray();

        // Upcoming retentions
        $upcomingRetentions = $this->getRecordsNearingRetention(7);
        $report['upcoming_retentions'] = $upcomingRetentions->take(20)->toArray();

        return $report;
    }

    public function validatePolicy(array $policyData): array
    {
        $errors = [];

        // Validate model type
        if (!class_exists($policyData['model_type'])) {
            $errors[] = "Model class {$policyData['model_type']} does not exist";
        }

        // Validate retention days
        if (($policyData['retention_days'] ?? 0) < 1) {
            $errors[] = 'Retention days must be at least 1';
        }

        // Validate date field
        if (!empty($policyData['model_type']) && class_exists($policyData['model_type'])) {
            $model = new $policyData['model_type'];
            $dateField = $policyData['date_field'] ?? 'created_at';
            
            if (!in_array($dateField, $model->getFillable()) && 
                !in_array($dateField, ['created_at', 'updated_at'])) {
                $errors[] = "Date field '{$dateField}' is not valid for this model";
            }
        }

        // Validate warning days
        $warningDays = $policyData['warning_days'] ?? 30;
        $retentionDays = $policyData['retention_days'] ?? 0;
        
        if ($warningDays >= $retentionDays) {
            $errors[] = 'Warning days must be less than retention days';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    public function anonymizeExpiredData(DataRetentionPolicy $policy): array
    {
        $modelClass = $policy->model_type;
        $cutoffDate = $policy->getRetentionCutoffDate();
        
        $query = $modelClass::where($policy->date_field, '<', $cutoffDate);
        
        // Apply additional conditions
        if ($policy->conditions) {
            foreach ($policy->conditions as $field => $value) {
                $query->where($field, $value);
            }
        }

        $processedRecords = 0;
        $errors = [];

        $query->chunk(100, function ($records) use (&$processedRecords, &$errors) {
            foreach ($records as $record) {
                try {
                    $this->anonymizeRecord($record);
                    $processedRecords++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'record_id' => $record->id,
                        'error' => $e->getMessage(),
                    ];
                }
            }
        });

        return [
            'processed_records' => $processedRecords,
            'errors' => $errors,
        ];
    }

    protected function anonymizeRecord($record): void
    {
        // Define common anonymization patterns
        $anonymizationMap = [
            'email' => 'anonymized_' . uniqid() . '@example.com',
            'phone' => '***-***-****',
            'first_name' => 'Anonymized',
            'last_name' => 'User',
            'name' => 'Anonymized User',
            'address' => 'Anonymized Address',
            'city' => 'Anonymized City',
            'postal_code' => '00000',
        ];

        $updates = [];
        foreach ($anonymizationMap as $field => $anonymizedValue) {
            if ($record->getAttribute($field)) {
                $updates[$field] = $anonymizedValue;
            }
        }

        if (!empty($updates)) {
            $record->update($updates);
        }
    }

    public function scheduleRetentionExecution(): void
    {
        // This would typically be called by a scheduled job
        $this->executeAllPolicies();
    }
}