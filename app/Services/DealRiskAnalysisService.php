<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\DealRiskAnalysis;
use App\Models\AiModel;
use Illuminate\Support\Facades\Log;

class DealRiskAnalysisService
{
    public function analyzeDeal(Deal $deal): DealRiskAnalysis
    {
        try {
            $riskFactors = $this->calculateRiskFactors($deal);
            $riskScore = $this->calculateRiskScore($riskFactors);
            $riskLevel = $this->determineRiskLevel($riskScore);
            $interventions = $this->generateInterventions($riskFactors, $riskLevel);
            $probability = $this->calculateProbabilityToClose($deal, $riskFactors);
            $predictedDate = $this->predictCloseDate($deal, $riskFactors);

            return DealRiskAnalysis::updateOrCreate(
                ['deal_id' => $deal->id],
                [
                    'risk_score' => $riskScore,
                    'risk_level' => $riskLevel,
                    'risk_factors' => $riskFactors,
                    'intervention_recommendations' => $interventions,
                    'probability_to_close' => $probability,
                    'predicted_close_date' => $predictedDate,
                    'confidence_score' => 0.85, // Mock confidence score
                    'model_version' => '1.0',
                    'last_analyzed_at' => now(),
                ]
            );
        } catch (\Exception $e) {
            Log::error('Deal risk analysis failed', [
                'deal_id' => $deal->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function calculateRiskFactors(Deal $deal): array
    {
        $factors = [];

        // Time-based risk factors
        if ($deal->expected_close_date && $deal->expected_close_date->isPast()) {
            $factors['overdue'] = [
                'weight' => 0.3,
                'value' => $deal->expected_close_date->diffInDays(now()),
                'description' => 'Deal is overdue by ' . $deal->expected_close_date->diffInDays(now()) . ' days'
            ];
        }

        // Activity-based risk factors
        $recentActivityCount = $deal->tasks()->where('created_at', '>=', now()->subDays(7))->count();
        if ($recentActivityCount < 2) {
            $factors['low_activity'] = [
                'weight' => 0.25,
                'value' => $recentActivityCount,
                'description' => 'Low recent activity: ' . $recentActivityCount . ' tasks in last 7 days'
            ];
        }

        // Stage duration risk
        $stageAge = $deal->updated_at->diffInDays(now());
        if ($stageAge > 30) {
            $factors['stage_stagnation'] = [
                'weight' => 0.2,
                'value' => $stageAge,
                'description' => 'Deal has been in current stage for ' . $stageAge . ' days'
            ];
        }

        // Value-based risk
        if ($deal->value > 50000) {
            $factors['high_value'] = [
                'weight' => 0.15,
                'value' => $deal->value,
                'description' => 'High-value deal requires extra attention'
            ];
        }

        // Communication risk
        $emailCount = $deal->emails()->where('created_at', '>=', now()->subDays(14))->count();
        if ($emailCount < 3) {
            $factors['poor_communication'] = [
                'weight' => 0.1,
                'value' => $emailCount,
                'description' => 'Limited communication: ' . $emailCount . ' emails in last 14 days'
            ];
        }

        return $factors;
    }

    private function calculateRiskScore(array $factors): int
    {
        $totalRisk = 0;
        $totalWeight = 0;

        foreach ($factors as $factor) {
            $totalRisk += $factor['weight'] * 100;
            $totalWeight += $factor['weight'];
        }

        return $totalWeight > 0 ? min(100, round($totalRisk / $totalWeight)) : 0;
    }

    private function determineRiskLevel(int $score): string
    {
        return match (true) {
            $score >= 80 => 'critical',
            $score >= 60 => 'high',
            $score >= 40 => 'medium',
            default => 'low'
        };
    }

    private function generateInterventions(array $factors, string $level): array
    {
        $interventions = [];

        if (isset($factors['overdue'])) {
            $interventions[] = [
                'action' => 'Schedule immediate follow-up',
                'priority' => 'high',
                'description' => 'Contact prospect to understand delays and set new timeline'
            ];
        }

        if (isset($factors['low_activity'])) {
            $interventions[] = [
                'action' => 'Increase engagement frequency',
                'priority' => 'medium',
                'description' => 'Schedule weekly check-ins and add value with relevant content'
            ];
        }

        if (isset($factors['stage_stagnation'])) {
            $interventions[] = [
                'action' => 'Review stage progression',
                'priority' => 'medium',
                'description' => 'Identify blockers and create action plan to move deal forward'
            ];
        }

        if ($level === 'critical') {
            $interventions[] = [
                'action' => 'Manager review required',
                'priority' => 'critical',
                'description' => 'Escalate to sales manager for immediate intervention'
            ];
        }

        return $interventions;
    }

    private function calculateProbabilityToClose(Deal $deal, array $factors): float
    {
        $baseProbability = $deal->pipelineStage->probability ?? 0.5;
        $riskAdjustment = count($factors) * 0.1;
        
        return max(0.1, min(0.9, $baseProbability - $riskAdjustment));
    }

    private function predictCloseDate(Deal $deal, array $factors): ?\Carbon\Carbon
    {
        if (!$deal->expected_close_date) {
            return null;
        }

        $delayDays = count($factors) * 7; // Each risk factor adds a week delay
        return $deal->expected_close_date->addDays($delayDays);
    }

    public function analyzeMultipleDeals(array $dealIds): array
    {
        $results = [];
        
        foreach ($dealIds as $dealId) {
            $deal = Deal::find($dealId);
            if ($deal) {
                $results[$dealId] = $this->analyzeDeal($deal);
            }
        }

        return $results;
    }
}