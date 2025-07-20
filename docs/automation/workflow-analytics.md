# Workflow Analytics

Workflow Analytics provides comprehensive performance monitoring and optimization insights for all automation processes in Stafe CRM. Track execution metrics, identify bottlenecks, measure success rates, and optimize your automation workflows with detailed analytics and intelligent recommendations.

## Overview

Workflow Analytics enables you to:

- **Monitor performance** across all workflow types and automation processes
- **Track key metrics** including execution times, success rates, and conversion metrics
- **Identify optimization opportunities** through detailed performance analysis
- **Generate comprehensive reports** with trends, comparisons, and insights
- **Make data-driven improvements** to enhance automation effectiveness

## Key Features

### 📊 Comprehensive Metrics
- Execution counts and success rates
- Performance timing and duration analysis
- Conversion tracking and funnel analysis
- Error monitoring and failure pattern detection

### 🎯 Multi-dimensional Analysis
- Workflow type comparisons
- User and team performance breakdowns
- Time-based trend analysis
- Segmentation by entity types and attributes

### 🔍 Performance Optimization
- Bottleneck identification and resolution
- Success rate optimization recommendations
- Resource utilization analysis
- Automated performance alerts

### 📈 Advanced Reporting
- Real-time dashboards and visualizations
- Scheduled reports and notifications
- Custom metric definitions and tracking
- Export capabilities for external analysis

## Supported Workflow Types

### 1. Workflow Templates
- Template execution analytics
- Step performance analysis
- Success/failure rate tracking
- Duration and efficiency metrics

### 2. Event Triggers
- Trigger execution monitoring
- Action performance tracking
- Rate limiting effectiveness
- Error pattern analysis

### 3. A/B Tests
- Test performance metrics
- Participant engagement tracking
- Conversion rate analysis
- Statistical significance monitoring

### 4. Dynamic Content
- Usage frequency and patterns
- Content effectiveness metrics
- Personalization impact analysis
- Template performance comparison

## Core Metrics

### Execution Metrics

```php
use App\Models\WorkflowAnalytics;

// Record workflow execution
WorkflowAnalytics::recordMetric(
    'workflow_template',      // workflow type
    '123',                   // workflow ID
    'executions_count',      // metric name
    1,                       // metric value
    [                        // metadata
        'instance_id' => 456,
        'success' => true
    ],
    [                        // dimensions
        'template_name' => 'Lead Qualification',
        'status' => 'completed'
    ]
);

// Record execution duration
WorkflowAnalytics::recordMetric(
    'workflow_template',
    '123',
    'execution_duration_seconds',
    45.2,
    ['instance_id' => 456],
    ['template_name' => 'Lead Qualification']
);
```

### Success Rate Tracking

```php
// Record success/failure
WorkflowAnalytics::recordMetric(
    'event_trigger',
    '789',
    'success_rate',
    1, // 1 for success, 0 for failure
    ['execution_id' => 101],
    ['trigger_name' => 'Deal Stage Change']
);

// Automated recording for workflow instances
WorkflowAnalytics::recordWorkflowExecution($workflowInstance);
```

### Conversion Metrics

```php
// Track conversion events
WorkflowAnalytics::recordMetric(
    'ab_test',
    '456',
    'conversion_rate_email_open_variant_a',
    0.25, // 25% conversion rate
    ['test_results' => $testResults],
    [
        'test_name' => 'Email Subject Test',
        'metric' => 'email_open',
        'variant' => 'a'
    ]
);
```

## Analytics Retrieval

### Time-Series Data

Get metric trends over time:

```php
// Get daily execution counts for last 30 days
$executionTrends = WorkflowAnalytics::getWorkflowMetrics(
    'workflow_template',     // workflow type
    '123',                  // workflow ID
    'executions_count',     // metric name
    30,                     // days
    'daily'                 // aggregation period
);

/*
Returns:
[
    '2024-01-01' => 15,
    '2024-01-02' => 18,
    '2024-01-03' => 12,
    ...
]
*/
```

### Performance Summary

