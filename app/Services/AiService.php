<?php

namespace App\Services;

use App\Models\AiProvider;
use App\Models\AiModel;
use App\Models\UserAiConfiguration;
use App\Models\Contact;
use App\Models\LeadScore;
use App\Models\ScoringFactor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    public function __construct()
    {
        //
    }

    /**
     * Get available AI providers
     */
    public function getAvailableProviders()
    {
        return AiProvider::active()->get();
    }

    /**
     * Get models for a specific provider
     */
    public function getProviderModels(int $providerId)
    {
        return AiModel::active()->byProvider($providerId)->get();
    }

    /**
     * Get user's AI configurations
     */
    public function getUserConfigurations(int $userId)
    {
        return UserAiConfiguration::forUser($userId)
            ->active()
            ->with(['aiProvider', 'aiProvider.aiModels'])
            ->get();
    }

    /**
     * Create or update user AI configuration
     */
    public function configureUserAi(int $userId, array $data): UserAiConfiguration
    {
        $config = UserAiConfiguration::updateOrCreate(
            [
                'user_id' => $userId,
                'ai_provider_id' => $data['ai_provider_id'],
                'name' => $data['name']
            ],
            [
                'credentials' => $data['credentials'],
                'default_models' => $data['default_models'] ?? [],
                'settings' => $data['settings'] ?? [],
                'is_active' => $data['is_active'] ?? true,
                'is_default' => $data['is_default'] ?? false,
            ]
        );

        // If this is set as default, remove default from others
        if ($data['is_default'] ?? false) {
            UserAiConfiguration::forUser($userId)
                ->where('id', '!=', $config->id)
                ->update(['is_default' => false]);
        }

        return $config;
    }

    /**
     * Test AI configuration
     */
    public function testConfiguration(UserAiConfiguration $config): array
    {
        try {
            $provider = $config->aiProvider;
            
            // Simple test request based on provider
            $response = match($provider->slug) {
                'openai' => $this->testOpenAI($config),
                'anthropic' => $this->testAnthropic($config),
                'google' => $this->testGoogle($config),
                default => throw new \Exception('Provider not supported for testing')
            };

            $config->markAsUsed();

            return [
                'success' => true,
                'message' => 'AI configuration tested successfully',
                'response' => $response
            ];

        } catch (\Exception $e) {
            Log::error('AI Configuration Test Failed', [
                'config_id' => $config->id,
                'provider' => $config->aiProvider->slug,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Configuration test failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Calculate lead score for a contact
     */
    public function calculateLeadScore(Contact $contact, ?UserAiConfiguration $config = null): LeadScore
    {
        $factors = ScoringFactor::active()->ordered()->get();
        $totalScore = 0;
        $factorScores = [];
        $explanations = [];

        foreach ($factors as $factor) {
            $score = $this->calculateFactorScore($contact, $factor);
            $weightedScore = $score * $factor->weight;
            $totalScore += $weightedScore;

            $factorScores[$factor->name] = [
                'raw_score' => $score,
                'weight' => $factor->weight,
                'weighted_score' => $weightedScore,
                'display_name' => $factor->display_name
            ];

            $explanations[] = $this->generateFactorExplanation($factor, $score);
        }

        // Normalize to 0-100 scale
        $normalizedScore = min(100, max(0, round($totalScore)));
        
        // Determine grade
        $grade = $this->scoreToGrade($normalizedScore);

        // Create or update lead score
        $leadScore = LeadScore::updateOrCreate(
            ['contact_id' => $contact->id],
            [
                'score' => $normalizedScore,
                'probability' => $this->scoreToProbability($normalizedScore),
                'grade' => $grade,
                'factors' => $factorScores,
                'explanations' => $explanations,
                'model_version' => '1.0',
                'last_calculated_at' => now(),
                'ai_model_id' => $config?->getDefaultModel('lead_scoring'),
            ]
        );

        return $leadScore;
    }

    /**
     * Calculate individual factor score
     */
    private function calculateFactorScore(Contact $contact, ScoringFactor $factor): float
    {
        return match($factor->calculation_method) {
            'rule_based' => $this->calculateRuleBasedScore($contact, $factor),
            'ml_model' => $this->calculateMLScore($contact, $factor),
            'api_call' => $this->calculateAPIScore($contact, $factor),
            default => 50.0 // Default neutral score
        };
    }

    /**
     * Calculate rule-based score
     */
    private function calculateRuleBasedScore(Contact $contact, ScoringFactor $factor): float
    {
        $config = $factor->configuration;

        return match($factor->name) {
            'company_size' => $this->calculateCompanySizeScore($contact, $config),
            'industry_match' => $this->calculateIndustryScore($contact, $config),
            'email_engagement' => $this->calculateEmailEngagementScore($contact, $config),
            'website_activity' => $this->calculateWebsiteActivityScore($contact, $config),
            'lead_source_quality' => $this->calculateLeadSourceScore($contact, $config),
            default => 50.0
        };
    }

    /**
     * Test OpenAI configuration
     */
    private function testOpenAI(UserAiConfiguration $config): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $config->getCredential('api_key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => 'Hello! This is a test message.']
            ],
            'max_tokens' => 10
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('OpenAI API test failed: ' . $response->body());
    }

    /**
     * Test Anthropic configuration
     */
    private function testAnthropic(UserAiConfiguration $config): array
    {
        $response = Http::withHeaders([
            'x-api-key' => $config->getCredential('api_key'),
            'Content-Type' => 'application/json',
            'anthropic-version' => '2023-06-01'
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-3-haiku-20240307',
            'max_tokens' => 10,
            'messages' => [
                ['role' => 'user', 'content' => 'Hello! This is a test message.']
            ]
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Anthropic API test failed: ' . $response->body());
    }

    /**
     * Test Google AI configuration
     */
    private function testGoogle(UserAiConfiguration $config): array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent', [
            'key' => $config->getCredential('api_key'),
            'contents' => [
                ['parts' => [['text' => 'Hello! This is a test message.']]]
            ]
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Google AI API test failed: ' . $response->body());
    }

    // Helper methods for scoring calculations
    private function calculateCompanySizeScore(Contact $contact, array $config): float
    {
        $companySize = $contact->company?->employee_count ?? 0;
        
        foreach ($config['size_ranges'] as $range => $score) {
            if (str_contains($range, '-')) {
                [$min, $max] = explode('-', $range);
                if ($companySize >= (int)$min && $companySize <= (int)$max) {
                    return $score;
                }
            } elseif (str_contains($range, '+')) {
                $min = (int)str_replace('+', '', $range);
                if ($companySize >= $min) {
                    return $score;
                }
            }
        }
        
        return 0;
    }

    private function calculateIndustryScore(Contact $contact, array $config): float
    {
        $industry = strtolower($contact->company?->industry ?? '');
        
        foreach ($config['target_industries'] as $index => $targetIndustry) {
            if (str_contains($industry, $targetIndustry)) {
                return $config['scores'][$index] ?? 0;
            }
        }
        
        return 0;
    }

    private function calculateEmailEngagementScore(Contact $contact, array $config): float
    {
        // This would integrate with actual email metrics
        // For now, return a placeholder score
        return rand(30, 90);
    }

    private function calculateWebsiteActivityScore(Contact $contact, array $config): float
    {
        // This would integrate with analytics data
        // For now, return a placeholder score
        return rand(20, 80);
    }

    private function calculateLeadSourceScore(Contact $contact, array $config): float
    {
        $source = strtolower($contact->source ?? '');
        return $config['source_scores'][$source] ?? 30;
    }

    private function calculateMLScore(Contact $contact, ScoringFactor $factor): float
    {
        // Placeholder for ML model scoring
        return rand(40, 90);
    }

    private function calculateAPIScore(Contact $contact, ScoringFactor $factor): float
    {
        // Placeholder for external API scoring
        return rand(30, 85);
    }

    private function scoreToGrade(int $score): string
    {
        return match (true) {
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            $score >= 60 => 'D',
            default => 'F'
        };
    }

    private function scoreToProbability(int $score): float
    {
        return round($score / 100, 4);
    }

    private function generateFactorExplanation(ScoringFactor $factor, float $score): string
    {
        $level = match (true) {
            $score >= 80 => 'excellent',
            $score >= 60 => 'good',
            $score >= 40 => 'fair',
            default => 'poor'
        };

        return "{$factor->display_name} shows {$level} indicators (score: {$score}).";
    }

    /**
     * Analyze text content for sentiment and insights
     */
    public function analyzeText(string $text, ?UserAiConfiguration $config = null): ?array
    {
        if (!$config) {
            $config = UserAiConfiguration::active()->default()->first();
        }

        if (!$config) {
            return null;
        }

        try {
            $prompt = "Analyze the following text for sentiment, key topics, and intent. Return a JSON response with sentiment_score (-1 to 1), topics (array), intent, and key_phrases:\n\n{$text}";
            
            $response = $this->makeAiRequest($config, $prompt);
            
            if ($response) {
                return [
                    'sentiment_score' => $response['sentiment_score'] ?? 0.0,
                    'topics' => $response['topics'] ?? [],
                    'intent' => $response['intent'] ?? 'unknown',
                    'key_phrases' => $response['key_phrases'] ?? [],
                    'analyzed_at' => now()->toISOString(),
                ];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Text analysis failed', [
                'error' => $e->getMessage(),
                'text_length' => strlen($text),
            ]);
            
            return null;
        }
    }

    /**
     * Analyze conversation transcript for insights
     */
    public function analyzeConversation(string $transcript, ?UserAiConfiguration $config = null): ?array
    {
        if (!$config) {
            $config = UserAiConfiguration::active()->default()->first();
        }

        if (!$config) {
            return null;
        }

        try {
            $prompt = "Analyze this sales conversation transcript. Provide insights including:\n" .
                     "- Overall sentiment (-1 to 1)\n" .
                     "- Key topics discussed\n" .
                     "- Customer pain points\n" .
                     "- Buying signals\n" .
                     "- Next steps mentioned\n" .
                     "- Competitor mentions\n" .
                     "Return as JSON:\n\n{$transcript}";

            $response = $this->makeAiRequest($config, $prompt);
            
            if ($response) {
                return array_merge($response, [
                    'analyzed_at' => now()->toISOString(),
                    'transcript_length' => strlen($transcript),
                ]);
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Conversation analysis failed', [
                'error' => $e->getMessage(),
                'transcript_length' => strlen($transcript),
            ]);
            
            return null;
        }
    }

    /**
     * Generate follow-up suggestions based on communication
     */
    public function generateFollowUpSuggestions(string $content, array $context = [], ?UserAiConfiguration $config = null): ?array
    {
        if (!$config) {
            $config = UserAiConfiguration::active()->default()->first();
        }

        if (!$config) {
            return null;
        }

        try {
            $contextStr = json_encode($context);
            $prompt = "Based on this communication and context, suggest 3-5 specific follow-up actions for the salesperson. " .
                     "Consider the conversation content, timing, and customer context. Return as JSON array with 'action', 'priority', 'timing', and 'reason' for each suggestion:\n\n" .
                     "Content: {$content}\n\nContext: {$contextStr}";

            $response = $this->makeAiRequest($config, $prompt);
            
            if ($response && isset($response['suggestions'])) {
                return $response['suggestions'];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Follow-up suggestions failed', [
                'error' => $e->getMessage(),
                'content_length' => strlen($content),
            ]);
            
            return null;
        }
    }

    /**
     * Generate chatbot response
     */
    public function generateChatbotResponse(string $message, array $context = [], ?UserAiConfiguration $config = null): ?string
    {
        if (!$config) {
            $config = UserAiConfiguration::active()->default()->first();
        }

        if (!$config) {
            return null;
        }

        try {
            $contextStr = json_encode($context);
            $prompt = "You are a helpful sales assistant chatbot for a CRM system. Respond professionally and helpfully to this customer message. " .
                     "Use the context to personalize your response. Keep responses concise but friendly:\n\n" .
                     "Customer message: {$message}\n\nContext: {$contextStr}";

            $response = $this->makeAiRequest($config, $prompt);
            
            if ($response && isset($response['message'])) {
                return $response['message'];
            }

            // Fallback to direct text response if structured response fails
            if (is_string($response)) {
                return $response;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Chatbot response generation failed', [
                'error' => $e->getMessage(),
                'message' => $message,
            ]);
            
            return null;
        }
    }

    /**
     * Make AI request using the appropriate provider
     */
    private function makeAiRequest(UserAiConfiguration $config, string $prompt): mixed
    {
        $provider = $config->aiProvider->slug;

        return match ($provider) {
            'openai' => $this->makeOpenAiRequest($config, $prompt),
            'anthropic' => $this->makeAnthropicRequest($config, $prompt),
            'google' => $this->makeGoogleAiRequest($config, $prompt),
            default => null
        };
    }

    /**
     * Make OpenAI request
     */
    private function makeOpenAiRequest(UserAiConfiguration $config, string $prompt): mixed
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $config->getCredential('api_key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $config->getDefaultModel('general') ?? 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful AI assistant for CRM analysis. Always return valid JSON when requested.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => 1000,
            'temperature' => 0.3,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';
            
            // Try to parse as JSON, fallback to raw content
            $decoded = json_decode($content, true);
            return $decoded ?? $content;
        }

        return null;
    }

    /**
     * Make Anthropic request
     */
    private function makeAnthropicRequest(UserAiConfiguration $config, string $prompt): mixed
    {
        $response = Http::withHeaders([
            'x-api-key' => $config->getCredential('api_key'),
            'Content-Type' => 'application/json',
            'anthropic-version' => '2023-06-01'
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => $config->getDefaultModel('general') ?? 'claude-3-haiku-20240307',
            'max_tokens' => 1000,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $content = $data['content'][0]['text'] ?? '';
            
            // Try to parse as JSON, fallback to raw content
            $decoded = json_decode($content, true);
            return $decoded ?? $content;
        }

        return null;
    }

    /**
     * Make Google AI request
     */
    private function makeGoogleAiRequest(UserAiConfiguration $config, string $prompt): mixed
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $config->getCredential('api_key'), [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'maxOutputTokens' => 1000,
                'temperature' => 0.3,
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // Try to parse as JSON, fallback to raw content
            $decoded = json_decode($content, true);
            return $decoded ?? $content;
        }

        return null;
    }
}