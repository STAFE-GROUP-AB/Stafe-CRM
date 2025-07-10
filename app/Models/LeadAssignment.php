<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'user_id',
        'lead_routing_rule_id',
        'assignment_reason',
        'ai_confidence_score',
        'assignment_method',
        'assigned_at',
        'assigned_by_user_id',
        'tenant_id',
    ];

    protected $casts = [
        'assignment_reason' => 'array',
        'assigned_at' => 'datetime',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leadRoutingRule(): BelongsTo
    {
        return $this->belongsTo(LeadRoutingRule::class);
    }

    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('assignment_method', $method);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('assigned_at', '>=', now()->subDays($days));
    }

    public function isAiAssigned(): bool
    {
        return $this->assignment_method === 'ai_powered';
    }

    public function hasHighConfidence(): bool
    {
        return $this->ai_confidence_score && $this->ai_confidence_score >= 0.8;
    }

    public function getAssignmentSummary(): string
    {
        $reason = $this->assignment_reason['primary_reason'] ?? 'Unknown';
        
        switch ($this->assignment_method) {
            case 'ai_powered':
                return "AI Assignment: {$reason} (Confidence: " . 
                       number_format($this->ai_confidence_score * 100, 1) . "%)";
            case 'rule_based':
                return "Rule-based: {$reason}";
            case 'manual':
                return "Manual Assignment: {$reason}";
            default:
                return ucfirst(str_replace('_', ' ', $this->assignment_method)) . ": {$reason}";
        }
    }
}