<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\Contact;
use App\Models\Company;
use App\Models\PriceOptimization;
use Illuminate\Support\Facades\Log;

class PriceOptimizationService
{
    public function optimizePrice(Deal $deal): PriceOptimization
    {
        try {
            $factors = $this->analyzePriceFactors($deal);
            $marketAnalysis = $this->analyzeMarket($deal);
            $competitorPricing = $this->getCompetitorPricing($deal);
            $historicalData = $this->getHistoricalComparison($deal);
            $suggestedPrice = $this->calculateOptimalPrice($deal, $factors);
            $confidence = $this->calculateConfidence($factors);
            $discountRecs = $this->generateDiscountRecommendations($deal, $suggestedPrice);
            $strategy = $this->recommendPricingStrategy($deal, $factors);
            $marginAnalysis = $this->analyzeMargins($deal, $suggestedPrice);

            return PriceOptimization::updateOrCreate(
                ['deal_id' => $deal->id],
                [
                    'contact_id' => $deal->contact_id,
                    'company_id' => $deal->company_id,
                    'suggested_price' => $suggestedPrice,
                    'confidence_score' => $confidence,
                    'price_factors' => $factors,
                    'market_analysis' => $marketAnalysis,
                    'competitor_pricing' => $competitorPricing,
                    'historical_comparison' => $historicalData,
                    'discount_recommendations' => $discountRecs,
                    'pricing_strategy' => $strategy,
                    'margin_analysis' => $marginAnalysis,
                    'model_version' => '1.0',
                    'last_calculated_at' => now(),
                ]
            );
        } catch (\Exception $e) {
            Log::error('Price optimization failed', [
                'deal_id' => $deal->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function analyzePriceFactors(Deal $deal): array
    {
        $factors = [];

        // Company size factor
        if ($deal->company) {
            $employeeCount = $deal->company->employee_count ?? 0;
            $factors['company_size'] = [
                'weight' => 0.25,
                'value' => $employeeCount,
                'impact' => $employeeCount > 500 ? 'increase' : 'decrease',
                'description' => "Company size: {$employeeCount} employees"
            ];
        }

        // Deal size factor
        $dealValue = $deal->value ?? 0;
        $factors['deal_size'] = [
            'weight' => 0.3,
            'value' => $dealValue,
            'impact' => $dealValue > 50000 ? 'volume_discount' : 'premium_pricing',
            'description' => "Deal size: $" . number_format($dealValue)
        ];

        // Urgency factor
        if ($deal->expected_close_date) {
            $daysToClose = now()->diffInDays($deal->expected_close_date);
            $factors['urgency'] = [
                'weight' => 0.15,
                'value' => $daysToClose,
                'impact' => $daysToClose < 30 ? 'premium' : 'standard',
                'description' => "Timeline: {$daysToClose} days to close"
            ];
        }

        // Pipeline stage factor
        if ($deal->pipelineStage) {
            $probability = $deal->pipelineStage->probability ?? 0;
            $factors['stage_probability'] = [
                'weight' => 0.2,
                'value' => $probability,
                'impact' => $probability > 0.7 ? 'standard' : 'discount_needed',
                'description' => "Stage probability: " . ($probability * 100) . "%"
            ];
        }

        // Competitive factor
        $competitorCount = $this->getCompetitorCount($deal);
        $factors['competition'] = [
            'weight' => 0.1,
            'value' => $competitorCount,
            'impact' => $competitorCount > 2 ? 'competitive_pricing' : 'premium_opportunity',
            'description' => "Competitors in deal: {$competitorCount}"
        ];

        return $factors;
    }

    private function analyzeMarket(Deal $deal): array
    {
        return [
            'market_segment' => $this->getMarketSegment($deal),
            'price_sensitivity' => $this->analyzePriceSensitivity($deal),
            'market_trends' => $this->getMarketTrends($deal),
            'seasonal_factors' => $this->getSeasonalFactors(),
        ];
    }

    private function getCompetitorPricing(Deal $deal): array
    {
        // Mock competitor pricing data
        return [
            'average_market_price' => $this->getAverageMarketPrice($deal),
            'price_range' => [
                'low' => ($deal->value ?? 10000) * 0.8,
                'high' => ($deal->value ?? 10000) * 1.3
            ],
            'competitor_positions' => [
                'premium' => ['Salesforce', 'Microsoft'],
                'value' => ['HubSpot', 'Pipedrive'],
                'budget' => ['Zoho', 'Freshworks']
            ]
        ];
    }

    private function getHistoricalComparison(Deal $deal): array
    {
        // Analyze similar historical deals
        $similarDeals = Deal::where('company_id', $deal->company_id)
            ->orWhere('value', '>', ($deal->value ?? 0) * 0.8)
            ->where('value', '<', ($deal->value ?? 0) * 1.2)
            ->where('id', '!=', $deal->id)
            ->limit(10)
            ->get();

        return [
            'similar_deals_count' => $similarDeals->count(),
            'average_value' => $similarDeals->avg('value') ?? 0,
            'win_rate' => $this->calculateHistoricalWinRate($similarDeals),
            'average_sales_cycle' => $this->calculateAverageSalesCycle($similarDeals),
        ];
    }

    private function calculateOptimalPrice(Deal $deal, array $factors): float
    {
        $basePrice = $deal->value ?? 10000;
        $adjustment = 1.0;

        foreach ($factors as $factor) {
            $weight = $factor['weight'];
            $impact = match ($factor['impact']) {
                'increase', 'premium', 'premium_opportunity' => 1 + ($weight * 0.1),
                'decrease', 'volume_discount', 'competitive_pricing' => 1 - ($weight * 0.05),
                default => 1.0
            };
            $adjustment *= $impact;
        }

        return round($basePrice * $adjustment, 2);
    }

    private function calculateConfidence(array $factors): float
    {
        $totalWeight = array_sum(array_column($factors, 'weight'));
        $completeness = min(1.0, $totalWeight / 1.0); // Expected total weight is 1.0
        
        return round($completeness * 0.9, 4); // Max 90% confidence for mock data
    }

    private function generateDiscountRecommendations(Deal $deal, float $suggestedPrice): array
    {
        $currentPrice = $deal->value ?? $suggestedPrice;
        $recommendations = [];

        if ($suggestedPrice < $currentPrice) {
            $discountPercent = round((($currentPrice - $suggestedPrice) / $currentPrice) * 100, 1);
            
            $recommendations[] = [
                'type' => 'competitive_discount',
                'percentage' => $discountPercent,
                'amount' => $currentPrice - $suggestedPrice,
                'justification' => 'Market analysis suggests competitive pricing needed',
                'conditions' => ['Quick decision required', 'Multi-year commitment']
            ];
        }

        // Volume discount
        if ($currentPrice > 50000) {
            $recommendations[] = [
                'type' => 'volume_discount',
                'percentage' => 5,
                'amount' => $currentPrice * 0.05,
                'justification' => 'Volume pricing for large deals',
                'conditions' => ['Minimum order value', 'Payment terms']
            ];
        }

        // Early bird discount
        if ($deal->expected_close_date && now()->diffInDays($deal->expected_close_date) > 60) {
            $recommendations[] = [
                'type' => 'early_bird',
                'percentage' => 3,
                'amount' => $currentPrice * 0.03,
                'justification' => 'Incentive for early commitment',
                'conditions' => ['Sign within 30 days']
            ];
        }

        return $recommendations;
    }

    private function recommendPricingStrategy(Deal $deal, array $factors): string
    {
        $competitionLevel = $factors['competition']['value'] ?? 0;
        $dealSize = $factors['deal_size']['value'] ?? 0;
        $urgency = $factors['urgency']['value'] ?? 60;

        if ($competitionLevel > 2 && $urgency < 30) {
            return 'competitive_aggressive';
        } elseif ($dealSize > 100000) {
            return 'value_based';
        } elseif ($urgency < 15) {
            return 'premium_opportunity';
        } else {
            return 'market_standard';
        }
    }

    private function analyzeMargins(Deal $deal, float $suggestedPrice): array
    {
        $baselineMargin = 0.40; // 40% baseline margin
        $costs = $suggestedPrice * (1 - $baselineMargin);
        
        return [
            'suggested_margin' => round((($suggestedPrice - $costs) / $suggestedPrice) * 100, 2),
            'margin_amount' => $suggestedPrice - $costs,
            'cost_breakdown' => [
                'product_cost' => $costs * 0.6,
                'sales_cost' => $costs * 0.2,
                'support_cost' => $costs * 0.1,
                'overhead' => $costs * 0.1
            ]
        ];
    }

    // Helper methods
    private function getCompetitorCount(Deal $deal): int
    {
        // In real implementation, this would check competitive intelligence data
        return rand(1, 4);
    }

    private function getMarketSegment(Deal $deal): string
    {
        $value = $deal->value ?? 0;
        return match (true) {
            $value > 100000 => 'enterprise',
            $value > 25000 => 'mid_market',
            default => 'small_business'
        };
    }

    private function analyzePriceSensitivity(Deal $deal): string
    {
        // Mock price sensitivity analysis
        return $deal->company && $deal->company->industry === 'Non-profit' ? 'high' : 'medium';
    }

    private function getMarketTrends(Deal $deal): array
    {
        return [
            'trend_direction' => 'stable',
            'price_pressure' => 'medium',
            'market_growth' => 'positive'
        ];
    }

    private function getSeasonalFactors(): array
    {
        $month = now()->month;
        return [
            'quarter_end_pressure' => in_array($month, [3, 6, 9, 12]),
            'holiday_season' => in_array($month, [11, 12]),
            'budget_cycle' => $month === 1
        ];
    }

    private function getAverageMarketPrice(Deal $deal): float
    {
        // Mock market price calculation
        return ($deal->value ?? 10000) * (1 + (rand(-10, 10) / 100));
    }

    private function calculateHistoricalWinRate($deals): float
    {
        // Mock win rate calculation
        return 0.65; // 65% win rate
    }

    private function calculateAverageSalesCycle($deals): int
    {
        // Mock sales cycle calculation
        return 45; // 45 days average
    }
}