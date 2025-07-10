<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PipelineVisualization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'visualization_type',
        'pipeline_config',
        'visual_config',
        'data_points',
        'filters',
        'date_from',
        'date_to',
        'user_id',
        'tenant_id',
    ];

    protected $casts = [
        'pipeline_config' => 'array',
        'visual_config' => 'array',
        'data_points' => 'array',
        'filters' => 'array',
        'date_from' => 'date',
        'date_to' => 'date',
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
        return $query->where('visualization_type', $type);
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
        return $query->whereBetween('date_from', [$startDate, $endDate])
                     ->orWhereBetween('date_to', [$startDate, $endDate]);
    }

    public function getConversionRates(): array
    {
        $stages = $this->pipeline_config['stages'] ?? [];
        $conversions = [];
        
        for ($i = 0; $i < count($stages) - 1; $i++) {
            $currentStage = $stages[$i]['name'];
            $nextStage = $stages[$i + 1]['name'];
            
            $currentCount = $this->getStageCount($currentStage);
            $nextCount = $this->getStageCount($nextStage);
            
            $conversions[$currentStage . ' → ' . $nextStage] = $currentCount > 0 ? ($nextCount / $currentCount) * 100 : 0;
        }
        
        return $conversions;
    }

    public function getStageCount($stageName): int
    {
        return collect($this->data_points)->where('stage', $stageName)->sum('count');
    }

    public function getTotalPipelineValue(): float
    {
        return collect($this->data_points)->sum('value');
    }

    public function getAverageDealValue(): float
    {
        $totalValue = $this->getTotalPipelineValue();
        $totalCount = collect($this->data_points)->sum('count');
        
        return $totalCount > 0 ? $totalValue / $totalCount : 0;
    }

    public function getTopPerformingStage(): ?string
    {
        $stagePerformance = collect($this->data_points)
            ->groupBy('stage')
            ->map(function ($items) {
                return $items->sum('value');
            })
            ->sortDesc();
        
        return $stagePerformance->keys()->first();
    }
}