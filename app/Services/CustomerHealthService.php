<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\CustomerHealthScore;
use App\Models\CustomerHealthScoreHistory;
use App\Models\CustomerTicket;
use App\Models\Communication;
use App\Models\Deal;
use App\Models\SurveyResponse;
use App\Models\ActivityLog;
use App\Notifications\HealthScoreDroppedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerHealthService
{
    /**
     * Factor weights for health score calculation.
     * These can be customized per tenant in the future.
     */
    protected array $factorWeights = [
        'engagement' => 0.20,
        'support_satisfaction' => 0.15,
        'product_usage' => 0.15,
        'payment_history' => 0.15,
        'communication_frequency' => 0.10,
        'nps_csat' => 0.10,
        'deal_activity' => 0.10,
        'recency' => 0.05,
    ];

    /**
     * Thresholds for health status categorization.
     */
    protected array $statusThresholds = [
        'excellent' => 85,
        'good' => 70,
        'at_risk' => 40,
    ];

    /**
     * Calculate health score for a single contact.
     */
    public function calculateForContact(Contact $contact, string $trigger = 'manual'): CustomerHealthScore
    {
        $factors = $this->calculateAllFactors($contact);
        $overallScore = $this->calculateOverallScore($factors);
        $status = $this->determineHealthStatus($overallScore);
        $riskFactors = $this->identifyRiskFactors($factors);
        $suggestions = $this->generateSuggestions($factors, $contact);

        $existingScore = CustomerHealthScore::where('contact_id', $contact->id)->first();
        $previousScore = $existingScore?->overall_score;
        $previousStatus = $existingScore?->health_status;

        $trend = $this->determineTrend($overallScore, $previousScore);
        $trendPercentage = $previousScore ? round((($overallScore - $previousScore) / $previousScore) * 100, 2) : 0;

        $healthScore = CustomerHealthScore::updateOrCreate(
            ['contact_id' => $contact->id],
            [
                'overall_score' => $overallScore,
                'previous_score' => $previousScore,
                'score_breakdown' => $factors,
                'health_status' => $status,
                'trend' => $trend,
                'trend_percentage' => $trendPercentage,
                'risk_factors' => $riskFactors,
                'improvement_suggestions' => $suggestions,
                'last_calculated_at' => now(),
            ]
        );

        $this->recordHistory($contact, $healthScore, $previousScore, $previousStatus, $trigger);

        if ($this->shouldSendAlert($healthScore, $previousStatus)) {
            $this->sendHealthAlert($healthScore);
        }

        return $healthScore;
    }

    /**
     * Calculate health scores for all contacts.
     */
    public function calculateForAllContacts(string $trigger = 'scheduled'): array
    {
        $processed = 0;
        $failed = 0;
        $errors = [];

        Contact::chunk(100, function ($contacts) use (&$processed, &$failed, &$errors, $trigger) {
            foreach ($contacts as $contact) {
                try {
                    $this->calculateForContact($contact, $trigger);
                    $processed++;
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = [
                        'contact_id' => $contact->id,
                        'error' => $e->getMessage(),
                    ];
                    Log::error("Failed to calculate health score for contact {$contact->id}: {$e->getMessage()}");
                }
            }
        });

        return [
            'processed' => $processed,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }

    /**
     * Calculate all health factors for a contact.
     */
    protected function calculateAllFactors(Contact $contact): array
    {
        return [
            'engagement' => $this->calculateEngagementScore($contact),
            'support_satisfaction' => $this->calculateSupportSatisfactionScore($contact),
            'product_usage' => $this->calculateProductUsageScore($contact),
            'payment_history' => $this->calculatePaymentHistoryScore($contact),
            'communication_frequency' => $this->calculateCommunicationFrequencyScore($contact),
            'nps_csat' => $this->calculateNpsCsatScore($contact),
            'deal_activity' => $this->calculateDealActivityScore($contact),
            'recency' => $this->calculateRecencyScore($contact),
        ];
    }

    /**
     * Calculate engagement score based on activity and interactions.
     */
    protected function calculateEngagementScore(Contact $contact): float
    {
        $score = 50;

        $recentActivities = ActivityLog::where('loggable_type', Contact::class)
            ->where('loggable_id', $contact->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        if ($recentActivities >= 10) {
            $score += 30;
        } elseif ($recentActivities >= 5) {
            $score += 20;
        } elseif ($recentActivities >= 1) {
            $score += 10;
        } else {
            $score -= 20;
        }

        $recentEmails = $contact->emails()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        if ($recentEmails >= 5) {
            $score += 15;
        } elseif ($recentEmails >= 1) {
            $score += 10;
        }

        $recentNotes = $contact->notes()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        if ($recentNotes >= 3) {
            $score += 5;
        }

        return max(0, min(100, $score));
    }

    /**
     * Calculate support satisfaction score based on ticket history.
     */
    protected function calculateSupportSatisfactionScore(Contact $contact): float
    {
        $score = 80;

        $tickets = CustomerTicket::where('contact_id', $contact->id)
            ->where('created_at', '>=', now()->subDays(90))
            ->get();

        if ($tickets->isEmpty()) {
            return $score;
        }

        $totalTickets = $tickets->count();
        $resolvedTickets = $tickets->where('status', 'resolved')->count();
        $closedTickets = $tickets->where('status', 'closed')->count();
        $openTickets = $tickets->whereIn('status', ['open', 'in_progress', 'waiting_customer'])->count();

        $highPriorityTickets = $tickets->whereIn('priority', ['high', 'urgent'])->count();
        $urgentOpenTickets = $tickets->where('priority', 'urgent')
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        $resolutionRate = $totalTickets > 0
            ? (($resolvedTickets + $closedTickets) / $totalTickets) * 100
            : 100;

        $score = ($resolutionRate / 100) * 60 + 20;

        if ($highPriorityTickets > 3) {
            $score -= 15;
        } elseif ($highPriorityTickets > 0) {
            $score -= 5;
        }

        if ($urgentOpenTickets > 0) {
            $score -= 20;
        }

        if ($openTickets > 5) {
            $score -= 15;
        } elseif ($openTickets > 2) {
            $score -= 5;
        }

        $ticketFrequency = $totalTickets / 3;
        if ($ticketFrequency > 5) {
            $score -= 15;
        } elseif ($ticketFrequency > 3) {
            $score -= 10;
        }

        return max(0, min(100, $score));
    }

    /**
     * Calculate product usage score based on activity patterns.
     */
    protected function calculateProductUsageScore(Contact $contact): float
    {
        $score = 50;

        $last30DaysActivities = ActivityLog::where('loggable_type', Contact::class)
            ->where('loggable_id', $contact->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $previous30DaysActivities = ActivityLog::where('loggable_type', Contact::class)
            ->where('loggable_id', $contact->id)
            ->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])
            ->count();

        if ($last30DaysActivities > 0) {
            $activityRatio = $previous30DaysActivities > 0
                ? $last30DaysActivities / $previous30DaysActivities
                : 2;

            if ($activityRatio >= 1.2) {
                $score += 30;
            } elseif ($activityRatio >= 0.8) {
                $score += 20;
            } elseif ($activityRatio >= 0.5) {
                $score += 10;
            } else {
                $score -= 10;
            }
        } else {
            $score -= 30;
        }

        $activeDays = ActivityLog::where('loggable_type', Contact::class)
            ->where('loggable_id', $contact->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'))
            ->distinct()
            ->count();

        if ($activeDays >= 15) {
            $score += 20;
        } elseif ($activeDays >= 7) {
            $score += 10;
        } elseif ($activeDays >= 3) {
            $score += 5;
        }

        return max(0, min(100, $score));
    }

    /**
     * Calculate payment history score.
     */
    protected function calculatePaymentHistoryScore(Contact $contact): float
    {
        $score = 85;

        if ($contact->lifetime_value && $contact->lifetime_value > 0) {
            $score += 10;
        }

        $deals = Deal::where('contact_id', $contact->id)->get();

        if ($deals->isEmpty()) {
            return $score;
        }

        $wonDeals = $deals->where('status', 'won')->count();
        $lostDeals = $deals->where('status', 'lost')->count();
        $totalDeals = $wonDeals + $lostDeals;

        if ($totalDeals > 0) {
            $winRate = ($wonDeals / $totalDeals) * 100;

            if ($winRate >= 70) {
                $score += 5;
            } elseif ($winRate >= 50) {
                $score += 0;
            } else {
                $score -= 10;
            }
        }

        $recentWonDeals = $deals->where('status', 'won')
            ->where('actual_close_date', '>=', now()->subMonths(6))
            ->count();

        if ($recentWonDeals >= 2) {
            $score += 5;
        }

        return max(0, min(100, $score));
    }

    /**
     * Calculate communication frequency score.
     */
    protected function calculateCommunicationFrequencyScore(Contact $contact): float
    {
        $score = 50;

        $communications = Communication::where('communicable_type', Contact::class)
            ->where('communicable_id', $contact->id)
            ->where('created_at', '>=', now()->subDays(90))
            ->get();

        $totalCommunications = $communications->count();
        $calls = $communications->whereIn('type', ['call', 'video'])->count();
        $messages = $communications->whereIn('type', ['email', 'sms', 'whatsapp', 'chat'])->count();

        if ($totalCommunications >= 10) {
            $score += 30;
        } elseif ($totalCommunications >= 5) {
            $score += 20;
        } elseif ($totalCommunications >= 1) {
            $score += 10;
        } else {
            $score -= 20;
        }

        if ($calls >= 2) {
            $score += 10;
        }

        if ($messages >= 5) {
            $score += 5;
        }

        $lastContactDays = $contact->last_contacted_at
            ? $contact->last_contacted_at->diffInDays(now())
            : 365;

        if ($lastContactDays <= 7) {
            $score += 10;
        } elseif ($lastContactDays <= 30) {
            $score += 5;
        } elseif ($lastContactDays > 90) {
            $score -= 15;
        }

        $sentimentScores = $communications->whereNotNull('sentiment_score')
            ->pluck('sentiment_score');

        if ($sentimentScores->isNotEmpty()) {
            $avgSentiment = $sentimentScores->avg();

            if ($avgSentiment >= 0.3) {
                $score += 5;
            } elseif ($avgSentiment <= -0.3) {
                $score -= 10;
            }
        }

        return max(0, min(100, $score));
    }

    /**
     * Calculate NPS/CSAT score based on survey responses.
     */
    protected function calculateNpsCsatScore(Contact $contact): float
    {
        $score = 70;

        $responses = SurveyResponse::where('contact_id', $contact->id)
            ->where('is_completed', true)
            ->where('created_at', '>=', now()->subMonths(12))
            ->orderBy('created_at', 'desc')
            ->get();

        if ($responses->isEmpty()) {
            return $score;
        }

        $latestWithNps = $responses->whereNotNull('nps_score')->first();
        if ($latestWithNps) {
            $nps = $latestWithNps->nps_score;

            if ($nps >= 9) {
                $score += 25;
            } elseif ($nps >= 7) {
                $score += 10;
            } elseif ($nps >= 5) {
                $score -= 5;
            } else {
                $score -= 25;
            }
        }

        $latestWithCsat = $responses->whereNotNull('csat_score')->first();
        if ($latestWithCsat) {
            $csat = $latestWithCsat->csat_score;

            if ($csat >= 4) {
                $score += 15;
            } elseif ($csat >= 3) {
                $score += 5;
            } else {
                $score -= 15;
            }
        }

        $latestWithCes = $responses->whereNotNull('ces_score')->first();
        if ($latestWithCes) {
            $ces = $latestWithCes->ces_score;

            if ($ces <= 2) {
                $score += 10;
            } elseif ($ces >= 5) {
                $score -= 10;
            }
        }

        return max(0, min(100, $score));
    }

    /**
     * Calculate deal activity score.
     */
    protected function calculateDealActivityScore(Contact $contact): float
    {
        $score = 60;

        $deals = Deal::where('contact_id', $contact->id)->get();

        if ($deals->isEmpty()) {
            return $score;
        }

        $openDeals = $deals->where('status', 'open');
        $totalValue = $openDeals->sum('value');
        $avgProbability = $openDeals->avg('probability') ?? 0;

        if ($openDeals->isNotEmpty()) {
            $score += 15;

            if ($avgProbability >= 70) {
                $score += 15;
            } elseif ($avgProbability >= 50) {
                $score += 10;
            } elseif ($avgProbability >= 30) {
                $score += 5;
            }
        }

        $upcomingCloseDeals = $openDeals->filter(function ($deal) {
            return $deal->expected_close_date && $deal->expected_close_date->isBetween(now(), now()->addDays(30));
        })->count();

        if ($upcomingCloseDeals > 0) {
            $score += 10;
        }

        $recentWonDeals = $deals->where('status', 'won')
            ->filter(function ($deal) {
                return $deal->actual_close_date && $deal->actual_close_date >= now()->subMonths(3);
            })->count();

        if ($recentWonDeals >= 2) {
            $score += 10;
        } elseif ($recentWonDeals >= 1) {
            $score += 5;
        }

        $recentLostDeals = $deals->where('status', 'lost')
            ->filter(function ($deal) {
                return $deal->actual_close_date && $deal->actual_close_date >= now()->subMonths(3);
            })->count();

        if ($recentLostDeals >= 2) {
            $score -= 15;
        } elseif ($recentLostDeals >= 1) {
            $score -= 5;
        }

        return max(0, min(100, $score));
    }

    /**
     * Calculate recency score based on last interaction.
     */
    protected function calculateRecencyScore(Contact $contact): float
    {
        $score = 50;

        $lastContactDays = $contact->last_contacted_at
            ? $contact->last_contacted_at->diffInDays(now())
            : 365;

        if ($lastContactDays <= 7) {
            $score = 100;
        } elseif ($lastContactDays <= 14) {
            $score = 90;
        } elseif ($lastContactDays <= 30) {
            $score = 80;
        } elseif ($lastContactDays <= 60) {
            $score = 60;
        } elseif ($lastContactDays <= 90) {
            $score = 40;
        } else {
            $score = 20;
        }

        $latestActivity = ActivityLog::where('loggable_type', Contact::class)
            ->where('loggable_id', $contact->id)
            ->latest()
            ->first();

        if ($latestActivity) {
            $activityDays = $latestActivity->created_at->diffInDays(now());

            if ($activityDays < $lastContactDays) {
                $adjustment = min(20, ($lastContactDays - $activityDays) / 2);
                $score += $adjustment;
            }
        }

        return max(0, min(100, $score));
    }

    /**
     * Calculate overall score from all factors.
     */
    protected function calculateOverallScore(array $factors): float
    {
        $weightedSum = 0;
        $totalWeight = 0;

        foreach ($factors as $factor => $factorScore) {
            $weight = $this->factorWeights[$factor] ?? 0.1;
            $weightedSum += $factorScore * $weight;
            $totalWeight += $weight;
        }

        return $totalWeight > 0 ? round($weightedSum / $totalWeight * $totalWeight, 2) : 0;
    }

    /**
     * Determine health status based on overall score.
     */
    protected function determineHealthStatus(float $score): string
    {
        if ($score >= $this->statusThresholds['excellent']) {
            return 'excellent';
        }

        if ($score >= $this->statusThresholds['good']) {
            return 'good';
        }

        if ($score >= $this->statusThresholds['at_risk']) {
            return 'at_risk';
        }

        return 'critical';
    }

    /**
     * Determine trend based on score comparison.
     */
    protected function determineTrend(float $currentScore, ?float $previousScore): string
    {
        if ($previousScore === null) {
            return 'stable';
        }

        $change = $currentScore - $previousScore;
        $changePercentage = abs($previousScore) > 0 ? abs($change / $previousScore) * 100 : 0;

        if ($changePercentage < 3) {
            return 'stable';
        }

        return $change > 0 ? 'improving' : 'declining';
    }

    /**
     * Identify risk factors from scores.
     */
    protected function identifyRiskFactors(array $factors): array
    {
        $risks = [];

        $riskThreshold = 50;
        $riskDescriptions = [
            'engagement' => 'Low customer engagement detected',
            'support_satisfaction' => 'Support satisfaction concerns',
            'product_usage' => 'Declining product usage',
            'payment_history' => 'Payment history concerns',
            'communication_frequency' => 'Insufficient communication frequency',
            'nps_csat' => 'Low satisfaction scores (NPS/CSAT)',
            'deal_activity' => 'Stagnant deal activity',
            'recency' => 'No recent interactions',
        ];

        foreach ($factors as $factor => $score) {
            if ($score < $riskThreshold && isset($riskDescriptions[$factor])) {
                $risks[] = [
                    'factor' => $factor,
                    'score' => $score,
                    'description' => $riskDescriptions[$factor],
                    'severity' => $score < 30 ? 'high' : 'medium',
                ];
            }
        }

        usort($risks, fn($a, $b) => $a['score'] <=> $b['score']);

        return $risks;
    }

    /**
     * Generate improvement suggestions based on factors.
     */
    protected function generateSuggestions(array $factors, Contact $contact): array
    {
        $suggestions = [];

        if ($factors['engagement'] < 60) {
            $suggestions[] = [
                'priority' => 'high',
                'action' => 'Increase engagement through personalized outreach',
                'details' => 'Schedule a check-in call or send personalized content relevant to their interests.',
            ];
        }

        if ($factors['support_satisfaction'] < 70) {
            $suggestions[] = [
                'priority' => 'high',
                'action' => 'Review and address open support tickets',
                'details' => 'Follow up on any unresolved issues and ensure customer satisfaction.',
            ];
        }

        if ($factors['product_usage'] < 50) {
            $suggestions[] = [
                'priority' => 'medium',
                'action' => 'Provide product training or onboarding assistance',
                'details' => 'Offer webinar invitations or personalized training sessions.',
            ];
        }

        if ($factors['communication_frequency'] < 50) {
            $suggestions[] = [
                'priority' => 'medium',
                'action' => 'Establish regular communication cadence',
                'details' => 'Set up recurring check-ins or add to a nurture campaign.',
            ];
        }

        if ($factors['nps_csat'] < 60) {
            $suggestions[] = [
                'priority' => 'high',
                'action' => 'Address satisfaction concerns directly',
                'details' => 'Reach out to understand specific pain points and work on solutions.',
            ];
        }

        if ($factors['deal_activity'] < 50) {
            $suggestions[] = [
                'priority' => 'medium',
                'action' => 'Review opportunity pipeline and engagement',
                'details' => 'Identify cross-sell or upsell opportunities to re-engage.',
            ];
        }

        if ($factors['recency'] < 40) {
            $suggestions[] = [
                'priority' => 'high',
                'action' => 'Immediate outreach required',
                'details' => "Contact has not been engaged recently. Reach out immediately to maintain relationship.",
            ];
        }

        usort($suggestions, function ($a, $b) {
            $priorityOrder = ['high' => 0, 'medium' => 1, 'low' => 2];
            return ($priorityOrder[$a['priority']] ?? 2) <=> ($priorityOrder[$b['priority']] ?? 2);
        });

        return $suggestions;
    }

    /**
     * Record score history for trend analysis.
     */
    protected function recordHistory(
        Contact $contact,
        CustomerHealthScore $healthScore,
        ?float $previousScore,
        ?string $previousStatus,
        string $trigger
    ): void {
        CustomerHealthScoreHistory::create([
            'contact_id' => $contact->id,
            'overall_score' => $healthScore->overall_score,
            'score_breakdown' => $healthScore->score_breakdown,
            'health_status' => $healthScore->health_status,
            'previous_score' => $previousScore,
            'previous_status' => $previousStatus,
            'score_change' => $previousScore ? $healthScore->overall_score - $previousScore : null,
            'trigger' => $trigger,
        ]);
    }

    /**
     * Determine if an alert should be sent.
     */
    protected function shouldSendAlert(CustomerHealthScore $healthScore, ?string $previousStatus): bool
    {
        if ($healthScore->alert_sent_at && $healthScore->alert_sent_at > now()->subHours(24)) {
            return false;
        }

        if ($healthScore->health_status === 'critical') {
            return true;
        }

        if ($previousStatus && in_array($previousStatus, ['excellent', 'good']) &&
            in_array($healthScore->health_status, ['at_risk', 'critical'])) {
            return true;
        }

        if ($healthScore->previous_score &&
            ($healthScore->previous_score - $healthScore->overall_score) >= 15) {
            return true;
        }

        return false;
    }

    /**
     * Send health alert notification.
     */
    protected function sendHealthAlert(CustomerHealthScore $healthScore): void
    {
        try {
            $contact = $healthScore->contact;
            $owner = $contact->owner;

            if ($owner) {
                $owner->notify(new HealthScoreDroppedNotification($healthScore));
            }

            $healthScore->update(['alert_sent_at' => now()]);
        } catch (\Exception $e) {
            Log::error("Failed to send health score alert: {$e->getMessage()}");
        }
    }

    /**
     * Get at-risk contacts with their health scores.
     */
    public function getAtRiskContacts(int $limit = 50): Collection
    {
        return CustomerHealthScore::with('contact')
            ->whereIn('health_status', ['at_risk', 'critical'])
            ->orderBy('overall_score', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get declining health scores.
     */
    public function getDecliningScores(int $limit = 20): Collection
    {
        return CustomerHealthScore::with('contact')
            ->where('trend', 'declining')
            ->orderBy('trend_percentage', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get health score statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total_customers' => CustomerHealthScore::count(),
            'excellent' => CustomerHealthScore::where('health_status', 'excellent')->count(),
            'good' => CustomerHealthScore::where('health_status', 'good')->count(),
            'at_risk' => CustomerHealthScore::where('health_status', 'at_risk')->count(),
            'critical' => CustomerHealthScore::where('health_status', 'critical')->count(),
            'average_score' => round(CustomerHealthScore::avg('overall_score') ?? 0, 1),
            'improving' => CustomerHealthScore::where('trend', 'improving')->count(),
            'declining' => CustomerHealthScore::where('trend', 'declining')->count(),
            'stable' => CustomerHealthScore::where('trend', 'stable')->count(),
        ];
    }

    /**
     * Get score trend for a contact.
     */
    public function getScoreTrend(Contact $contact, int $days = 30): Collection
    {
        return CustomerHealthScoreHistory::where('contact_id', $contact->id)
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'asc')
            ->get(['overall_score', 'health_status', 'created_at']);
    }
}
