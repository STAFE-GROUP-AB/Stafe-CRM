<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_ticket_id',
        'user_id',
        'contact_id',
        'message',
        'response_type',
        'is_internal_note',
        'response_time_minutes',
        'attachments',
    ];

    protected $casts = [
        'is_internal_note' => 'boolean',
        'attachments' => 'array',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(CustomerTicket::class, 'customer_ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function scopeAgentResponses($query)
    {
        return $query->where('response_type', 'agent');
    }

    public function scopeCustomerResponses($query)
    {
        return $query->where('response_type', 'customer');
    }

    public function scopePublic($query)
    {
        return $query->where('is_internal_note', false);
    }

    public function isFromAgent(): bool
    {
        return $this->response_type === 'agent';
    }

    public function isFromCustomer(): bool
    {
        return $this->response_type === 'customer';
    }

    public function getAuthorNameAttribute(): string
    {
        if ($this->response_type === 'agent' && $this->user) {
            return $this->user->name;
        }

        if ($this->response_type === 'customer' && $this->contact) {
            return "{$this->contact->first_name} {$this->contact->last_name}";
        }

        return 'System';
    }

    public function getFormattedResponseTimeAttribute(): ?string
    {
        if (! $this->response_time_minutes) {
            return null;
        }

        if ($this->response_time_minutes < 60) {
            return "{$this->response_time_minutes} min";
        }

        $hours = floor($this->response_time_minutes / 60);
        $minutes = $this->response_time_minutes % 60;

        if ($hours < 24) {
            return $minutes > 0 ? "{$hours}h {$minutes}m" : "{$hours}h";
        }

        $days = floor($hours / 24);
        $remainingHours = $hours % 24;

        return $remainingHours > 0 ? "{$days}d {$remainingHours}h" : "{$days}d";
    }
}