Get comprehensive performance overview:

```php
$performanceSummary = WorkflowAnalytics::getWorkflowPerformanceSummary(
    'workflow_template',
    '123',
    30 // days
);

/*
Returns:
[
    'total_executions' => 450,
    'success_rate' => 92.5,
    'average_duration' => 38.7,
    'error_rate' => 7.5,
    'trends' => [
        'executions_count' => [
            'current' => 15.2,
            'previous' => 12.8,
            'change_percent' => 18.75,
            'direction' => 'up'
        ],
        'success_rate' => [
            'current' => 0.925,
            'previous' => 0.885,
            'change_percent' => 4.52,
            'direction' => 'up'
        ]
    ]
]
*/
```

### Top Performers

Identify best-performing workflows:

```php
$topPerformers = WorkflowAnalytics::getTopPerformingWorkflows(
    'workflow_template',    // workflow type
    'executions_count',     // metric to rank by
    10,                     // limit
    30                      // days
);

/*
Returns:
[
    [
        'workflow_id' => '123',
        'avg_metric_value' => 15.2,
        'workflow_name' => 'Lead Qualification'
    ],
    [
        'workflow_id' => '456', 
        'avg_metric_value' => 12.8,
        'workflow_name' => 'Follow-up Sequence'
    ]
]
*/
```

## Automated Recording

### Workflow Instance Tracking

Automatically track workflow executions:

```php
use App\Models\WorkflowInstance;

// In WorkflowInstance model observer
class WorkflowInstanceObserver
{
    public function updated(WorkflowInstance $instance)
    {
        if ($instance->isCompleted() || $instance->hasFailed()) {
            WorkflowAnalytics::recordWorkflowExecution($instance);
        }
    }
}

// Register observer in AppServiceProvider
WorkflowInstance::observe(WorkflowInstanceObserver::class);
```

### Event Trigger Monitoring

Track event trigger performance:

```php
use App\Models\EventTriggerExecution;

class EventTriggerObserver
{
    public function updated(EventTriggerExecution $execution)
    {
        if ($execution->isCompleted() || $execution->hasFailed()) {
            WorkflowAnalytics::recordEventTriggerExecution($execution);
        }
    }
}
```

### A/B Test Analytics

Monitor A/B test performance:

```php
use App\Models\AbTest;

class AbTestObserver
{
    public function updated(AbTest $test)
    {
        if ($test->status === 'completed') {
            WorkflowAnalytics::recordAbTestMetrics($test);
        }
    }
}
```

## Custom Metrics

### Define Custom Metrics

Create application-specific metrics:

```php
class CustomWorkflowMetrics
{
    public static function recordLeadQualificationMetrics($contact, $workflowInstance)
    {
        // Lead qualification success
        if ($contact->status === 'qualified') {
            WorkflowAnalytics::recordMetric(
                'custom_workflow',
                'lead_qualification',
                'qualification_success',
                1,
                ['contact_id' => $contact->id],
                ['lead_source' => $contact->source]
            );
        }

        // Time to qualification
        $timeToQualification = $contact->qualified_at?->diffInHours($contact->created_at);
        if ($timeToQualification) {
            WorkflowAnalytics::recordMetric(
                'custom_workflow',
                'lead_qualification',
                'time_to_qualification_hours',
                $timeToQualification,
                ['contact_id' => $contact->id],
                ['lead_source' => $contact->source]
            );
        }
    }

    public static function recordRevenueMetrics($deal, $workflowType, $workflowId)
    {
        if ($deal->stage === 'won') {
            // Record revenue attribution
            WorkflowAnalytics::recordMetric(
                $workflowType,
                $workflowId,
                'revenue_attributed',
                $deal->value,
                ['deal_id' => $deal->id],
                [
                    'deal_size_category' => $deal->getSizeCategory(),
                    'industry' => $deal->company->industry
                ]
            );

            // Record sales cycle length
            $salesCycleLength = $deal->closed_at->diffInDays($deal->created_at);
            WorkflowAnalytics::recordMetric(
                $workflowType,
                $workflowId,
                'sales_cycle_length_days',
                $salesCycleLength,
                ['deal_id' => $deal->id],
                ['deal_value_range' => $deal->getValueRange()]
            );
        }
    }
}
```

