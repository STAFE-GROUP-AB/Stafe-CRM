<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CadenceStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'cadence_sequence_id',
        'step_number',
        'name',
        'description',
        'action_type',
        'action_config',
        'delay_days',
        'delay_hours',
        'conditions',
        'personalization_rules',
        'is_active',
        'allows_manual_skip',
    ];

    protected $casts = [
        'action_config' => 'array',
        'conditions' => 'array',
        'personalization_rules' => 'array',
        'is_active' => 'boolean',
        'allows_manual_skip' => 'boolean',
    ];

    public function cadenceSequence(): BelongsTo
    {
        return $this->belongsTo(CadenceSequence::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSequence($query, $sequenceId)
    {
        return $query->where('cadence_sequence_id', $sequenceId);
    }

    public function shouldExecute($contact): bool
    {
        if (!$this->is_active) return false;

        // Check step-specific conditions
        if (empty($this->conditions)) return true;

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

    public function generatePersonalizedContent($contact): array
    {
        $content = $this->action_config;
        
        if (empty($this->personalization_rules)) {
            return $content;
        }

        // Apply personalization rules
        foreach ($this->personalization_rules as $rule) {
            $placeholder = $rule['placeholder'] ?? null;
            $source = $rule['source'] ?? null;
            $defaultValue = $rule['default'] ?? '';

            if (!$placeholder || !$source) continue;

            $value = $this->getPersonalizationValue($contact, $source, $defaultValue);
            
            // Replace placeholder in content
            $content = $this->replacePlaceholders($content, $placeholder, $value);
        }

        return $content;
    }

    public function execute($contact, $enrollment): array
    {
        if (!$this->shouldExecute($contact)) {
            return [
                'status' => 'skipped',
                'reason' => 'Conditions not met',
                'data' => null
            ];
        }

        $personalizedContent = $this->generatePersonalizedContent($contact);
        
        switch ($this->action_type) {
            case 'email':
                return $this->executeEmailAction($contact, $personalizedContent);
            case 'call':
                return $this->executeCallAction($contact, $personalizedContent);
            case 'task':
                return $this->executeTaskAction($contact, $personalizedContent);
            case 'sms':
                return $this->executeSmsAction($contact, $personalizedContent);
            case 'social':
                return $this->executeSocialAction($contact, $personalizedContent);
            case 'wait':
                return $this->executeWaitAction($personalizedContent);
            case 'condition_check':
                return $this->executeConditionCheck($contact, $personalizedContent);
            default:
                return [
                    'status' => 'error',
                    'reason' => 'Unknown action type',
                    'data' => null
                ];
        }
    }

    public function getTotalDelay(): int
    {
        return ($this->delay_days * 24) + $this->delay_hours;
    }

    private function evaluateCondition($contactValue, $operator, $expectedValue): bool
    {
        switch ($operator) {
            case '=':
                return $contactValue == $expectedValue;
            case '!=':
                return $contactValue != $expectedValue;
            case '>':
                return $contactValue > $expectedValue;
            case '>=':
                return $contactValue >= $expectedValue;
            case '<':
                return $contactValue < $expectedValue;
            case '<=':
                return $contactValue <= $expectedValue;
            case 'contains':
                return str_contains(strtolower($contactValue), strtolower($expectedValue));
            default:
                return false;
        }
    }

    private function getPersonalizationValue($contact, $source, $default)
    {
        // Handle different data sources
        if (str_starts_with($source, 'contact.')) {
            return data_get($contact, substr($source, 8)) ?? $default;
        }
        
        if (str_starts_with($source, 'company.')) {
            return data_get($contact->company, substr($source, 8)) ?? $default;
        }
        
        if ($source === 'current_time') {
            return now()->format('H:i');
        }
        
        if ($source === 'current_date') {
            return now()->format('Y-m-d');
        }
        
        return $default;
    }

    private function replacePlaceholders($content, $placeholder, $value)
    {
        if (is_array($content)) {
            return array_map(function ($item) use ($placeholder, $value) {
                return is_string($item) ? str_replace($placeholder, $value, $item) : $item;
            }, $content);
        }
        
        return is_string($content) ? str_replace($placeholder, $value, $content) : $content;
    }

    private function executeEmailAction($contact, $content): array
    {
        // Create email record and send
        try {
            $email = Email::create([
                'subject' => $content['subject'] ?? 'Cadence Email',
                'body' => $content['body'] ?? '',
                'to_email' => $contact->email,
                'from_email' => $content['from_email'] ?? config('mail.from.address'),
                'status' => 'sent',
                'contact_id' => $contact->id,
                'tenant_id' => $contact->tenant_id,
            ]);

            return ['status' => 'completed', 'data' => ['email_id' => $email->id]];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'reason' => $e->getMessage()];
        }
    }

    private function executeTaskAction($contact, $content): array
    {
        try {
            $task = Task::create([
                'name' => $content['name'] ?? 'Cadence Task',
                'description' => $content['description'] ?? '',
                'type' => $content['task_type'] ?? 'call',
                'priority' => $content['priority'] ?? 'medium',
                'due_date' => now()->addDays($content['due_in_days'] ?? 1),
                'taskable_type' => Contact::class,
                'taskable_id' => $contact->id,
                'user_id' => $content['assigned_to'] ?? $contact->user_id,
                'tenant_id' => $contact->tenant_id,
            ]);

            return ['status' => 'completed', 'data' => ['task_id' => $task->id]];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'reason' => $e->getMessage()];
        }
    }

    private function executeCallAction($contact, $content): array
    {
        // For call actions, we typically create a task for the user to make the call
        return $this->executeTaskAction($contact, array_merge($content, ['task_type' => 'call']));
    }

    private function executeSmsAction($contact, $content): array
    {
        // SMS sending logic would go here
        return ['status' => 'completed', 'data' => ['message' => 'SMS functionality not implemented']];
    }

    private function executeSocialAction($contact, $content): array
    {
        // Social media action logic would go here
        return ['status' => 'completed', 'data' => ['message' => 'Social action functionality not implemented']];
    }

    private function executeWaitAction($content): array
    {
        return ['status' => 'completed', 'data' => ['wait_time' => $content['wait_time'] ?? $this->getTotalDelay()]];
    }

    private function executeConditionCheck($contact, $content): array
    {
        $conditions = $content['conditions'] ?? [];
        $result = true;

        foreach ($conditions as $condition) {
            $field = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? '=';
            $value = $condition['value'] ?? null;

            if (!$field || $value === null) continue;

            $contactValue = data_get($contact, $field);
            
            if (!$this->evaluateCondition($contactValue, $operator, $value)) {
                $result = false;
                break;
            }
        }

        return ['status' => 'completed', 'data' => ['condition_result' => $result]];
    }
}