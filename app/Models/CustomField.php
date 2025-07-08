<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomField extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'entity_type',
        'description',
        'options',
        'default_value',
        'is_required',
        'is_active',
        'order',
        'validation_rules',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'validation_rules' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForEntity($query, $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
