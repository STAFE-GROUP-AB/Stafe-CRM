<?php

namespace App\Livewire;

use App\Models\Contact;
use App\Models\CustomerHealthScore;
use App\Models\CustomerHealthScoreHistory;
use App\Services\CustomerHealthService;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerHealthDashboard extends Component
{
    use WithPagination;

    public ?int $selectedHealthScoreId = null;
    public ?CustomerHealthScore $healthScoreDetails = null;
    public string $filterStatus = '';
    public string $filterTrend = '';
    public string $searchQuery = '';
    public string $sortField = 'overall_score';
    public string $sortDirection = 'asc';

    protected $queryString = [
        'filterStatus' => ['except' => ''],
        'filterTrend' => ['except' => ''],
        'searchQuery' => ['except' => ''],
        'sortField' => ['except' => 'overall_score'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function render()
    {
        $healthService = app(CustomerHealthService::class);

        $query = CustomerHealthScore::with('contact')
            ->when($this->filterStatus, fn($q) => $q->where('health_status', $this->filterStatus))
            ->when($this->filterTrend, fn($q) => $q->where('trend', $this->filterTrend))
            ->when($this->searchQuery, function ($q) {
                $q->whereHas('contact', function ($contactQuery) {
                    $contactQuery->where('first_name', 'like', "%{$this->searchQuery}%")
                        ->orWhere('last_name', 'like', "%{$this->searchQuery}%")
                        ->orWhere('email', 'like', "%{$this->searchQuery}%");
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $healthScores = $query->paginate(20);

        $stats = $healthService->getStatistics();

        $atRiskContacts = $healthService->getAtRiskContacts(5);
        $decliningScores = $healthService->getDecliningScores(5);

        $scoreHistory = [];
        if ($this->healthScoreDetails) {
            $scoreHistory = $healthService->getScoreTrend(
                $this->healthScoreDetails->contact,
                30
            )->map(fn($h) => [
                'date' => $h->created_at->format('M d'),
                'score' => $h->overall_score,
            ])->toArray();
        }

        return view('livewire.customer-health-dashboard', compact(
            'healthScores',
            'stats',
            'atRiskContacts',
            'decliningScores',
            'scoreHistory'
        ));
    }

    public function viewDetails(int $healthScoreId): void
    {
        $this->selectedHealthScoreId = $healthScoreId;
        $this->healthScoreDetails = CustomerHealthScore::with(['contact', 'history' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(10);
        }])->find($healthScoreId);
    }

    public function closeDetails(): void
    {
        $this->selectedHealthScoreId = null;
        $this->healthScoreDetails = null;
    }

    public function recalculateScore(int $healthScoreId): void
    {
        $healthScore = CustomerHealthScore::find($healthScoreId);

        if (!$healthScore) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Halsopoang hittades inte.',
            ]);

            return;
        }

        $healthScore->recalculate('manual');

        if ($this->selectedHealthScoreId === $healthScoreId) {
            $this->healthScoreDetails = $healthScore->fresh(['contact', 'history']);
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Halsopoang har beraknats om.',
        ]);
    }

    public function recalculateAllScores(): void
    {
        $healthService = app(CustomerHealthService::class);
        $results = $healthService->calculateForAllContacts('manual');

        $this->dispatch('notify', [
            'type' => $results['failed'] > 0 ? 'warning' : 'success',
            'message' => "Beraknade {$results['processed']} halsopoang. {$results['failed']} misslyckades.",
        ]);
    }

    public function generateHealthScore(int $contactId): void
    {
        $contact = Contact::find($contactId);

        if (!$contact) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Kontakt hittades inte.',
            ]);

            return;
        }

        $healthService = app(CustomerHealthService::class);
        $healthService->calculateForContact($contact, 'manual');

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Halsopoang skapad for {$contact->full_name}.",
        ]);
    }

    public function generateMissingHealthScores(): void
    {
        $healthService = app(CustomerHealthService::class);
        $processed = 0;

        Contact::whereDoesntHave('customerHealthScore')
            ->chunk(50, function ($contacts) use ($healthService, &$processed) {
                foreach ($contacts as $contact) {
                    try {
                        $healthService->calculateForContact($contact, 'initial');
                        $processed++;
                    } catch (\Exception $e) {
                        // Log and continue
                    }
                }
            });

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Skapade {$processed} nya halsopoang.",
        ]);
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetFilters(): void
    {
        $this->filterStatus = '';
        $this->filterTrend = '';
        $this->searchQuery = '';
        $this->sortField = 'overall_score';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    public function updatedSearchQuery(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterTrend(): void
    {
        $this->resetPage();
    }
}