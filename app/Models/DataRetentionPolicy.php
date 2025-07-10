<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DataRetentionPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'model_type',
        'retention_days',
        'action_after_retention',
        'conditions',
        'date_field',
        'is_active',
        'description',
        'warning_days',
        'last_executed_at',
        'execution_results',
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'boolean',
        'last_executed_at' => 'datetime',
        'execution_results' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForModel($query, string $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    public function scopeDueForExecution($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('last_executed_at')
                          ->orWhere('last_executed_at', '<', now()->subDay());
                    });
    }

    public function getRetentionCutoffDate(): Carbon
    {
        return now()->subDays($this->retention_days);
    }

    public function getWarningCutoffDate(): Carbon
    {
        return now()->subDays($this->retention_days - $this->warning_days);
    }

    public function execute(): array
    {
        $modelClass = $this->model_type;
        $cutoffDate = $this->getRetentionCutoffDate();
        
        $query = $modelClass::where($this->date_field, '<', $cutoffDate);
        
        // Apply additional conditions if specified
        if ($this->conditions) {
            foreach ($this->conditions as $field => $value) {
                $query->where($field, $value);
            }
        }

        $affectedRecords = $query->count();
        $processedRecords = 0;

        switch ($this->action_after_retention) {
            case 'delete':
                $processedRecords = $query->delete();
                break;
            case 'anonymize':
                $processedRecords = $this->anonymizeRecords($query);
                break;
            case 'archive':
                $processedRecords = $this->archiveRecords($query);
                break;
        }

        $results = [
            'executed_at' => now(),
            'affected_records' => $affectedRecords,
            'processed_records' => $processedRecords,
            'action' => $this->action_after_retention,
            'cutoff_date' => $cutoffDate->toDateTimeString(),
        ];

        $this->update([
            'last_executed_at' => now(),
            'execution_results' => $results,
        ]);

        return $results;
    }

    protected function anonymizeRecords($query): int
    {
        // Implementation would depend on the specific model and fields
        // This is a placeholder for the anonymization logic
        return 0;
    }

    protected function archiveRecords($query): int
    {
        // Implementation would move records to an archive table
        // This is a placeholder for the archiving logic
        return 0;
    }
}