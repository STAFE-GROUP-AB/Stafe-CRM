<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentUsageAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id',
        'user_id',
        'deal_id',
        'contact_id',
        'action',
        'metadata',
        'ip_address',
        'user_agent',
        'duration_seconds',
    ];

    protected $casts = [
        'metadata' => 'array',
        'duration_seconds' => 'integer',
    ];

    public function content(): BelongsTo
    {
        return $this->belongsTo(SalesContent::class, 'content_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}