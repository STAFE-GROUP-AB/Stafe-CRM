<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'signer_id',
        'signer_name',
        'signer_email',
        'signer_title',
        'signature_data',
        'ip_address',
        'user_agent',
        'signature_type',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function signer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signer_id');
    }

    public function getSignatureImageAttribute(): string
    {
        return 'data:image/png;base64,' . $this->signature_data;
    }
}