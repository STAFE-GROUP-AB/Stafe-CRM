<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CustomerJourney;
use App\Models\CustomerJourneyStage;
use App\Models\Contact;

class JourneyMappingDashboard extends Component
{
    public $selectedContact = null;
    public $showStageModal = false;
    public $editingStage = null;
    public $stageName = '';
    public $stageDescription = '';
    public $stageColor = '#3B82F6';
    public $orderIndex = 1;
    
    public function render()
    {
        $stages = CustomerJourneyStage::active()
            ->ordered()
            ->withCount('customerJourneys')
            ->get();

        $journeys = CustomerJourney::with(['contact', 'currentStage'])
            ->latest('updated_at')
            ->take(10)
            ->get();

        $stats = [
            'total_journeys' => CustomerJourney::count(),
            'active_stages' => CustomerJourneyStage::active()->count(),
            'avg_progression' => CustomerJourney::avg('progression_score') ?? 0,
            'customers_at_risk' => CustomerJourney::whereHas('contact.healthScore', function($query) {
                $query->whereIn('health_status', ['at_risk', 'critical']);
            })->count()
        ];

        return view('livewire.journey-mapping-dashboard', compact('stages', 'journeys', 'stats'));
    }

    public function createStage()
    {
        $this->showStageModal = true;
        $this->reset(['stageName', 'stageDescription', 'stageColor', 'orderIndex']);
        $this->orderIndex = CustomerJourneyStage::max('order_index') + 1;
    }

    public function editStage($stageId)
    {
        $stage = CustomerJourneyStage::findOrFail($stageId);
        $this->editingStage = $stage;
        $this->stageName = $stage->name;
        $this->stageDescription = $stage->description;
        $this->stageColor = $stage->color;
        $this->orderIndex = $stage->order_index;
        $this->showStageModal = true;
    }

    public function saveStage()
    {
        $this->validate([
            'stageName' => 'required|min:3|max:255',
            'stageDescription' => 'nullable|max:1000',
            'stageColor' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'orderIndex' => 'required|integer|min:1'
        ]);

        $data = [
            'name' => $this->stageName,
            'description' => $this->stageDescription,
            'color' => $this->stageColor,
            'order_index' => $this->orderIndex,
            'is_active' => true
        ];

        if ($this->editingStage) {
            $this->editingStage->update($data);
            $this->dispatch('stage-updated');
        } else {
            CustomerJourneyStage::create($data);
            $this->dispatch('stage-created');
        }

        $this->closeStageModal();
    }

    public function deleteStage($stageId)
    {
        $stage = CustomerJourneyStage::findOrFail($stageId);
        
        // Check if stage has active journeys
        if ($stage->customerJourneys()->count() > 0) {
            $this->dispatch('stage-has-journeys');
            return;
        }

        $stage->delete();
        $this->dispatch('stage-deleted');
    }

    public function viewContactJourney($contactId)
    {
        $this->selectedContact = $contactId;
        $this->dispatch('contact-journey-selected');
    }

    public function createJourneyForContact($contactId)
    {
        $contact = Contact::findOrFail($contactId);
        
        // Check if journey already exists
        $existingJourney = CustomerJourney::where('contact_id', $contactId)->first();
        if ($existingJourney) {
            $this->dispatch('journey-already-exists');
            return;
        }

        // Get first stage
        $firstStage = CustomerJourneyStage::active()->ordered()->first();
        if (!$firstStage) {
            $this->dispatch('no-stages-available');
            return;
        }

        CustomerJourney::create([
            'contact_id' => $contactId,
            'current_stage_id' => $firstStage->id,
            'stage_entered_at' => now(),
            'stage_history' => [],
            'touchpoints' => [],
            'progression_score' => 0
        ]);

        $this->dispatch('journey-created');
    }

    public function moveContactToStage($contactId, $stageId)
    {
        $journey = CustomerJourney::where('contact_id', $contactId)->first();
        if (!$journey) {
            $this->createJourneyForContact($contactId);
            $journey = CustomerJourney::where('contact_id', $contactId)->first();
        }

        if ($journey && $journey->moveToStage($stageId)) {
            $journey->calculateProgressionScore();
            $this->dispatch('contact-moved-to-stage');
        }
    }

    public function addTouchpoint($contactId, $type, $description = '')
    {
        $journey = CustomerJourney::where('contact_id', $contactId)->first();
        if ($journey) {
            $journey->addTouchpoint($type, [
                'description' => $description,
                'source' => 'manual'
            ]);
            $this->dispatch('touchpoint-added');
        }
    }

    public function closeStageModal()
    {
        $this->showStageModal = false;
        $this->editingStage = null;
        $this->reset(['stageName', 'stageDescription', 'stageColor', 'orderIndex']);
    }

    public function getJourneyVisualization()
    {
        $stages = CustomerJourneyStage::active()->ordered()->get();
        $journeys = CustomerJourney::with('contact')->get();

        $visualization = [];
        foreach ($stages as $stage) {
            $stageJourneys = $journeys->where('current_stage_id', $stage->id);
            $visualization[] = [
                'stage' => $stage,
                'count' => $stageJourneys->count(),
                'customers' => $stageJourneys->take(5)->pluck('contact.first_name')->toArray()
            ];
        }

        return $visualization;
    }
}