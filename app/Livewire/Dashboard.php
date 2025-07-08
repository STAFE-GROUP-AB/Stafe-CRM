<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Task;

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

        return view('livewire.dashboard', [
            'stats' => $stats,
            'recent_deals' => $recent_deals,
            'upcoming_tasks' => $upcoming_tasks,
        ])->layout('layouts.app');
    }
}
