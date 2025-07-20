<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventTrigger extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'trigger_type',
        'model_type',
        'trigger_conditions',
        'action_configuration',
        'delay_minutes',
        'is_active',
        'allow_retries',
        'max_retries',
        'retry_configuration',
        'rate_limiting',
        'created_by_user_id',
        'tenant_id',
    ];

    protected $casts = [
        'trigger_conditions' => 'array',
        'action_configuration' => 'array',
        'retry_configuration' => 'array',
        'rate_limiting' => 'array',
        'is_active' => 'boolean',
        'allow_retries' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function executions(): HasMany
    {
        return $this->hasMany(EventTriggerExecution::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByTriggerType($query, string $triggerType)
    {
        return $query->where('trigger_type', $triggerType);
    }

    public function scopeByModelType($query, string $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Check if trigger should execute for given entity and action
     */
    public function shouldTrigger($entity, string $action, array $changes = []): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Check rate limiting
        if (!$this->checkRateLimit()) {
            return false;
        }

        // Check model type match
        if ($this->model_type && get_class($entity) !== $this->model_type) {
            return false;
        }

        // Check trigger type match
        if (!$this->matchesTriggerType($action)) {
            return false;
        }

        // Evaluate trigger conditions
        return $this->evaluateConditions($entity, $action, $changes);
    }

    /**
     * Execute the trigger for the given entity
     */
    public function execute($entity, array $context = []): EventTriggerExecution
    {
        $execution = EventTriggerExecution::create([
            'event_trigger_id' => $this->id,
            'entity_type' => get_class($entity),
            'entity_id' => $entity->id,
            'context' => $context,
            'status' => 'pending',
            'scheduled_at' => $this->delay_minutes > 0 ? now()->addMinutes($this->delay_minutes) : now(),
        ]);

        if ($this->delay_minutes === 0) {
            $this->executeImmediately($execution, $entity, $context);
        }

        return $execution;
    }

    /**
     * Execute the trigger immediately
     */
    private function executeImmediately(EventTriggerExecution $execution, $entity, array $context): void
    {
        try {
            $execution->update(['status' => 'running', 'started_at' => now()]);

            foreach ($this->action_configuration['actions'] as $action) {
                $this->executeAction($action, $entity, $context);
            }

            $execution->update(['status' => 'completed', 'completed_at' => now()]);
        } catch (\Exception $e) {
            $execution->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            if ($this->allow_retries && $execution->retry_count < $this->max_retries) {
                $this->scheduleRetry($execution);
            }
        }
    }

    /**
     * Execute a specific action
     */
    private function executeAction(array $action, $entity, array $context): void
    {
        match ($action['type']) {
            'send_email' => $this->sendEmail($action, $entity, $context),
            'create_task' => $this->createTask($action, $entity, $context),
            'update_field' => $this->updateField($action, $entity, $context),
            'create_note' => $this->createNote($action, $entity, $context),
            'trigger_workflow' => $this->triggerWorkflow($action, $entity, $context),
            'webhook' => $this->callWebhook($action, $entity, $context),
            'assign_to_user' => $this->assignToUser($action, $entity, $context),
            'add_to_cadence' => $this->addToCadence($action, $entity, $context),
            default => throw new \InvalidArgumentException("Unknown action type: {$action['type']}"),
        };
    }

    /**
     * Send email action
     */
    private function sendEmail(array $action, $entity, array $context): void
    {
        $templateId = $action['template_id'] ?? null;
        $recipient = $action['recipient'] ?? 'entity_email';

        $emailAddress = match ($recipient) {
            'entity_email' => $entity->email,
            'assigned_user' => $entity->assignedUser?->email,
            'custom' => $action['custom_email'],
            default => $entity->email,
        };

        if (!$emailAddress) {
            throw new \Exception('No email address available for recipient');
        }

        // Create email using dynamic content if template specified
        if ($templateId) {
            $template = DynamicContentTemplate::find($templateId);
            if ($template) {
                $subject = $template->generateContent($entity, array_merge($context, ['type' => 'subject']));
                $body = $template->generateContent($entity, array_merge($context, ['type' => 'body']));
            }
        }

        // Queue email sending
        \Mail::to($emailAddress)->queue(new \App\Mail\TriggerBasedEmail(
            $subject ?? $action['subject'] ?? 'Automated Email',
            $body ?? $action['body'] ?? 'This is an automated email.',
            $entity
        ));
    }

    /**
     * Create task action
     */
    private function createTask(array $action, $entity, array $context): void
    {
        Task::create([
            'title' => $action['title'] ?? 'Automated Task',
            'description' => $action['description'] ?? '',
            'taskable_type' => get_class($entity),
            'taskable_id' => $entity->id,
            'assigned_to' => $action['assigned_to'] ?? null,
            'due_date' => isset($action['due_days']) ? now()->addDays($action['due_days']) : null,
            'priority' => $action['priority'] ?? 'medium',
            'status' => 'pending',
        ]);
    }

    /**
     * Update field action
     */
    private function updateField(array $action, $entity, array $context): void
    {
        $field = $action['field'];
        $value = $action['value'];

        // Support dynamic values
        if (str_contains($value, '{{')) {
            $template = new DynamicContentTemplate();
            $template->base_template = $value;
            $template->variable_mappings = $action['variable_mappings'] ?? [];
            $value = $template->generateContent($entity, $context);
        }

        $entity->update([$field => $value]);
    }

    /**
     * Create note action
     */
    private function createNote(array $action, $entity, array $context): void
    {
        Note::create([
            'content' => $action['content'] ?? 'Automated note',
            'noteable_type' => get_class($entity),
            'noteable_id' => $entity->id,
            'created_by' => $action['created_by'] ?? null,
            'is_private' => $action['is_private'] ?? false,
        ]);
    }

    /**
     * Trigger workflow action
     */
    private function triggerWorkflow(array $action, $entity, array $context): void
    {
        $workflowId = $action['workflow_id'];
        $workflow = WorkflowTemplate::find($workflowId);

        if ($workflow && $workflow->canTrigger($entity)) {
            $workflow->execute($entity, $context);
        }
    }

    /**
     * Call webhook action
     */
    private function callWebhook(array $action, $entity, array $context): void
    {
        $url = $action['url'];
        $method = $action['method'] ?? 'POST';
        $headers = $action['headers'] ?? [];
        $payload = array_merge($context, [
            'entity_type' => get_class($entity),
            'entity_id' => $entity->id,
            'entity_data' => $entity->toArray(),
        ]);

        \Http::withHeaders($headers)->$method($url, $payload);
    }

    /**
     * Assign to user action
     */
    private function assignToUser(array $action, $entity, array $context): void
    {
        $userId = $action['user_id'];
        
        if (method_exists($entity, 'assigned_to')) {
            $entity->update(['assigned_to' => $userId]);
        }
    }

    /**
     * Add to cadence action
     */
    private function addToCadence(array $action, $entity, array $context): void
    {
        $cadenceId = $action['cadence_id'];
        
        if ($entity instanceof Contact) {
            CadenceEnrollment::create([
                'cadence_sequence_id' => $cadenceId,
                'contact_id' => $entity->id,
                'status' => 'active',
                'current_step' => 1,
            ]);
        }
    }

    /**
     * Check if action matches trigger type
     */
    private function matchesTriggerType(string $action): bool
    {
        return match ($this->trigger_type) {
            'model_created' => $action === 'created',
            'model_updated' => $action === 'updated',
            'model_deleted' => $action === 'deleted',
            'field_changed' => $action === 'updated',
            'time_based' => $action === 'scheduled',
            'external_api' => $action === 'api_call',
            'webhook' => $action === 'webhook',
            'user_action' => $action === 'user_triggered',
            default => false,
        };
    }

    /**
     * Evaluate trigger conditions
     */
    private function evaluateConditions($entity, string $action, array $changes = []): bool
    {
        if (empty($this->trigger_conditions)) {
            return true;
        }

        foreach ($this->trigger_conditions as $condition) {
            if (!$this->evaluateCondition($condition, $entity, $action, $changes)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Evaluate a single condition
     */
    private function evaluateCondition(array $condition, $entity, string $action, array $changes): bool
    {
        $field = $condition['field'];
        $operator = $condition['operator'];
        $value = $condition['value'];

        // For field changes, check the changes array
        if ($this->trigger_type === 'field_changed' && isset($changes[$field])) {
            $entityValue = $changes[$field]['new'] ?? data_get($entity, $field);
        } else {
            $entityValue = data_get($entity, $field);
        }

        return match ($operator) {
            'equals' => $entityValue == $value,
            'not_equals' => $entityValue != $value,
            'greater_than' => $entityValue > $value,
            'less_than' => $entityValue < $value,
            'greater_than_or_equal' => $entityValue >= $value,
            'less_than_or_equal' => $entityValue <= $value,
            'contains' => str_contains((string)$entityValue, $value),
            'not_contains' => !str_contains((string)$entityValue, $value),
            'in' => in_array($entityValue, (array)$value),
            'not_in' => !in_array($entityValue, (array)$value),
            'is_null' => is_null($entityValue),
            'is_not_null' => !is_null($entityValue),
            'changed' => isset($changes[$field]),
            'changed_from' => isset($changes[$field]) && $changes[$field]['old'] == $value,
            'changed_to' => isset($changes[$field]) && $changes[$field]['new'] == $value,
            default => false,
        };
    }

    /**
     * Check rate limiting
     */
    private function checkRateLimit(): bool
    {
        if (!$this->rate_limiting) {
            return true;
        }

        $limit = $this->rate_limiting['limit'] ?? 10;
        $period = $this->rate_limiting['period'] ?? 'hour'; // minute, hour, day

        $since = match ($period) {
            'minute' => now()->subMinute(),
            'hour' => now()->subHour(),
            'day' => now()->subDay(),
            default => now()->subHour(),
        };

        $recentExecutions = $this->executions()
            ->where('created_at', '>=', $since)
            ->count();

        return $recentExecutions < $limit;
    }

    /**
     * Schedule retry for failed execution
     */
    private function scheduleRetry(EventTriggerExecution $execution): void
    {
        $retryConfig = $this->retry_configuration ?? [];
        $delay = $retryConfig['delay_minutes'] ?? 15;
        $backoff = $retryConfig['backoff_multiplier'] ?? 2;

        $actualDelay = $delay * pow($backoff, $execution->retry_count);

        EventTriggerExecution::create([
            'event_trigger_id' => $this->id,
            'entity_type' => $execution->entity_type,
            'entity_id' => $execution->entity_id,
            'context' => $execution->context,
            'status' => 'pending',
            'scheduled_at' => now()->addMinutes($actualDelay),
            'retry_count' => $execution->retry_count + 1,
            'parent_execution_id' => $execution->id,
        ]);
    }
}