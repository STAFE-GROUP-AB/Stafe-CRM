<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoringFactor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'category',
        'weight',
        'calculation_method',
        'configuration',
        'is_active',
        'sort_order',
        'data_source',
    ];

    protected $casts = [
        'weight' => 'decimal:4',
        'configuration' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('display_name');
    }

    public static function getCategories(): array
    {
        return [
            'demographic' => 'Demographic',
            'behavioral' => 'Behavioral',
            'engagement' => 'Engagement',
            'firmographic' => 'Firmographic',
        ];
    }

    public static function getCalculationMethods(): array
    {
        return [
            'rule_based' => 'Rule Based',
            'ml_model' => 'Machine Learning Model',
            'api_call' => 'External API Call',
        ];
    }

    public function getCategoryLabel(): string
    {
        return self::getCategories()[$this->category] ?? ucfirst($this->category);
    }

    public function getMethodLabel(): string
    {
        return self::getCalculationMethods()[$this->calculation_method] ?? ucfirst($this->calculation_method);
    }
}
