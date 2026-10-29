<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\ForecastSimulation;
use App\Models\PipelineStage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PredictiveForecastingService
{
    /**
     * Generate revenue forecast using historical data and ML predictions
     */
    public function generateForecast(array $parameters): array
    {
        $months = $parameters['forecast_months'] ?? 6;
        $includeSeasonality = $parameters['include_seasonality'] ?? true;
        $confidenceLevel = $parameters['confidence_level'] ?? 0.95;

        // Get historical data
        $historicalData = $this->getHistoricalData($parameters['lookback_months'] ?? 12);

        // Calculate baseline metrics
        $baseline = $this->calculateBaseline($historicalData);

        // Apply ML predictions
        $predictions = $this->generatePredictions($historicalData, $months, $includeSeasonality);

        // Calculate confidence intervals
        $confidenceIntervals = $this->calculateConfidenceIntervals($predictions, $confidenceLevel);

        // Generate scenarios
        $scenarios = $this->generateScenarios($baseline, $predictions);

        return [
            'baseline' => $baseline,
            'predictions' => $predictions,
            'confidence_intervals' => $confidenceIntervals,
            'scenarios' => $scenarios,
            'accuracy_metrics' => $this->calculateAccuracyMetrics($historicalData),
        ];
    }

    /**
     * Get historical deal data
     */
    protected function getHistoricalData(int $months): Collection
    {
        return Deal::where('created_at', '>=', now()->subMonths($months))
            ->with(['pipelineStage'])
            ->get()
            ->groupBy(function ($deal) {
                return $deal->created_at->format('Y-m');
            })
            ->map(function ($deals, $month) {
                return [
                    'month' => $month,
                    'total_deals' => $deals->count(),
                    'total_value' => $deals->sum('value'),
                    'won_deals' => $deals->filter(fn($d) => $d->pipelineStage?->is_won)->count(),
                    'won_value' => $deals->filter(fn($d) => $d->pipelineStage?->is_won)->sum('value'),
                    'lost_deals' => $deals->filter(fn($d) => $d->pipelineStage?->is_lost)->count(),
                    'average_deal_size' => $deals->avg('value'),
                    'win_rate' => $deals->count() > 0 
                        ? ($deals->filter(fn($d) => $d->pipelineStage?->is_won)->count() / $deals->count()) * 100 
                        : 0,
                ];
            });
    }

    /**
     * Calculate baseline metrics
     */
    protected function calculateBaseline(Collection $historicalData): array
    {
        return [
            'average_monthly_revenue' => $historicalData->avg('won_value'),
            'average_deal_size' => $historicalData->avg('average_deal_size'),
            'average_win_rate' => $historicalData->avg('win_rate'),
            'average_deals_per_month' => $historicalData->avg('total_deals'),
            'growth_rate' => $this->calculateGrowthRate($historicalData),
            'volatility' => $this->calculateVolatility($historicalData),
        ];
    }

    /**
     * Generate ML-based predictions
     */
    protected function generatePredictions(Collection $historicalData, int $months, bool $includeSeasonality): array
    {
        $predictions = [];
        $lastMonth = $historicalData->last();

        for ($i = 1; $i <= $months; $i++) {
            $prediction = $this->predictMonth($historicalData, $i, $includeSeasonality);
            $predictions[] = $prediction;
        }

        return $predictions;
    }

    /**
     * Predict values for a specific month
     */
    protected function predictMonth(Collection $historicalData, int $monthsAhead, bool $includeSeasonality): array
    {
        $targetMonth = now()->addMonths($monthsAhead);

        // Linear regression for trend
        $trend = $this->calculateTrend($historicalData);
        
        // Seasonal adjustment
        $seasonalFactor = $includeSeasonality 
            ? $this->getSeasonalFactor($targetMonth->month, $historicalData) 
            : 1.0;

        // Base prediction using trend
        $baseRevenue = $trend['intercept'] + ($trend['slope'] * $monthsAhead);
        
        // Apply seasonal adjustment
        $predictedRevenue = $baseRevenue * $seasonalFactor;

        // Predict deal count
        $avgDealsPerMonth = $historicalData->avg('total_deals');
        $predictedDeals = round($avgDealsPerMonth * $seasonalFactor);

        // Predict win rate (with slight regression to mean)
        $avgWinRate = $historicalData->avg('win_rate');
        $recentWinRate = $historicalData->take(-3)->avg('win_rate');
        $predictedWinRate = ($avgWinRate * 0.3) + ($recentWinRate * 0.7);

        return [
            'month' => $targetMonth->format('Y-m'),
            'predicted_revenue' => max(0, $predictedRevenue),
            'predicted_deals' => max(0, $predictedDeals),
            'predicted_win_rate' => max(0, min(100, $predictedWinRate)),
            'predicted_average_deal_size' => $predictedDeals > 0 ? $predictedRevenue / $predictedDeals : 0,
            'seasonal_factor' => $seasonalFactor,
            'confidence' => $this->calculatePredictionConfidence($monthsAhead),
        ];
    }

    /**
     * Calculate linear regression trend
     */
    protected function calculateTrend(Collection $historicalData): array
    {
        $n = $historicalData->count();
        $x = range(1, $n);
        $y = $historicalData->pluck('won_value')->toArray();

        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $y[$i];
            $sumX2 += $x[$i] * $x[$i];
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;

        return [
            'slope' => $slope,
            'intercept' => $intercept,
            'r_squared' => $this->calculateRSquared($x, $y, $slope, $intercept),
        ];
    }

    /**
     * Calculate R-squared for trend
     */
    protected function calculateRSquared(array $x, array $y, float $slope, float $intercept): float
    {
        $n = count($x);
        $yMean = array_sum($y) / $n;
        
        $ssTotal = 0;
        $ssResidual = 0;

        for ($i = 0; $i < $n; $i++) {
            $yPredicted = $slope * $x[$i] + $intercept;
            $ssTotal += pow($y[$i] - $yMean, 2);
            $ssResidual += pow($y[$i] - $yPredicted, 2);
        }

        return $ssTotal > 0 ? 1 - ($ssResidual / $ssTotal) : 0;
    }

    /**
     * Get seasonal factor for a month
     */
    protected function getSeasonalFactor(int $month, Collection $historicalData): float
    {
        // Calculate average for this month across historical data
        $monthData = $historicalData->filter(function ($data) use ($month) {
            return (int) substr($data['month'], 5, 2) === $month;
        });

        if ($monthData->isEmpty()) {
            return 1.0;
        }

        $monthAvg = $monthData->avg('won_value');
        $overallAvg = $historicalData->avg('won_value');

        return $overallAvg > 0 ? $monthAvg / $overallAvg : 1.0;
    }

    /**
     * Calculate confidence intervals
     */
    protected function calculateConfidenceIntervals(array $predictions, float $confidenceLevel): array
    {
        $zScore = $this->getZScore($confidenceLevel);
        
        return array_map(function ($prediction) use ($zScore) {
            $stdError = $prediction['predicted_revenue'] * (1 - $prediction['confidence']);
            $marginOfError = $zScore * $stdError;

            return [
                'month' => $prediction['month'],
                'lower_bound' => max(0, $prediction['predicted_revenue'] - $marginOfError),
                'upper_bound' => $prediction['predicted_revenue'] + $marginOfError,
                'margin_of_error' => $marginOfError,
            ];
        }, $predictions);
    }

    /**
     * Get Z-score for confidence level
     */
    protected function getZScore(float $confidenceLevel): float
    {
        return match ($confidenceLevel) {
            0.90 => 1.645,
            0.95 => 1.96,
            0.99 => 2.576,
            default => 1.96,
        };
    }

    /**
     * Calculate prediction confidence (decreases with time)
     */
    protected function calculatePredictionConfidence(int $monthsAhead): float
    {
        // Confidence decreases exponentially with time
        return max(0.5, 0.95 * exp(-0.1 * $monthsAhead));
    }

    /**
     * Generate forecast scenarios (optimistic, realistic, pessimistic)
     */
    protected function generateScenarios(array $baseline, array $predictions): array
    {
        return [
            'optimistic' => $this->generateOptimisticScenario($baseline, $predictions),
            'realistic' => $predictions,
            'pessimistic' => $this->generatePessimisticScenario($baseline, $predictions),
        ];
    }

    /**
     * Generate optimistic scenario (20% above baseline)
     */
    protected function generateOptimisticScenario(array $baseline, array $predictions): array
    {
        return array_map(function ($prediction) {
            return array_merge($prediction, [
                'predicted_revenue' => $prediction['predicted_revenue'] * 1.20,
                'predicted_deals' => round($prediction['predicted_deals'] * 1.15),
                'scenario' => 'optimistic',
            ]);
        }, $predictions);
    }

    /**
     * Generate pessimistic scenario (20% below baseline)
     */
    protected function generatePessimisticScenario(array $baseline, array $predictions): array
    {
        return array_map(function ($prediction) {
            return array_merge($prediction, [
                'predicted_revenue' => $prediction['predicted_revenue'] * 0.80,
                'predicted_deals' => round($prediction['predicted_deals'] * 0.85),
                'scenario' => 'pessimistic',
            ]);
        }, $predictions);
    }

    /**
     * Calculate growth rate from historical data
     */
    protected function calculateGrowthRate(Collection $historicalData): float
    {
        if ($historicalData->count() < 2) {
            return 0;
        }

        $first = $historicalData->first()['won_value'];
        $last = $historicalData->last()['won_value'];

        if ($first == 0) {
            return 0;
        }

        return (($last - $first) / $first) * 100;
    }

    /**
     * Calculate volatility (standard deviation)
     */
    protected function calculateVolatility(Collection $historicalData): float
    {
        $values = $historicalData->pluck('won_value')->toArray();
        $mean = array_sum($values) / count($values);
        
        $variance = array_sum(array_map(fn($v) => pow($v - $mean, 2), $values)) / count($values);
        
        return sqrt($variance);
    }

    /**
     * Calculate forecast accuracy metrics
     */
    protected function calculateAccuracyMetrics(Collection $historicalData): array
    {
        // Use last 3 months as test set
        $testData = $historicalData->take(-3);
        $trainData = $historicalData->take(-($historicalData->count() - 3));

        if ($testData->isEmpty() || $trainData->isEmpty()) {
            return [
                'mae' => null,
                'rmse' => null,
                'mape' => null,
            ];
        }

        $errors = [];
        $percentErrors = [];

        foreach ($testData as $actual) {
            // Make prediction for this month using training data
            $predicted = $this->predictSingleMonth($trainData);
            $actualValue = $actual['won_value'];
            
            $error = abs($actualValue - $predicted);
            $errors[] = $error;
            
            if ($actualValue > 0) {
                $percentErrors[] = ($error / $actualValue) * 100;
            }
        }

        return [
            'mae' => array_sum($errors) / count($errors), // Mean Absolute Error
            'rmse' => sqrt(array_sum(array_map(fn($e) => $e * $e, $errors)) / count($errors)), // Root Mean Square Error
            'mape' => !empty($percentErrors) ? array_sum($percentErrors) / count($percentErrors) : null, // Mean Absolute Percentage Error
        ];
    }

    /**
     * Predict single month value
     */
    protected function predictSingleMonth(Collection $historicalData): float
    {
        $trend = $this->calculateTrend($historicalData);
        return max(0, $trend['intercept'] + ($trend['slope'] * ($historicalData->count() + 1)));
    }

    /**
     * Create and save forecast simulation
     */
    public function createSimulation(string $name, array $parameters, ?int $userId = null): ForecastSimulation
    {
        $forecast = $this->generateForecast($parameters);

        return ForecastSimulation::create([
            'name' => $name,
            'description' => $parameters['description'] ?? null,
            'simulation_type' => $parameters['type'] ?? 'revenue',
            'parameters' => $parameters,
            'forecast_data' => $forecast,
            'accuracy_score' => $this->calculateOverallAccuracy($forecast['accuracy_metrics']),
            'created_by_user_id' => $userId ?? auth()->id(),
            'tenant_id' => auth()->user()->tenant_id ?? null,
        ]);
    }

    /**
     * Calculate overall accuracy score
     */
    protected function calculateOverallAccuracy(array $metrics): float
    {
        if (!isset($metrics['mape']) || $metrics['mape'] === null) {
            return 0.5; // Default moderate accuracy
        }

        // Convert MAPE to accuracy score (0-1 scale)
        // Lower MAPE = higher accuracy
        return max(0, min(1, 1 - ($metrics['mape'] / 100)));
    }

    /**
     * Run Monte Carlo simulation for forecast uncertainty
     */
    public function runMonteCarloSimulation(array $baseline, int $iterations = 1000): array
    {
        $results = [];

        for ($i = 0; $i < $iterations; $i++) {
            $simulated = $this->simulateSinglePath($baseline);
            $results[] = $simulated;
        }

        // Calculate percentiles
        $revenues = array_column($results, 'total_revenue');
        sort($revenues);

        return [
            'iterations' => $iterations,
            'mean' => array_sum($revenues) / count($revenues),
            'median' => $revenues[floor(count($revenues) / 2)],
            'p10' => $revenues[floor(count($revenues) * 0.1)],
            'p25' => $revenues[floor(count($revenues) * 0.25)],
            'p75' => $revenues[floor(count($revenues) * 0.75)],
            'p90' => $revenues[floor(count($revenues) * 0.9)],
            'min' => min($revenues),
            'max' => max($revenues),
        ];
    }

    /**
     * Simulate single forecast path with randomness
     */
    protected function simulateSinglePath(array $baseline): array
    {
        $avgRevenue = $baseline['average_monthly_revenue'];
        $volatility = $baseline['volatility'] ?? ($avgRevenue * 0.15);

        // Add random variation based on volatility
        $randomFactor = 1 + (($this->randomNormal() * $volatility) / $avgRevenue);
        
        return [
            'total_revenue' => max(0, $avgRevenue * $randomFactor),
        ];
    }

    /**
     * Generate random number from normal distribution
     */
    protected function randomNormal(float $mean = 0, float $stdDev = 1): float
    {
        // Box-Muller transform
        $u1 = mt_rand() / mt_getrandmax();
        $u2 = mt_rand() / mt_getrandmax();
        
        $z0 = sqrt(-2 * log($u1)) * cos(2 * pi() * $u2);
        
        return $z0 * $stdDev + $mean;
    }

    /**
     * Analyze current pipeline for forecast adjustments
     */
    public function analyzePipeline(): array
    {
        $openDeals = Deal::whereHas('pipelineStage', function ($query) {
            $query->where('is_won', false)->where('is_lost', false);
        })->get();

        $weightedValue = $openDeals->sum(function ($deal) {
            return $deal->value * ($deal->probability / 100);
        });

        $stages = PipelineStage::withCount('deals')
            ->with(['deals' => function ($query) {
                $query->select('id', 'pipeline_stage_id', 'value', 'probability');
            }])
            ->get();

        return [
            'total_pipeline_value' => $openDeals->sum('value'),
            'weighted_pipeline_value' => $weightedValue,
            'deal_count' => $openDeals->count(),
            'average_deal_size' => $openDeals->avg('value'),
            'stages_breakdown' => $stages->map(function ($stage) {
                return [
                    'stage_name' => $stage->name,
                    'deal_count' => $stage->deals_count,
                    'total_value' => $stage->deals->sum('value'),
                    'weighted_value' => $stage->deals->sum(fn($d) => $d->value * ($d->probability / 100)),
                ];
            }),
        ];
    }
}
