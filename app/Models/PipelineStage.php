<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PipelineStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'order',
        'default_probability',
        'is_active',
        'is_closed',
        'is_won',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_closed' => 'boolean',
        'is_won' => 'boolean',
        'settings' => 'array',
    ];

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOpen($query)
    {
        return $query->where('is_closed', false);
    }

    public function scopeClosed($query)
    {
        return $query->where('is_closed', true);
    }

    public function scopeWon($query)
    {
        return $query->where('is_won', true);
    }

    public function scopeLost($query)
    {
        return $query->where('is_closed', true)->where('is_won', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
