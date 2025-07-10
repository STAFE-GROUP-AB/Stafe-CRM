<?php

namespace App\Livewire\Analytics;

use App\Models\Dashboard;
use App\Models\DashboardWidget;
use Livewire\Component;
use Livewire\WithPagination;

class DashboardBuilder extends Component
{
    use WithPagination;

    public $dashboard;
    public $dashboardName = '';
    public $dashboardDescription = '';
    public $dashboardType = 'personal';
    public $isPublic = false;
    public $isEditing = false;
    public $availableWidgets = [];
    public $selectedWidget = null;
    public $showWidgetModal = false;

    public function mount($dashboardId = null)
    {
        $this->availableWidgets = $this->getAvailableWidgetTypes();
        
        if ($dashboardId) {
            $this->dashboard = Dashboard::findOrFail($dashboardId);
            $this->dashboardName = $this->dashboard->name;
            $this->dashboardDescription = $this->dashboard->description;
            $this->dashboardType = $this->dashboard->type;
            $this->isPublic = $this->dashboard->is_public;
            $this->isEditing = true;
        }
    }

    public function createDashboard()
    {
        $this->validate([
            'dashboardName' => 'required|string|max:255',
            'dashboardDescription' => 'nullable|string',
            'dashboardType' => 'required|in:personal,team,company',
        ]);

        $this->dashboard = Dashboard::create([
            'name' => $this->dashboardName,
            'description' => $this->dashboardDescription,
            'type' => $this->dashboardType,
            'is_public' => $this->isPublic,
            'layout_config' => ['columns' => 12, 'rows' => 10],
            'widgets' => [],
            'user_id' => auth()->id(),
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        $this->isEditing = true;
        $this->dispatch('dashboard-created', $this->dashboard->id);
    }

    public function updateDashboard()
    {
        $this->validate([
            'dashboardName' => 'required|string|max:255',
            'dashboardDescription' => 'nullable|string',
            'dashboardType' => 'required|in:personal,team,company',
        ]);

        $this->dashboard->update([
            'name' => $this->dashboardName,
            'description' => $this->dashboardDescription,
            'type' => $this->dashboardType,
            'is_public' => $this->isPublic,
        ]);

        $this->dispatch('dashboard-updated');
    }

    public function addWidget($widgetType)
    {
        $this->selectedWidget = $widgetType;
        $this->showWidgetModal = true;
    }

    public function createWidget($widgetData)
    {
        DashboardWidget::create([
            'dashboard_id' => $this->dashboard->id,
            'widget_type' => $widgetData['type'],
            'title' => $widgetData['title'],
            'description' => $widgetData['description'] ?? null,
            'configuration' => $widgetData['configuration'] ?? [],
            'data_source' => $widgetData['data_source'] ?? [],
            'position' => $widgetData['position'] ?? ['x' => 0, 'y' => 0, 'w' => 4, 'h' => 3],
            'filters' => $widgetData['filters'] ?? [],
            'refresh_interval' => $widgetData['refresh_interval'] ?? 300,
        ]);

        $this->showWidgetModal = false;
        $this->selectedWidget = null;
        $this->dispatch('widget-added');
    }

    public function deleteWidget($widgetId)
    {
        DashboardWidget::findOrFail($widgetId)->delete();
        $this->dispatch('widget-deleted');
    }

    public function updateWidgetPosition($widgetId, $position)
    {
        $widget = DashboardWidget::findOrFail($widgetId);
        $widget->update(['position' => $position]);
    }

    private function getAvailableWidgetTypes(): array
    {
        return [
            'chart' => [
                'name' => 'Chart',
                'description' => 'Various chart types for data visualization',
                'icon' => 'chart-bar',
                'types' => ['line', 'bar', 'pie', 'area', 'scatter']
            ],
            'metric' => [
                'name' => 'Metric',
                'description' => 'Key performance indicators and metrics',
                'icon' => 'calculator',
                'types' => ['number', 'percentage', 'currency', 'progress']
            ],
            'table' => [
                'name' => 'Table',
                'description' => 'Data tables with sorting and filtering',
                'icon' => 'table',
                'types' => ['basic', 'advanced', 'pivot']
            ],
            'heatmap' => [
                'name' => 'Heat Map',
                'description' => 'Heat map visualizations',
                'icon' => 'fire',
                'types' => ['calendar', 'geographic', 'matrix']
            ],
            'list' => [
                'name' => 'List',
                'description' => 'Lists and feeds',
                'icon' => 'list',
                'types' => ['recent', 'top', 'activity']
            ]
        ];
    }

    public function render()
    {
        $userDashboards = Dashboard::where('user_id', auth()->id())
            ->orWhere('is_public', true)
            ->orWhere('tenant_id', auth()->user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.analytics.dashboard-builder', [
            'userDashboards' => $userDashboards,
            'widgets' => $this->dashboard ? $this->dashboard->dashboardWidgets()->active()->get() : collect(),
        ]);
    }
}