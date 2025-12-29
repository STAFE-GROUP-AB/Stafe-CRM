<?php

namespace App\Livewire;

use App\Models\Deal;
use App\Models\Company;
use App\Models\Contact;
use App\Models\User;
use App\Models\PipelineStage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

#[Layout('layouts.app')]
#[Title('Deals')]
class DealManager extends Component
{
    use WithPagination;

    // View state
    public $view = 'list'; // list, create, edit, show
    public $selectedDeal = null;
    
    // Search and filters
    public $search = '';
    public $statusFilter = '';
    public $stageFilter = '';
    public $ownerFilter = '';
    public $companyFilter = '';
    public $perPage = 10;
    
    // Form fields
    public $name = '';
    public $description = '';
    public $value = '';
    public $currency = 'USD';
    public $probability = 20;
    public $expected_close_date = '';
    public $actual_close_date = '';
    public $status = 'open';
    public $pipeline_stage_id = '';
    public $company_id = '';
    public $contact_id = '';
    public $source = '';
    public $type = '';
    public $close_reason = '';
    public $custom_fields = [];
    public $owner_id = '';
    
    // Bulk operations
    public $selectAll = false;
    public $selectedIds = [];
    public $bulkAction = '';
    
    // Modal states
    public $showDeleteModal = false;
    public $showBulkDeleteModal = false;
    public $dealToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'stageFilter' => ['except' => ''],
        'ownerFilter' => ['except' => ''],
        'companyFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'value' => 'required|numeric|min:0',
        'currency' => 'required|string|max:3',
        'probability' => 'required|integer|min:0|max:100',
        'expected_close_date' => 'required|date',
        'actual_close_date' => 'nullable|date',
        'status' => 'required|in:open,won,lost',
        'pipeline_stage_id' => 'required|exists:pipeline_stages,id',
        'company_id' => 'nullable|exists:companies,id',
        'contact_id' => 'nullable|exists:contacts,id',
        'source' => 'nullable|string|max:255',
        'type' => 'nullable|string|max:255',
        'close_reason' => 'nullable|string|max:255',
        'owner_id' => 'required|exists:users,id',
    ];

    public function mount()
    {
        $this->owner_id = auth()->id();
        $this->expected_close_date = Carbon::now()->addMonth()->format('Y-m-d');
        
        // Set default pipeline stage if available
        $defaultStage = PipelineStage::active()->ordered()->first();
        if ($defaultStage) {
            $this->pipeline_stage_id = $defaultStage->id;
            $this->probability = $defaultStage->default_probability ?? 20;
        }
        
        // Check if we should open create form
        if (request()->query('action') === 'create') {
            $this->create();
        }
    }

    public function render()
    {
        $data = [
            'deals' => $this->getDeals(),
            'companies' => Company::select('id', 'name')->orderBy('name')->get(),
            'contacts' => Contact::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'users' => User::select('id', 'name')->get(),
            'pipelineStages' => PipelineStage::active()->ordered()->get(),
            'statuses' => $this->getStatuses(),
            'sources' => $this->getSources(),
            'types' => $this->getTypes(),
            'currencies' => $this->getCurrencies(),
        ];

        return view('livewire.deal-manager', $data);
    }

    public function getDeals()
    {
        if ($this->view !== 'list') {
            return collect();
        }

        return Deal::with(['company', 'contact', 'owner', 'pipelineStage'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->stageFilter, function ($query) {
                $query->where('pipeline_stage_id', $this->stageFilter);
            })
            ->when($this->ownerFilter, function ($query) {
                $query->where('owner_id', $this->ownerFilter);
            })
            ->when($this->companyFilter, function ($query) {
                $query->where('company_id', $this->companyFilter);
            })
            ->latest()
            ->paginate($this->perPage);
    }

    public function create()
    {
        $this->reset(['selectedDeal']);
        $this->resetForm();
        $this->view = 'create';
    }

    public function store()
    {
        $this->validate();

        $data = $this->getFormData();
        
        // Generate slug from name
        $data['slug'] = \Str::slug($this->name);

        Deal::create($data);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Deal created successfully!'
        ]);

        $this->view = 'list';
        $this->resetForm();
    }

    public function show($id)
    {
        $this->selectedDeal = Deal::with(['company', 'contact', 'owner', 'pipelineStage', 'tasks', 'notes', 'emails', 'activityLogs'])
            ->findOrFail($id);
        $this->view = 'show';
    }

    public function edit($id)
    {
        $deal = Deal::findOrFail($id);
        $this->selectedDeal = $deal;
        $this->fillForm($deal);
        $this->view = 'edit';
    }

    public function update()
    {
        $this->validate();

        $data = $this->getFormData();
        
        // Update slug if name changed
        if ($this->selectedDeal->name !== $this->name) {
            $data['slug'] = \Str::slug($this->name);
        }

        $this->selectedDeal->update($data);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Deal updated successfully!'
        ]);

        $this->view = 'show';
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->dealToDelete = Deal::findOrFail($id);
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->dealToDelete) {
            $this->dealToDelete->delete();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Deal deleted successfully!'
            ]);
        }

        $this->showDeleteModal = false;
        $this->dealToDelete = null;
        
        if ($this->view === 'show' || $this->view === 'edit') {
            $this->view = 'list';
        }
    }

    public function backToList()
    {
        $this->view = 'list';
        $this->reset(['selectedDeal']);
        $this->resetForm();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->getDeals()->pluck('id')->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedSelectedIds()
    {
        $this->selectAll = count($this->selectedIds) === $this->getDeals()->count();
    }

    public function updatedPipelineStageId($value)
    {
        // Update probability based on selected stage
        $stage = PipelineStage::find($value);
        if ($stage && $stage->default_probability) {
            $this->probability = $stage->default_probability;
        }
        
        // Update status if stage is closed
        if ($stage && $stage->is_closed) {
            $this->status = $stage->is_won ? 'won' : 'lost';
        } else {
            $this->status = 'open';
        }
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Please select at least one deal.'
            ]);
            return;
        }

        switch ($this->bulkAction) {
            case 'delete':
                $this->showBulkDeleteModal = true;
                break;
            case 'export':
                $this->exportSelected();
                break;
            case 'change_status':
                // TODO: Implement bulk status change
                break;
            case 'change_stage':
                // TODO: Implement bulk stage change
                break;
        }
    }

    public function bulkDelete()
    {
        Deal::whereIn('id', $this->selectedIds)->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => count($this->selectedIds) . ' deals deleted successfully!'
        ]);

        $this->showBulkDeleteModal = false;
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function exportSelected()
    {
        // TODO: Implement export functionality
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Export functionality coming soon!'
        ]);
    }

    private function resetForm()
    {
        $this->reset([
            'name', 'description', 'value', 'currency', 'probability',
            'expected_close_date', 'actual_close_date', 'status',
            'pipeline_stage_id', 'company_id', 'contact_id', 'source',
            'type', 'close_reason', 'custom_fields'
        ]);
        
        $this->owner_id = auth()->id();
        $this->currency = 'USD';
        $this->probability = 20;
        $this->status = 'open';
        $this->expected_close_date = Carbon::now()->addMonth()->format('Y-m-d');
        
        // Set default pipeline stage
        $defaultStage = PipelineStage::active()->ordered()->first();
        if ($defaultStage) {
            $this->pipeline_stage_id = $defaultStage->id;
            $this->probability = $defaultStage->default_probability ?? 20;
        }
        
        $this->resetValidation();
    }

    private function fillForm($deal)
    {
        $this->name = $deal->name;
        $this->description = $deal->description ?? '';
        $this->value = $deal->value;
        $this->currency = $deal->currency ?? 'USD';
        $this->probability = $deal->probability ?? 20;
        $this->expected_close_date = $deal->expected_close_date?->format('Y-m-d') ?? '';
        $this->actual_close_date = $deal->actual_close_date?->format('Y-m-d') ?? '';
        $this->status = $deal->status ?? 'open';
        $this->pipeline_stage_id = $deal->pipeline_stage_id;
        $this->company_id = $deal->company_id ?? '';
        $this->contact_id = $deal->contact_id ?? '';
        $this->source = $deal->source ?? '';
        $this->type = $deal->type ?? '';
        $this->close_reason = $deal->close_reason ?? '';
        $this->owner_id = $deal->owner_id;
        $this->custom_fields = $deal->custom_fields ?? [];
    }

    private function getFormData()
    {
        return [
            'name' => $this->name,
            'description' => $this->description ?: null,
            'value' => $this->value,
            'currency' => $this->currency,
            'probability' => $this->probability,
            'expected_close_date' => $this->expected_close_date,
            'actual_close_date' => $this->actual_close_date ?: null,
            'status' => $this->status,
            'pipeline_stage_id' => $this->pipeline_stage_id,
            'company_id' => $this->company_id ?: null,
            'contact_id' => $this->contact_id ?: null,
            'source' => $this->source ?: null,
            'type' => $this->type ?: null,
            'close_reason' => $this->close_reason ?: null,
            'owner_id' => $this->owner_id,
            'custom_fields' => $this->custom_fields,
        ];
    }

    private function getStatuses()
    {
        return [
            'open' => 'Open',
            'won' => 'Won',
            'lost' => 'Lost',
        ];
    }

    private function getSources()
    {
        return [
            'Website',
            'Email Campaign',
            'Social Media',
            'Referral',
            'Partner',
            'Trade Show',
            'Cold Call',
            'Advertisement',
            'Other'
        ];
    }

    private function getTypes()
    {
        return [
            'New Business',
            'Existing Business - Upgrade',
            'Existing Business - Renewal',
            'Existing Business - Downgrade',
        ];
    }

    private function getCurrencies()
    {
        return [
            'USD' => 'USD - US Dollar',
            'EUR' => 'EUR - Euro',
            'GBP' => 'GBP - British Pound',
            'JPY' => 'JPY - Japanese Yen',
            'CAD' => 'CAD - Canadian Dollar',
            'AUD' => 'AUD - Australian Dollar',
            'CHF' => 'CHF - Swiss Franc',
            'CNY' => 'CNY - Chinese Yuan',
            'SEK' => 'SEK - Swedish Krona',
            'NZD' => 'NZD - New Zealand Dollar',
        ];
    }
}