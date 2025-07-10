<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_session_id',
        'sender_type',
        'user_id',
        'message',
        'metadata',
        'ai_analysis',
        'sentiment_score',
        'detected_intent',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'ai_analysis' => 'array',
        'detected_intent' => 'array',
        'sentiment_score' => 'decimal:2',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the chat session this message belongs to
     */
    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    /**
     * Get the user who sent this message (if agent)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for visitor messages
     */
    public function scopeFromVisitor($query)
    {
        return $query->where('sender_type', 'visitor');
    }

    /**
     * Scope for agent messages
     */
    public function scopeFromAgent($query)
    {
        return $query->where('sender_type', 'agent');
    }

    /**
     * Scope for bot messages
     */
    public function scopeFromBot($query)
    {
        return $query->where('sender_type', 'bot');
    }

    /**
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Check if message is from visitor
     */
    public function isFromVisitor(): bool
    {
        return $this->sender_type === 'visitor';
    }

    /**
     * Check if message is from agent
     */
    public function isFromAgent(): bool
    {
        return $this->sender_type === 'agent';
    }

    /**
     * Check if message is from bot
     */
    public function isFromBot(): bool
    {
        return $this->sender_type === 'bot';
    }

    /**
     * Get sentiment as human readable
     */
    public function getSentimentAttribute(): string
    {
        if ($this->sentiment_score === null) {
            return 'neutral';
        }

        return match (true) {
            $this->sentiment_score >= 0.3 => 'positive',
            $this->sentiment_score <= -0.3 => 'negative',
            default => 'neutral'
        };
    }

    /**
     * Get the sender's display name
     */
    public function getSenderNameAttribute(): string
    {
        return match ($this->sender_type) {
            'agent' => $this->user?->name ?? 'Agent',
            'bot' => 'AI Assistant',
            'visitor' => $this->chatSession?->visitor_display_name ?? 'Visitor',
            default => 'Unknown'
        };
    }

    /**
     * Add AI analysis to the message
     */
    public function addAiAnalysis(array $analysis): void
    {
        $this->update(['ai_analysis' => array_merge($this->ai_analysis ?? [], $analysis)]);
    }

    /**
     * Set detected intent
     */
    public function setDetectedIntent(array $intent): void
    {
        $this->update(['detected_intent' => $intent]);
    }

    /**
     * Set sentiment score
     */
    public function setSentimentScore(float $score): void
    {
        $this->update(['sentiment_score' => $score]);
    }

    /**
     * Check if message contains specific intent
     */
    public function hasIntent(string $intent): bool
    {
        $detectedIntent = $this->detected_intent;
        
        if (!$detectedIntent || !isset($detectedIntent['intent'])) {
            return false;
        }

        return strtolower($detectedIntent['intent']) === strtolower($intent);
    }

    /**
     * Get intent confidence
     */
    public function getIntentConfidence(): float
    {
        return $this->detected_intent['confidence'] ?? 0.0;
    }
}