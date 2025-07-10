<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'contact_id',
        'user_id',
        'status',
        'visitor_name',
        'visitor_email',
        'visitor_phone',
        'page_url',
        'referrer',
        'visitor_info',
        'bot_active',
        'ai_model_id',
        'bot_context',
        'qualified_lead',
        'lead_score',
        'started_at',
        'ended_at',
        'duration_seconds',
    ];

    protected $casts = [
        'visitor_info' => 'array',
        'bot_context' => 'array',
        'bot_active' => 'boolean',
        'qualified_lead' => 'boolean',
        'lead_score' => 'decimal:2',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration_seconds' => 'integer',
    ];

    /**
     * Get the associated contact
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the assigned agent
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the AI model being used
     */
    public function aiModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class);
    }

    /**
     * Get all messages in this session
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    /**
     * Scope for active sessions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for waiting sessions
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    /**
     * Generate unique session ID
     */
    public static function generateSessionId(): string
    {
        do {
            $sessionId = 'chat_' . uniqid() . '_' . random_int(1000, 9999);
        } while (self::where('session_id', $sessionId)->exists());

        return $sessionId;
    }

    /**
     * Start the session
     */
    public function start(): void
    {
        $this->update([
            'status' => 'active',
            'started_at' => now(),
        ]);
    }

    /**
     * End the session
     */
    public function end(): void
    {
        $duration = $this->started_at ? now()->diffInSeconds($this->started_at) : 0;
        
        $this->update([
            'status' => 'completed',
            'ended_at' => now(),
            'duration_seconds' => $duration,
        ]);
    }

    /**
     * Assign to agent and deactivate bot
     */
    public function assignToAgent(User $agent): void
    {
        $this->update([
            'user_id' => $agent->id,
            'bot_active' => false,
            'status' => 'active',
        ]);
    }

    /**
     * Update bot context for AI conversations
     */
    public function updateBotContext(array $context): void
    {
        $currentContext = $this->bot_context ?? [];
        $this->update([
            'bot_context' => array_merge($currentContext, $context)
        ]);
    }

    /**
     * Mark as qualified lead
     */
    public function markAsQualified(float $score = null): void
    {
        $this->update([
            'qualified_lead' => true,
            'lead_score' => $score,
        ]);
    }

    /**
     * Check if session is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if bot is handling the conversation
     */
    public function isBotActive(): bool
    {
        return $this->bot_active && $this->status === 'active';
    }

    /**
     * Check if an agent is assigned
     */
    public function hasAssignedAgent(): bool
    {
        return !is_null($this->user_id);
    }

    /**
     * Get the latest message
     */
    public function getLatestMessage()
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Get visitor display name
     */
    public function getVisitorDisplayNameAttribute(): string
    {
        return $this->visitor_name ?? $this->visitor_email ?? 'Anonymous Visitor';
    }
}