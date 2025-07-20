<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DynamicContentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'content_type',
        'base_template',
        'personalization_rules',
        'variable_mappings',
        'conditional_content',
        'is_active',
        'usage_statistics',
        'created_by_user_id',
        'tenant_id',
    ];

    protected $casts = [
        'personalization_rules' => 'array',
        'variable_mappings' => 'array',
        'conditional_content' => 'array',
        'usage_statistics' => 'array',
        'is_active' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByContentType($query, string $contentType)
    {
        return $query->where('content_type', $contentType);
    }

    /**
     * Generate personalized content based on entity data
     */
    public function generateContent($entity, array $context = []): string
    {
        $content = $this->base_template;
        $variables = $this->extractVariables($entity, $context);

        // Replace variables in template
        foreach ($variables as $key => $value) {
            $content = str_replace("{{" . $key . "}}", $value, $content);
        }

        // Apply conditional content rules
        if ($this->conditional_content) {
            $content = $this->applyConditionalRules($content, $entity, $context);
        }

        // Track usage
        $this->incrementUsageStatistics();

        return $content;
    }

    /**
     * Extract variables from entity based on mappings
     */
    private function extractVariables($entity, array $context): array
    {
        $variables = [];

        foreach ($this->variable_mappings as $variable => $mapping) {
            if (isset($context[$variable])) {
                $variables[$variable] = $context[$variable];
            } elseif ($entity && $mapping['source'] === 'entity') {
                $variables[$variable] = data_get($entity, $mapping['field']);
            } elseif ($mapping['source'] === 'computed') {
                $variables[$variable] = $this->computeVariable($mapping, $entity, $context);
            }
        }

        return $variables;
    }

    /**
     * Apply conditional content rules
     */
    private function applyConditionalRules(string $content, $entity, array $context): string
    {
        foreach ($this->conditional_content as $rule) {
            if ($this->evaluateCondition($rule['condition'], $entity, $context)) {
                $content = str_replace($rule['placeholder'], $rule['content'], $content);
            } else {
                $content = str_replace($rule['placeholder'], $rule['fallback'] ?? '', $content);
            }
        }

        return $content;
    }

    /**
     * Evaluate a condition
     */
    private function evaluateCondition(array $condition, $entity, array $context): bool
    {
        $field = $condition['field'];
        $operator = $condition['operator'];
        $value = $condition['value'];

        $entityValue = data_get($entity, $field) ?? $context[$field] ?? null;

        return match ($operator) {
            'equals' => $entityValue == $value,
            'not_equals' => $entityValue != $value,
            'greater_than' => $entityValue > $value,
            'less_than' => $entityValue < $value,
            'contains' => str_contains((string)$entityValue, $value),
            'in' => in_array($entityValue, (array)$value),
            'not_in' => !in_array($entityValue, (array)$value),
            default => false,
        };
    }

    /**
     * Compute dynamic variables
     */
    private function computeVariable(array $mapping, $entity, array $context)
    {
        return match ($mapping['computation']) {
            'days_since_last_contact' => $entity?->last_contacted_at?->diffInDays(now()) ?? 0,
            'total_deal_value' => $entity?->deals()?->sum('value') ?? 0,
            'engagement_score' => $this->calculateEngagementScore($entity),
            'time_of_day_greeting' => $this->getTimeBasedGreeting(),
            default => $mapping['default'] ?? '',
        };
    }

    /**
     * Calculate engagement score for an entity
     */
    private function calculateEngagementScore($entity): float
    {
        if (!$entity) return 0;

        $score = 0;
        
        // Email engagement
        if (method_exists($entity, 'emails')) {
            $emailCount = $entity->emails()->count();
            $score += min($emailCount * 0.1, 2.0);
        }

        // Recent activity
        if (method_exists($entity, 'activities')) {
            $recentActivities = $entity->activities()->where('created_at', '>=', now()->subDays(30))->count();
            $score += min($recentActivities * 0.2, 3.0);
        }

        return min($score, 10.0);
    }

    /**
     * Get time-based greeting
     */
    private function getTimeBasedGreeting(): string
    {
        $hour = now()->hour;

        return match (true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default => 'Good evening',
        };
    }

    /**
     * Increment usage statistics
     */
    private function incrementUsageStatistics(): void
    {
        $stats = $this->usage_statistics ?? [];
        $today = now()->toDateString();

        if (!isset($stats[$today])) {
            $stats[$today] = 0;
        }

        $stats[$today]++;

        $this->update(['usage_statistics' => $stats]);
    }

    /**
     * Get usage statistics for a date range
     */
    public function getUsageStats(int $days = 30): array
    {
        $stats = $this->usage_statistics ?? [];
        $result = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $result[$date] = $stats[$date] ?? 0;
        }

        return $result;
    }

    /**
     * Get total usage count
     */
    public function getTotalUsage(): int
    {
        $stats = $this->usage_statistics ?? [];
        return array_sum($stats);
    }
}