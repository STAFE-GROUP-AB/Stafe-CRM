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
     * Get LinkedIn mentions using LinkedIn API
     */
    private function getLinkedInMentions(SocialMediaAccount $account): array
    {
        try {
            $keywords = $account->monitoring_keywords ?? [];
            if (empty($keywords)) {
                return [];
            }

            // LinkedIn API requires organization URN
            $organizationUrn = $account->metadata['organization_urn'] ?? null;
            if (!$organizationUrn) {
                Log::warning('LinkedIn organization URN not configured', [
                    'account_id' => $account->id,
                ]);
                return [];
            }

            // Fetch organization shares/posts
            $response = Http::withToken($account->access_token)
                ->get('https://api.linkedin.com/v2/shares', [
                    'q' => 'owners',
                    'owners' => $organizationUrn,
                    'count' => 10,
                    'sortBy' => 'LAST_MODIFIED',
                ]);

            if (!$response->successful()) {
                Log::warning('LinkedIn API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $data = $response->json();
            $shares = $data['elements'] ?? [];

            // Transform shares to standard mention format
            return collect($shares)->map(function ($share) {
                return [
                    'id' => $share['id'],
                    'text' => $share['text']['text'] ?? '',
                    'author' => [
                        'id' => $share['owner'] ?? 'unknown',
                        'name' => $share['author'] ?? 'LinkedIn User',
                    ],
                    'created_at' => isset($share['created']['time']) 
                        ? date('c', $share['created']['time'] / 1000) 
                        : now()->toISOString(),
                    'engagement' => [
                        'likes' => $share['totalShareStatistics']['likeCount'] ?? 0,
                        'comments' => $share['totalShareStatistics']['commentCount'] ?? 0,
                        'shares' => $share['totalShareStatistics']['shareCount'] ?? 0,
                    ],
                    'url' => $share['shareUrl'] ?? null,
                ];
            })->toArray();

        } catch (\Exception $e) {
            Log::error('LinkedIn mentions fetch failed', [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get Twitter mentions using Twitter API v2
     */
    private function getTwitterMentions(SocialMediaAccount $account): array
    {
        try {
            $keywords = $account->monitoring_keywords ?? [];
            if (empty($keywords)) {
                return [];
            }

            // Build search query for Twitter API v2
            $query = implode(' OR ', array_map(fn($k) => "\"{$k}\"", $keywords));
            
            // Make actual Twitter API v2 call
            $response = Http::withToken($account->access_token)
                ->get('https://api.twitter.com/2/tweets/search/recent', [
                    'query' => $query,
                    'max_results' => 10,
                    'tweet.fields' => 'created_at,author_id,public_metrics,entities',
                    'user.fields' => 'name,username,profile_image_url',
                    'expansions' => 'author_id'
                ]);

            if (!$response->successful()) {
                Log::warning('Twitter API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $data = $response->json();
            $tweets = $data['data'] ?? [];
            $users = collect($data['includes']['users'] ?? [])->keyBy('id');

            // Transform tweets to standard mention format
            return collect($tweets)->map(function ($tweet) use ($users) {
                $author = $users->get($tweet['author_id']);
                
                return [
                    'id' => $tweet['id'],
                    'text' => $tweet['text'],
                    'author' => [
                        'id' => $tweet['author_id'],
                        'name' => $author['name'] ?? 'Unknown',
                        'username' => $author['username'] ?? 'unknown',
                        'profile_image' => $author['profile_image_url'] ?? null,
                    ],
                    'created_at' => $tweet['created_at'],
                    'engagement' => [
                        'likes' => $tweet['public_metrics']['like_count'] ?? 0,
                        'retweets' => $tweet['public_metrics']['retweet_count'] ?? 0,
                        'replies' => $tweet['public_metrics']['reply_count'] ?? 0,
                    ],
                    'url' => "https://twitter.com/i/web/status/{$tweet['id']}",
                ];
            })->toArray();

        } catch (\Exception $e) {
            Log::error('Twitter mentions fetch failed', [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get Facebook mentions using Graph API
     */
    private function getFacebookMentions(SocialMediaAccount $account): array
    {
        try {
            $pageId = $account->metadata['page_id'] ?? null;
            if (!$pageId) {
                Log::warning('Facebook page ID not configured', [
                    'account_id' => $account->id,
                ]);
                return [];
            }

            // Fetch page mentions and posts
            $response = Http::get('https://graph.facebook.com/v18.0/' . $pageId . '/feed', [
                'access_token' => $account->access_token,
                'fields' => 'id,message,created_time,from,likes.summary(true),comments.summary(true),shares',
                'limit' => 10,
            ]);

            if (!$response->successful()) {
                Log::warning('Facebook API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $data = $response->json();
            $posts = $data['data'] ?? [];

            // Also fetch mentions
            $mentionsResponse = Http::get('https://graph.facebook.com/v18.0/' . $pageId . '/tagged', [
                'access_token' => $account->access_token,
                'fields' => 'id,message,created_time,from,likes.summary(true),comments.summary(true)',
                'limit' => 10,
            ]);

            if ($mentionsResponse->successful()) {
                $mentionsData = $mentionsResponse->json();
                $posts = array_merge($posts, $mentionsData['data'] ?? []);
            }

            // Transform posts to standard mention format
            return collect($posts)->map(function ($post) {
                return [
                    'id' => $post['id'],
                    'text' => $post['message'] ?? '',
                    'author' => [
                        'id' => $post['from']['id'] ?? 'unknown',
                        'name' => $post['from']['name'] ?? 'Facebook User',
                    ],
                    'created_at' => $post['created_time'] ?? now()->toISOString(),
                    'engagement' => [
                        'likes' => $post['likes']['summary']['total_count'] ?? 0,
                        'comments' => $post['comments']['summary']['total_count'] ?? 0,
                        'shares' => $post['shares']['count'] ?? 0,
                    ],
                    'url' => "https://facebook.com/{$post['id']}",
                ];
            })->toArray();

        } catch (\Exception $e) {
            Log::error('Facebook mentions fetch failed', [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
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
        try {
            $organizationUrn = $account->metadata['organization_urn'] ?? null;
            if (!$organizationUrn) {
                return false;
            }

            // Create a share/comment on LinkedIn
            $responseData = Http::withToken($account->access_token)
                ->post('https://api.linkedin.com/v2/ugcPosts', [
                    'author' => $organizationUrn,
                    'lifecycleState' => 'PUBLISHED',
                    'specificContent' => [
                        'com.linkedin.ugc.ShareContent' => [
                            'shareCommentary' => [
                                'text' => $response,
                            ],
                            'shareMediaCategory' => 'NONE',
                        ],
                    ],
                    'visibility' => [
                        'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
                    ],
                ]);

            return $responseData->successful();

        } catch (\Exception $e) {
            Log::error('LinkedIn response posting failed', [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Post Twitter response (reply to tweet)
     */
    private function postTwitterResponse(SocialMediaAccount $account, string $mentionId, string $response): bool
    {
        try {
            // Post a reply to the original tweet using Twitter API v2
            $responseData = Http::withToken($account->access_token)
                ->post('https://api.twitter.com/2/tweets', [
                    'text' => $response,
                    'reply' => [
                        'in_reply_to_tweet_id' => $mentionId,
                    ],
                ]);

            if ($responseData->successful()) {
                $tweetData = $responseData->json();
                Log::info('Twitter response posted', [
                    'tweet_id' => $tweetData['data']['id'] ?? null,
                    'in_reply_to' => $mentionId,
                ]);
                return true;
            }

            Log::warning('Twitter response failed', [
                'status' => $responseData->status(),
                'body' => $responseData->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Twitter response posting failed', [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Post Facebook response (comment on post)
     */
    private function postFacebookResponse(SocialMediaAccount $account, string $mentionId, string $response): bool
    {
        try {
            // Post a comment on the Facebook post
            $responseData = Http::post("https://graph.facebook.com/v18.0/{$mentionId}/comments", [
                'access_token' => $account->access_token,
                'message' => $response,
            ]);

            if ($responseData->successful()) {
                $commentData = $responseData->json();
                Log::info('Facebook response posted', [
                    'comment_id' => $commentData['id'] ?? null,
                    'post_id' => $mentionId,
                ]);
                return true;
            }

            Log::warning('Facebook response failed', [
                'status' => $responseData->status(),
                'body' => $responseData->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Facebook response posting failed', [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
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
        try {
            $response = Http::withToken($account->access_token)
                ->get('https://api.linkedin.com/v2/me');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'LinkedIn connection successful',
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'LinkedIn API returned error: ' . $response->status(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'LinkedIn connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Test Twitter connection
     */
    private function testTwitterConnection(SocialMediaAccount $account): array
    {
        try {
            $response = Http::withToken($account->access_token)
                ->get('https://api.twitter.com/2/users/me');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Twitter connection successful',
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Twitter API returned error: ' . $response->status(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Twitter connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Test Facebook connection
     */
    private function testFacebookConnection(SocialMediaAccount $account): array
    {
        try {
            $pageId = $account->metadata['page_id'] ?? null;
            if (!$pageId) {
                return [
                    'success' => false,
                    'message' => 'Facebook page ID not configured',
                ];
            }

            $response = Http::get("https://graph.facebook.com/v18.0/{$pageId}", [
                'access_token' => $account->access_token,
                'fields' => 'id,name,access_token',
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Facebook connection successful',
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Facebook API returned error: ' . $response->status(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Facebook connection failed: ' . $e->getMessage(),
            ];
        }
    }
}