### Business KPI Tracking

Track high-level business metrics:

```php
class BusinessKPITracker
{
    public static function updateDailyKPIs()
    {
        $today = now()->toDateString();
        
        // Lead conversion rate
        $totalLeads = Contact::whereDate('created_at', $today)->count();
        $qualifiedLeads = Contact::whereDate('qualified_at', $today)->count();
        $conversionRate = $totalLeads > 0 ? $qualifiedLeads / $totalLeads : 0;
        
        WorkflowAnalytics::recordMetric(
            'business_kpi',
            'daily_metrics',
            'lead_conversion_rate',
            $conversionRate,
            ['total_leads' => $totalLeads, 'qualified_leads' => $qualifiedLeads],
            ['date' => $today]
        );

        // Deal velocity
        $avgDealVelocity = Deal::where('stage', 'won')
            ->whereDate('closed_at', $today)
            ->avg('sales_cycle_length');
            
        if ($avgDealVelocity) {
            WorkflowAnalytics::recordMetric(
                'business_kpi',
                'daily_metrics',
                'average_deal_velocity_days',
                $avgDealVelocity,
                ['deals_closed_today' => Deal::whereDate('closed_at', $today)->count()],
                ['date' => $today]
            );
        }
    }
}
```

## Performance Analysis

### Bottleneck Detection

Identify performance bottlenecks:

```php
class PerformanceAnalyzer
{
    public static function identifySlowWorkflows($threshold = 60)
    {
        return WorkflowAnalytics::where('metric_name', 'execution_duration_seconds')
            ->where('metric_date', '>=', now()->subDays(7))
            ->selectRaw('workflow_type, workflow_id, AVG(metric_value) as avg_duration')
            ->groupBy(['workflow_type', 'workflow_id'])
            ->having('avg_duration', '>', $threshold)
            ->orderByDesc('avg_duration')
            ->get()
            ->map(function ($item) {
                return [
                    'workflow_type' => $item->workflow_type,
                    'workflow_id' => $item->workflow_id,
                    'avg_duration' => round($item->avg_duration, 2),
                    'performance_category' => $item->avg_duration > 120 ? 'critical' : 'warning'
                ];
            });
    }

    public static function identifyErrorPatterns()
    {
        return WorkflowAnalytics::where('metric_name', 'success_rate')
            ->where('metric_value', 0) // failures
            ->where('metric_date', '>=', now()->subDays(7))
            ->selectRaw('workflow_type, workflow_id, COUNT(*) as failure_count')
            ->groupBy(['workflow_type', 'workflow_id'])
            ->having('failure_count', '>', 5)
            ->orderByDesc('failure_count')
            ->get();
    }

    public static function analyzeResourceUtilization()
    {
        $concurrentExecutions = WorkflowAnalytics::where('metric_name', 'executions_count')
            ->where('metric_date', now()->toDateString())
            ->selectRaw('HOUR(created_at) as hour, SUM(metric_value) as total_executions')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return [
            'peak_hours' => $concurrentExecutions->where('total_executions', '>', 50)->pluck('hour'),
            'low_utilization_hours' => $concurrentExecutions->where('total_executions', '<', 10)->pluck('hour'),
            'hourly_distribution' => $concurrentExecutions->toArray()
        ];
    }
}
```

### Success Rate Optimization

Analyze and improve success rates:

