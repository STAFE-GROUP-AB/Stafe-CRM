<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaybookStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'playbook_id',
        'title',
        'description',
        'instructions',
        'step_type',
        'content',
        'resources',
        'success_criteria',
        'failure_handling',
        'sort_order',
        'estimated_duration_minutes',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'content' => 'array',
        'resources' => 'array',
        'sort_order' => 'integer',
        'estimated_duration_minutes' => 'integer',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function playbook(): BelongsTo
    {
        return $this->belongsTo(SalesPlaybook::class, 'playbook_id');
    }

    // Type helpers
    public function isAction(): bool
    {
        return $this->step_type === 'action';
    }

    public function isQuestion(): bool
    {
        return $this->step_type === 'question';
    }

    public function isScript(): bool
    {
        return $this->step_type === 'script';
    }

    public function isDecision(): bool
    {
        return $this->step_type === 'decision';
    }

    public function isChecklist(): bool
    {
        return $this->step_type === 'checklist';
    }

    public function getTypeIcon(): string
    {
        return match ($this->step_type) {
            'action' => 'bolt',
            'question' => 'question-mark-circle',
            'script' => 'document-text',
            'decision' => 'switch-horizontal',
            'checklist' => 'check-circle',
            default => 'document'
        };
    }

    public function getTypeColor(): string
    {
        return match ($this->step_type) {
            'action' => 'blue',
            'question' => 'green',
            'script' => 'purple',
            'decision' => 'orange',
            'checklist' => 'indigo',
            default => 'gray'
        };
    }
}