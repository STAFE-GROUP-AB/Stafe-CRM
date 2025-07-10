<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CustomerHealthScore;
use App\Models\Contact;

class CustomerHealthDashboard extends Component
{
    public $selectedContact = null;
    public $healthScoreDetails = null;
    public $filterStatus = '';
    
    public function render()
    {
        $healthScores = CustomerHealthScore::with('contact')
            ->when($this->filterStatus, fn($query) => 
                $query->where('health_status', $this->filterStatus)
            )
            ->latest('last_calculated_at')
            ->take(20)
            ->get();

        $stats = [
            'total_customers' => CustomerHealthScore::count(),
            'excellent' => CustomerHealthScore::where('health_status', 'excellent')->count(),
            'good' => CustomerHealthScore::where('health_status', 'good')->count(),
            'at_risk' => CustomerHealthScore::where('health_status', 'at_risk')->count(),
            'critical' => CustomerHealthScore::where('health_status', 'critical')->count(),
            'avg_score' => CustomerHealthScore::avg('overall_score') ?? 0
        ];

        return view('livewire.customer-health-dashboard', compact('healthScores', 'stats'));
    }

    public function viewDetails($healthScoreId)
    {
        $this->healthScoreDetails = CustomerHealthScore::with('contact')->find($healthScoreId);
    }

    public function recalculateScore($healthScoreId)
    {
        $healthScore = CustomerHealthScore::find($healthScoreId);
        if ($healthScore) {
            $healthScore->recalculate();
            $this->dispatch('score-recalculated');
        }
    }

    public function recalculateAllScores()
    {
        CustomerHealthScore::chunk(100, function($scores) {
            foreach ($scores as $score) {
                if ($score->needsUpdate()) {
                    $score->recalculate();
                }
            }
        });
        
        $this->dispatch('all-scores-recalculated');
    }

    public function generateHealthScore($contactId)
    {
        $contact = Contact::find($contactId);
        if (!$contact) {
            return;
        }

        // Check if health score already exists
        $existingScore = CustomerHealthScore::where('contact_id', $contactId)->first();
        
        if ($existingScore) {
            $existingScore->recalculate();
        } else {
            // Create new health score
            $healthScore = new CustomerHealthScore([
                'contact_id' => $contactId,
                'overall_score' => 75, // Default starting score
                'score_breakdown' => [
                    'engagement' => 75,
                    'support_satisfaction' => 80,
                    'product_usage' => 70,
                    'payment_history' => 85,
                    'communication_frequency' => 65
                ],
                'health_status' => 'good',
                'risk_factors' => [],
                'improvement_suggestions' => [],
                'last_calculated_at' => now()
            ]);
            
            $healthScore->save();
            $healthScore->recalculate();
        }

        $this->dispatch('health-score-generated');
    }

    public function closeDetails()
    {
        $this->healthScoreDetails = null;
    }

    public function updatedFilterStatus()
    {
        // Component will re-render automatically
    }
}