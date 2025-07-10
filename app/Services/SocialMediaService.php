<?php

namespace App\Services;

use App\Models\SocialMediaAccount;
use App\Models\Communication;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialMediaService
{
    /**
     * Monitor brand mentions across connected social media accounts
     */
    public function monitorMentions(): void
    {
        $accounts = SocialMediaAccount::active()->get();

        foreach ($accounts as $account) {
            if ($account->isTokenExpired()) {
                Log::warning('Social media account token expired', [
                    'account_id' => $account->id,
                    'platform' => $account->platform,
                ]);
                continue;
            }

            try {
                $this->monitorAccountMentions($account);
            } catch (\Exception $e) {
                Log::error('Failed to monitor social media mentions', [
                    'account_id' => $account->id,
                    'platform' => $account->platform,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Monitor mentions for a specific account
     */
    private function monitorAccountMentions(SocialMediaAccount $account): void
    {
        $mentions = match ($account->platform) {
            'linkedin' => $this->getLinkedInMentions($account),
            'twitter' => $this->getTwitterMentions($account),
            'facebook' => $this->getFacebookMentions($account),
            default => []
        };

        foreach ($mentions as $mention) {
            $this->processMention($account, $mention);
        }

        $account->markAsSynced();
    }

    /**
     * Get LinkedIn mentions
     */
    private function getLinkedInMentions(SocialMediaAccount $account): array
    {
        // LinkedIn API implementation would go here
        // For now, return empty array as placeholder
        return [];
    }

    /**
     * Get Twitter mentions
     */
    private function getTwitterMentions(SocialMediaAccount $account): array
    {
        // Twitter API v2 implementation would go here
        // This would search for mentions of keywords or company name
        try {
            $keywords = $account->monitoring_keywords ?? [];
            if (empty($keywords)) {
                return [];
            }

            $query = implode(' OR ', array_map(fn($k) => "\"{$k}\"", $keywords));
            
            // Placeholder for Twitter API call
            // $response = Http::withToken($account->access_token)
            //     ->get('https://api.twitter.com/2/tweets/search/recent', [
            //         'query' => $query,
            //         'max_results' => 10,
            //         'tweet.fields' => 'created_at,author_id,public_metrics'
            //     ]);

            return []; // Placeholder

        } catch (\Exception $e) {
            Log::error('Twitter mentions fetch failed', [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get Facebook mentions
     */
    private function getFacebookMentions(SocialMediaAccount $account): array
    {
        // Facebook Graph API implementation would go here
        return [];
    }

    /**
     * Process a social media mention
     */
    private function processMention(SocialMediaAccount $account, array $mention): void
    {
        // Check if we've already processed this mention
        $existingCommunication = Communication::where('external_id', $mention['id'])
            ->where('provider', $account->platform)
            ->first();

        if ($existingCommunication) {
            return;
        }

        // Create communication record
        $communication = Communication::create([
            'type' => 'social',
            'direction' => 'inbound',
            'status' => 'completed',
            'content' => $mention['text'] ?? '',
            'external_id' => $mention['id'],
            'provider' => $account->platform,
            'provider_data' => $mention,
            'metadata' => [
                'social_account_id' => $account->id,
                'author' => $mention['author'] ?? null,
                'engagement' => $mention['engagement'] ?? [],
                'url' => $mention['url'] ?? null,
            ],
        ]);

        // Analyze sentiment and extract insights
        if (!empty($mention['text'])) {
            $this->analyzeMention($communication);
        }

        Log::info('Social media mention processed', [
            'communication_id' => $communication->id,
            'platform' => $account->platform,
            'mention_id' => $mention['id'],
        ]);
    }

    /**
     * Analyze social media mention with AI
     */
    private function analyzeMention(Communication $communication): void
    {
        try {
            $aiService = app(AiService::class);
            
            // Analyze sentiment and extract insights
            $analysis = $aiService->analyzeText($communication->content);
            
            if ($analysis) {
                $communication->addAiAnalysis([
                    'social_analysis' => $analysis,
                    'mention_type' => $this->classifyMentionType($communication->content),
                    'requires_response' => $this->requiresResponse($analysis),
                ]);

                if (isset($analysis['sentiment_score'])) {
                    $communication->update(['sentiment_score' => $analysis['sentiment_score']]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to analyze social media mention', [
                'communication_id' => $communication->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Classify mention type
     */
    private function classifyMentionType(string $content): string
    {
        $content = strtolower($content);

        if (str_contains($content, 'complaint') || str_contains($content, 'problem') || str_contains($content, 'issue')) {
            return 'complaint';
        }

        if (str_contains($content, 'praise') || str_contains($content, 'great') || str_contains($content, 'excellent')) {
            return 'praise';
        }

        if (str_contains($content, 'question') || str_contains($content, '?')) {
            return 'question';
        }

        return 'mention';
    }

    /**
     * Determine if mention requires response
     */
    private function requiresResponse(array $analysis): bool
    {
        // Negative sentiment should trigger response
        if (isset($analysis['sentiment_score']) && $analysis['sentiment_score'] < -0.3) {
            return true;
        }

        // Questions should trigger response
        if (isset($analysis['intent']) && str_contains(strtolower($analysis['intent']), 'question')) {
            return true;
        }

        return false;
    }

    /**
     * Post response to social media
     */
    public function postResponse(SocialMediaAccount $account, string $mentionId, string $response): bool
    {
        try {
            return match ($account->platform) {
                'linkedin' => $this->postLinkedInResponse($account, $mentionId, $response),
                'twitter' => $this->postTwitterResponse($account, $mentionId, $response),
                'facebook' => $this->postFacebookResponse($account, $mentionId, $response),
                default => false
            };

        } catch (\Exception $e) {
            Log::error('Failed to post social media response', [
                'account_id' => $account->id,
                'platform' => $account->platform,
                'mention_id' => $mentionId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Post LinkedIn response
     */
    private function postLinkedInResponse(SocialMediaAccount $account, string $mentionId, string $response): bool
    {
        // LinkedIn API response implementation
        return false; // Placeholder
    }

    /**
     * Post Twitter response
     */
    private function postTwitterResponse(SocialMediaAccount $account, string $mentionId, string $response): bool
    {
        // Twitter API response implementation
        return false; // Placeholder
    }

    /**
     * Post Facebook response
     */
    private function postFacebookResponse(SocialMediaAccount $account, string $mentionId, string $response): bool
    {
        // Facebook API response implementation
        return false; // Placeholder
    }

    /**
     * Get social media analytics
     */
    public function getAnalytics(SocialMediaAccount $account, array $dateRange = []): array
    {
        $startDate = $dateRange['start'] ?? now()->subDays(30);
        $endDate = $dateRange['end'] ?? now();

        $mentions = Communication::where('provider', $account->platform)
            ->where('type', 'social')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalMentions = $mentions->count();
        $positiveMentions = $mentions->where('sentiment_score', '>', 0.3)->count();
        $negativeMentions = $mentions->where('sentiment_score', '<', -0.3)->count();
        $neutralMentions = $totalMentions - $positiveMentions - $negativeMentions;

        return [
            'total_mentions' => $totalMentions,
            'positive_mentions' => $positiveMentions,
            'negative_mentions' => $negativeMentions,
            'neutral_mentions' => $neutralMentions,
            'sentiment_score' => $mentions->avg('sentiment_score') ?? 0,
            'engagement_rate' => $this->calculateEngagementRate($mentions),
            'top_keywords' => $this->extractTopKeywords($mentions),
        ];
    }

    /**
     * Calculate engagement rate
     */
    private function calculateEngagementRate($mentions): float
    {
        $totalEngagement = 0;
        $count = 0;

        foreach ($mentions as $mention) {
            $engagement = $mention->provider_data['engagement'] ?? [];
            if (!empty($engagement)) {
                $totalEngagement += array_sum($engagement);
                $count++;
            }
        }

        return $count > 0 ? round($totalEngagement / $count, 2) : 0;
    }

    /**
     * Extract top keywords from mentions
     */
    private function extractTopKeywords($mentions): array
    {
        $keywords = [];

        foreach ($mentions as $mention) {
            $analysis = $mention->ai_analysis['social_analysis'] ?? [];
            if (isset($analysis['key_phrases'])) {
                foreach ($analysis['key_phrases'] as $phrase) {
                    $keywords[] = strtolower($phrase);
                }
            }
        }

        return array_slice(array_count_values($keywords), 0, 10);
    }

    /**
     * Test social media account connection
     */
    public function testConnection(SocialMediaAccount $account): array
    {
        try {
            $result = match ($account->platform) {
                'linkedin' => $this->testLinkedInConnection($account),
                'twitter' => $this->testTwitterConnection($account),
                'facebook' => $this->testFacebookConnection($account),
                default => ['success' => false, 'message' => 'Platform not supported']
            };

            return $result;

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Test LinkedIn connection
     */
    private function testLinkedInConnection(SocialMediaAccount $account): array
    {
        // Test LinkedIn API connection
        return ['success' => false, 'message' => 'LinkedIn integration not implemented'];
    }

    /**
     * Test Twitter connection
     */
    private function testTwitterConnection(SocialMediaAccount $account): array
    {
        // Test Twitter API connection
        return ['success' => false, 'message' => 'Twitter integration not implemented'];
    }

    /**
     * Test Facebook connection
     */
    private function testFacebookConnection(SocialMediaAccount $account): array
    {
        // Test Facebook API connection
        return ['success' => false, 'message' => 'Facebook integration not implemented'];
    }
}