<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'value',
        'currency',
        'probability',
        'expected_close_date',
        'actual_close_date',
        'status',
        'pipeline_stage_id',
        'company_id',
        'contact_id',
        'source',
        'type',
        'close_reason',
        'custom_fields',
        'owner_id',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'expected_close_date' => 'date',
        'actual_close_date' => 'date',
        'custom_fields' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function pipelineStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class);
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

    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }

    public function getFormattedValueAttribute(): string
    {
        return number_format($this->value, 2) . ' ' . $this->currency;
    }

    public function getWeightedValueAttribute(): float
    {
        return $this->value * ($this->probability / 100);
    }

    public function getFormattedWeightedValueAttribute(): string
    {
        return number_format($this->getWeightedValueAttribute(), 2) . ' ' . $this->currency;
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isWon(): bool
    {
        return $this->status === 'won';
    }

    public function isLost(): bool
    {
        return $this->status === 'lost';
    }
}
