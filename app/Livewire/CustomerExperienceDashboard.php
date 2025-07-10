<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CustomerTicket;
use App\Models\KnowledgeBaseArticle;
use App\Models\Survey;
use App\Models\CustomerHealthScore;
use App\Models\CustomerJourney;
use App\Models\LoyaltyProgram;

class CustomerExperienceDashboard extends Component
{
    public $activeTab = 'overview';

    public function render()
    {
        $stats = [
            'tickets' => [
                'total' => CustomerTicket::count(),
                'open' => CustomerTicket::open()->count(),
                'high_priority' => CustomerTicket::highPriority()->count(),
                'resolved_today' => CustomerTicket::whereDate('resolved_at', today())->count(),
            ],
            'knowledge_base' => [
                'total_articles' => KnowledgeBaseArticle::count(),
                'published' => KnowledgeBaseArticle::published()->count(),
                'total_views' => KnowledgeBaseArticle::sum('view_count'),
                'avg_helpfulness' => $this->calculateAverageHelpfulness(),
            ],
            'surveys' => [
                'total' => Survey::count(),
                'active' => Survey::active()->count(),
                'responses_today' => \App\Models\SurveyResponse::whereDate('created_at', today())->count(),
                'avg_nps' => $this->calculateAverageNPS(),
            ],
            'health_scores' => [
                'total_customers' => CustomerHealthScore::count(),
                'excellent' => CustomerHealthScore::where('health_status', 'excellent')->count(),
                'at_risk' => CustomerHealthScore::atRisk()->count(),
                'avg_score' => CustomerHealthScore::avg('overall_score') ?? 0,
            ],
            'journeys' => [
                'active_journeys' => CustomerJourney::count(),
                'completed_today' => $this->getCompletedJourneysToday(),
                'avg_progression' => CustomerJourney::avg('progression_score') ?? 0,
                'stages_count' => \App\Models\CustomerJourneyStage::active()->count(),
            ],
            'loyalty' => [
                'active_programs' => LoyaltyProgram::active()->count(),
                'enrolled_customers' => \App\Models\CustomerLoyaltyPoints::distinct('contact_id')->count(),
                'points_issued_today' => $this->getPointsIssuedToday(),
                'points_redeemed_today' => $this->getPointsRedeemedToday(),
            ]
        ];

        $recentActivity = $this->getRecentActivity();
        $alertsAndNotifications = $this->getAlertsAndNotifications();

        return view('livewire.customer-experience-dashboard', compact('stats', 'recentActivity', 'alertsAndNotifications'));
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    private function calculateAverageHelpfulness(): float
    {
        return KnowledgeBaseArticle::whereRaw('(helpful_votes + unhelpful_votes) > 0')
            ->selectRaw('AVG(helpful_votes / (helpful_votes + unhelpful_votes) * 100) as avg')
            ->value('avg') ?? 0;
    }

    private function calculateAverageNPS(): float
    {
        return \App\Models\SurveyResponse::whereNotNull('nps_score')
            ->where('is_completed', true)
            ->avg('nps_score') ?? 0;
    }

    private function getCompletedJourneysToday(): int
    {
        // Count journeys that moved to final stage today
        return CustomerJourney::whereDate('updated_at', today())
            ->whereHas('currentStage', function($query) {
                $query->where('order_index', function($subQuery) {
                    $subQuery->select(\DB::raw('MAX(order_index)'))
                        ->from('customer_journey_stages')
                        ->where('is_active', true);
                });
            })
            ->count();
    }

    private function getPointsIssuedToday(): int
    {
        // This would need a points transactions table in real implementation
        return rand(500, 2000); // Mock data
    }

    private function getPointsRedeemedToday(): int
    {
        // This would need a points transactions table in real implementation
        return rand(200, 800); // Mock data
    }

    private function getRecentActivity(): array
    {
        $activities = [];

        // Recent tickets
        $recentTickets = CustomerTicket::with('contact')
            ->latest()
            ->take(5)
            ->get();

        foreach ($recentTickets as $ticket) {
            $activities[] = [
                'type' => 'ticket',
                'message' => "New ticket: {$ticket->subject}",
                'time' => $ticket->created_at,
                'contact' => $ticket->contact->first_name . ' ' . $ticket->contact->last_name,
                'priority' => $ticket->priority
            ];
        }

        // Recent survey responses
        $recentResponses = \App\Models\SurveyResponse::with(['contact', 'survey'])
            ->where('is_completed', true)
            ->latest()
            ->take(3)
            ->get();

        foreach ($recentResponses as $response) {
            $activities[] = [
                'type' => 'survey',
                'message' => "Survey completed: {$response->survey->name}",
                'time' => $response->completed_at,
                'contact' => $response->contact->first_name . ' ' . $response->contact->last_name,
                'score' => $response->nps_score ?? $response->csat_score
            ];
        }

        // Sort by time
        usort($activities, function($a, $b) {
            return $b['time']->timestamp - $a['time']->timestamp;
        });

        return array_slice($activities, 0, 10);
    }

    private function getAlertsAndNotifications(): array
    {
        $alerts = [];

        // High priority tickets
        $highPriorityTickets = CustomerTicket::highPriority()->open()->count();
        if ($highPriorityTickets > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$highPriorityTickets} high priority tickets need attention",
                'action' => 'View Tickets',
                'route' => 'customer-experience.tickets'
            ];
        }

        // At-risk customers
        $atRiskCustomers = CustomerHealthScore::atRisk()->count();
        if ($atRiskCustomers > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "{$atRiskCustomers} customers are at risk",
                'action' => 'View Health Scores',
                'route' => 'customer-experience.health'
            ];
        }

        // Knowledge base articles needing updates
        $outdatedArticles = KnowledgeBaseArticle::where('updated_at', '<', now()->subMonths(3))->count();
        if ($outdatedArticles > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$outdatedArticles} knowledge base articles may need updates",
                'action' => 'Review Articles',
                'route' => 'customer-experience.knowledge-base'
            ];
        }

        return $alerts;
    }
}