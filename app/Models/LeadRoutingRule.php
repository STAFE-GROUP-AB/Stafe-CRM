<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadRoutingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'conditions',
        'assignment_rules',
        'priority',
        'is_active',
        'use_ai_scoring',
        'ai_parameters',
        'tenant_id',
    ];

    protected $casts = [
        'conditions' => 'array',
        'assignment_rules' => 'array',
        'is_active' => 'boolean',
        'use_ai_scoring' => 'boolean',
        'ai_parameters' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function leadAssignments(): HasMany
    {
        return $this->hasMany(LeadAssignment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    public function matchesConditions($contact): bool
    {
        foreach ($this->conditions as $condition) {
            $field = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? '=';
            $value = $condition['value'] ?? null;

            if (!$field || $value === null) continue;

            $contactValue = data_get($contact, $field);
            
            if (!$this->evaluateCondition($contactValue, $operator, $value)) {
                return false;
            }
        }

        return true;
    }

    public function assignLead($contact): ?int
    {
        $assignmentMethod = $this->assignment_rules['method'] ?? 'round_robin';
        
        switch ($assignmentMethod) {
            case 'round_robin':
                return $this->assignRoundRobin();
            case 'load_based':
                return $this->assignLoadBased();
            case 'skill_based':
                return $this->assignSkillBased($contact);
            case 'ai_powered':
                return $this->assignAiPowered($contact);
            default:
                return null;
        }
    }

    private function evaluateCondition($contactValue, $operator, $expectedValue): bool
    {
        switch ($operator) {
            case '=':
            case 'equals':
                return $contactValue == $expectedValue;
            case '!=':
            case 'not_equals':
                return $contactValue != $expectedValue;
            case '>':
            case 'greater_than':
                return $contactValue > $expectedValue;
            case '>=':
            case 'greater_than_or_equal':
                return $contactValue >= $expectedValue;
            case '<':
            case 'less_than':
                return $contactValue < $expectedValue;
            case '<=':
            case 'less_than_or_equal':
                return $contactValue <= $expectedValue;
            case 'contains':
                return str_contains(strtolower($contactValue), strtolower($expectedValue));
            case 'starts_with':
                return str_starts_with(strtolower($contactValue), strtolower($expectedValue));
            case 'ends_with':
                return str_ends_with(strtolower($contactValue), strtolower($expectedValue));
            case 'in':
                return in_array($contactValue, (array) $expectedValue);
            case 'not_in':
                return !in_array($contactValue, (array) $expectedValue);
            default:
                return false;
        }
    }

    private function assignRoundRobin(): ?int
    {
        $userIds = $this->assignment_rules['user_ids'] ?? [];
        if (empty($userIds)) return null;

        $lastAssignmentIndex = cache()->get("lead_routing_rule_{$this->id}_last_assignment", -1);
        $nextIndex = ($lastAssignmentIndex + 1) % count($userIds);
        
        cache()->put("lead_routing_rule_{$this->id}_last_assignment", $nextIndex, 3600);
        
        return $userIds[$nextIndex];
    }

    private function assignLoadBased(): ?int
    {
        $userIds = $this->assignment_rules['user_ids'] ?? [];
        if (empty($userIds)) return null;

        // Find user with lowest current lead count
        $userLoads = [];
        foreach ($userIds as $userId) {
            $activeLeads = LeadAssignment::where('user_id', $userId)
                ->whereHas('contact', function ($query) {
                    $query->whereNull('closed_at'); // Assuming contacts have a closed_at field
                })
                ->count();
            $userLoads[$userId] = $activeLeads;
        }

        return array_keys($userLoads, min($userLoads))[0];
    }

    private function assignSkillBased($contact): ?int
    {
        // Simplified skill-based assignment
        $userIds = $this->assignment_rules['user_ids'] ?? [];
        $skillRequirements = $this->assignment_rules['skill_requirements'] ?? [];
        
        // For now, return first available user
        // In a full implementation, this would match user skills to requirements
        return $userIds[0] ?? null;
    }

    private function assignAiPowered($contact): ?int
    {
        if (!$this->use_ai_scoring) {
            return $this->assignRoundRobin();
        }

        // AI-powered assignment would use ML models to determine best fit
        // For now, fall back to round robin
        return $this->assignRoundRobin();
    }
}