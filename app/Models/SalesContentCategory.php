<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SalesContentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function content(): HasMany
    {
        return $this->hasMany(SalesContent::class, 'category_id');
    }

    public function publishedContent(): HasMany
    {
        return $this->hasMany(SalesContent::class, 'category_id')
            ->where('status', 'published');
    }

    public function getContentCountAttribute(): int
    {
        return $this->content()->count();
    }

    public function getPublishedContentCountAttribute(): int
    {
        return $this->publishedContent()->count();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}