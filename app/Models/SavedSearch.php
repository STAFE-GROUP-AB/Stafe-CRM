<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavedSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'entity_type',
        'filters',
        'columns',
        'sort_field',
        'sort_direction',
        'is_global',
        'user_id',
    ];

    protected $casts = [
        'filters' => 'array',
        'columns' => 'array',
        'is_global' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForEntity($query, string $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_global', false);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere('is_global', true);
        });
    }

    /**
     * Apply this saved search to a query
     */
    public function applyToQuery($query)
    {
        foreach ($this->filters as $filter) {
            $this->applyFilter($query, $filter);
        }

        if ($this->sort_field) {
            $query->orderBy($this->sort_field, $this->sort_direction);
        }

        return $query;
    }

    /**
     * Apply a single filter to the query
     */
    private function applyFilter($query, array $filter)
    {
        $field = $filter['field'];
        $operator = $filter['operator'];
        $value = $filter['value'];

        switch ($operator) {
            case 'equals':
                $query->where($field, $value);
                break;
            case 'not_equals':
                $query->where($field, '!=', $value);
                break;
            case 'contains':
                $query->where($field, 'LIKE', '%' . $value . '%');
                break;
            case 'not_contains':
                $query->where($field, 'NOT LIKE', '%' . $value . '%');
                break;
            case 'starts_with':
                $query->where($field, 'LIKE', $value . '%');
                break;
            case 'ends_with':
                $query->where($field, 'LIKE', '%' . $value);
                break;
            case 'greater_than':
                $query->where($field, '>', $value);
                break;
            case 'greater_than_equal':
                $query->where($field, '>=', $value);
                break;
            case 'less_than':
                $query->where($field, '<', $value);
                break;
            case 'less_than_equal':
                $query->where($field, '<=', $value);
                break;
            case 'between':
                $query->whereBetween($field, [$value['start'], $value['end']]);
                break;
            case 'in':
                $query->whereIn($field, $value);
                break;
            case 'not_in':
                $query->whereNotIn($field, $value);
                break;
            case 'is_null':
                $query->whereNull($field);
                break;
            case 'is_not_null':
                $query->whereNotNull($field);
                break;
        }
    }
}