```php
class SuccessRateOptimizer
{
    public static function analyzeFailurePatterns($workflowType, $workflowId)
    {
        $failures = WorkflowAnalytics::where('workflow_type', $workflowType)
            ->where('workflow_id', $workflowId)
            ->where('metric_name', 'success_rate')
            ->where('metric_value', 0)
            ->where('metric_date', '>=', now()->subDays(30))
            ->get();

        // Group failures by dimensions
        $failuresByDimension = $failures->groupBy(function ($item) {
            return $item->dimensions['error_type'] ?? 'unknown';
        });

        // Calculate failure rates by dimension
        $analysis = [];
        foreach ($failuresByDimension as $errorType => $errorGroup) {
            $analysis[$errorType] = [
                'count' => $errorGroup->count(),
                'percentage' => ($errorGroup->count() / $failures->count()) * 100,
                'recent_trend' => $this->calculateTrend($errorGroup)
            ];
        }

        return [
            'total_failures' => $failures->count(),
            'failure_breakdown' => $analysis,
            'recommendations' => $this->generateRecommendations($analysis)
        ];
    }

    private static function generateRecommendations($analysis)
    {
        $recommendations = [];

        foreach ($analysis as $errorType => $data) {
            if ($data['percentage'] > 20) {
                $recommendations[] = [
                    'priority' => 'high',
                    'error_type' => $errorType,
                    'suggestion' => "Focus on reducing {$errorType} errors - they account for {$data['percentage']}% of failures"
                ];
            }
        }

        return $recommendations;
    }
}
```

## Reporting & Dashboards

### Executive Dashboard

Create high-level performance dashboard:

```php
class ExecutiveDashboard
{
    public static function generateDashboardData($timeframe = 30)
    {
        return [
            'summary' => [
                'total_workflows_executed' => self::getTotalExecutions($timeframe),
                'overall_success_rate' => self::getOverallSuccessRate($timeframe),
                'average_execution_time' => self::getAverageExecutionTime($timeframe),
                'automation_savings_hours' => self::calculateAutomationSavings($timeframe)
            ],
            'trends' => [
                'execution_volume' => self::getExecutionTrends($timeframe),
                'success_rate_trend' => self::getSuccessRateTrends($timeframe),
                'performance_trend' => self::getPerformanceTrends($timeframe)
            ],
            'top_performers' => [
                'most_used_workflows' => self::getMostUsedWorkflows($timeframe),
                'highest_success_rate' => self::getHighestSuccessRateWorkflows($timeframe),
                'fastest_workflows' => self::getFastestWorkflows($timeframe)
            ],
            'issues' => [
                'slow_workflows' => PerformanceAnalyzer::identifySlowWorkflows(),
                'error_patterns' => PerformanceAnalyzer::identifyErrorPatterns(),
                'optimization_opportunities' => self::getOptimizationOpportunities()
            ]
        ];
    }

    private static function getTotalExecutions($days)
    {
        return WorkflowAnalytics::where('metric_name', 'executions_count')
            ->where('metric_date', '>=', now()->subDays($days))
            ->sum('metric_value');
    }

    private static function getOverallSuccessRate($days)
    {
        $successMetrics = WorkflowAnalytics::where('metric_name', 'success_rate')
            ->where('metric_date', '>=', now()->subDays($days))
            ->get();

        return $successMetrics->count() > 0 ? $successMetrics->avg('metric_value') * 100 : 0;
    }
}
```

### Detailed Workflow Report

Generate comprehensive workflow analysis:

