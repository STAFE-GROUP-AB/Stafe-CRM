<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'noteable_type',
        'noteable_id',
        'is_private',
        'is_pinned',
        'attachments',
        'created_by',
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'is_pinned' => 'boolean',
        'attachments' => 'array',
    ];

    public function noteable(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_private', true);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }
}
