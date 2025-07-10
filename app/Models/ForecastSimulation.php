<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForecastSimulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'base_scenario',
        'scenarios',
        'variables',
        'results',
        'assumptions',
        'simulation_type',
        'forecast_start_date',
        'forecast_end_date',
        'user_id',
        'tenant_id',
    ];

    protected $casts = [
        'base_scenario' => 'array',
        'scenarios' => 'array',
        'variables' => 'array',
        'results' => 'array',
        'assumptions' => 'array',
        'forecast_start_date' => 'date',
        'forecast_end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('simulation_type', $type);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('forecast_start_date', [$startDate, $endDate])
                     ->orWhereBetween('forecast_end_date', [$startDate, $endDate]);
    }

    public function getScenarioNames(): array
    {
        return array_column($this->scenarios, 'name');
    }

    public function getScenarioResult($scenarioName): ?array
    {
        $scenarios = collect($this->scenarios);
        $scenario = $scenarios->firstWhere('name', $scenarioName);
        
        if (!$scenario) return null;
        
        return $this->results[$scenarioName] ?? null;
    }

    public function getBestCaseScenario(): ?array
    {
        $bestCase = null;
        $bestValue = 0;
        
        foreach ($this->scenarios as $scenario) {
            $result = $this->getScenarioResult($scenario['name']);
            if ($result && ($result['total_value'] ?? 0) > $bestValue) {
                $bestValue = $result['total_value'];
                $bestCase = $scenario;
            }
        }
        
        return $bestCase;
    }

    public function getWorstCaseScenario(): ?array
    {
        $worstCase = null;
        $worstValue = PHP_FLOAT_MAX;
        
        foreach ($this->scenarios as $scenario) {
            $result = $this->getScenarioResult($scenario['name']);
            if ($result && ($result['total_value'] ?? 0) < $worstValue) {
                $worstValue = $result['total_value'];
                $worstCase = $scenario;
            }
        }
        
        return $worstCase;
    }

    public function getConfidenceInterval(): array
    {
        $values = [];
        foreach ($this->scenarios as $scenario) {
            $result = $this->getScenarioResult($scenario['name']);
            if ($result) {
                $values[] = $result['total_value'] ?? 0;
            }
        }
        
        sort($values);
        $count = count($values);
        
        if ($count < 2) return ['min' => 0, 'max' => 0];
        
        $lower = $values[floor($count * 0.1)];
        $upper = $values[ceil($count * 0.9)];
        
        return ['min' => $lower, 'max' => $upper];
    }

    public function getForecastAccuracy(): ?float
    {
        $actual = $this->results['actual'] ?? null;
        $predicted = $this->results['predicted'] ?? null;
        
        if (!$actual || !$predicted) return null;
        
        $actualValue = $actual['total_value'] ?? 0;
        $predictedValue = $predicted['total_value'] ?? 0;
        
        if ($actualValue == 0) return null;
        
        $accuracy = 100 - (abs($actualValue - $predictedValue) / $actualValue * 100);
        return max(0, $accuracy);
    }
}