```php
class WorkflowReporter
{
    public static function generateWorkflowReport($workflowType, $workflowId, $timeframe = 30)
    {
        $startDate = now()->subDays($timeframe);
        
        return [
            'workflow_info' => self::getWorkflowInfo($workflowType, $workflowId),
            'performance_summary' => WorkflowAnalytics::getWorkflowPerformanceSummary(
                $workflowType, $workflowId, $timeframe
            ),
            'execution_details' => [
                'daily_executions' => self::getDailyExecutions($workflowType, $workflowId, $startDate),
                'success_rate_by_day' => self::getSuccessRateByDay($workflowType, $workflowId, $startDate),
                'duration_analysis' => self::getDurationAnalysis($workflowType, $workflowId, $startDate)
            ],
            'comparative_analysis' => [
                'vs_similar_workflows' => self::compareWithSimilarWorkflows($workflowType, $workflowId),
                'historical_comparison' => self::getHistoricalComparison($workflowType, $workflowId)
            ],
            'optimization_insights' => [
                'bottlenecks' => self::identifyBottlenecks($workflowType, $workflowId),
                'recommendations' => self::generateOptimizationRecommendations($workflowType, $workflowId)
            ]
        ];
    }

    private static function generateOptimizationRecommendations($workflowType, $workflowId)
    {
        $performance = WorkflowAnalytics::getWorkflowPerformanceSummary($workflowType, $workflowId, 30);
        $recommendations = [];

        // Success rate recommendations
        if ($performance['success_rate'] < 85) {
            $recommendations[] = [
                'category' => 'reliability',
                'priority' => 'high',
                'title' => 'Improve Success Rate',
                'description' => "Current success rate of {$performance['success_rate']}% is below optimal threshold",
                'actions' => [
                    'Review failure patterns and error logs',
                    'Add retry logic for transient failures',
                    'Improve input validation and error handling'
                ]
            ];
        }

        // Performance recommendations
        if ($performance['average_duration'] > 60) {
            $recommendations[] = [
                'category' => 'performance',
                'priority' => 'medium',
                'title' => 'Optimize Execution Time',
                'description' => "Average execution time of {$performance['average_duration']} seconds could be improved",
                'actions' => [
                    'Identify slow operations within the workflow',
                    'Consider parallel execution where possible',
                    'Optimize database queries and external API calls'
                ]
            ];
        }

        return $recommendations;
    }
}
```

## Real-time Monitoring

### Performance Alerts

Set up automated performance alerts:

```php
class PerformanceMonitor
{
    public static function checkPerformanceThresholds()
    {
        $alerts = [];
        
        // Check for high failure rates
        $highFailureWorkflows = WorkflowAnalytics::where('metric_name', 'success_rate')
            ->where('metric_date', now()->toDateString())
            ->selectRaw('workflow_type, workflow_id, AVG(metric_value) as avg_success_rate')
            ->groupBy(['workflow_type', 'workflow_id'])
            ->having('avg_success_rate', '<', 0.8)
            ->get();

        foreach ($highFailureWorkflows as $workflow) {
            $alerts[] = [
                'type' => 'high_failure_rate',
                'severity' => 'critical',
                'workflow_type' => $workflow->workflow_type,
                'workflow_id' => $workflow->workflow_id,
                'success_rate' => $workflow->avg_success_rate * 100,
                'message' => "Workflow {$workflow->workflow_id} has success rate of {$workflow->avg_success_rate * 100}%"
            ];
        }

        // Check for slow execution times
        $slowWorkflows = WorkflowAnalytics::where('metric_name', 'execution_duration_seconds')
            ->where('metric_date', '>=', now()->subHours(1))
            ->where('metric_value', '>', 120)
            ->get();

        foreach ($slowWorkflows as $execution) {
            $alerts[] = [
                'type' => 'slow_execution',
                'severity' => 'warning',
                'workflow_type' => $execution->workflow_type,
                'workflow_id' => $execution->workflow_id,
                'duration' => $execution->metric_value,
                'message' => "Workflow execution took {$execution->metric_value} seconds"
            ];
        }

        // Send alerts if any found
        if (!empty($alerts)) {
            self::sendAlerts($alerts);
        }

        return $alerts;
    }

    private static function sendAlerts($alerts)
    {
        foreach ($alerts as $alert) {
            // Send to appropriate channels (Slack, email, etc.)
            if ($alert['severity'] === 'critical') {
                Notification::send(
                    User::role('admin')->get(),
                    new CriticalPerformanceAlert($alert)
                );
            }
        }
    }
}
```

### Live Dashboard

Create real-time performance dashboard:

