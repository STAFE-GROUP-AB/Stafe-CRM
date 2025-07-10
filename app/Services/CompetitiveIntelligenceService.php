<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\CompetitiveIntelligence;
use Illuminate\Support\Facades\Log;

class CompetitiveIntelligenceService
{
    public function analyzeCompetition(Deal $deal, string $competitorName): CompetitiveIntelligence
    {
        try {
            $strengths = $this->analyzeCompetitorStrengths($competitorName);
            $weaknesses = $this->analyzeCompetitorWeaknesses($competitorName);
            $factors = $this->analyzeCompetitiveFactors($deal, $competitorName);
            $probability = $this->calculateWinProbability($deal, $factors);
            $battleCards = $this->generateBattleCards($competitorName, $strengths, $weaknesses);
            $pricingComparison = $this->analyzePricing($competitorName);
            $featureComparison = $this->analyzeFeatures($competitorName);

            return CompetitiveIntelligence::updateOrCreate(
                [
                    'deal_id' => $deal->id,
                    'competitor_name' => $competitorName
                ],
                [
                    'competitor_strength' => $strengths,
                    'competitor_weaknesses' => $weaknesses,
                    'competitive_factors' => $factors,
                    'win_loss_probability' => $probability,
                    'battle_card_recommendations' => $battleCards,
                    'pricing_comparison' => $pricingComparison,
                    'feature_comparison' => $featureComparison,
                    'source' => 'ai_analysis',
                    'confidence_score' => 0.80,
                    'last_updated_at' => now(),
                ]
            );
        } catch (\Exception $e) {
            Log::error('Competitive intelligence analysis failed', [
                'deal_id' => $deal->id,
                'competitor' => $competitorName,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function analyzeCompetitorStrengths(string $competitor): array
    {
        // Mock data - in real implementation, this would pull from external sources
        $competitorData = [
            'Salesforce' => [
                'Brand Recognition' => 'Market leader with strong brand presence',
                'Feature Completeness' => 'Comprehensive platform with extensive features',
                'Integration Ecosystem' => 'Large number of third-party integrations',
                'Enterprise Focus' => 'Strong enterprise-grade security and compliance'
            ],
            'HubSpot' => [
                'Ease of Use' => 'User-friendly interface and setup',
                'Inbound Marketing' => 'Strong marketing automation capabilities',
                'Free Tier' => 'Attractive free offering for small businesses',
                'Content Marketing' => 'Excellent educational content and resources'
            ],
            'Pipedrive' => [
                'Simplicity' => 'Clean, simple interface focused on sales',
                'Pipeline Management' => 'Excellent visual pipeline management',
                'Pricing' => 'Competitive pricing for small to medium businesses',
                'Sales Focus' => 'Purpose-built for sales teams'
            ]
        ];

        return $competitorData[$competitor] ?? [
            'Unknown Competitor' => 'Limited information available'
        ];
    }

    private function analyzeCompetitorWeaknesses(string $competitor): array
    {
        $weaknessData = [
            'Salesforce' => [
                'Complexity' => 'Steep learning curve and complex implementation',
                'Cost' => 'Expensive, especially for smaller organizations',
                'Customization Overhead' => 'Requires significant customization effort',
                'User Adoption' => 'Can be overwhelming for non-technical users'
            ],
            'HubSpot' => [
                'Limited Customization' => 'Less flexible for complex business processes',
                'Reporting Limitations' => 'Basic reporting compared to enterprise solutions',
                'Enterprise Features' => 'Missing some advanced enterprise features',
                'Integration Depth' => 'Shallow integrations with some platforms'
            ],
            'Pipedrive' => [
                'Limited Features' => 'Lacks advanced CRM features',
                'Marketing Automation' => 'Weak marketing automation capabilities',
                'Reporting' => 'Basic reporting and analytics',
                'Enterprise Scaling' => 'Not suitable for large enterprises'
            ]
        ];

        return $weaknessData[$competitor] ?? [
            'Information Gap' => 'Need more intelligence on this competitor'
        ];
    }

    private function analyzeCompetitiveFactors(Deal $deal, string $competitor): array
    {
        $factors = [];

        // Deal size factor
        $dealValue = $deal->value ?? 0;
        if ($dealValue > 100000) {
            $factors['enterprise_deal'] = [
                'weight' => 0.3,
                'advantage' => $competitor === 'Salesforce' ? 'competitor' : 'us',
                'description' => 'Large enterprise deals favor established players'
            ];
        } else {
            $factors['smb_deal'] = [
                'weight' => 0.3,
                'advantage' => $competitor === 'HubSpot' ? 'competitor' : 'us',
                'description' => 'Small/medium business deals favor user-friendly solutions'
            ];
        }

        // Industry factor
        if ($deal->company && $deal->company->industry) {
            $factors['industry_fit'] = [
                'weight' => 0.2,
                'advantage' => 'neutral',
                'description' => 'Industry-specific requirements analysis needed'
            ];
        }

        // Timeline factor
        if ($deal->expected_close_date && $deal->expected_close_date->diffInDays(now()) < 30) {
            $factors['urgent_timeline'] = [
                'weight' => 0.25,
                'advantage' => 'us',
                'description' => 'Quick implementation timeline favors agile solutions'
            ];
        }

        // Budget consideration
        $factors['budget_constraints'] = [
            'weight' => 0.25,
            'advantage' => $competitor === 'Salesforce' ? 'us' : 'competitor',
            'description' => 'Budget considerations in competitive analysis'
        ];

        return $factors;
    }

    private function calculateWinProbability(Deal $deal, array $factors): float
    {
        $baseScore = 0.5; // 50% baseline
        $adjustment = 0;

        foreach ($factors as $factor) {
            $weight = $factor['weight'];
            $impact = match ($factor['advantage']) {
                'us' => $weight * 0.3,
                'competitor' => -$weight * 0.3,
                default => 0
            };
            $adjustment += $impact;
        }

        return max(0.1, min(0.9, $baseScore + $adjustment));
    }

    private function generateBattleCards(string $competitor, array $strengths, array $weaknesses): array
    {
        $battleCards = [];

        // Counter their strengths
        foreach ($strengths as $strength => $description) {
            $battleCards['counter_' . strtolower(str_replace(' ', '_', $strength))] = [
                'competitor_claim' => $description,
                'our_response' => $this->generateCounter($strength),
                'supporting_evidence' => $this->getSupportingEvidence($strength)
            ];
        }

        // Exploit their weaknesses
        foreach ($weaknesses as $weakness => $description) {
            $battleCards['exploit_' . strtolower(str_replace(' ', '_', $weakness))] = [
                'competitor_weakness' => $description,
                'our_advantage' => $this->generateAdvantage($weakness),
                'positioning' => $this->getPositioning($weakness)
            ];
        }

        return $battleCards;
    }

    private function generateCounter(string $strength): string
    {
        $counters = [
            'Brand Recognition' => 'We offer personalized service and innovative features that large enterprises lack',
            'Feature Completeness' => 'Our focused feature set reduces complexity and improves user adoption',
            'Integration Ecosystem' => 'We provide deeper, more meaningful integrations with key business tools',
            'Enterprise Focus' => 'We deliver enterprise-grade features with small business simplicity',
            'Ease of Use' => 'We combine ease of use with powerful enterprise capabilities',
            'Free Tier' => 'Our value proposition includes comprehensive support and advanced features',
            'Simplicity' => 'We offer simplicity without sacrificing the advanced features you need'
        ];

        return $counters[$strength] ?? 'We provide a unique value proposition in this area';
    }

    private function getSupportingEvidence(string $strength): array
    {
        return [
            'customer_testimonials' => 'Customer feedback on our superior approach',
            'feature_comparison' => 'Side-by-side feature comparison showing our advantages',
            'case_studies' => 'Success stories demonstrating real-world benefits'
        ];
    }

    private function generateAdvantage(string $weakness): string
    {
        $advantages = [
            'Complexity' => 'Our intuitive interface reduces training time by 60%',
            'Cost' => 'We deliver 40% more value at 30% lower total cost of ownership',
            'Limited Customization' => 'Extensive customization options to fit your unique workflow',
            'Limited Features' => 'Comprehensive feature set that grows with your business'
        ];

        return $advantages[$weakness] ?? 'We excel where they fall short';
    }

    private function getPositioning(string $weakness): string
    {
        return "Position our solution as the better alternative that addresses their specific weakness in {$weakness}";
    }

    private function analyzePricing(string $competitor): array
    {
        // Mock pricing data
        return [
            'competitor_price_range' => 'Unknown - requires research',
            'our_price_advantage' => 'Competitive pricing with better ROI',
            'value_comparison' => 'Superior value proposition analysis needed'
        ];
    }

    private function analyzeFeatures(string $competitor): array
    {
        // Mock feature comparison
        return [
            'unique_features' => ['AI-powered insights', 'Advanced automation', 'Integrated communications'],
            'parity_features' => ['Contact management', 'Deal tracking', 'Reporting'],
            'missing_features' => []
        ];
    }

    public function trackWinLoss(Deal $deal, string $outcome, ?string $competitor = null): void
    {
        // Track win/loss data for model improvement
        Log::info('Win/Loss tracked', [
            'deal_id' => $deal->id,
            'outcome' => $outcome,
            'competitor' => $competitor,
            'deal_value' => $deal->value
        ]);

        // In real implementation, this would update ML models
    }
}