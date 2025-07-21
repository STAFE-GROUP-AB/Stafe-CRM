<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Task;
use App\Models\Email;
use App\Models\Team;
use App\Models\ImportJob;
use App\Models\ActivityLog;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'companies' => Company::count(),
            'contacts' => Contact::count(),
            'open_deals' => Deal::where('status', 'open')->count(),
            'total_deal_value' => Deal::where('status', 'open')->sum('value'),
            'pending_tasks' => Task::where('status', 'pending')->count(),
            'overdue_tasks' => Task::where('due_date', '<', now())
                                ->where('status', '!=', 'completed')
                                ->count(),
            // Phase 2 Analytics
            'emails_sent' => Email::where('direction', 'outbound')->count(),
            'emails_opened' => Email::whereNotNull('opened_at')->count(),
            'active_teams' => Team::where('is_active', true)->count(),
            'recent_imports' => ImportJob::where('created_at', '>=', now()->subDays(7))->count(),
            // Stalled Customers
            'stalled_customers' => Contact::where(function($query) {
                $query->where('last_contacted_at', '<=', now()->subDays(30))
                      ->orWhereNull('last_contacted_at');
            })->count(),
        ];

        $recent_deals = Deal::with(['company', 'contact', 'pipelineStage'])
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $upcoming_tasks = Task::with(['taskable', 'assignedTo'])
            ->where('status', 'pending')
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Phase 2 Data
        $recent_emails = Email::with(['emailable', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recent_activity = ActivityLog::with(['loggable', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        $recent_companies = Company::with('owner')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $recent_contacts = Contact::with(['company', 'owner'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Analytics for charts
        $deal_analytics = Deal::selectRaw('pipeline_stage_id, COUNT(*) as count, SUM(value) as total_value')
            ->with('pipelineStage')
            ->groupBy('pipeline_stage_id')
            ->get();

        $revenue_trends = Deal::selectRaw('DATE(created_at) as date, SUM(value) as revenue')
            ->where('status', 'won')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('livewire.dashboard', [
            'stats' => $stats,
            'recent_deals' => $recent_deals,
            'upcoming_tasks' => $upcoming_tasks,
            'recent_emails' => $recent_emails,
            'recent_activity' => $recent_activity,
            'recent_companies' => $recent_companies,
            'recent_contacts' => $recent_contacts,
            'deal_analytics' => $deal_analytics,
            'revenue_trends' => $revenue_trends,
        ])->layout('layouts.app');
    }
}
