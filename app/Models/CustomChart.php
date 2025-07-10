<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomChart extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'chart_type',
        'data_source',
        'chart_config',
        'styling',
        'filters',
        'drill_down_config',
        'is_real_time',
        'refresh_interval',
        'is_public',
        'user_id',
        'tenant_id',
    ];

    protected $casts = [
        'data_source' => 'array',
        'chart_config' => 'array',
        'styling' => 'array',
        'filters' => 'array',
        'drill_down_config' => 'array',
        'is_real_time' => 'boolean',
        'is_public' => 'boolean',
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
        return $query->where('chart_type', $type);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeRealTime($query)
    {
        return $query->where('is_real_time', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function shouldRefresh(): bool
    {
        if (!$this->is_real_time) return false;
        
        return $this->updated_at->addSeconds($this->refresh_interval)->isPast();
    }

    public function getChartData(): array
    {
        $dataSource = $this->data_source;
        $model = $dataSource['model'] ?? null;
        $fields = $dataSource['fields'] ?? [];
        $filters = array_merge($dataSource['filters'] ?? [], $this->filters ?? []);
        
        if (!$model || !class_exists($model)) {
            return [];
        }
        
        $query = $model::query();
        
        // Apply tenant scoping if applicable
        if ($this->tenant_id && method_exists($model, 'scopeForTenant')) {
            $query->forTenant($this->tenant_id);
        }
        
        // Apply filters
        foreach ($filters as $filter) {
            $field = $filter['field'] ?? null;
            $operator = $filter['operator'] ?? '=';
            $value = $filter['value'] ?? null;
            
            if ($field && $value !== null) {
                $query->where($field, $operator, $value);
            }
        }
        
        // Apply grouping and aggregation
        $groupBy = $dataSource['group_by'] ?? null;
        $aggregateFunction = $dataSource['aggregate'] ?? 'count';
        $aggregateField = $dataSource['aggregate_field'] ?? '*';
        
        if ($groupBy) {
            $query->groupBy($groupBy);
            $query->selectRaw("{$groupBy}, {$aggregateFunction}({$aggregateField}) as value");
        }
        
        return $query->get()->toArray();
    }

    public function getChartConfig(): array
    {
        $config = $this->chart_config;
        $config['type'] = $this->chart_type;
        $config['data'] = $this->getChartData();
        $config['styling'] = $this->styling;
        
        return $config;
    }

    public function isAccessibleBy(User $user): bool
    {
        return $this->user_id === $user->id || 
               $this->is_public || 
               $this->tenant_id === $user->tenant_id;
    }

    public function hasDrillDown(): bool
    {
        return !empty($this->drill_down_config);
    }

    public function getDrillDownData($level = 1): array
    {
        if (!$this->hasDrillDown()) return [];
        
        $config = $this->drill_down_config;
        $levelConfig = $config["level_{$level}"] ?? null;
        
        if (!$levelConfig) return [];
        
        // Similar logic to getChartData but with drill-down specific configuration
        return $this->getChartData(); // Simplified for now
    }
}