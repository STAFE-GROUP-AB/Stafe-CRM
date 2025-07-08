<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'filters',
        'columns',
        'config',
        'is_public',
        'is_scheduled',
        'schedule_frequency',
        'schedule_recipients',
        'last_generated_at',
        'user_id',
    ];

    protected $casts = [
        'filters' => 'array',
        'columns' => 'array',
        'config' => 'array',
        'is_public' => 'boolean',
        'is_scheduled' => 'boolean',
        'schedule_recipients' => 'array',
        'last_generated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function scopeScheduled($query)
    {
        return $query->where('is_scheduled', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Generate report data based on type and filters
     */
    public function generateData(): array
    {
        switch ($this->type) {
            case 'deals':
                return $this->generateDealsReport();
            case 'contacts':
                return $this->generateContactsReport();
            case 'companies':
                return $this->generateCompaniesReport();
            case 'tasks':
                return $this->generateTasksReport();
            case 'analytics':
                return $this->generateAnalyticsReport();
            default:
                return [];
        }
    }

    private function generateDealsReport(): array
    {
        $query = Deal::with(['company', 'contact', 'pipelineStage']);
        
        // Apply filters
        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        
        if (isset($this->filters['date_range'])) {
            $query->whereBetween('created_at', [
                $this->filters['date_range']['start'],
                $this->filters['date_range']['end'],
            ]);
        }
        
        return $query->get()->toArray();
    }

    private function generateContactsReport(): array
    {
        $query = Contact::with(['company']);
        
        // Apply filters
        if (isset($this->filters['company_id'])) {
            $query->where('company_id', $this->filters['company_id']);
        }
        
        return $query->get()->toArray();
    }

    private function generateCompaniesReport(): array
    {
        $query = Company::withCount(['contacts', 'deals']);
        
        // Apply filters
        if (isset($this->filters['industry'])) {
            $query->where('industry', $this->filters['industry']);
        }
        
        return $query->get()->toArray();
    }

    private function generateTasksReport(): array
    {
        $query = Task::with(['taskable', 'assignedTo']);
        
        // Apply filters
        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        
        return $query->get()->toArray();
    }

    private function generateAnalyticsReport(): array
    {
        return [
            'deals_by_stage' => $this->getDealsAnalytics(),
            'revenue_trends' => $this->getRevenueTrends(),
            'activity_summary' => $this->getActivitySummary(),
            'performance_metrics' => $this->getPerformanceMetrics(),
        ];
    }

    private function getDealsAnalytics(): array
    {
        return Deal::select('pipeline_stage_id')
            ->selectRaw('COUNT(*) as count, SUM(value) as total_value')
            ->with('pipelineStage')
            ->groupBy('pipeline_stage_id')
            ->get()
            ->toArray();
    }

    private function getRevenueTrends(): array
    {
        return Deal::selectRaw('DATE(created_at) as date, SUM(value) as revenue')
            ->where('status', 'won')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getActivitySummary(): array
    {
        return [
            'total_companies' => Company::count(),
            'total_contacts' => Contact::count(),
            'total_deals' => Deal::count(),
            'open_deals' => Deal::where('status', 'open')->count(),
            'won_deals' => Deal::where('status', 'won')->count(),
            'total_tasks' => Task::count(),
            'pending_tasks' => Task::where('status', 'pending')->count(),
        ];
    }

    private function getPerformanceMetrics(): array
    {
        $totalDeals = Deal::count();
        $wonDeals = Deal::where('status', 'won')->count();
        $winRate = $totalDeals > 0 ? ($wonDeals / $totalDeals) * 100 : 0;
        
        return [
            'win_rate' => round($winRate, 2),
            'average_deal_size' => Deal::where('status', 'won')->avg('value') ?? 0,
            'total_revenue' => Deal::where('status', 'won')->sum('value') ?? 0,
            'pipeline_value' => Deal::where('status', 'open')->sum('value') ?? 0,
        ];
    }
}