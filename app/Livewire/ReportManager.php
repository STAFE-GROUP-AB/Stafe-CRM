<?php

namespace App\Livewire;

use App\Models\Report;
use App\Models\ActivityLog;
use App\Models\Contact;
use App\Models\Company;
use App\Models\Deal;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ReportManager extends Component
{
    use WithPagination;

    public $activeTab = 'reports';
    public $search = '';
    public $showCreateModal = false;
    public $selectedReport = null;
    
    // Report creation properties
    public $name = '';
    public $description = '';
    public $type = 'contacts';
    public $filters = [];
    public $groupBy = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    
    protected $queryString = ['search', 'activeTab'];
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'type' => 'required|in:contacts,companies,deals,activities',
    ];

    public function mount($action = null, $report = null)
    {
        if ($action === 'create') {
            $this->showCreateModal = true;
        }
        
        if ($report) {
            $this->selectedReport = $report;
            $this->activeTab = 'view';
        }
    }

    public function showCreateModal()
    {
        $this->showCreateModal = true;
        $this->resetForm();
    }

    public function hideCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->type = 'contacts';
        $this->filters = [];
        $this->groupBy = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetErrorBag();
    }

    public function createReport()
    {
        $this->validate();

        try {
            $report = Report::create([
                'name' => $this->name,
                'description' => $this->description,
                'type' => $this->type,
                'filters' => json_encode($this->filters),
                'group_by' => $this->groupBy,
                'sort_by' => $this->sortBy,
                'sort_direction' => $this->sortDirection,
                'user_id' => auth()->id(),
            ]);

            session()->flash('message', 'Report created successfully!');
            $this->hideCreateModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create report: ' . $e->getMessage());
        }
    }

    public function generateReport($reportId)
    {
        try {
            $report = Report::findOrFail($reportId);
            $data = $this->buildReportData($report);
            
            // Update report with generated data
            $report->update([
                'data' => json_encode($data),
                'generated_at' => now(),
            ]);

            session()->flash('message', 'Report generated successfully!');
            $this->selectedReport = $reportId;
            $this->activeTab = 'view';
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }

    private function buildReportData($report)
    {
        $query = $this->getBaseQuery($report->type);
        
        // Apply filters
        if ($report->filters) {
            $filters = json_decode($report->filters, true);
            foreach ($filters as $filter) {
                $this->applyFilter($query, $filter);
            }
        }
        
        // Apply sorting
        $query->orderBy($report->sort_by, $report->sort_direction);
        
        $results = $query->get();
        
        // Apply grouping if specified
        if ($report->group_by) {
            $results = $results->groupBy($report->group_by);
        }
        
        return [
            'total_records' => $results->count(),
            'data' => $results->toArray(),
            'summary' => $this->generateSummary($report->type, $results),
        ];
    }

    private function getBaseQuery($type)
    {
        switch ($type) {
            case 'contacts':
                return Contact::with(['company', 'tags']);
            case 'companies':
                return Company::with(['contacts', 'deals', 'tags']);
            case 'deals':
                return Deal::with(['contact', 'company', 'pipelineStage', 'tags']);
            case 'activities':
                return ActivityLog::with(['user']);
            default:
                throw new \InvalidArgumentException("Unknown report type: {$type}");
        }
    }

    private function applyFilter($query, $filter)
    {
        $field = $filter['field'] ?? '';
        $operator = $filter['operator'] ?? '=';
        $value = $filter['value'] ?? '';
        
        switch ($operator) {
            case 'contains':
                $query->where($field, 'like', "%{$value}%");
                break;
            case 'equals':
                $query->where($field, '=', $value);
                break;
            case 'greater_than':
                $query->where($field, '>', $value);
                break;
            case 'less_than':
                $query->where($field, '<', $value);
                break;
            case 'between':
                if (isset($filter['value2'])) {
                    $query->whereBetween($field, [$value, $filter['value2']]);
                }
                break;
        }
    }

    private function generateSummary($type, $results)
    {
        switch ($type) {
            case 'contacts':
                return [
                    'total_contacts' => $results->count(),
                    'with_company' => $results->whereNotNull('company_id')->count(),
                    'without_company' => $results->whereNull('company_id')->count(),
                ];
            case 'companies':
                return [
                    'total_companies' => $results->count(),
                    'avg_employees' => $results->avg('employee_count'),
                    'total_revenue' => $results->sum('annual_revenue'),
                ];
            case 'deals':
                return [
                    'total_deals' => $results->count(),
                    'total_value' => $results->sum('value'),
                    'avg_value' => $results->avg('value'),
                    'won_deals' => $results->where('status', 'won')->count(),
                    'lost_deals' => $results->where('status', 'lost')->count(),
                ];
            case 'activities':
                return [
                    'total_activities' => $results->count(),
                    'unique_users' => $results->pluck('user_id')->unique()->count(),
                    'recent_activities' => $results->where('created_at', '>=', now()->subDays(7))->count(),
                ];
            default:
                return [];
        }
    }

    public function deleteReport($reportId)
    {
        try {
            $report = Report::findOrFail($reportId);
            $report->delete();
            
            session()->flash('message', 'Report deleted successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete report: ' . $e->getMessage());
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function getReportsProperty()
    {
        $query = Report::query()
            ->with(['user'])
            ->where('user_id', auth()->id());

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate(12);
    }

    public function getAnalyticsDataProperty()
    {
        return [
            'total_contacts' => Contact::count(),
            'total_companies' => Company::count(),
            'total_deals' => Deal::count(),
            'total_deal_value' => Deal::sum('value'),
            'recent_activities' => ActivityLog::where('created_at', '>=', now()->subDays(7))->count(),
            'contacts_this_month' => Contact::where('created_at', '>=', now()->startOfMonth())->count(),
            'deals_this_month' => Deal::where('created_at', '>=', now()->startOfMonth())->count(),
            'deal_conversion_rate' => $this->calculateConversionRate(),
        ];
    }

    private function calculateConversionRate()
    {
        $totalDeals = Deal::count();
        $wonDeals = Deal::where('status', 'won')->count();
        
        return $totalDeals > 0 ? round(($wonDeals / $totalDeals) * 100, 2) : 0;
    }

    public function getSelectedReportDataProperty()
    {
        if (!$this->selectedReport) {
            return null;
        }

        return Report::find($this->selectedReport);
    }

    public function render()
    {
        return view('livewire.report-manager', [
            'reports' => $this->reports,
            'analyticsData' => $this->analyticsData,
            'selectedReportData' => $this->selectedReportData,
        ]);
    }
}