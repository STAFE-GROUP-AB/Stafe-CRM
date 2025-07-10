<?php

namespace App\Livewire;

use App\Models\SalesPlaybook;
use App\Models\PlaybookExecution;
use App\Models\Deal;
use App\Models\Contact;
use Livewire\Component;
use Livewire\WithPagination;

class SalesPlaybooksManager extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $showExecutionModal = false;
    public $editingPlaybook = null;
    public $executingPlaybook = null;
    public $currentExecution = null;
    
    // Form fields
    public $title = '';
    public $description = '';
    public $type = 'discovery';
    public $difficulty_level = 'beginner';
    public $overview = '';
    public $objectives = [''];
    public $prerequisites = [''];
    public $estimated_duration = '';
    public $status = 'draft';
    
    // Execution fields
    public $execution_deal_id = '';
    public $execution_contact_id = '';
    
    // Search and filters
    public $search = '';
    public $typeFilter = '';
    public $difficultyFilter = '';
    public $statusFilter = '';

    public function render()
    {
        $playbooks = SalesPlaybook::with(['creator', 'steps'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('overview', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->difficultyFilter, function ($query) {
                $query->where('difficulty_level', $this->difficultyFilter);
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        $deals = Deal::select('id', 'name')->where('status', 'open')->get();
        $contacts = Contact::select('id', 'first_name', 'last_name')->get();

        // Get user's active executions
        $activeExecutions = PlaybookExecution::with('playbook')
            ->where('user_id', auth()->id())
            ->where('status', 'in_progress')
            ->get();

        return view('livewire.sales-enablement.sales-playbooks', [
            'playbooks' => $playbooks,
            'deals' => $deals,
            'contacts' => $contacts,
            'activeExecutions' => $activeExecutions,
        ]);
    }

    public function createPlaybook()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function editPlaybook(SalesPlaybook $playbook)
    {
        $this->editingPlaybook = $playbook;
        $this->title = $playbook->title;
        $this->description = $playbook->description;
        $this->type = $playbook->type;
        $this->difficulty_level = $playbook->difficulty_level;
        $this->overview = $playbook->overview;
        $this->objectives = $playbook->objectives ?? [''];
        $this->prerequisites = $playbook->prerequisites ?? [''];
        $this->estimated_duration = $playbook->estimated_duration;
        $this->status = $playbook->status;
        $this->showCreateModal = true;
    }

    public function startPlaybook(SalesPlaybook $playbook)
    {
        $this->executingPlaybook = $playbook;
        $this->execution_deal_id = '';
        $this->execution_contact_id = '';
        $this->showExecutionModal = true;
    }

    public function executePlaybook()
    {
        $this->validate([
            'execution_deal_id' => 'nullable|exists:deals,id',
            'execution_contact_id' => 'nullable|exists:contacts,id',
        ]);

        $deal = $this->execution_deal_id ? Deal::find($this->execution_deal_id) : null;
        $contact = $this->execution_contact_id ? Contact::find($this->execution_contact_id) : null;

        $execution = $this->executingPlaybook->startExecution(auth()->user(), $deal, $contact);
        
        $this->showExecutionModal = false;
        
        // Redirect to playbook execution page
        return redirect()->route('sales-enablement.playbook-execution', $execution->id);
    }

    public function continueExecution(PlaybookExecution $execution)
    {
        return redirect()->route('sales-enablement.playbook-execution', $execution->id);
    }

    public function savePlaybook()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:discovery,demo,objection_handling,closing,follow_up,onboarding',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'overview' => 'nullable|string',
            'estimated_duration' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
        ]);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'difficulty_level' => $this->difficulty_level,
            'overview' => $this->overview,
            'objectives' => array_filter($this->objectives),
            'prerequisites' => array_filter($this->prerequisites),
            'estimated_duration' => $this->estimated_duration,
            'status' => $this->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ];

        if ($this->editingPlaybook) {
            $this->editingPlaybook->update($data);
            session()->flash('message', 'Playbook updated successfully.');
        } else {
            SalesPlaybook::create($data);
            session()->flash('message', 'Playbook created successfully.');
        }

        $this->resetForm();
        $this->showCreateModal = false;
    }

    public function deletePlaybook(SalesPlaybook $playbook)
    {
        $playbook->delete();
        session()->flash('message', 'Playbook deleted successfully.');
    }

    public function duplicatePlaybook(SalesPlaybook $playbook)
    {
        $newPlaybook = $playbook->replicate();
        $newPlaybook->title = $playbook->title . ' (Copy)';
        $newPlaybook->status = 'draft';
        $newPlaybook->usage_count = 0;
        $newPlaybook->success_rate = 0;
        $newPlaybook->average_rating = 0;
        $newPlaybook->rating_count = 0;
        $newPlaybook->last_used_at = null;
        $newPlaybook->save();

        // Duplicate steps
        foreach ($playbook->steps as $step) {
            $newStep = $step->replicate();
            $newStep->playbook_id = $newPlaybook->id;
            $newStep->save();
        }

        session()->flash('message', 'Playbook duplicated successfully.');
    }

    // Dynamic array field methods
    public function addObjective()
    {
        $this->objectives[] = '';
    }

    public function removeObjective($index)
    {
        unset($this->objectives[$index]);
        $this->objectives = array_values($this->objectives);
    }

    public function addPrerequisite()
    {
        $this->prerequisites[] = '';
    }

    public function removePrerequisite($index)
    {
        unset($this->prerequisites[$index]);
        $this->prerequisites = array_values($this->prerequisites);
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->showExecutionModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingPlaybook = null;
        $this->executingPlaybook = null;
        $this->title = '';
        $this->description = '';
        $this->type = 'discovery';
        $this->difficulty_level = 'beginner';
        $this->overview = '';
        $this->objectives = [''];
        $this->prerequisites = [''];
        $this->estimated_duration = '';
        $this->status = 'draft';
    }
}