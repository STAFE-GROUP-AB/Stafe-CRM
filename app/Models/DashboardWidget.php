<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardWidget extends Model
{
    use HasFactory;

    protected $fillable = [
        'dashboard_id',
        'widget_type',
        'title',
        'description',
        'configuration',
        'data_source',
        'position',
        'filters',
        'refresh_interval',
        'is_active',
    ];

    protected $casts = [
        'configuration' => 'array',
        'data_source' => 'array',
        'position' => 'array',
        'filters' => 'array',
        'is_active' => 'boolean',
    ];

    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(Dashboard::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('widget_type', $type);
    }

    public function getDataSourceAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getConfigurationAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getPositionAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getFiltersAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function shouldRefresh(): bool
    {
        return $this->updated_at->addSeconds($this->refresh_interval)->isPast();
    }
}