```php
class LiveDashboard
{
    public static function getLiveMetrics()
    {
        $now = now();
        $hourAgo = $now->copy()->subHour();
        
        return [
            'current_executions' => self::getCurrentExecutions(),
            'last_hour_summary' => [
                'executions' => self::getExecutionsInPeriod($hourAgo, $now),
                'success_rate' => self::getSuccessRateInPeriod($hourAgo, $now),
                'avg_duration' => self::getAverageDurationInPeriod($hourAgo, $now)
            ],
            'active_workflows' => self::getActiveWorkflows(),
            'recent_failures' => self::getRecentFailures(10),
            'system_health' => [
                'queue_depth' => Queue::size(),
                'memory_usage' => memory_get_usage(true),
                'cpu_load' => sys_getloadavg()[0]
            ]
        ];
    }

    private static function getCurrentExecutions()
    {
        return WorkflowInstance::where('status', 'running')->count() +
               EventTriggerExecution::where('status', 'running')->count();
    }

    private static function getActiveWorkflows()
    {
        return collect([
            'workflow_templates' => WorkflowTemplate::active()->count(),
            'event_triggers' => EventTrigger::active()->count(),
            'ab_tests' => AbTest::active()->count(),
            'dynamic_content_templates' => DynamicContentTemplate::active()->count()
        ]);
    }
}
```

## API Reference

### Record Metric
```http
POST /api/workflow-analytics/metrics
Content-Type: application/json

{
    "workflow_type": "workflow_template",
    "workflow_id": "123",
    "metric_name": "executions_count",
    "metric_value": 1,
    "metric_metadata": {...},
    "dimensions": {...}
}
```

### Get Workflow Metrics
```http
GET /api/workflow-analytics/workflows/{type}/{id}/metrics?metric=executions_count&days=30&period=daily
```

### Get Performance Summary
```http
GET /api/workflow-analytics/workflows/{type}/{id}/performance?days=30
```

### Generate Report
```http
POST /api/workflow-analytics/reports
Content-Type: application/json

{
    "report_type": "workflow_performance",
    "filters": {
        "workflow_types": ["workflow_template", "event_trigger"],
        "date_range": {
            "start": "2024-01-01",
            "end": "2024-01-31"
        }
    }
}
```

## Best Practices

### 1. Metric Design
- **Consistent naming**: Use standardized metric names across workflow types
- **Meaningful dimensions**: Include relevant context for analysis
- **Appropriate granularity**: Balance detail with storage efficiency
- **Business alignment**: Track metrics that align with business objectives

### 2. Performance Monitoring
- **Regular review**: Establish regular performance review cycles
- **Threshold setting**: Define appropriate performance thresholds
- **Alert configuration**: Set up meaningful alerts for critical issues
- **Continuous improvement**: Use analytics to drive ongoing optimization

### 3. Data Quality
- **Validation**: Validate metric data before recording
- **Consistency**: Ensure consistent recording across all workflow types
- **Completeness**: Record all relevant metrics for comprehensive analysis
- **Accuracy**: Implement checks to ensure data accuracy

### 4. Analysis & Reporting
- **Context awareness**: Always analyze metrics in business context
- **Trend analysis**: Focus on trends rather than single data points
- **Comparative analysis**: Compare performance across workflows and time periods
- **Actionable insights**: Ensure analytics lead to actionable recommendations

## Troubleshooting

### Common Issues

**Issue**: Missing or incomplete metrics
- **Solution**: Check metric recording implementation, verify observers are registered

**Issue**: Performance degradation in analytics queries
- **Solution**: Add appropriate database indexes, consider data archiving strategies

**Issue**: Inconsistent results across reports
- **Solution**: Verify date range calculations, check for timezone issues

**Issue**: High storage usage
- **Solution**: Implement data retention policies, archive old metrics

### Debug Tools

```php
// Enable detailed metric logging
WorkflowAnalytics::enableDebugMode();

// Validate metric recording
$validator = new MetricValidator();
$validator->validateWorkflowMetrics($workflowType, $workflowId);

// Check data consistency
$consistencyChecker = new DataConsistencyChecker();
$issues = $consistencyChecker->checkWorkflowAnalytics();
```

---

Workflow Analytics provides the intelligence layer that transforms your automation from simple task execution into a continuously improving, data-driven system. By systematically measuring, analyzing, and optimizing your workflows, you can achieve higher efficiency, better results, and more reliable automation processes.