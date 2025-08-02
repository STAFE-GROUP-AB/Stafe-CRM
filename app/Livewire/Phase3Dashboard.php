<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WorkflowTemplate;
use App\Models\Integration;
use App\Models\Role;
use App\Models\Tenant;

class Phase3Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'workflows' => WorkflowTemplate::count(),
            'active_workflows' => WorkflowTemplate::where('is_active', true)->count(),
            'integrations' => Integration::where('is_active', true)->count(),
            'installed_integrations' => auth()->user()->apiConnections()->count(),
            'roles' => Role::count(),
            'tenants' => Tenant::where('status', 'active')->count(),
        ];

        $recent_workflows = WorkflowTemplate::latest()->limit(5)->get();
        $available_integrations = Integration::where('is_active', true)->limit(6)->get();

        return view('livewire.phase3-dashboard', [
            'stats' => $stats,
            'recent_workflows' => $recent_workflows,
            'available_integrations' => $available_integrations,
        ]);
    }
}