<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\DealRiskAnalysis;
use App\Models\CompetitiveIntelligence;
use App\Models\PriceOptimization;
use App\Models\TerritoryPerformance;
use App\Models\CommissionTracking;
use App\Models\SalesCoaching;
use App\Services\DealRiskAnalysisService;
use App\Services\CompetitiveIntelligenceService;
use App\Services\PriceOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RevenueIntelligenceController extends Controller
{
    public function __construct(
        private DealRiskAnalysisService $riskService,
        private CompetitiveIntelligenceService $competitiveService,
        private PriceOptimizationService $priceService
    ) {}

    /**
     * Revenue Intelligence Dashboard
     */
    public function dashboard()
    {
        $highRiskDeals = DealRiskAnalysis::with('deal')
            ->whereIn('risk_level', ['high', 'critical'])
            ->limit(10)
            ->get();

        $competitiveThreats = CompetitiveIntelligence::with('deal')
            ->where('win_loss_probability', '<=', 0.5)
            ->limit(10)
            ->get();

        $priceOptimizations = PriceOptimization::with('deal')
            ->where('confidence_score', '>=', 0.7)
            ->limit(10)
            ->get();

        $territoryPerformance = TerritoryPerformance::currentPeriod()
            ->with('user')
            ->orderBy('performance_score', 'desc')
            ->limit(10)
            ->get();

        $pendingCommissions = CommissionTracking::pending()
            ->with(['user', 'deal'])
            ->orderBy('commission_amount', 'desc')
            ->limit(10)
            ->get();

        $salesCoaching = SalesCoaching::highPriority()
            ->with(['user', 'deal'])
            ->where('implementation_status', 'pending')
            ->limit(10)
            ->get();

        return view('revenue-intelligence.dashboard', compact(
            'highRiskDeals',
            'competitiveThreats',
            'priceOptimizations',
            'territoryPerformance',
            'pendingCommissions',
            'salesCoaching'
        ));
    }

    /**
     * Deal Risk Analysis
     */
    public function analyzeDealRisk(Deal $deal): JsonResponse
    {
        try {
            $analysis = $this->riskService->analyzeDeal($deal);
            
            return response()->json([
                'success' => true,
                'data' => $analysis,
                'message' => 'Deal risk analysis completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze deal risk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Competitive Intelligence Analysis
     */
    public function analyzeCompetition(Request $request, Deal $deal): JsonResponse
    {
        $request->validate([
            'competitor_name' => 'required|string|max:255'
        ]);

        try {
            $analysis = $this->competitiveService->analyzeCompetition(
                $deal,
                $request->competitor_name
            );
            
            return response()->json([
                'success' => true,
                'data' => $analysis,
                'message' => 'Competitive analysis completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze competition: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Price Optimization
     */
    public function optimizePrice(Deal $deal): JsonResponse
    {
        try {
            $optimization = $this->priceService->optimizePrice($deal);
            
            return response()->json([
                'success' => true,
                'data' => $optimization,
                'message' => 'Price optimization completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to optimize price: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk Deal Analysis
     */
    public function bulkAnalysis(Request $request): JsonResponse
    {
        $request->validate([
            'deal_ids' => 'required|array',
            'deal_ids.*' => 'exists:deals,id',
            'analysis_types' => 'required|array',
            'analysis_types.*' => 'in:risk,competition,pricing'
        ]);

        $results = [];
        $dealIds = $request->deal_ids;
        $analysisTypes = $request->analysis_types;

        foreach ($dealIds as $dealId) {
            $deal = Deal::find($dealId);
            if (!$deal) continue;

            $dealResults = [];

            if (in_array('risk', $analysisTypes)) {
                try {
                    $dealResults['risk'] = $this->riskService->analyzeDeal($deal);
                } catch (\Exception $e) {
                    $dealResults['risk'] = ['error' => $e->getMessage()];
                }
            }

            if (in_array('pricing', $analysisTypes)) {
                try {
                    $dealResults['pricing'] = $this->priceService->optimizePrice($deal);
                } catch (\Exception $e) {
                    $dealResults['pricing'] = ['error' => $e->getMessage()];
                }
            }

            $results[$dealId] = $dealResults;
        }

        return response()->json([
            'success' => true,
            'data' => $results,
            'message' => 'Bulk analysis completed'
        ]);
    }

    /**
     * Get Territory Performance Data
     */
    public function territoryPerformance(Request $request): JsonResponse
    {
        $query = TerritoryPerformance::with('user');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->period) {
            $query->where('period_start', '>=', $request->period)
                  ->where('period_end', '<=', now());
        }

        $performances = $query->orderBy('performance_score', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $performances
        ]);
    }

    /**
     * Get Commission Tracking Data
     */
    public function commissionTracking(Request $request): JsonResponse
    {
        $query = CommissionTracking::with(['user', 'deal']);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->status) {
            $query->where('payment_status', $request->status);
        }

        if ($request->period_start && $request->period_end) {
            $query->whereBetween('payment_period_start', [
                $request->period_start,
                $request->period_end
            ]);
        }

        $commissions = $query->orderBy('commission_amount', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $commissions
        ]);
    }

    /**
     * Get Sales Coaching Recommendations
     */
    public function salesCoaching(Request $request): JsonResponse
    {
        $query = SalesCoaching::with(['user', 'deal']);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->coaching_type) {
            $query->where('coaching_type', $request->coaching_type);
        }

        if ($request->priority_level) {
            $query->where('priority_level', $request->priority_level);
        }

        $coaching = $query->orderBy('priority_level', 'desc')
                          ->orderBy('last_generated_at', 'desc')
                          ->get();

        return response()->json([
            'success' => true,
            'data' => $coaching
        ]);
    }

    /**
     * Update Coaching Implementation Status
     */
    public function updateCoachingStatus(Request $request, SalesCoaching $coaching): JsonResponse
    {
        $request->validate([
            'implementation_status' => 'required|in:not_started,pending,in_progress,completed,skipped',
            'coach_notes' => 'nullable|string'
        ]);

        $coaching->update([
            'implementation_status' => $request->implementation_status,
            'coach_notes' => $request->coach_notes
        ]);

        return response()->json([
            'success' => true,
            'data' => $coaching,
            'message' => 'Coaching status updated successfully'
        ]);
    }

    /**
     * Revenue Intelligence Analytics
     */
    public function analytics(): JsonResponse
    {
        $analytics = [
            'deals_at_risk' => DealRiskAnalysis::whereIn('risk_level', ['high', 'critical'])->count(),
            'competitive_threats' => CompetitiveIntelligence::where('win_loss_probability', '<=', 0.5)->count(),
            'optimization_opportunities' => PriceOptimization::where('confidence_score', '>=', 0.7)->count(),
            'pending_commissions_total' => CommissionTracking::pending()->sum('commission_amount'),
            'coaching_actions_pending' => SalesCoaching::where('implementation_status', 'pending')->count(),
            'territory_performance_avg' => TerritoryPerformance::currentPeriod()->avg('performance_score'),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }
}
