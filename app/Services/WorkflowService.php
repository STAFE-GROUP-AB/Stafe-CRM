<?php

namespace App\Services;

use App\Models\WorkflowTemplate;
use App\Models\WorkflowInstance;

class WorkflowService
{
    /**
     * Execute workflow for a given trigger and entity
     */
    public function executeWorkflows(string $triggerType, $entity = null, array $context = []): array
    {
        $results = [];
        
        $workflows = WorkflowTemplate::active()
            ->byTriggerType($triggerType)
            ->get();

        foreach ($workflows as $workflow) {
            if ($workflow->canTrigger($entity)) {
                $instance = $workflow->execute($entity, $context);
                $results[] = $instance;
                
                // Queue the workflow execution
                $this->queueWorkflowExecution($instance);
            }
        }

        return $results;
    }

    /**
     * Queue workflow execution
     */
    protected function queueWorkflowExecution(WorkflowInstance $instance): void
    {
        // In a real implementation, this would dispatch a job
        // For now, we'll execute synchronously
        $instance->execute();
    }

    /**
     * Create workflow template
     */
    public function createWorkflowTemplate(array $data): WorkflowTemplate
    {
        $workflow = WorkflowTemplate::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'trigger_type' => $data['trigger_type'],
            'trigger_config' => $data['trigger_config'] ?? [],
            'is_active' => $data['is_active'] ?? true,
            'created_by' => auth()->id(),
        ]);

        if (isset($data['steps'])) {
            $this->createWorkflowSteps($workflow, $data['steps']);
        }

        return $workflow;
    }

    /**
     * Create workflow steps
     */
    protected function createWorkflowSteps(WorkflowTemplate $workflow, array $steps): void
    {
        foreach ($steps as $index => $stepData) {
            $workflow->steps()->create([
                'name' => $stepData['name'],
                'type' => $stepData['type'],
                'config' => $stepData['config'] ?? [],
                'order' => $stepData['order'] ?? $index,
                'is_active' => $stepData['is_active'] ?? true,
            ]);
        }
    }

    /**
     * Get workflow execution statistics
     */
    public function getWorkflowStats(WorkflowTemplate $workflow): array
    {
        $instances = $workflow->instances();
        
        return [
            'total_executions' => $instances->count(),
            'successful_executions' => $instances->completed()->count(),
            'failed_executions' => $instances->failed()->count(),
            'pending_executions' => $instances->pending()->count(),
            'running_executions' => $instances->running()->count(),
            'success_rate' => $this->calculateSuccessRate($workflow),
            'average_execution_time' => $this->calculateAverageExecutionTime($workflow),
        ];
    }

    /**
     * Calculate workflow success rate
     */
    protected function calculateSuccessRate(WorkflowTemplate $workflow): float
    {
        $total = $workflow->instances()->count();
        if ($total === 0) {
            return 0;
        }

        $successful = $workflow->instances()->completed()->count();
        return round(($successful / $total) * 100, 2);
    }

    /**
     * Calculate average execution time
     */
    protected function calculateAverageExecutionTime(WorkflowTemplate $workflow): ?float
    {
        $completedInstances = $workflow->instances()
            ->completed()
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at')
            ->get();

        if ($completedInstances->isEmpty()) {
            return null;
        }

        $totalTime = 0;
        foreach ($completedInstances as $instance) {
            $totalTime += $instance->started_at->diffInSeconds($instance->completed_at);
        }

        return round($totalTime / $completedInstances->count(), 2);
    }
}