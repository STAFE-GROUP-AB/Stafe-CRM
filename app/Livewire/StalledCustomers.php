<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Contact;
use App\Models\Communication;
use App\Models\User;

class StalledCustomers extends Component
{
    use WithPagination;

    public $stalledDays = 30; // Default to 30 days
    public $search = '';
    public $selectedOwner = '';
    public $sortBy = 'last_contacted_at';
    public $sortDirection = 'asc';
    public $stats = [];

    protected $queryString = ['search', 'selectedOwner', 'stalledDays'];

    public function render()
    {
        $salesReps = User::orderBy('name')->get();
        
        $stalledCustomers = Contact::query()
            ->with(['owner', 'company', 'communications' => function($query) {
                $query->latest()->limit(1);
            }])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhereHas('company', function($companyQuery) {
                          $companyQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->selectedOwner, function($query) {
                $query->where('owner_id', $this->selectedOwner);
            })
            ->where(function($query) {
                $query->where('last_contacted_at', '<=', now()->subDays($this->stalledDays))
                      ->orWhereNull('last_contacted_at');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(20);

        // Get the count for each sales rep
        $stalledByRep = Contact::query()
            ->selectRaw('owner_id, count(*) as count')
            ->with('owner')
            ->where(function($query) {
                $query->where('last_contacted_at', '<=', now()->subDays($this->stalledDays))
                      ->orWhereNull('last_contacted_at');
            })
            ->groupBy('owner_id')
            ->get();

        $this->stats = [
            'total_stalled' => Contact::where(function($query) {
                $query->where('last_contacted_at', '<=', now()->subDays($this->stalledDays))
                      ->orWhereNull('last_contacted_at');
            })->count(),
            'never_contacted' => Contact::whereNull('last_contacted_at')->count(),
            'avg_days_since_contact' => Contact::whereNotNull('last_contacted_at')
                ->selectRaw('AVG(CAST(julianday("now") - julianday(last_contacted_at) AS INTEGER)) as avg_days')
                ->value('avg_days') ?? 0,
        ];

        return view('livewire.stalled-customers', [
            'stalledCustomers' => $stalledCustomers,
            'salesReps' => $salesReps,
            'stalledByRep' => $stalledByRep,
        ])->layout('layouts.app');
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updateStalledDays($days)
    {
        $this->stalledDays = $days;
        $this->resetPage();
    }

    public function updateLastContacted($contactId)
    {
        $contact = Contact::find($contactId);
        if ($contact) {
            $contact->update(['last_contacted_at' => now()]);
            $this->dispatch('contact-updated', ['message' => 'Contact updated successfully']);
        }
    }
}