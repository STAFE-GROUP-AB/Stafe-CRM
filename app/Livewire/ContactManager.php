<?php

namespace App\Livewire;

use App\Models\Contact;
use App\Models\Company;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
#[Title('Contacts')]
class ContactManager extends Component
{
    use WithPagination, WithFileUploads;

    // View state
    public $view = 'list'; // list, create, edit, show
    public $selectedContact = null;
    
    // Search and filters
    public $search = '';
    public $statusFilter = '';
    public $companyFilter = '';
    public $ownerFilter = '';
    public $perPage = 10;
    
    // Form fields
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $phone = '';
    public $mobile = '';
    public $title = '';
    public $department = '';
    public $company_id = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $postal_code = '';
    public $country = '';
    public $timezone = 'UTC';
    public $birthday = '';
    public $bio = '';
    public $social_links = [];
    public $custom_fields = [];
    public $avatar_url = '';
    public $avatar = null;
    public $status = 'active';
    public $source = '';
    public $lifetime_value = '';
    public $owner_id = '';
    
    // Social media fields
    public $linkedin_url = '';
    public $twitter_url = '';
    public $facebook_url = '';
    
    // Bulk operations
    public $selectAll = false;
    public $selectedIds = [];
    public $bulkAction = '';
    
    // Modal states
    public $showDeleteModal = false;
    public $showBulkDeleteModal = false;
    public $contactToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'companyFilter' => ['except' => ''],
        'ownerFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:contacts,email',
        'phone' => 'nullable|string|max:255',
        'mobile' => 'nullable|string|max:255',
        'title' => 'nullable|string|max:255',
        'department' => 'nullable|string|max:255',
        'company_id' => 'nullable|exists:companies,id',
        'address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'state' => 'nullable|string|max:255',
        'postal_code' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'timezone' => 'required|string|max:255',
        'birthday' => 'nullable|date',
        'bio' => 'nullable|string',
        'avatar' => 'nullable|image|max:2048',
        'status' => 'required|in:active,inactive,lead,customer',
        'source' => 'nullable|string|max:255',
        'lifetime_value' => 'nullable|numeric|min:0',
        'owner_id' => 'required|exists:users,id',
    ];

    public function mount()
    {
        $this->owner_id = auth()->id();
        
        // Check if we should open create form
        if (request()->query('action') === 'create') {
            $this->create();
        }
    }

    public function render()
    {
        $data = [
            'contacts' => $this->getContacts(),
            'companies' => Company::select('id', 'name')->orderBy('name')->get(),
            'users' => User::select('id', 'name')->get(),
            'statuses' => $this->getStatuses(),
            'sources' => $this->getSources(),
            'timezones' => $this->getTimezones(),
        ];

        return view('livewire.contact-manager', $data);
    }

    public function getContacts()
    {
        if ($this->view !== 'list') {
            return collect();
        }

        return Contact::with(['company', 'owner'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('mobile', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->companyFilter, function ($query) {
                $query->where('company_id', $this->companyFilter);
            })
            ->when($this->ownerFilter, function ($query) {
                $query->where('owner_id', $this->ownerFilter);
            })
            ->latest()
            ->paginate($this->perPage);
    }

    public function create()
    {
        $this->reset(['selectedContact']);
        $this->resetForm();
        $this->view = 'create';
    }

    public function store()
    {
        $this->validate();

        $data = $this->getFormData();

        if ($this->avatar) {
            $data['avatar_url'] = $this->avatar->store('contact-avatars', 'public');
        }

        // Prepare social links
        $socialLinks = [];
        if ($this->linkedin_url) $socialLinks['linkedin'] = $this->linkedin_url;
        if ($this->twitter_url) $socialLinks['twitter'] = $this->twitter_url;
        if ($this->facebook_url) $socialLinks['facebook'] = $this->facebook_url;
        $data['social_links'] = $socialLinks;

        Contact::create($data);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Contact created successfully!'
        ]);

        $this->view = 'list';
        $this->resetForm();
    }

    public function show($id)
    {
        $this->selectedContact = Contact::with(['company', 'owner', 'deals', 'tasks', 'notes', 'communications'])
            ->findOrFail($id);
        $this->view = 'show';
    }

    public function edit($id)
    {
        $contact = Contact::findOrFail($id);
        $this->selectedContact = $contact;
        $this->fillForm($contact);
        $this->view = 'edit';
    }

    public function update()
    {
        $rules = $this->rules;
        // Update email validation to ignore current contact
        $rules['email'] = 'required|email|max:255|unique:contacts,email,' . $this->selectedContact->id;
        
        $this->validate($rules);

        $data = $this->getFormData();

        if ($this->avatar) {
            // Delete old avatar if exists
            if ($this->selectedContact->avatar_url) {
                Storage::disk('public')->delete($this->selectedContact->avatar_url);
            }
            $data['avatar_url'] = $this->avatar->store('contact-avatars', 'public');
        }

        // Prepare social links
        $socialLinks = [];
        if ($this->linkedin_url) $socialLinks['linkedin'] = $this->linkedin_url;
        if ($this->twitter_url) $socialLinks['twitter'] = $this->twitter_url;
        if ($this->facebook_url) $socialLinks['facebook'] = $this->facebook_url;
        $data['social_links'] = $socialLinks;

        $this->selectedContact->update($data);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Contact updated successfully!'
        ]);

        $this->view = 'show';
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->contactToDelete = Contact::findOrFail($id);
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->contactToDelete) {
            // Delete avatar if exists
            if ($this->contactToDelete->avatar_url) {
                Storage::disk('public')->delete($this->contactToDelete->avatar_url);
            }
            
            $this->contactToDelete->delete();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Contact deleted successfully!'
            ]);
        }

        $this->showDeleteModal = false;
        $this->contactToDelete = null;
        
        if ($this->view === 'show' || $this->view === 'edit') {
            $this->view = 'list';
        }
    }

    public function backToList()
    {
        $this->view = 'list';
        $this->reset(['selectedContact']);
        $this->resetForm();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->getContacts()->pluck('id')->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedSelectedIds()
    {
        $this->selectAll = count($this->selectedIds) === $this->getContacts()->count();
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Please select at least one contact.'
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
        }
    }

    public function bulkDelete()
    {
        $contacts = Contact::whereIn('id', $this->selectedIds)->get();
        
        foreach ($contacts as $contact) {
            if ($contact->avatar_url) {
                Storage::disk('public')->delete($contact->avatar_url);
            }
            $contact->delete();
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => count($this->selectedIds) . ' contacts deleted successfully!'
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
            'first_name', 'last_name', 'email', 'phone', 'mobile', 'title',
            'department', 'company_id', 'address', 'city', 'state', 'postal_code',
            'country', 'timezone', 'birthday', 'bio', 'social_links', 'custom_fields',
            'avatar', 'avatar_url', 'status', 'source', 'lifetime_value',
            'linkedin_url', 'twitter_url', 'facebook_url'
        ]);
        
        $this->owner_id = auth()->id();
        $this->timezone = 'UTC';
        $this->status = 'active';
        $this->resetValidation();
    }

    private function fillForm($contact)
    {
        $this->first_name = $contact->first_name;
        $this->last_name = $contact->last_name;
        $this->email = $contact->email;
        $this->phone = $contact->phone ?? '';
        $this->mobile = $contact->mobile ?? '';
        $this->title = $contact->title ?? '';
        $this->department = $contact->department ?? '';
        $this->company_id = $contact->company_id ?? '';
        $this->address = $contact->address ?? '';
        $this->city = $contact->city ?? '';
        $this->state = $contact->state ?? '';
        $this->postal_code = $contact->postal_code ?? '';
        $this->country = $contact->country ?? '';
        $this->timezone = $contact->timezone ?? 'UTC';
        $this->birthday = $contact->birthday?->format('Y-m-d') ?? '';
        $this->bio = $contact->bio ?? '';
        $this->avatar_url = $contact->avatar_url ?? '';
        $this->status = $contact->status ?? 'active';
        $this->source = $contact->source ?? '';
        $this->lifetime_value = $contact->lifetime_value ?? '';
        $this->owner_id = $contact->owner_id;
        $this->custom_fields = $contact->custom_fields ?? [];
        
        // Extract social links
        $socialLinks = $contact->social_links ?? [];
        $this->linkedin_url = $socialLinks['linkedin'] ?? '';
        $this->twitter_url = $socialLinks['twitter'] ?? '';
        $this->facebook_url = $socialLinks['facebook'] ?? '';
    }

    private function getFormData()
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'mobile' => $this->mobile ?: null,
            'title' => $this->title ?: null,
            'department' => $this->department ?: null,
            'company_id' => $this->company_id ?: null,
            'address' => $this->address ?: null,
            'city' => $this->city ?: null,
            'state' => $this->state ?: null,
            'postal_code' => $this->postal_code ?: null,
            'country' => $this->country ?: null,
            'timezone' => $this->timezone,
            'birthday' => $this->birthday ?: null,
            'bio' => $this->bio ?: null,
            'status' => $this->status,
            'source' => $this->source ?: null,
            'lifetime_value' => $this->lifetime_value ?: null,
            'owner_id' => $this->owner_id,
            'custom_fields' => $this->custom_fields,
        ];
    }

    private function getStatuses()
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'lead' => 'Lead',
            'customer' => 'Customer',
        ];
    }

    private function getSources()
    {
        return [
            'Website',
            'Email Campaign',
            'Social Media',
            'Referral',
            'Trade Show',
            'Cold Call',
            'Partner',
            'Advertisement',
            'Other'
        ];
    }

    private function getTimezones()
    {
        return [
            'UTC' => 'UTC',
            'America/New_York' => 'Eastern Time',
            'America/Chicago' => 'Central Time',
            'America/Denver' => 'Mountain Time',
            'America/Los_Angeles' => 'Pacific Time',
            'Europe/London' => 'London',
            'Europe/Paris' => 'Paris',
            'Europe/Berlin' => 'Berlin',
            'Asia/Tokyo' => 'Tokyo',
            'Asia/Shanghai' => 'Shanghai',
            'Asia/Dubai' => 'Dubai',
            'Australia/Sydney' => 'Sydney',
        ];
    }
}