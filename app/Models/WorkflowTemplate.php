<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkflowTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'trigger_type',
        'trigger_config',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'trigger_config' => 'array',
        'is_active' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(WorkflowStep::class)->orderBy('order');
    }

    public function instances(): HasMany
    {
        return $this->hasMany(WorkflowInstance::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByTriggerType($query, string $triggerType)
    {
        return $query->where('trigger_type', $triggerType);
    }

    /**
     * Check if workflow can be triggered for the given entity
     */
    public function canTrigger($entity = null): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Add trigger-specific logic here
        return true;
    }

    /**
     * Execute the workflow for the given entity
     */
    public function execute($entity = null, array $context = []): WorkflowInstance
    {
        return WorkflowInstance::create([
            'workflow_template_id' => $this->id,
            'status' => 'pending',
            'entity_type' => $entity ? get_class($entity) : null,
            'entity_id' => $entity?->id,
            'context' => $context,
        ]);
    }
}