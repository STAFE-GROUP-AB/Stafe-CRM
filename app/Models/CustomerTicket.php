<?php

namespace App\Models;

use App\Services\TicketSlaService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'assigned_to',
        'subject',
        'description',
        'priority',
        'status',
        'category',
        'tags',
        'resolved_at',
        'first_response_at',
        'response_times',
        'first_response_sla_breached',
        'resolution_sla_breached',
        'total_response_time_minutes',
        'total_resolution_time_minutes',
        'sla_due_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'response_times' => 'array',
        'resolved_at' => 'datetime',
        'first_response_at' => 'datetime',
        'sla_due_at' => 'datetime',
        'first_response_sla_breached' => 'boolean',
        'resolution_sla_breached' => 'boolean',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(TicketResponse::class);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'waiting_customer']);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    public function scopeSlaBreached($query)
    {
        return $query->where(function ($q) {
            $q->where('first_response_sla_breached', true)
                ->orWhere('resolution_sla_breached', true);
        });
    }

    public function scopeAwaitingFirstResponse($query)
    {
        return $query->whereNull('first_response_at')
            ->whereIn('status', ['open', 'in_progress']);
    }

    public function scopeSlaDueSoon($query, int $minutesThreshold = 60)
    {
        return $query->whereNotNull('sla_due_at')
            ->where('sla_due_at', '<=', now()->addMinutes($minutesThreshold))
            ->where('sla_due_at', '>', now());
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'red',
            'in_progress' => 'blue',
            'waiting_customer' => 'yellow',
            'resolved' => 'green',
            'closed' => 'gray',
            default => 'gray'
        };
    }

    public function getSlaStatusAttribute(): array
    {
        return app(TicketSlaService::class)->getSlaStatus($this);
    }

    public function getFirstResponseSlaStatusAttribute(): array
    {
        return app(TicketSlaService::class)->getFirstResponseStatus($this);
    }

    public function getResolutionSlaStatusAttribute(): array
    {
        return app(TicketSlaService::class)->getResolutionStatus($this);
    }

    public function getSlaColorAttribute(): string
    {
        $status = $this->sla_status['overall'];

        return app(TicketSlaService::class)->getSlaColorClass($status);
    }

    public function getFormattedFirstResponseTimeAttribute(): ?string
    {
        if (! $this->total_response_time_minutes) {
            return null;
        }

        return app(TicketSlaService::class)->formatMinutes($this->total_response_time_minutes);
    }

    public function getFormattedResolutionTimeAttribute(): ?string
    {
        if (! $this->total_resolution_time_minutes) {
            return null;
        }

        return app(TicketSlaService::class)->formatMinutes($this->total_resolution_time_minutes);
    }

    public function getTimeToSlaDueAttribute(): ?string
    {
        if (! $this->sla_due_at || $this->sla_due_at->isPast()) {
            return null;
        }

        return $this->sla_due_at->diffForHumans(['parts' => 2]);
    }

    public function getIsSlaPausedAttribute(): bool
    {
        return app(TicketSlaService::class)->isSlaPaused($this);
    }

    public function hasFirstResponse(): bool
    {
        return $this->first_response_at !== null;
    }

    public function isResolved(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    public function getAverageResponseTimeAttribute(): ?int
    {
        if (empty($this->response_times)) {
            return null;
        }

        return (int) round(array_sum($this->response_times) / count($this->response_times));
    }

    public function getFormattedAverageResponseTimeAttribute(): ?string
    {
        $avg = $this->average_response_time;

        if (! $avg) {
            return null;
        }

        return app(TicketSlaService::class)->formatMinutes($avg);
    }

    public function addResponse(array $data): TicketResponse
    {
        $response = $this->responses()->create($data);
        app(TicketSlaService::class)->recordResponse($this, $response);

        return $response;
    }

    public function resolve(): void
    {
        app(TicketSlaService::class)->markResolved($this);
    }

    public function updateSlaDue(): void
    {
        app(TicketSlaService::class)->updateSlaDueAt($this);
    }
}
