<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\AiService;
use App\Models\Contact;
use App\Models\LeadScore;
use App\Models\ScoringFactor;
use Illuminate\Support\Facades\Auth;

class LeadScoringDashboard extends Component
{
    public $contacts = [];
    public $scoringFactors = [];
    public $selectedContact = null;
    public $leadScore = null;
    public $isCalculating = false;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Get contacts with their lead scores
        $this->contacts = Contact::with(['leadScore', 'company'])
            ->latest()
            ->take(20)
            ->get();

        $this->scoringFactors = ScoringFactor::active()->ordered()->get();
    }

    public function calculateScore($contactId)
    {
        $this->isCalculating = true;
        
        try {
            $contact = Contact::find($contactId);
            $aiService = new AiService();
            
            // Get user's default AI configuration
            $userConfig = \App\Models\UserAiConfiguration::forUser(Auth::id())
                ->default()
                ->first();
            
            $this->leadScore = $aiService->calculateLeadScore($contact, $userConfig);
            $this->selectedContact = $contact;
            
            // Refresh contacts to show updated score
            $this->loadData();
            
            session()->flash('success', 'Lead score calculated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error calculating lead score: ' . $e->getMessage());
        } finally {
            $this->isCalculating = false;
        }
    }

    public function viewScoreDetails($contactId)
    {
        $this->selectedContact = Contact::with(['leadScore', 'company'])->find($contactId);
        $this->leadScore = $this->selectedContact->leadScore;
    }

    public function closeModal()
    {
        $this->selectedContact = null;
        $this->leadScore = null;
    }

    public function calculateAllScores()
    {
        $this->isCalculating = true;
        
        try {
            $aiService = new AiService();
            $userConfig = \App\Models\UserAiConfiguration::forUser(Auth::id())
                ->default()
                ->first();

            $processedCount = 0;
            
            foreach ($this->contacts as $contact) {
                if (!$contact->leadScore || $contact->leadScore->isStale()) {
                    $aiService->calculateLeadScore($contact, $userConfig);
                    $processedCount++;
                }
            }
            
            $this->loadData();
            
            session()->flash('success', "Calculated scores for {$processedCount} contacts!");
        } catch (\Exception $e) {
            session()->flash('error', 'Error calculating scores: ' . $e->getMessage());
        } finally {
            $this->isCalculating = false;
        }
    }

    public function getScoreColor($score)
    {
        return match (true) {
            $score >= 80 => 'green',
            $score >= 60 => 'yellow', 
            $score >= 40 => 'orange',
            default => 'red'
        };
    }

    public function render()
    {
        return view('livewire.lead-scoring-dashboard');
    }
}
