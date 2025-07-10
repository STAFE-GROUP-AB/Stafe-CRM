<?php

namespace App\Services;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\Contact;
use App\Models\UserAiConfiguration;
use Illuminate\Support\Facades\Log;

class ChatBotService
{
    protected AiService $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Start a new chat session
     */
    public function startSession(array $visitorData = []): ChatSession
    {
        $session = ChatSession::create([
            'session_id' => ChatSession::generateSessionId(),
            'visitor_name' => $visitorData['name'] ?? null,
            'visitor_email' => $visitorData['email'] ?? null,
            'visitor_phone' => $visitorData['phone'] ?? null,
            'page_url' => $visitorData['page_url'] ?? null,
            'referrer' => $visitorData['referrer'] ?? null,
            'visitor_info' => $visitorData['browser_info'] ?? [],
            'bot_active' => true,
            'status' => 'active',
            'started_at' => now(),
        ]);

        // Send welcome message
        $this->sendWelcomeMessage($session);

        Log::info('Chat session started', [
            'session_id' => $session->session_id,
            'visitor_email' => $session->visitor_email,
        ]);

        return $session;
    }

    /**
     * Process incoming message from visitor
     */
    public function processMessage(ChatSession $session, string $message): ChatMessage
    {
        // Store the visitor's message
        $visitorMessage = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type' => 'visitor',
            'message' => $message,
        ]);

        // Analyze the message with AI
        $this->analyzeMessage($visitorMessage);

        // Generate and send bot response if bot is active
        if ($session->isBotActive()) {
            $this->generateBotResponse($session, $visitorMessage);
        }

        // Check if we should qualify this lead or escalate to human
        $this->evaluateSession($session);

        return $visitorMessage;
    }

    /**
     * Generate bot response to visitor message
     */
    public function generateBotResponse(ChatSession $session, ChatMessage $visitorMessage): ?ChatMessage
    {
        try {
            // Build context for the AI
            $context = $this->buildConversationContext($session);
            
            // Generate response using AI
            $response = $this->aiService->generateChatbotResponse(
                $visitorMessage->message,
                $context,
                $session->aiModel ? UserAiConfiguration::where('ai_model_id', $session->ai_model_id)->first() : null
            );

            if (!$response) {
                $response = $this->getFallbackResponse($visitorMessage);
            }

            // Create bot message
            $botMessage = ChatMessage::create([
                'chat_session_id' => $session->id,
                'sender_type' => 'bot',
                'message' => $response,
                'metadata' => [
                    'responding_to' => $visitorMessage->id,
                    'generated_at' => now()->toISOString(),
                ],
            ]);

            // Update session context
            $session->updateBotContext([
                'last_interaction' => now()->toISOString(),
                'message_count' => $session->messages()->count(),
                'visitor_intent' => $visitorMessage->detected_intent,
            ]);

            return $botMessage;

        } catch (\Exception $e) {
            Log::error('Bot response generation failed', [
                'session_id' => $session->session_id,
                'message_id' => $visitorMessage->id,
                'error' => $e->getMessage(),
            ]);

            // Send error fallback message
            return ChatMessage::create([
                'chat_session_id' => $session->id,
                'sender_type' => 'bot',
                'message' => "I'm having trouble processing your message right now. Let me connect you with a human agent.",
            ]);
        }
    }

    /**
     * Analyze visitor message for intent and sentiment
     */
    private function analyzeMessage(ChatMessage $message): void
    {
        try {
            $analysis = $this->aiService->analyzeText($message->message);
            
            if ($analysis) {
                $message->addAiAnalysis($analysis);
                
                if (isset($analysis['sentiment_score'])) {
                    $message->setSentimentScore($analysis['sentiment_score']);
                }

                if (isset($analysis['intent'])) {
                    $message->setDetectedIntent([
                        'intent' => $analysis['intent'],
                        'confidence' => $analysis['intent_confidence'] ?? 0.8,
                        'topics' => $analysis['topics'] ?? [],
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Message analysis failed', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Build conversation context for AI
     */
    private function buildConversationContext(ChatSession $session): array
    {
        $context = [
            'session_info' => [
                'visitor_name' => $session->visitor_name,
                'visitor_email' => $session->visitor_email,
                'page_url' => $session->page_url,
                'session_duration' => $session->started_at ? now()->diffInMinutes($session->started_at) : 0,
            ],
            'conversation_history' => [],
            'visitor_profile' => [],
        ];

        // Add recent conversation history
        $recentMessages = $session->messages()
            ->latest()
            ->limit(10)
            ->get()
            ->reverse();

        foreach ($recentMessages as $msg) {
            $context['conversation_history'][] = [
                'sender' => $msg->sender_type,
                'message' => $msg->message,
                'timestamp' => $msg->created_at->toISOString(),
                'sentiment' => $msg->sentiment,
                'intent' => $msg->detected_intent,
            ];
        }

        // Add visitor profile if we have contact information
        if ($session->contact_id) {
            $contact = $session->contact;
            $context['visitor_profile'] = [
                'name' => $contact->name,
                'company' => $contact->company?->name,
                'industry' => $contact->company?->industry,
                'previous_interactions' => $contact->communications()->count(),
            ];
        }

        return $context;
    }

    /**
     * Evaluate session for lead qualification or escalation
     */
    private function evaluateSession(ChatSession $session): void
    {
        $messageCount = $session->messages()->count();
        $duration = $session->started_at ? now()->diffInMinutes($session->started_at) : 0;

        // Check for automatic escalation triggers
        $shouldEscalate = false;
        $qualificationScore = 0;

        // Analyze recent messages for buying intent
        $recentMessages = $session->messages()
            ->fromVisitor()
            ->latest()
            ->limit(5)
            ->get();

        foreach ($recentMessages as $message) {
            // Check for high-intent keywords
            if ($this->containsHighIntentKeywords($message->message)) {
                $qualificationScore += 20;
            }

            // Check for negative sentiment requiring human intervention
            if ($message->sentiment_score && $message->sentiment_score < -0.5) {
                $shouldEscalate = true;
            }

            // Check for specific escalation intents
            if ($message->hasIntent('speak_to_human') || $message->hasIntent('complaint')) {
                $shouldEscalate = true;
            }
        }

        // Session duration-based escalation
        if ($duration > 15) { // 15 minutes
            $shouldEscalate = true;
        }

        // Message count-based escalation
        if ($messageCount > 20) {
            $shouldEscalate = true;
        }

        // Update lead qualification score
        if ($qualificationScore > 40) {
            $session->markAsQualified($qualificationScore);
            
            // Try to create or update contact
            $this->createOrUpdateContact($session);
        }

        // Handle escalation
        if ($shouldEscalate && $session->isBotActive()) {
            $this->escalateToHuman($session);
        }
    }

    /**
     * Check if message contains high-intent keywords
     */
    private function containsHighIntentKeywords(string $message): bool
    {
        $highIntentKeywords = [
            'pricing', 'price', 'cost', 'quote', 'demo', 'trial',
            'buy', 'purchase', 'subscribe', 'upgrade', 'plan',
            'meeting', 'call', 'schedule', 'contact', 'sales'
        ];

        $messageLower = strtolower($message);

        foreach ($highIntentKeywords as $keyword) {
            if (str_contains($messageLower, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create or update contact from chat session
     */
    private function createOrUpdateContact(ChatSession $session): ?Contact
    {
        if (!$session->visitor_email && !$session->visitor_phone) {
            return null;
        }

        try {
            $contact = Contact::where('email', $session->visitor_email)
                ->orWhere('phone', $session->visitor_phone)
                ->first();

            if (!$contact) {
                $contact = Contact::create([
                    'name' => $session->visitor_name ?? 'Chat Visitor',
                    'email' => $session->visitor_email,
                    'phone' => $session->visitor_phone,
                    'source' => 'live_chat',
                    'notes' => 'Contact created from live chat session',
                ]);
            }

            // Update session with contact
            $session->update(['contact_id' => $contact->id]);

            Log::info('Contact created/updated from chat', [
                'session_id' => $session->session_id,
                'contact_id' => $contact->id,
            ]);

            return $contact;

        } catch (\Exception $e) {
            Log::error('Failed to create contact from chat', [
                'session_id' => $session->session_id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Escalate session to human agent
     */
    private function escalateToHuman(ChatSession $session): void
    {
        $session->update([
            'status' => 'waiting',
            'bot_active' => false,
        ]);

        // Send escalation message
        ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type' => 'bot',
            'message' => "I'm connecting you with one of our team members who can better assist you. Please hold on for just a moment.",
            'metadata' => [
                'escalation' => true,
                'escalated_at' => now()->toISOString(),
            ],
        ]);

        // TODO: Notify available agents about the waiting session
        // This could trigger real-time notifications, emails, or Slack messages

        Log::info('Chat session escalated to human', [
            'session_id' => $session->session_id,
            'visitor_email' => $session->visitor_email,
        ]);
    }

    /**
     * Send welcome message to new session
     */
    private function sendWelcomeMessage(ChatSession $session): ChatMessage
    {
        $welcomeMessage = "Hi there! 👋 Welcome to our website. I'm here to help answer any questions you might have. How can I assist you today?";

        return ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type' => 'bot',
            'message' => $welcomeMessage,
            'metadata' => [
                'welcome_message' => true,
                'sent_at' => now()->toISOString(),
            ],
        ]);
    }

    /**
     * Get fallback response when AI fails
     */
    private function getFallbackResponse(ChatMessage $message): string
    {
        $fallbackResponses = [
            "I understand you're looking for help. Could you tell me more about what you need?",
            "That's a great question! Let me see how I can help you with that.",
            "I'd be happy to assist you. Could you provide a bit more detail?",
            "Thanks for reaching out! What specific information are you looking for?",
            "I'm here to help. Could you rephrase your question so I can better assist you?",
        ];

        return $fallbackResponses[array_rand($fallbackResponses)];
    }

    /**
     * Get session by session ID
     */
    public function getSession(string $sessionId): ?ChatSession
    {
        return ChatSession::where('session_id', $sessionId)->first();
    }

    /**
     * End chat session
     */
    public function endSession(ChatSession $session): void
    {
        $session->end();

        // Send goodbye message if bot is still active
        if ($session->isBotActive()) {
            ChatMessage::create([
                'chat_session_id' => $session->id,
                'sender_type' => 'bot',
                'message' => "Thank you for chatting with us today! If you need any further assistance, feel free to start a new conversation.",
                'metadata' => [
                    'goodbye_message' => true,
                    'sent_at' => now()->toISOString(),
                ],
            ]);
        }

        Log::info('Chat session ended', [
            'session_id' => $session->session_id,
            'duration_seconds' => $session->duration_seconds,
            'message_count' => $session->messages()->count(),
        ]);
    }

    /**
     * Check if chat widget should be shown on page
     */
    public function shouldShowChatWidget(string $pageUrl, array $visitorInfo = []): bool
    {
        // Business logic to determine if chat should be shown
        // Could be based on page rules, visitor behavior, time of day, etc.
        
        return true; // For now, always show
    }
}