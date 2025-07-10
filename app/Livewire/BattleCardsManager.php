<?php

namespace App\Livewire;

use App\Models\BattleCard;
use App\Models\CompetitiveIntelligence;
use Livewire\Component;
use Livewire\WithPagination;

class BattleCardsManager extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $editingCard = null;
    public $selectedCard = null;
    
    // Form fields
    public $title = '';
    public $competitor_name = '';
    public $overview = '';
    public $our_strengths = [''];
    public $our_weaknesses = [''];
    public $competitor_strengths = [''];
    public $competitor_weaknesses = [''];
    public $key_differentiators = [''];
    public $objection_handling = [['objection' => '', 'response' => '']];
    public $winning_strategies = [''];
    public $threat_level = 'medium';
    public $status = 'draft';
    public $sales_notes = '';
    
    // Search and filters
    public $search = '';
    public $threatFilter = '';
    public $statusFilter = '';

    public function render()
    {
        $battleCards = BattleCard::with('creator')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('competitor_name', 'like', '%' . $this->search . '%')
                      ->orWhere('overview', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->threatFilter, function ($query) {
                $query->where('threat_level', $this->threatFilter);
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        // Get competitive intelligence data for insights
        $competitorInsights = CompetitiveIntelligence::selectRaw('competitor_name, COUNT(*) as deals_count, AVG(win_loss_probability) as avg_win_rate')
            ->groupBy('competitor_name')
            ->get();

        return view('livewire.sales-enablement.battle-cards', [
            'battleCards' => $battleCards,
            'competitorInsights' => $competitorInsights,
        ]);
    }

    public function createCard()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function editCard(BattleCard $card)
    {
        $this->editingCard = $card;
        $this->title = $card->title;
        $this->competitor_name = $card->competitor_name;
        $this->overview = $card->overview;
        $this->our_strengths = $card->our_strengths ?? [''];
        $this->our_weaknesses = $card->our_weaknesses ?? [''];
        $this->competitor_strengths = $card->competitor_strengths ?? [''];
        $this->competitor_weaknesses = $card->competitor_weaknesses ?? [''];
        $this->key_differentiators = $card->key_differentiators ?? [''];
        $this->objection_handling = $card->objection_handling ?? [['objection' => '', 'response' => '']];
        $this->winning_strategies = $card->winning_strategies ?? [''];
        $this->threat_level = $card->threat_level;
        $this->status = $card->status;
        $this->sales_notes = $card->sales_notes;
        $this->showCreateModal = true;
    }

    public function viewCard(BattleCard $card)
    {
        $this->selectedCard = $card;
        
        // Record view analytics
        $card->recordView(auth()->user());
    }

    public function saveCard()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'competitor_name' => 'required|string|max:255',
            'overview' => 'nullable|string',
            'threat_level' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:draft,active,archived',
            'sales_notes' => 'nullable|string',
        ]);

        $data = [
            'title' => $this->title,
            'competitor_name' => $this->competitor_name,
            'overview' => $this->overview,
            'our_strengths' => array_filter($this->our_strengths),
            'our_weaknesses' => array_filter($this->our_weaknesses),
            'competitor_strengths' => array_filter($this->competitor_strengths),
            'competitor_weaknesses' => array_filter($this->competitor_weaknesses),
            'key_differentiators' => array_filter($this->key_differentiators),
            'objection_handling' => array_filter($this->objection_handling, function($item) {
                return !empty($item['objection']) && !empty($item['response']);
            }),
            'winning_strategies' => array_filter($this->winning_strategies),
            'threat_level' => $this->threat_level,
            'status' => $this->status,
            'sales_notes' => $this->sales_notes,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ];

        if ($this->editingCard) {
            $this->editingCard->update($data);
            session()->flash('message', 'Battle card updated successfully.');
        } else {
            $card = BattleCard::create($data);
            // Update win rate from competitive intelligence
            $card->updateWinRate();
            session()->flash('message', 'Battle card created successfully.');
        }

        $this->resetForm();
        $this->showCreateModal = false;
    }

    public function deleteCard(BattleCard $card)
    {
        $card->delete();
        session()->flash('message', 'Battle card deleted successfully.');
    }

    public function duplicateCard(BattleCard $card)
    {
        $newCard = $card->replicate();
        $newCard->title = $card->title . ' (Copy)';
        $newCard->status = 'draft';
        $newCard->view_count = 0;
        $newCard->usage_count = 0;
        $newCard->save();

        session()->flash('message', 'Battle card duplicated successfully.');
    }

    // Dynamic array field methods
    public function addStrength()
    {
        $this->our_strengths[] = '';
    }

    public function removeStrength($index)
    {
        unset($this->our_strengths[$index]);
        $this->our_strengths = array_values($this->our_strengths);
    }

    public function addWeakness()
    {
        $this->our_weaknesses[] = '';
    }

    public function removeWeakness($index)
    {
        unset($this->our_weaknesses[$index]);
        $this->our_weaknesses = array_values($this->our_weaknesses);
    }

    public function addCompetitorStrength()
    {
        $this->competitor_strengths[] = '';
    }

    public function removeCompetitorStrength($index)
    {
        unset($this->competitor_strengths[$index]);
        $this->competitor_strengths = array_values($this->competitor_strengths);
    }

    public function addCompetitorWeakness()
    {
        $this->competitor_weaknesses[] = '';
    }

    public function removeCompetitorWeakness($index)
    {
        unset($this->competitor_weaknesses[$index]);
        $this->competitor_weaknesses = array_values($this->competitor_weaknesses);
    }

    public function addDifferentiator()
    {
        $this->key_differentiators[] = '';
    }

    public function removeDifferentiator($index)
    {
        unset($this->key_differentiators[$index]);
        $this->key_differentiators = array_values($this->key_differentiators);
    }

    public function addObjection()
    {
        $this->objection_handling[] = ['objection' => '', 'response' => ''];
    }

    public function removeObjection($index)
    {
        unset($this->objection_handling[$index]);
        $this->objection_handling = array_values($this->objection_handling);
    }

    public function addStrategy()
    {
        $this->winning_strategies[] = '';
    }

    public function removeStrategy($index)
    {
        unset($this->winning_strategies[$index]);
        $this->winning_strategies = array_values($this->winning_strategies);
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->selectedCard = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingCard = null;
        $this->title = '';
        $this->competitor_name = '';
        $this->overview = '';
        $this->our_strengths = [''];
        $this->our_weaknesses = [''];
        $this->competitor_strengths = [''];
        $this->competitor_weaknesses = [''];
        $this->key_differentiators = [''];
        $this->objection_handling = [['objection' => '', 'response' => '']];
        $this->winning_strategies = [''];
        $this->threat_level = 'medium';
        $this->status = 'draft';
        $this->sales_notes = '';
    }
}