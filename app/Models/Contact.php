<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'title',
        'department',
        'company_id',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'timezone',
        'birthday',
        'bio',
        'social_links',
        'custom_fields',
        'avatar_url',
        'status',
        'source',
        'lifetime_value',
        'last_contacted_at',
        'owner_id',
    ];

    protected $casts = [
        'birthday' => 'date',
        'social_links' => 'array',
        'custom_fields' => 'array',
        'lifetime_value' => 'decimal:2',
        'last_contacted_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->getFullNameAttribute();
    }

    public function getFormattedValueAttribute(): string
    {
        if (!$this->lifetime_value) {
            return '';
        }
        
        return number_format($this->lifetime_value, 2) . ' USD';
    }
}
