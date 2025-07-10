<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesCoaching extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'deal_id',
        'coaching_type',
        'priority_level',
        'recommendations',
        'skill_gaps',
        'performance_metrics',
        'action_items',
        'suggested_resources',
        'coaching_score',
        'implementation_status',
        'coach_notes',
        'follow_up_date',
        'model_version',
        'last_generated_at',
        'ai_model_id',
    ];

    protected $casts = [
        'recommendations' => 'array',
        'skill_gaps' => 'array',
        'performance_metrics' => 'array',
        'action_items' => 'array',
        'suggested_resources' => 'array',
        'coaching_score' => 'float',
        'follow_up_date' => 'date',
        'last_generated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function aiModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class);
    }

    public function getPriorityColor(): string
    {
        return match ($this->priority_level) {
            'critical' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray'
        };
    }

    public function getStatusColor(): string
    {
        return match ($this->implementation_status) {
            'completed' => 'green',
            'in_progress' => 'blue',
            'pending' => 'yellow',
            'not_started' => 'gray',
            'skipped' => 'red',
            default => 'gray'
        };
    }

    public function scopeByType($query, $type)
    {
        return $query->where('coaching_type', $type);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority_level', ['critical', 'high']);
    }

    public function scopePending($query)
    {
        return $query->where('implementation_status', 'pending');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDueForFollowUp($query)
    {
        return $query->where('follow_up_date', '<=', now());
    }

    public static function getCoachingTypes(): array
    {
        return [
            'deal_strategy' => 'Deal Strategy',
            'communication' => 'Communication Skills',
            'negotiation' => 'Negotiation Tactics',
            'product_knowledge' => 'Product Knowledge',
            'time_management' => 'Time Management',
            'prospecting' => 'Prospecting Techniques',
            'objection_handling' => 'Objection Handling',
            'closing' => 'Closing Techniques',
        ];
    }

    public static function getPriorityLevels(): array
    {
        return [
            'critical' => 'Critical',
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low',
        ];
    }

    public static function getImplementationStatuses(): array
    {
        return [
            'not_started' => 'Not Started',
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'skipped' => 'Skipped',
        ];
    }
}