<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkflowStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_template_id',
        'name',
        'type',
        'config',
        'order',
        'is_active',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    public function workflowTemplate(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplate::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Execute this workflow step
     */
    public function execute(WorkflowInstance $instance, array $context = []): array
    {
        // Step execution logic based on type
        switch ($this->type) {
            case 'action':
                return $this->executeAction($instance, $context);
            case 'condition':
                return $this->executeCondition($instance, $context);
            case 'delay':
                return $this->executeDelay($instance, $context);
            default:
                return ['success' => false, 'message' => 'Unknown step type'];
        }
    }

    /**
     * Execute action step
     */
    protected function executeAction(WorkflowInstance $instance, array $context): array
    {
        $actionType = $this->config['action_type'] ?? null;
        
        switch ($actionType) {
            case 'send_email':
                return $this->sendEmail($instance, $context);
            case 'create_task':
                return $this->createTask($instance, $context);
            case 'update_field':
                return $this->updateField($instance, $context);
            default:
                return ['success' => false, 'message' => 'Unknown action type'];
        }
    }

    /**
     * Execute condition step
     */
    protected function executeCondition(WorkflowInstance $instance, array $context): array
    {
        // Implement condition logic
        return ['success' => true, 'condition_met' => true];
    }

    /**
     * Execute delay step
     */
    protected function executeDelay(WorkflowInstance $instance, array $context): array
    {
        // Implement delay logic (could schedule job for later execution)
        return ['success' => true, 'delayed' => true];
    }

    /**
     * Send email action
     */
    protected function sendEmail(WorkflowInstance $instance, array $context): array
    {
        // Implement email sending logic
        return ['success' => true, 'message' => 'Email sent'];
    }

    /**
     * Create task action
     */
    protected function createTask(WorkflowInstance $instance, array $context): array
    {
        // Implement task creation logic
        return ['success' => true, 'message' => 'Task created'];
    }

    /**
     * Update field action
     */
    protected function updateField(WorkflowInstance $instance, array $context): array
    {
        // Implement field update logic
        return ['success' => true, 'message' => 'Field updated'];
    }
}