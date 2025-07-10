<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsHeatMap extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'configuration',
        'data_points',
        'color_scheme',
        'date_from',
        'date_to',
        'user_id',
        'tenant_id',
    ];

    protected $casts = [
        'configuration' => 'array',
        'data_points' => 'array',
        'color_scheme' => 'array',
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
        return $query->where('type', $type);
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

    public function getIntensityLevel($value): string
    {
        $max = max($this->data_points);
        $min = min($this->data_points);
        $range = $max - $min;
        
        if ($range == 0) return 'medium';
        
        $percentage = ($value - $min) / $range;
        
        if ($percentage >= 0.8) return 'very-high';
        if ($percentage >= 0.6) return 'high';
        if ($percentage >= 0.4) return 'medium';
        if ($percentage >= 0.2) return 'low';
        return 'very-low';
    }
}