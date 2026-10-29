<?php

namespace App\Livewire;

use App\Models\Contact;
use App\Models\LeadScoreHistory;
use App\Models\ScoringFactor;
use App\Models\UserAiConfiguration;
use App\Services\LeadScoringService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LeadScoringDashboard extends Component
{
    public $contacts = [];

    public $scoringFactors = [];

    public $selectedContact = null;

    public $leadScore = null;

    public $scoreHistory = [];

    public $recommendations = [];

    public $categoryScores = [];

    public $isCalculating = false;

    // Manual adjustment
    public bool $showAdjustmentModal = false;

    public int $adjustmentAmount = 0;

    public string $adjustmentReason = '';

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        // Get contacts with their lead scores
        $this->contacts = Contact::with(['leadScore', 'company'])
            ->latest()
            ->take(20)
            ->get();

        $this->scoringFactors = ScoringFactor::active()->ordered()->get();
    }

    public function calculateScore($contactId): void
    {
        $this->isCalculating = true;

        try {
            $contact = Contact::with(['company', 'emails', 'communications', 'deals'])->find($contactId);
            $scoringService = new LeadScoringService;

            // Get user's default AI configuration
            $userConfig = UserAiConfiguration::forUser(Auth::id())
                ->default()
                ->first();

            $this->leadScore = $scoringService->calculateScore($contact, $userConfig);
            $this->selectedContact = $contact;
            $this->recommendations = $scoringService->getRecommendations();
            $this->categoryScores = $scoringService->getCategoryScores();

            // Load score history
            $this->loadScoreHistory($this->leadScore->id);

            // Refresh contacts to show updated score
            $this->loadData();

            session()->flash('success', 'Lead score calculated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error calculating lead score: '.$e->getMessage());
        } finally {
            $this->isCalculating = false;
        }
    }

    public function viewScoreDetails($contactId): void
    {
        $this->selectedContact = Contact::with(['leadScore', 'company'])->find($contactId);
        $this->leadScore = $this->selectedContact->leadScore;

        if ($this->leadScore) {
            // Extract recommendations from raw_predictions
            $this->recommendations = $this->leadScore->raw_predictions['recommendations'] ?? [];
            $this->categoryScores = $this->leadScore->raw_predictions['category_scores'] ?? [];

            // Load score history
            $this->loadScoreHistory($this->leadScore->id);
        }
    }

    private function loadScoreHistory(int $leadScoreId): void
    {
        try {
            $this->scoreHistory = LeadScoreHistory::where('lead_score_id', $leadScoreId)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            // History table might not exist yet
            $this->scoreHistory = [];
        }
    }

    public function closeModal(): void
    {
        $this->selectedContact = null;
        $this->leadScore = null;
        $this->recommendations = [];
        $this->categoryScores = [];
        $this->scoreHistory = [];
        $this->showAdjustmentModal = false;
        $this->adjustmentAmount = 0;
        $this->adjustmentReason = '';
    }

    public function calculateAllScores(): void
    {
        $this->isCalculating = true;

        try {
            $scoringService = new LeadScoringService;
            $userConfig = UserAiConfiguration::forUser(Auth::id())
                ->default()
                ->first();

            $processedCount = 0;

            foreach ($this->contacts as $contact) {
                if (! $contact->leadScore || $contact->leadScore->isStale()) {
                    $fullContact = Contact::with(['company', 'emails', 'communications', 'deals'])->find($contact->id);
                    $scoringService->calculateScore($fullContact, $userConfig);
                    $processedCount++;
                }
            }

            $this->loadData();

            session()->flash('success', "Calculated scores for {$processedCount} contacts!");
        } catch (\Exception $e) {
            session()->flash('error', 'Error calculating scores: '.$e->getMessage());
        } finally {
            $this->isCalculating = false;
        }
    }

    public function openAdjustmentModal(): void
    {
        $this->showAdjustmentModal = true;
        $this->adjustmentAmount = 0;
        $this->adjustmentReason = '';
    }

    public function submitAdjustment(): void
    {
        if (! $this->leadScore || empty($this->adjustmentReason)) {
            session()->flash('error', 'Please provide a reason for the adjustment.');

            return;
        }

        try {
            $scoringService = new LeadScoringService;
            $this->leadScore = $scoringService->adjustScore(
                $this->leadScore,
                $this->adjustmentAmount,
                $this->adjustmentReason
            );

            $this->showAdjustmentModal = false;
            $this->adjustmentAmount = 0;
            $this->adjustmentReason = '';

            // Reload history
            $this->loadScoreHistory($this->leadScore->id);
            $this->loadData();

            session()->flash('success', 'Score adjusted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error adjusting score: '.$e->getMessage());
        }
    }

    public function getScoreColor($score): string
    {
        return match (true) {
            $score >= 80 => 'green',
            $score >= 60 => 'yellow',
            $score >= 40 => 'orange',
            default => 'red'
        };
    }

    public function getScoreColorClass($score): string
    {
        return match (true) {
            $score >= 80 => 'bg-green-500',
            $score >= 60 => 'bg-yellow-500',
            $score >= 40 => 'bg-orange-500',
            default => 'bg-red-500'
        };
    }

    public function getCategoryLabel(string $category): string
    {
        return match ($category) {
            'engagement' => 'Engagement',
            'firmographic' => 'Company Fit',
            'behavioral' => 'Behavior',
            'demographic' => 'Demographics',
            default => ucfirst($category)
        };
    }

    public function render()
    {
        return view('livewire.lead-scoring-dashboard');
    }
}
