<?php

namespace App\Livewire;

use App\Models\SavedSearch;
use App\Models\Contact;
use App\Models\Company;
use App\Models\Deal;
use App\Models\Task;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class SearchManager extends Component
{
    use WithPagination;

    public $query = '';
    public $searchType = 'all';
    public $activeTab = 'search';
    public $showSaveModal = false;
    public $searchResults = [];
    public $totalResults = 0;
    
    // Save search properties
    public $savedSearchName = '';
    public $savedSearchDescription = '';
    
    // Advanced filters
    public $filters = [
        'date_from' => '',
        'date_to' => '',
        'status' => '',
        'assigned_to' => '',
        'tags' => [],
    ];
    
    protected $queryString = ['query', 'searchType'];
    
    protected $rules = [
        'savedSearchName' => 'required|string|max:255',
        'savedSearchDescription' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        if ($this->query) {
            $this->performSearch();
        }
    }

    public function updatedQuery()
    {
        if (strlen($this->query) >= 2) {
            $this->performSearch();
        } else {
            $this->resetSearch();
        }
    }

    public function updatedSearchType()
    {
        if ($this->query) {
            $this->performSearch();
        }
    }

    public function performSearch()
    {
        if (empty($this->query)) {
            $this->resetSearch();
            return;
        }

        $this->searchResults = [];
        $this->totalResults = 0;

        try {
            if ($this->searchType === 'all' || $this->searchType === 'contacts') {
                $contacts = $this->searchContacts();
                $this->searchResults['contacts'] = $contacts;
                $this->totalResults += $contacts->count();
            }

            if ($this->searchType === 'all' || $this->searchType === 'companies') {
                $companies = $this->searchCompanies();
                $this->searchResults['companies'] = $companies;
                $this->totalResults += $companies->count();
            }

            if ($this->searchType === 'all' || $this->searchType === 'deals') {
                $deals = $this->searchDeals();
                $this->searchResults['deals'] = $deals;
                $this->totalResults += $deals->count();
            }

            if ($this->searchType === 'all' || $this->searchType === 'tasks') {
                $tasks = $this->searchTasks();
                $this->searchResults['tasks'] = $tasks;
                $this->totalResults += $tasks->count();
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Search failed: ' . $e->getMessage());
        }
    }

    private function searchContacts()
    {
        $query = Contact::with(['company', 'tags'])
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->query . '%')
                  ->orWhere('email', 'like', '%' . $this->query . '%')
                  ->orWhere('phone', 'like', '%' . $this->query . '%')
                  ->orWhere('title', 'like', '%' . $this->query . '%');
            });

        $this->applyFilters($query, 'contacts');
        
        return $query->limit(20)->get();
    }

    private function searchCompanies()
    {
        $query = Company::with(['contacts', 'tags'])
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->query . '%')
                  ->orWhere('email', 'like', '%' . $this->query . '%')
                  ->orWhere('website', 'like', '%' . $this->query . '%')
                  ->orWhere('industry', 'like', '%' . $this->query . '%');
            });

        $this->applyFilters($query, 'companies');
        
        return $query->limit(20)->get();
    }

    private function searchDeals()
    {
        $query = Deal::with(['contact', 'company', 'pipelineStage', 'tags'])
            ->where(function($q) {
                $q->where('title', 'like', '%' . $this->query . '%')
                  ->orWhere('description', 'like', '%' . $this->query . '%')
                  ->orWhere('source', 'like', '%' . $this->query . '%');
            });

        $this->applyFilters($query, 'deals');
        
        return $query->limit(20)->get();
    }

    private function searchTasks()
    {
        $query = Task::with(['taskable', 'user'])
            ->where(function($q) {
                $q->where('title', 'like', '%' . $this->query . '%')
                  ->orWhere('description', 'like', '%' . $this->query . '%');
            });

        $this->applyFilters($query, 'tasks');
        
        return $query->limit(20)->get();
    }

    private function applyFilters($query, $type)
    {
        // Date filters
        if ($this->filters['date_from']) {
            $query->where('created_at', '>=', $this->filters['date_from']);
        }
        
        if ($this->filters['date_to']) {
            $query->where('created_at', '<=', $this->filters['date_to']);
        }

        // Status filters
        if ($this->filters['status'] && in_array($type, ['deals', 'tasks'])) {
            $query->where('status', $this->filters['status']);
        }

        // Assignment filters
        if ($this->filters['assigned_to'] && $type === 'tasks') {
            $query->where('user_id', $this->filters['assigned_to']);
        }

        // Tag filters
        if (!empty($this->filters['tags'])) {
            $query->whereHas('tags', function($q) {
                $q->whereIn('tags.id', $this->filters['tags']);
            });
        }
    }

    public function resetSearch()
    {
        $this->searchResults = [];
        $this->totalResults = 0;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->filters = [
            'date_from' => '',
            'date_to' => '',
            'status' => '',
            'assigned_to' => '',
            'tags' => [],
        ];
        
        if ($this->query) {
            $this->performSearch();
        }
    }

    public function showSaveModal()
    {
        if (empty($this->query)) {
            session()->flash('error', 'Please enter a search query first.');
            return;
        }
        
        $this->showSaveModal = true;
        $this->savedSearchName = '';
        $this->savedSearchDescription = '';
    }

    public function hideSaveModal()
    {
        $this->showSaveModal = false;
        $this->resetErrorBag();
    }

    public function saveSearch()
    {
        $this->validate();

        try {
            SavedSearch::create([
                'user_id' => auth()->id(),
                'name' => $this->savedSearchName,
                'description' => $this->savedSearchDescription,
                'query' => $this->query,
                'search_type' => $this->searchType,
                'filters' => json_encode($this->filters),
            ]);

            session()->flash('message', 'Search saved successfully!');
            $this->hideSaveModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save search: ' . $e->getMessage());
        }
    }

    public function loadSavedSearch($savedSearchId)
    {
        try {
            $savedSearch = SavedSearch::findOrFail($savedSearchId);
            
            $this->query = $savedSearch->query;
            $this->searchType = $savedSearch->search_type;
            $this->filters = json_decode($savedSearch->filters, true) ?? [];
            
            $this->performSearch();
            $this->activeTab = 'search';
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load saved search: ' . $e->getMessage());
        }
    }

    public function deleteSavedSearch($savedSearchId)
    {
        try {
            $savedSearch = SavedSearch::where('id', $savedSearchId)
                                   ->where('user_id', auth()->id())
                                   ->firstOrFail();
            
            $savedSearch->delete();
            session()->flash('message', 'Saved search deleted successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete saved search: ' . $e->getMessage());
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function getSavedSearchesProperty()
    {
        return SavedSearch::where('user_id', auth()->id())
                         ->orderBy('created_at', 'desc')
                         ->get();
    }

    public function render()
    {
        return view('livewire.search-manager', [
            'savedSearches' => $this->savedSearches,
        ]);
    }
}