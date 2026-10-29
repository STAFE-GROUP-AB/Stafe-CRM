<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\LeadScore;
use App\Models\LeadScoreHistory;
use App\Models\ScoringFactor;
use App\Models\UserAiConfiguration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class LeadScoringService
{
    private array $categoryScores = [];

    private array $factorDetails = [];

    private array $recommendations = [];

    /**
     * Target industries for ideal customer profile (ICP)
     */
    private array $targetIndustries = [
        'technology' => 100,
        'software' => 100,
        'saas' => 100,
        'fintech' => 95,
        'finance' => 90,
        'banking' => 85,
        'healthcare' => 80,
        'e-commerce' => 80,
        'retail' => 70,
        'manufacturing' => 65,
        'consulting' => 75,
        'marketing' => 70,
        'education' => 60,
    ];

    /**
     * Title seniority levels and their scores
     */
    private array $seniorityScores = [
        'c-level' => ['ceo', 'cto', 'cfo', 'coo', 'cmo', 'chief'],
        'vp' => ['vp', 'vice president', 'vice-president'],
        'director' => ['director', 'head of'],
        'manager' => ['manager', 'lead', 'team lead'],
        'senior' => ['senior', 'sr.', 'sr '],
        'individual' => [],
    ];

    private array $seniorityValues = [
        'c-level' => 100,
        'vp' => 90,
        'director' => 80,
        'manager' => 70,
        'senior' => 60,
        'individual' => 40,
    ];

    /**
     * Lead source quality scores
     */
    private array $sourceScores = [
        'demo_request' => 100,
        'free_trial' => 95,
        'contact_form' => 85,
        'webinar' => 80,
        'content_download' => 70,
        'referral' => 90,
        'partner' => 85,
        'paid_ad' => 60,
        'organic' => 50,
        'social_media' => 45,
        'cold_outreach' => 30,
        'purchased_list' => 20,
    ];

    /**
     * Calculate lead score for a contact
     */
    public function calculateScore(Contact $contact, ?UserAiConfiguration $config = null): LeadScore
    {
        $this->resetState();

        // Load all active scoring factors
        $factors = ScoringFactor::active()->ordered()->get();

        // If no factors exist, use default scoring
        if ($factors->isEmpty()) {
            return $this->calculateDefaultScore($contact, $config);
        }

        $totalScore = 0;
        $totalWeight = 0;

        foreach ($factors as $factor) {
            $score = $this->calculateFactorScore($contact, $factor);
            $weightedScore = $score * $factor->weight;
            $totalScore += $weightedScore;
            $totalWeight += $factor->weight;

            $this->factorDetails[$factor->name] = [
                'raw_score' => round($score, 1),
                'weight' => $factor->weight,
                'weighted_score' => round($weightedScore, 2),
                'display_name' => $factor->display_name,
                'category' => $factor->category,
            ];

            // Track category scores
            $category = $factor->category;
            if (! isset($this->categoryScores[$category])) {
                $this->categoryScores[$category] = ['total' => 0, 'weight' => 0];
            }
            $this->categoryScores[$category]['total'] += $weightedScore;
            $this->categoryScores[$category]['weight'] += $factor->weight;
        }

        // Normalize to 0-100 scale
        $normalizedScore = $totalWeight > 0
            ? min(100, max(0, round(($totalScore / $totalWeight) * 100)))
            : 50;

        // Generate recommendations based on weak areas
        $this->generateRecommendations($contact);

        return $this->saveLeadScore($contact, $normalizedScore, $config);
    }

    /**
     * Calculate default score when no scoring factors are configured
     */
    private function calculateDefaultScore(Contact $contact, ?UserAiConfiguration $config = null): LeadScore
    {
        // Engagement Score (25%)
        $engagementScore = $this->calculateEngagementScore($contact);
        $this->categoryScores['engagement'] = ['total' => $engagementScore * 0.25, 'weight' => 0.25];

        // Company Fit Score (25%)
        $fitScore = $this->calculateCompanyFitScore($contact);
        $this->categoryScores['firmographic'] = ['total' => $fitScore * 0.25, 'weight' => 0.25];

        // Behavioral Score (25%)
        $behavioralScore = $this->calculateBehavioralScore($contact);
        $this->categoryScores['behavioral'] = ['total' => $behavioralScore * 0.25, 'weight' => 0.25];

        // Demographic Score (25%)
        $demographicScore = $this->calculateDemographicScore($contact);
        $this->categoryScores['demographic'] = ['total' => $demographicScore * 0.25, 'weight' => 0.25];

        $totalScore = ($engagementScore + $fitScore + $behavioralScore + $demographicScore) / 4;
        $normalizedScore = min(100, max(0, round($totalScore)));

        $this->factorDetails = [
            'engagement' => [
                'raw_score' => $engagementScore,
                'weight' => 0.25,
                'weighted_score' => $engagementScore * 0.25,
                'display_name' => 'Engagement',
                'category' => 'engagement',
            ],
            'company_fit' => [
                'raw_score' => $fitScore,
                'weight' => 0.25,
                'weighted_score' => $fitScore * 0.25,
                'display_name' => 'Company Fit',
                'category' => 'firmographic',
            ],
            'behavior' => [
                'raw_score' => $behavioralScore,
                'weight' => 0.25,
                'weighted_score' => $behavioralScore * 0.25,
                'display_name' => 'Behavior',
                'category' => 'behavioral',
            ],
            'demographics' => [
                'raw_score' => $demographicScore,
                'weight' => 0.25,
                'weighted_score' => $demographicScore * 0.25,
                'display_name' => 'Demographics',
                'category' => 'demographic',
            ],
        ];

        $this->generateRecommendations($contact);

        return $this->saveLeadScore($contact, $normalizedScore, $config);
    }

    /**
     * Calculate individual factor score based on configuration
     */
    private function calculateFactorScore(Contact $contact, ScoringFactor $factor): float
    {
        return match ($factor->calculation_method) {
            'rule_based' => $this->calculateRuleBasedScore($contact, $factor),
            'ml_model' => $this->calculateMLModelScore($contact, $factor),
            'api_call' => $this->calculateApiScore($contact, $factor),
            default => 50.0
        };
    }

    /**
     * Calculate rule-based score for a factor
     */
    private function calculateRuleBasedScore(Contact $contact, ScoringFactor $factor): float
    {
        $config = $factor->configuration ?? [];

        return match ($factor->name) {
            'company_size' => $this->calculateCompanySizeScore($contact, $config),
            'industry_match' => $this->calculateIndustryMatchScore($contact, $config),
            'email_engagement' => $this->calculateEmailEngagementFactorScore($contact, $config),
            'website_activity' => $this->calculateWebsiteActivityFactorScore($contact, $config),
            'lead_source_quality' => $this->calculateLeadSourceQualityScore($contact, $config),
            'title_seniority' => $this->calculateTitleSeniorityScore($contact, $config),
            'recency' => $this->calculateRecencyScore($contact, $config),
            'communication_frequency' => $this->calculateCommunicationFrequencyScore($contact, $config),
            'deal_history' => $this->calculateDealHistoryScore($contact, $config),
            'data_completeness' => $this->calculateDataCompletenessScore($contact, $config),
            default => $this->calculateGenericScore($contact, $factor)
        };
    }

    /**
     * Calculate ML model score - uses AI when available
     */
    private function calculateMLModelScore(Contact $contact, ScoringFactor $factor): float
    {
        // Try to use AI for enhanced scoring if configured
        $config = UserAiConfiguration::active()->default()->first();

        if (! $config) {
            // Fallback to rule-based calculation
            return $this->calculateRuleBasedScore($contact, $factor);
        }

        try {
            $aiService = new AiService;
            $result = $this->requestAiScoring($aiService, $config, $contact, $factor);

            if ($result !== null) {
                return $result;
            }
        } catch (\Exception $e) {
            Log::warning('AI scoring failed, using fallback', [
                'contact_id' => $contact->id,
                'factor' => $factor->name,
                'error' => $e->getMessage(),
            ]);
        }

        // Fallback to rule-based
        return $this->calculateRuleBasedScore($contact, $factor);
    }

    /**
     * Request AI-powered scoring
     */
    private function requestAiScoring(AiService $aiService, UserAiConfiguration $config, Contact $contact, ScoringFactor $factor): ?float
    {
        $contactData = $this->prepareContactDataForAi($contact);

        $prompt = $this->buildScoringPrompt($contactData, $factor);

        try {
            $response = $this->makeAiRequest($config, $prompt);

            if (isset($response['score'])) {
                return min(100, max(0, (float) $response['score']));
            }
        } catch (\Exception $e) {
            Log::error('AI scoring request failed', [
                'error' => $e->getMessage(),
                'contact_id' => $contact->id,
            ]);
        }

        return null;
    }

    /**
     * Calculate API-based score
     */
    private function calculateApiScore(Contact $contact, ScoringFactor $factor): float
    {
        // For now, fallback to rule-based scoring
        // In future, this could integrate with external enrichment APIs
        return $this->calculateRuleBasedScore($contact, $factor);
    }

    // =========================================================================
    // ENGAGEMENT SCORING
    // =========================================================================

    /**
     * Calculate overall engagement score
     */
    private function calculateEngagementScore(Contact $contact): float
    {
        $scores = [];

        // Email engagement
        $emailScore = $this->calculateEmailEngagementFactorScore($contact, []);
        $scores[] = $emailScore;

        // Communication engagement
        $commScore = $this->calculateCommunicationEngagementScore($contact);
        $scores[] = $commScore;

        // Activity recency
        $recencyScore = $this->calculateRecencyScore($contact, []);
        $scores[] = $recencyScore;

        return count($scores) > 0 ? array_sum($scores) / count($scores) : 50;
    }

    /**
     * Calculate email engagement score based on real data
     */
    private function calculateEmailEngagementFactorScore(Contact $contact, array $config): float
    {
        $emails = $contact->emails()->where('direction', 'outbound')->get();

        if ($emails->isEmpty()) {
            return 30; // No emails sent yet
        }

        $totalEmails = $emails->count();
        $openedEmails = $emails->whereNotNull('opened_at')->count();
        $clickedEmails = $emails->whereNotNull('clicked_at')->count();

        // Calculate open rate (weight: 40%)
        $openRate = $totalEmails > 0 ? ($openedEmails / $totalEmails) * 100 : 0;
        $openScore = min(100, $openRate * 2.5); // 40% open rate = 100 score

        // Calculate click rate (weight: 60%)
        $clickRate = $openedEmails > 0 ? ($clickedEmails / $openedEmails) * 100 : 0;
        $clickScore = min(100, $clickRate * 5); // 20% CTR = 100 score

        // Recent email activity bonus
        $recentEmails = $emails->where('sent_at', '>=', now()->subDays(30));
        $recencyBonus = $recentEmails->count() > 0 ? 10 : 0;

        $score = ($openScore * 0.4) + ($clickScore * 0.6) + $recencyBonus;

        return min(100, max(0, $score));
    }

    /**
     * Calculate communication engagement score
     */
    private function calculateCommunicationEngagementScore(Contact $contact): float
    {
        $communications = $contact->communications()
            ->where('created_at', '>=', now()->subDays(90))
            ->get();

        if ($communications->isEmpty()) {
            return 20;
        }

        $score = 0;

        // Volume score
        $count = $communications->count();
        $volumeScore = min(40, $count * 5); // Cap at 40 points for volume

        // Recency score
        $mostRecent = $communications->sortByDesc('created_at')->first();
        $daysSinceContact = $mostRecent->created_at->diffInDays(now());
        $recencyScore = match (true) {
            $daysSinceContact <= 7 => 30,
            $daysSinceContact <= 14 => 25,
            $daysSinceContact <= 30 => 20,
            $daysSinceContact <= 60 => 10,
            default => 5
        };

        // Sentiment score
        $avgSentiment = $communications->whereNotNull('sentiment_score')->avg('sentiment_score') ?? 0;
        $sentimentScore = max(0, min(30, ($avgSentiment + 1) * 15)); // -1 to 1 mapped to 0-30

        return min(100, $volumeScore + $recencyScore + $sentimentScore);
    }

    // =========================================================================
    // COMPANY FIT SCORING (FIRMOGRAPHIC)
    // =========================================================================

    /**
     * Calculate company fit score
     */
    private function calculateCompanyFitScore(Contact $contact): float
    {
        if (! $contact->company) {
            return 30; // No company data
        }

        $scores = [];

        // Company size score
        $sizeScore = $this->calculateCompanySizeScore($contact, []);
        $scores[] = $sizeScore;

        // Industry match score
        $industryScore = $this->calculateIndustryMatchScore($contact, []);
        $scores[] = $industryScore;

        // Revenue score
        $revenueScore = $this->calculateRevenueScore($contact);
        $scores[] = $revenueScore;

        return count($scores) > 0 ? array_sum($scores) / count($scores) : 50;
    }

    /**
     * Calculate company size score
     */
    private function calculateCompanySizeScore(Contact $contact, array $config): float
    {
        $employeeCount = $contact->company?->employee_count ?? 0;

        if ($employeeCount === 0) {
            return 30; // Unknown size
        }

        // Default size ranges if not configured
        $sizeRanges = $config['size_ranges'] ?? [
            '1-10' => 40,
            '11-50' => 60,
            '51-200' => 80,
            '201-1000' => 100,
            '1001-5000' => 90,
            '5000+' => 85,
        ];

        foreach ($sizeRanges as $range => $score) {
            if (str_contains($range, '-')) {
                [$min, $max] = explode('-', $range);
                if ($employeeCount >= (int) $min && $employeeCount <= (int) $max) {
                    return (float) $score;
                }
            } elseif (str_contains($range, '+')) {
                $min = (int) str_replace('+', '', $range);
                if ($employeeCount >= $min) {
                    return (float) $score;
                }
            }
        }

        return 50; // Default middle score
    }

    /**
     * Calculate industry match score
     */
    private function calculateIndustryMatchScore(Contact $contact, array $config): float
    {
        $industry = strtolower(trim($contact->company?->industry ?? ''));

        if (empty($industry)) {
            return 30; // Unknown industry
        }

        $targetIndustries = $config['target_industries'] ?? $this->targetIndustries;

        // Check for exact match first
        if (isset($targetIndustries[$industry])) {
            return (float) $targetIndustries[$industry];
        }

        // Check for partial matches
        foreach ($targetIndustries as $targetIndustry => $score) {
            if (str_contains($industry, $targetIndustry) || str_contains($targetIndustry, $industry)) {
                return (float) $score;
            }
        }

        return 40; // Non-target industry
    }

    /**
     * Calculate revenue score
     */
    private function calculateRevenueScore(Contact $contact): float
    {
        $revenue = $contact->company?->annual_revenue ?? 0;

        if ($revenue === 0) {
            return 30;
        }

        return match (true) {
            $revenue >= 100000000 => 95, // $100M+
            $revenue >= 50000000 => 90,  // $50M+
            $revenue >= 10000000 => 85,  // $10M+
            $revenue >= 5000000 => 80,   // $5M+
            $revenue >= 1000000 => 70,   // $1M+
            $revenue >= 500000 => 60,    // $500K+
            $revenue >= 100000 => 50,    // $100K+
            default => 40
        };
    }

    // =========================================================================
    // BEHAVIORAL SCORING
    // =========================================================================

    /**
     * Calculate behavioral score
     */
    private function calculateBehavioralScore(Contact $contact): float
    {
        $scores = [];

        // Lead source quality
        $sourceScore = $this->calculateLeadSourceQualityScore($contact, []);
        $scores[] = $sourceScore;

        // Deal history
        $dealScore = $this->calculateDealHistoryScore($contact, []);
        $scores[] = $dealScore;

        // Communication frequency
        $commScore = $this->calculateCommunicationFrequencyScore($contact, []);
        $scores[] = $commScore;

        return count($scores) > 0 ? array_sum($scores) / count($scores) : 50;
    }

    /**
     * Calculate website activity score
     */
    private function calculateWebsiteActivityFactorScore(Contact $contact, array $config): float
    {
        // Track activity logs for website-related actions
        $webActivities = $contact->activityLogs()
            ->where('created_at', '>=', now()->subDays(30))
            ->whereIn('action', ['page_view', 'website_visit', 'content_download', 'pricing_view', 'demo_request'])
            ->get();

        if ($webActivities->isEmpty()) {
            return 20; // No tracked web activity
        }

        $score = 0;

        // Count high-intent actions
        $highIntentActions = $webActivities->whereIn('action', ['demo_request', 'pricing_view', 'contact_form'])->count();
        $score += min(40, $highIntentActions * 20);

        // Count content engagement
        $contentActions = $webActivities->whereIn('action', ['content_download', 'page_view'])->count();
        $score += min(30, $contentActions * 5);

        // Recency bonus
        $recentActivity = $webActivities->where('created_at', '>=', now()->subDays(7))->count();
        $score += $recentActivity > 0 ? 20 : 0;

        // Frequency bonus
        $score += min(10, $webActivities->count());

        return min(100, $score);
    }

    /**
     * Calculate lead source quality score
     */
    private function calculateLeadSourceQualityScore(Contact $contact, array $config): float
    {
        $source = strtolower(str_replace([' ', '-'], '_', trim($contact->source ?? '')));

        if (empty($source)) {
            return 40; // Unknown source
        }

        $sourceScores = $config['source_scores'] ?? $this->sourceScores;

        // Exact match
        if (isset($sourceScores[$source])) {
            return (float) $sourceScores[$source];
        }

        // Partial match
        foreach ($sourceScores as $key => $score) {
            if (str_contains($source, $key) || str_contains($key, $source)) {
                return (float) $score;
            }
        }

        return 40;
    }

    /**
     * Calculate deal history score
     */
    private function calculateDealHistoryScore(Contact $contact, array $config): float
    {
        $deals = $contact->deals;

        if ($deals->isEmpty()) {
            return 40; // No deal history - neutral
        }

        $score = 50; // Base score with deal history

        // Won deals bonus
        $wonDeals = $deals->where('status', 'won')->count();
        $score += min(30, $wonDeals * 15);

        // Open deals bonus
        $openDeals = $deals->where('status', 'open')->count();
        $score += min(20, $openDeals * 10);

        // High-value deals bonus
        $highValueDeals = $deals->where('value', '>=', 10000)->count();
        $score += min(10, $highValueDeals * 5);

        // Recent deal activity
        $recentDeals = $deals->where('updated_at', '>=', now()->subDays(30));
        $score += $recentDeals->count() > 0 ? 10 : 0;

        // Lost deals penalty (minor)
        $lostDeals = $deals->where('status', 'lost')->count();
        $score -= min(15, $lostDeals * 5);

        return min(100, max(0, $score));
    }

    /**
     * Calculate communication frequency score
     */
    private function calculateCommunicationFrequencyScore(Contact $contact, array $config): float
    {
        $days = $config['lookback_days'] ?? 90;

        $communications = $contact->communications()
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        $emails = $contact->emails()
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        $totalInteractions = $communications->count() + $emails->count();

        if ($totalInteractions === 0) {
            return 20;
        }

        // Score based on frequency
        return match (true) {
            $totalInteractions >= 20 => 100,
            $totalInteractions >= 15 => 90,
            $totalInteractions >= 10 => 80,
            $totalInteractions >= 7 => 70,
            $totalInteractions >= 5 => 60,
            $totalInteractions >= 3 => 50,
            $totalInteractions >= 1 => 40,
            default => 30
        };
    }

    // =========================================================================
    // DEMOGRAPHIC SCORING
    // =========================================================================

    /**
     * Calculate demographic score
     */
    private function calculateDemographicScore(Contact $contact): float
    {
        $scores = [];

        // Title/seniority score
        $seniorityScore = $this->calculateTitleSeniorityScore($contact, []);
        $scores[] = $seniorityScore;

        // Data completeness
        $completenessScore = $this->calculateDataCompletenessScore($contact, []);
        $scores[] = $completenessScore;

        return count($scores) > 0 ? array_sum($scores) / count($scores) : 50;
    }

    /**
     * Calculate title seniority score
     */
    private function calculateTitleSeniorityScore(Contact $contact, array $config): float
    {
        $title = strtolower(trim($contact->title ?? ''));

        if (empty($title)) {
            return 40; // Unknown title
        }

        // Check each seniority level
        foreach ($this->seniorityScores as $level => $keywords) {
            if (empty($keywords)) {
                continue;
            }

            foreach ($keywords as $keyword) {
                if (str_contains($title, $keyword)) {
                    return (float) $this->seniorityValues[$level];
                }
            }
        }

        // Check department for bonus
        $department = strtolower($contact->department ?? '');
        $decisionMakingDepts = ['executive', 'c-suite', 'leadership', 'sales', 'marketing', 'procurement', 'purchasing'];

        foreach ($decisionMakingDepts as $dept) {
            if (str_contains($department, $dept)) {
                return 65;
            }
        }

        return $this->seniorityValues['individual'];
    }

    /**
     * Calculate data completeness score
     */
    private function calculateDataCompletenessScore(Contact $contact, array $config): float
    {
        $fields = [
            'first_name' => 5,
            'last_name' => 5,
            'email' => 15,
            'phone' => 10,
            'title' => 15,
            'department' => 10,
            'company_id' => 20,
            'source' => 10,
            'city' => 5,
            'country' => 5,
        ];

        $score = 0;

        foreach ($fields as $field => $points) {
            $value = $contact->$field;
            if (! empty($value)) {
                $score += $points;
            }
        }

        return min(100, $score);
    }

    /**
     * Calculate recency score
     */
    private function calculateRecencyScore(Contact $contact, array $config): float
    {
        $lastContactedAt = $contact->last_contacted_at;

        if (! $lastContactedAt) {
            // Check for any communications
            $lastComm = $contact->communications()->latest()->first();
            $lastEmail = $contact->emails()->latest()->first();

            $lastContactedAt = max(
                $lastComm?->created_at,
                $lastEmail?->created_at
            );
        }

        if (! $lastContactedAt) {
            return 20; // Never contacted
        }

        $daysSince = $lastContactedAt->diffInDays(now());

        return match (true) {
            $daysSince <= 3 => 100,
            $daysSince <= 7 => 90,
            $daysSince <= 14 => 80,
            $daysSince <= 30 => 65,
            $daysSince <= 60 => 50,
            $daysSince <= 90 => 35,
            default => 20
        };
    }

    /**
     * Calculate generic score for custom factors
     */
    private function calculateGenericScore(Contact $contact, ScoringFactor $factor): float
    {
        $config = $factor->configuration ?? [];

        // Check if there's a custom field to evaluate
        if (isset($config['custom_field'])) {
            $fieldValue = $contact->custom_fields[$config['custom_field']] ?? null;

            if ($fieldValue && isset($config['value_scores'][$fieldValue])) {
                return (float) $config['value_scores'][$fieldValue];
            }
        }

        return 50; // Default neutral score
    }

    // =========================================================================
    // RECOMMENDATIONS
    // =========================================================================

    /**
     * Generate recommendations based on scoring analysis
     */
    private function generateRecommendations(Contact $contact): void
    {
        $this->recommendations = [];

        // Check engagement
        $engagementScore = $this->categoryScores['engagement']['total'] ?? 0;
        $engagementWeight = $this->categoryScores['engagement']['weight'] ?? 1;
        if ($engagementWeight > 0 && ($engagementScore / $engagementWeight) < 50) {
            $this->recommendations[] = [
                'category' => 'engagement',
                'priority' => 'high',
                'action' => 'Increase engagement through personalized email campaigns or direct outreach.',
                'impact' => 'Could improve score by 10-20 points',
            ];
        }

        // Check company fit
        if (! $contact->company) {
            $this->recommendations[] = [
                'category' => 'firmographic',
                'priority' => 'high',
                'action' => 'Add company information to improve lead qualification.',
                'impact' => 'Could improve score by 15-25 points',
            ];
        }

        // Check data completeness
        $completeness = $this->calculateDataCompletenessScore($contact, []);
        if ($completeness < 60) {
            $this->recommendations[] = [
                'category' => 'demographic',
                'priority' => 'medium',
                'action' => 'Complete contact profile (title, department, phone).',
                'impact' => 'Could improve score by 5-15 points',
            ];
        }

        // Check recency
        $recency = $this->calculateRecencyScore($contact, []);
        if ($recency < 50) {
            $this->recommendations[] = [
                'category' => 'engagement',
                'priority' => 'high',
                'action' => 'Schedule follow-up - contact has gone cold.',
                'impact' => 'Could improve score by 15-30 points',
            ];
        }

        // Sort by priority
        usort($this->recommendations, function ($a, $b) {
            $priorities = ['high' => 1, 'medium' => 2, 'low' => 3];

            return ($priorities[$a['priority']] ?? 3) <=> ($priorities[$b['priority']] ?? 3);
        });
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Reset internal state
     */
    private function resetState(): void
    {
        $this->categoryScores = [];
        $this->factorDetails = [];
        $this->recommendations = [];
    }

    /**
     * Save lead score to database
     */
    private function saveLeadScore(Contact $contact, int $score, ?UserAiConfiguration $config = null): LeadScore
    {
        $grade = $this->scoreToGrade($score);
        $probability = $this->scoreToProbability($score);
        $explanations = $this->generateExplanations();

        // Get previous score for history
        $previousScore = $contact->leadScore;

        // Create or update lead score
        $leadScore = LeadScore::updateOrCreate(
            ['contact_id' => $contact->id],
            [
                'score' => $score,
                'probability' => $probability,
                'grade' => $grade,
                'factors' => $this->factorDetails,
                'explanations' => $explanations,
                'model_version' => '2.0',
                'last_calculated_at' => now(),
                'ai_model_id' => $config?->getDefaultModel('lead_scoring'),
                'raw_predictions' => [
                    'category_scores' => $this->categoryScores,
                    'recommendations' => $this->recommendations,
                ],
            ]
        );

        // Record history if score changed
        if ($previousScore && $previousScore->score !== $score) {
            $this->recordScoreHistory($leadScore, $previousScore->score, $score, 'recalculation');
        } elseif (! $previousScore) {
            $this->recordScoreHistory($leadScore, null, $score, 'initial_calculation');
        }

        return $leadScore;
    }

    /**
     * Record score history
     */
    private function recordScoreHistory(LeadScore $leadScore, ?int $previousScore, int $newScore, string $reason): void
    {
        try {
            LeadScoreHistory::create([
                'lead_score_id' => $leadScore->id,
                'contact_id' => $leadScore->contact_id,
                'previous_score' => $previousScore,
                'new_score' => $newScore,
                'change_reason' => $reason,
                'factors_snapshot' => $this->factorDetails,
                'changed_by' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            // History table might not exist yet
            Log::debug('Could not record score history', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Convert score to grade
     */
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

    /**
     * Convert score to probability
     */
    private function scoreToProbability(int $score): float
    {
        // Use sigmoid-like function for more realistic probability
        // Scores below 50 have much lower probability
        return round($score / 100, 4);
    }

    /**
     * Generate human-readable explanations
     */
    private function generateExplanations(): array
    {
        $explanations = [];

        foreach ($this->factorDetails as $factorName => $data) {
            $score = $data['raw_score'];
            $level = match (true) {
                $score >= 80 => 'excellent',
                $score >= 60 => 'good',
                $score >= 40 => 'fair',
                default => 'needs improvement'
            };

            $explanations[] = "{$data['display_name']} shows {$level} indicators (score: {$score}).";
        }

        return $explanations;
    }

    /**
     * Prepare contact data for AI scoring
     */
    private function prepareContactDataForAi(Contact $contact): array
    {
        return [
            'name' => $contact->full_name,
            'title' => $contact->title,
            'department' => $contact->department,
            'source' => $contact->source,
            'company' => [
                'name' => $contact->company?->name,
                'industry' => $contact->company?->industry,
                'employee_count' => $contact->company?->employee_count,
                'annual_revenue' => $contact->company?->annual_revenue,
            ],
            'engagement' => [
                'email_count' => $contact->emails()->count(),
                'emails_opened' => $contact->emails()->whereNotNull('opened_at')->count(),
                'emails_clicked' => $contact->emails()->whereNotNull('clicked_at')->count(),
                'communications' => $contact->communications()->count(),
                'last_contacted' => $contact->last_contacted_at?->toDateString(),
            ],
            'deals' => [
                'total' => $contact->deals()->count(),
                'won' => $contact->deals()->where('status', 'won')->count(),
                'open' => $contact->deals()->where('status', 'open')->count(),
                'total_value' => $contact->deals()->sum('value'),
            ],
        ];
    }

    /**
     * Build AI scoring prompt
     */
    private function buildScoringPrompt(array $contactData, ScoringFactor $factor): string
    {
        $data = json_encode($contactData, JSON_PRETTY_PRINT);

        return <<<PROMPT
Analyze this lead and provide a score from 0-100 for the "{$factor->display_name}" factor.

Factor description: {$factor->description}
Category: {$factor->category}

Lead data:
{$data}

Return a JSON response with:
{
    "score": <number between 0-100>,
    "reasoning": "<brief explanation>"
}
PROMPT;
    }

    /**
     * Make AI request
     */
    private function makeAiRequest(UserAiConfiguration $config, string $prompt): ?array
    {
        $provider = $config->aiProvider->slug;

        return match ($provider) {
            'openai' => $this->makeOpenAiRequest($config, $prompt),
            'anthropic' => $this->makeAnthropicRequest($config, $prompt),
            default => null
        };
    }

    /**
     * Make OpenAI request using gpt-5-mini
     */
    private function makeOpenAiRequest(UserAiConfiguration $config, string $prompt): ?array
    {
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer '.$config->getCredential('api_key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $config->getDefaultModel('lead_scoring') ?? 'gpt-5-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are an expert lead scoring analyst. Return only valid JSON.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 200,
            'temperature' => 0.2,
        ]);

        if ($response->successful()) {
            $content = $response->json()['choices'][0]['message']['content'] ?? '';

            return json_decode($content, true);
        }

        return null;
    }

    /**
     * Make Anthropic request using opus-4
     */
    private function makeAnthropicRequest(UserAiConfiguration $config, string $prompt): ?array
    {
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'x-api-key' => $config->getCredential('api_key'),
            'Content-Type' => 'application/json',
            'anthropic-version' => '2023-06-01',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => $config->getDefaultModel('lead_scoring') ?? 'claude-opus-4-20250514',
            'max_tokens' => 200,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        if ($response->successful()) {
            $content = $response->json()['content'][0]['text'] ?? '';

            return json_decode($content, true);
        }

        return null;
    }

    // =========================================================================
    // PUBLIC API
    // =========================================================================

    /**
     * Get category scores breakdown
     */
    public function getCategoryScores(): array
    {
        return $this->categoryScores;
    }

    /**
     * Get recommendations
     */
    public function getRecommendations(): array
    {
        return $this->recommendations;
    }

    /**
     * Manually adjust score with reason tracking
     */
    public function adjustScore(LeadScore $leadScore, int $adjustment, string $reason): LeadScore
    {
        $previousScore = $leadScore->score;
        $newScore = min(100, max(0, $leadScore->score + $adjustment));

        $leadScore->update([
            'score' => $newScore,
            'grade' => $this->scoreToGrade($newScore),
            'probability' => $this->scoreToProbability($newScore),
            'last_calculated_at' => now(),
        ]);

        $this->recordScoreHistory($leadScore, $previousScore, $newScore, "Manual adjustment: {$reason}");

        return $leadScore->fresh();
    }

    /**
     * Bulk calculate scores for multiple contacts
     */
    public function calculateBulkScores(Collection $contacts, ?UserAiConfiguration $config = null): array
    {
        $results = [];

        foreach ($contacts as $contact) {
            try {
                $results[$contact->id] = [
                    'success' => true,
                    'lead_score' => $this->calculateScore($contact, $config),
                ];
            } catch (\Exception $e) {
                $results[$contact->id] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }
}
