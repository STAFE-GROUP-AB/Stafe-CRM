<?php

namespace App\Livewire;

use App\Models\Company;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
#[Title('Companies')]
class CompanyManager extends Component
{
    use WithPagination, WithFileUploads;

    // View state
    public $view = 'list'; // list, create, edit, show
    public $selectedCompany = null;
    
    // Search and filters
    public $search = '';
    public $statusFilter = '';
    public $industryFilter = '';
    public $ownerFilter = '';
    public $perPage = 10;
    
    // Form fields
    public $name = '';
    public $email = '';
    public $phone = '';
    public $website = '';
    public $industry = '';
    public $company_size = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $postal_code = '';
    public $country = '';
    public $description = '';
    public $logo_url = '';
    public $logo = null;
    public $owner_id = '';
    public $annual_revenue = '';
    public $number_of_employees = '';
    public $founded_year = '';
    public $tax_id = '';
    public $linkedin_url = '';
    public $twitter_url = '';
    public $facebook_url = '';
    public $custom_fields = [];
    
    // Bulk operations
    public $selectAll = false;
    public $selectedIds = [];
    public $bulkAction = '';
    
    // Modal states
    public $showDeleteModal = false;
    public $showBulkDeleteModal = false;
    public $companyToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'industryFilter' => ['except' => ''],
        'ownerFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:255',
        'website' => 'nullable|url|max:255',
        'industry' => 'nullable|string|max:255',
        'company_size' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'state' => 'nullable|string|max:255',
        'postal_code' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'logo' => 'nullable|image|max:2048',
        'owner_id' => 'required|exists:users,id',
        'annual_revenue' => 'nullable|numeric|min:0',
        'number_of_employees' => 'nullable|integer|min:0',
        'founded_year' => 'nullable|integer|min:1800|max:2100',
        'tax_id' => 'nullable|string|max:255',
        'linkedin_url' => 'nullable|url|max:255',
        'twitter_url' => 'nullable|url|max:255',
        'facebook_url' => 'nullable|url|max:255',
    ];

    public function mount($company = null)
    {
        $this->owner_id = auth()->id();
        
        // Handle route actions
        $action = request()->route()->defaults['action'] ?? null;
        
        if ($action === 'create') {
            $this->create();
        } elseif ($action === 'show' && $company) {
            $this->show($company);
        } elseif ($action === 'edit' && $company) {
            $this->edit($company);
        }
        
        // Also check query parameter for backward compatibility
        if (!$action && request()->query('action') === 'create') {
            $this->create();
        }
    }

    public function render()
    {
        $data = [
            'companies' => $this->getCompanies(),
            'users' => User::select('id', 'name')->get(),
            'industries' => $this->getIndustries(),
            'companySizes' => $this->getCompanySizes(),
        ];

        return view('livewire.company-manager', $data);
    }

    public function getCompanies()
    {
        if ($this->view !== 'list') {
            return collect();
        }

        return Company::with(['owner', 'contacts'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('website', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->industryFilter, function ($query) {
                $query->where('industry', $this->industryFilter);
            })
            ->when($this->ownerFilter, function ($query) {
                $query->where('owner_id', $this->ownerFilter);
            })
            ->latest()
            ->paginate($this->perPage);
    }

    public function create()
    {
        $this->reset(['selectedCompany']);
        $this->resetForm();
        $this->view = 'create';
    }

    public function store()
    {
        $this->validate();

        $data = $this->getFormData();

        if ($this->logo) {
            $data['logo_url'] = $this->logo->store('company-logos', 'public');
        }

        Company::create($data);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Company created successfully!'
        ]);

        $this->view = 'list';
        $this->resetForm();
    }

    public function show($id)
    {
        $this->selectedCompany = Company::with(['owner', 'contacts', 'deals', 'tasks', 'notes'])
            ->findOrFail($id);
        $this->view = 'show';
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        $this->selectedCompany = $company;
        $this->fillForm($company);
        $this->view = 'edit';
    }

    public function update()
    {
        $this->validate();

        $data = $this->getFormData();

        if ($this->logo) {
            // Delete old logo if exists
            if ($this->selectedCompany->logo_url) {
                Storage::disk('public')->delete($this->selectedCompany->logo_url);
            }
            $data['logo_url'] = $this->logo->store('company-logos', 'public');
        }

        $this->selectedCompany->update($data);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Company updated successfully!'
        ]);

        $this->view = 'show';
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->companyToDelete = Company::findOrFail($id);
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->companyToDelete) {
            // Delete logo if exists
            if ($this->companyToDelete->logo_url) {
                Storage::disk('public')->delete($this->companyToDelete->logo_url);
            }
            
            $this->companyToDelete->delete();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Company deleted successfully!'
            ]);
        }

        $this->showDeleteModal = false;
        $this->companyToDelete = null;
        
        if ($this->view === 'show' || $this->view === 'edit') {
            $this->view = 'list';
        }
    }

    public function backToList()
    {
        $this->view = 'list';
        $this->reset(['selectedCompany']);
        $this->resetForm();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->getCompanies()->pluck('id')->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedSelectedIds()
    {
        $this->selectAll = count($this->selectedIds) === $this->getCompanies()->count();
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Please select at least one company.'
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
        }
    }

    public function bulkDelete()
    {
        $companies = Company::whereIn('id', $this->selectedIds)->get();
        
        foreach ($companies as $company) {
            if ($company->logo_url) {
                Storage::disk('public')->delete($company->logo_url);
            }
            $company->delete();
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => count($this->selectedIds) . ' companies deleted successfully!'
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
            'name', 'email', 'phone', 'website', 'industry', 'company_size',
            'address', 'city', 'state', 'postal_code', 'country', 'description',
            'logo', 'logo_url', 'annual_revenue', 'number_of_employees',
            'founded_year', 'tax_id', 'linkedin_url', 'twitter_url',
            'facebook_url', 'custom_fields'
        ]);
        
        $this->owner_id = auth()->id();
        $this->resetValidation();
    }

    private function fillForm($company)
    {
        $this->name = $company->name;
        $this->email = $company->email ?? '';
        $this->phone = $company->phone ?? '';
        $this->website = $company->website ?? '';
        $this->industry = $company->industry ?? '';
        $this->company_size = $company->company_size ?? '';
        $this->address = $company->address ?? '';
        $this->city = $company->city ?? '';
        $this->state = $company->state ?? '';
        $this->postal_code = $company->postal_code ?? '';
        $this->country = $company->country ?? '';
        $this->description = $company->description ?? '';
        $this->logo_url = $company->logo_url ?? '';
        $this->owner_id = $company->owner_id;
        $this->annual_revenue = $company->annual_revenue ?? '';
        $this->number_of_employees = $company->number_of_employees ?? '';
        $this->founded_year = $company->founded_year ?? '';
        $this->tax_id = $company->tax_id ?? '';
        $this->linkedin_url = $company->linkedin_url ?? '';
        $this->twitter_url = $company->twitter_url ?? '';
        $this->facebook_url = $company->facebook_url ?? '';
        $this->custom_fields = $company->custom_fields ?? [];
    }

    private function getFormData()
    {
        return [
            'name' => $this->name,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'website' => $this->website ?: null,
            'industry' => $this->industry ?: null,
            'company_size' => $this->company_size ?: null,
            'address' => $this->address ?: null,
            'city' => $this->city ?: null,
            'state' => $this->state ?: null,
            'postal_code' => $this->postal_code ?: null,
            'country' => $this->country ?: null,
            'description' => $this->description ?: null,
            'owner_id' => $this->owner_id,
            'annual_revenue' => $this->annual_revenue ?: null,
            'number_of_employees' => $this->number_of_employees ?: null,
            'founded_year' => $this->founded_year ?: null,
            'tax_id' => $this->tax_id ?: null,
            'linkedin_url' => $this->linkedin_url ?: null,
            'twitter_url' => $this->twitter_url ?: null,
            'facebook_url' => $this->facebook_url ?: null,
            'custom_fields' => $this->custom_fields,
        ];
    }

    private function getIndustries()
    {
        return [
            'Technology',
            'Healthcare',
            'Finance',
            'Retail',
            'Manufacturing',
            'Education',
            'Real Estate',
            'Consulting',
            'Media',
            'Transportation',
            'Energy',
            'Hospitality',
            'Other'
        ];
    }

    private function getCompanySizes()
    {
        return [
            '1-10' => '1-10 employees',
            '11-50' => '11-50 employees',
            '51-200' => '51-200 employees',
            '201-500' => '201-500 employees',
            '501-1000' => '501-1000 employees',
            '1001-5000' => '1001-5000 employees',
            '5001+' => '5001+ employees'
        ];
    }
}