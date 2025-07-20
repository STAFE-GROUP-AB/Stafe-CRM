<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkflowAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_type',
        'workflow_id',
        'metric_name',
        'metric_value',
        'metric_metadata',
        'metric_date',
        'aggregation_period',
        'dimensions',
        'tenant_id',
    ];

    protected $casts = [
        'metric_metadata' => 'array',
        'dimensions' => 'array',
        'metric_value' => 'decimal:4',
        'metric_date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeByWorkflow($query, string $workflowType, string $workflowId = null)
    {
        $query->where('workflow_type', $workflowType);
        
        if ($workflowId) {
            $query->where('workflow_id', $workflowId);
        }
        
        return $query;
    }

    public function scopeByMetric($query, string $metricName)
    {
        return $query->where('metric_name', $metricName);
    }

    public function scopeByPeriod($query, string $period)
    {
        return $query->where('aggregation_period', $period);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('metric_date', [$startDate, $endDate]);
    }

    public function scopeByDimension($query, string $dimensionKey, $dimensionValue)
    {
        return $query->whereJsonContains('dimensions', [$dimensionKey => $dimensionValue]);
    }

    /**
     * Record a workflow metric
     */
    public static function recordMetric(
        string $workflowType,
        string $workflowId,
        string $metricName,
        float $metricValue,
        array $metadata = [],
        array $dimensions = [],
        string $aggregationPeriod = 'daily',
        ?\Carbon\Carbon $date = null
    ): self {
        $date = $date ?? now();

        return self::create([
            'workflow_type' => $workflowType,
            'workflow_id' => $workflowId,
            'metric_name' => $metricName,
            'metric_value' => $metricValue,
            'metric_metadata' => $metadata,
            'metric_date' => $date->toDateString(),
            'aggregation_period' => $aggregationPeriod,
            'dimensions' => $dimensions,
            'tenant_id' => auth()->user()?->current_tenant_id,
        ]);
    }

    /**
     * Record workflow execution metrics
     */
    public static function recordWorkflowExecution(WorkflowInstance $instance): void
    {
        $template = $instance->workflowTemplate;
        $duration = $instance->duration ?? 0;
        $success = $instance->isCompleted();

        // Record execution count
        self::recordMetric(
            'workflow_template',
            (string)$template->id,
            'executions_count',
            1,
            ['instance_id' => $instance->id, 'success' => $success],
            ['template_name' => $template->name, 'status' => $instance->status]
        );

        // Record execution duration
        if ($duration > 0) {
            self::recordMetric(
                'workflow_template',
                (string)$template->id,
                'execution_duration_seconds',
                $duration,
                ['instance_id' => $instance->id],
                ['template_name' => $template->name]
            );
        }

        // Record success/failure rate
        self::recordMetric(
            'workflow_template',
            (string)$template->id,
            'success_rate',
            $success ? 1 : 0,
            ['instance_id' => $instance->id],
            ['template_name' => $template->name]
        );
    }

    /**
     * Record event trigger metrics
     */
    public static function recordEventTriggerExecution(EventTriggerExecution $execution): void
    {
        $trigger = $execution->eventTrigger;
        $duration = $execution->duration ?? 0;
        $success = $execution->isCompleted();

        // Record trigger execution count
        self::recordMetric(
            'event_trigger',
            (string)$trigger->id,
            'trigger_executions_count',
            1,
            ['execution_id' => $execution->id, 'success' => $success],
            ['trigger_name' => $trigger->name, 'trigger_type' => $trigger->trigger_type]
        );

        // Record execution duration
        if ($duration > 0) {
            self::recordMetric(
                'event_trigger',
                (string)$trigger->id,
                'trigger_duration_seconds',
                $duration,
                ['execution_id' => $execution->id],
                ['trigger_name' => $trigger->name]
            );
        }

        // Record success rate
        self::recordMetric(
            'event_trigger',
            (string)$trigger->id,
            'trigger_success_rate',
            $success ? 1 : 0,
            ['execution_id' => $execution->id],
            ['trigger_name' => $trigger->name]
        );
    }

    /**
     * Record A/B test metrics
     */
    public static function recordAbTestMetrics(AbTest $test): void
    {
        $results = $test->results ?? [];
        $participantsA = $results['participants']['variant_a'] ?? 0;
        $participantsB = $results['participants']['variant_b'] ?? 0;

        // Record participant counts
        self::recordMetric(
            'ab_test',
            (string)$test->id,
            'participants_count',
            $participantsA + $participantsB,
            $results,
            ['test_name' => $test->name, 'test_type' => $test->test_type]
        );

        // Record conversion rates for each metric
        foreach ($results['metrics'] ?? [] as $metricName => $metricData) {
            $rateA = $metricData['variant_a']['conversion_rate'] ?? 0;
            $rateB = $metricData['variant_b']['conversion_rate'] ?? 0;

            self::recordMetric(
                'ab_test',
                (string)$test->id,
                "conversion_rate_{$metricName}_variant_a",
                $rateA,
                $metricData,
                ['test_name' => $test->name, 'metric' => $metricName, 'variant' => 'a']
            );

            self::recordMetric(
                'ab_test',
                (string)$test->id,
                "conversion_rate_{$metricName}_variant_b",
                $rateB,
                $metricData,
                ['test_name' => $test->name, 'metric' => $metricName, 'variant' => 'b']
            );
        }
    }

    /**
     * Record dynamic content usage metrics
     */
    public static function recordDynamicContentUsage(DynamicContentTemplate $template): void
    {
        $totalUsage = $template->getTotalUsage();

        // Record usage count
        self::recordMetric(
            'dynamic_content',
            (string)$template->id,
            'usage_count',
            1,
            ['template_id' => $template->id],
            ['template_name' => $template->name, 'content_type' => $template->content_type]
        );

        // Record total usage
        self::recordMetric(
            'dynamic_content',
            (string)$template->id,
            'total_usage',
            $totalUsage,
            ['template_id' => $template->id],
            ['template_name' => $template->name, 'content_type' => $template->content_type]
        );
    }

    /**
     * Get aggregated metrics for a workflow
     */
    public static function getWorkflowMetrics(
        string $workflowType,
        string $workflowId,
        string $metricName,
        int $days = 30,
        string $aggregationPeriod = 'daily'
    ): array {
        $startDate = now()->subDays($days);
        $endDate = now();

        $metrics = self::byWorkflow($workflowType, $workflowId)
            ->byMetric($metricName)
            ->byPeriod($aggregationPeriod)
            ->dateRange($startDate, $endDate)
            ->orderBy('metric_date')
            ->get();

        // Fill in missing dates with zero values
        $result = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateString = $currentDate->toDateString();
            $metric = $metrics->firstWhere('metric_date', $currentDate->toDateString());
            
            $result[$dateString] = $metric ? $metric->metric_value : 0;
            $currentDate->addDay();
        }

        return $result;
    }

    /**
     * Get performance summary for a workflow
     */
    public static function getWorkflowPerformanceSummary(
        string $workflowType,
        string $workflowId,
        int $days = 30
    ): array {
        $startDate = now()->subDays($days);

        $metrics = self::byWorkflow($workflowType, $workflowId)
            ->dateRange($startDate, now())
            ->get()
            ->groupBy('metric_name');

        $summary = [
            'total_executions' => 0,
            'success_rate' => 0,
            'average_duration' => 0,
            'error_rate' => 0,
            'trends' => [],
        ];

        // Calculate totals
        if ($metrics->has('executions_count')) {
            $summary['total_executions'] = $metrics['executions_count']->sum('metric_value');
        }

        if ($metrics->has('success_rate')) {
            $successMetrics = $metrics['success_rate'];
            $summary['success_rate'] = $successMetrics->count() > 0 
                ? $successMetrics->avg('metric_value') * 100 
                : 0;
        }

        if ($metrics->has('execution_duration_seconds')) {
            $durationMetrics = $metrics['execution_duration_seconds'];
            $summary['average_duration'] = $durationMetrics->count() > 0 
                ? $durationMetrics->avg('metric_value') 
                : 0;
        }

        $summary['error_rate'] = 100 - $summary['success_rate'];

        // Calculate trends (compare with previous period)
        $previousPeriodMetrics = self::byWorkflow($workflowType, $workflowId)
            ->dateRange($startDate->copy()->subDays($days), $startDate)
            ->get()
            ->groupBy('metric_name');

        foreach (['executions_count', 'success_rate', 'execution_duration_seconds'] as $metricName) {
            $currentAvg = $metrics->get($metricName, collect())->avg('metric_value') ?? 0;
            $previousAvg = $previousPeriodMetrics->get($metricName, collect())->avg('metric_value') ?? 0;

            $trend = 0;
            if ($previousAvg > 0) {
                $trend = (($currentAvg - $previousAvg) / $previousAvg) * 100;
            }

            $summary['trends'][$metricName] = [
                'current' => $currentAvg,
                'previous' => $previousAvg,
                'change_percent' => round($trend, 2),
                'direction' => $trend > 0 ? 'up' : ($trend < 0 ? 'down' : 'stable'),
            ];
        }

        return $summary;
    }

    /**
     * Get top performing workflows by metric
     */
    public static function getTopPerformingWorkflows(
        string $workflowType,
        string $metricName,
        int $limit = 10,
        int $days = 30
    ): array {
        $startDate = now()->subDays($days);

        return self::where('workflow_type', $workflowType)
            ->byMetric($metricName)
            ->dateRange($startDate, now())
            ->selectRaw('workflow_id, AVG(metric_value) as avg_metric_value')
            ->groupBy('workflow_id')
            ->orderByDesc('avg_metric_value')
            ->limit($limit)
            ->get()
            ->map(function ($item) use ($workflowType) {
                return [
                    'workflow_id' => $item->workflow_id,
                    'avg_metric_value' => round($item->avg_metric_value, 4),
                    'workflow_name' => $this->getWorkflowName($workflowType, $item->workflow_id),
                ];
            })
            ->toArray();
    }

    /**
     * Get workflow name by type and ID
     */
    private function getWorkflowName(string $workflowType, string $workflowId): string
    {
        return match ($workflowType) {
            'workflow_template' => WorkflowTemplate::find($workflowId)?->name ?? 'Unknown Workflow',
            'event_trigger' => EventTrigger::find($workflowId)?->name ?? 'Unknown Trigger',
            'ab_test' => AbTest::find($workflowId)?->name ?? 'Unknown Test',
            'dynamic_content' => DynamicContentTemplate::find($workflowId)?->name ?? 'Unknown Template',
            default => 'Unknown',
        };
    }

    /**
     * Generate workflow analytics report
     */
    public static function generateReport(array $filters = []): array
    {
        $days = $filters['days'] ?? 30;
        $workflowTypes = $filters['workflow_types'] ?? ['workflow_template', 'event_trigger', 'ab_test', 'dynamic_content'];

        $report = [
            'summary' => [],
            'performance_by_type' => [],
            'trends' => [],
            'top_performers' => [],
        ];

        foreach ($workflowTypes as $type) {
            $typeMetrics = self::where('workflow_type', $type)
                ->dateRange(now()->subDays($days), now())
                ->get();

            $report['performance_by_type'][$type] = [
                'total_executions' => $typeMetrics->where('metric_name', 'like', '%count')->sum('metric_value'),
                'unique_workflows' => $typeMetrics->pluck('workflow_id')->unique()->count(),
                'average_success_rate' => $typeMetrics->where('metric_name', 'like', '%success_rate')->avg('metric_value') * 100,
            ];

            $report['top_performers'][$type] = self::getTopPerformingWorkflows($type, 'executions_count', 5, $days);
        }

        return $report;
    }
}