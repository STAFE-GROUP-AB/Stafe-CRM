<?php

namespace App\Livewire\Analytics;

use App\Models\CustomChart;
use App\Models\Deal;
use App\Models\Contact;
use App\Models\Company;
use App\Models\Task;
use Livewire\Component;
use Carbon\Carbon;

class CustomChartBuilder extends Component
{
    public $chart;
    public $chartName = '';
    public $chartDescription = '';
    public $chartType = 'line';
    public $dataSource = [];
    public $chartConfig = [];
    public $styling = [];
    public $filters = [];
    public $isRealTime = false;
    public $refreshInterval = 300;
    public $isPublic = false;
    public $showCreateModal = false;
    public $selectedChart = null;
    public $availableModels = [];
    public $selectedModel = null;
    public $availableFields = [];
    public $previewData = [];

    public function mount()
    {
        $this->availableModels = $this->getAvailableModels();
        $this->initializeDefaults();
    }

    public function createChart()
    {
        $this->validate([
            'chartName' => 'required|string|max:255',
            'chartDescription' => 'nullable|string',
            'chartType' => 'required|in:line,bar,pie,donut,scatter,bubble,area,radar,heatmap,treemap,gauge',
            'dataSource' => 'required|array',
        ]);

        $this->chart = CustomChart::create([
            'name' => $this->chartName,
            'description' => $this->chartDescription,
            'chart_type' => $this->chartType,
            'data_source' => $this->dataSource,
            'chart_config' => $this->chartConfig,
            'styling' => $this->styling,
            'filters' => $this->filters,
            'is_real_time' => $this->isRealTime,
            'refresh_interval' => $this->refreshInterval,
            'is_public' => $this->isPublic,
            'user_id' => auth()->id(),
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        $this->showCreateModal = false;
        $this->resetForm();
        $this->dispatch('chart-created');
    }

    public function loadChart($chartId)
    {
        $this->selectedChart = CustomChart::findOrFail($chartId);
        $this->chartName = $this->selectedChart->name;
        $this->chartDescription = $this->selectedChart->description;
        $this->chartType = $this->selectedChart->chart_type;
        $this->dataSource = $this->selectedChart->data_source;
        $this->chartConfig = $this->selectedChart->chart_config;
        $this->styling = $this->selectedChart->styling;
        $this->filters = $this->selectedChart->filters;
        $this->isRealTime = $this->selectedChart->is_real_time;
        $this->refreshInterval = $this->selectedChart->refresh_interval;
        $this->isPublic = $this->selectedChart->is_public;
        
        $this->updatePreview();
    }

    public function updateChart()
    {
        if (!$this->selectedChart) return;

        $this->validate([
            'chartName' => 'required|string|max:255',
            'chartDescription' => 'nullable|string',
            'chartType' => 'required|in:line,bar,pie,donut,scatter,bubble,area,radar,heatmap,treemap,gauge',
            'dataSource' => 'required|array',
        ]);

        $this->selectedChart->update([
            'name' => $this->chartName,
            'description' => $this->chartDescription,
            'chart_type' => $this->chartType,
            'data_source' => $this->dataSource,
            'chart_config' => $this->chartConfig,
            'styling' => $this->styling,
            'filters' => $this->filters,
            'is_real_time' => $this->isRealTime,
            'refresh_interval' => $this->refreshInterval,
            'is_public' => $this->isPublic,
        ]);

        $this->dispatch('chart-updated');
    }

    public function deleteChart($chartId)
    {
        CustomChart::findOrFail($chartId)->delete();
        if ($this->selectedChart && $this->selectedChart->id == $chartId) {
            $this->selectedChart = null;
            $this->resetForm();
        }
        $this->dispatch('chart-deleted');
    }

    public function updatedSelectedModel($value)
    {
        if ($value) {
            $this->availableFields = $this->getModelFields($value);
            $this->dataSource['model'] = $value;
            $this->dataSource['fields'] = [];
        }
    }

    public function updatedChartType($value)
    {
        $this->chartConfig = $this->getDefaultChartConfig($value);
        $this->styling = $this->getDefaultStyling($value);
        $this->updatePreview();
    }

    public function addFilter()
    {
        $this->filters[] = [
            'field' => '',
            'operator' => '=',
            'value' => '',
        ];
    }

    public function removeFilter($index)
    {
        unset($this->filters[$index]);
        $this->filters = array_values($this->filters);
        $this->updatePreview();
    }

    public function updatePreview()
    {
        if ($this->selectedChart) {
            $this->previewData = $this->selectedChart->getChartData();
        } else {
            $this->previewData = $this->generatePreviewData();
        }
    }

    private function generatePreviewData(): array
    {
        if (!$this->selectedModel) return [];

        $modelClass = $this->selectedModel;
        if (!class_exists($modelClass)) return [];

        $query = $modelClass::query();

        // Apply tenant scoping if available
        if (method_exists($modelClass, 'scopeForTenant') && auth()->user()->tenant_id) {
            $query->forTenant(auth()->user()->tenant_id);
        }

        // Apply filters
        foreach ($this->filters as $filter) {
            if ($filter['field'] && $filter['value']) {
                $query->where($filter['field'], $filter['operator'], $filter['value']);
            }
        }

        // Apply grouping and aggregation
        $groupBy = $this->dataSource['group_by'] ?? 'created_at';
        $aggregateFunction = $this->dataSource['aggregate'] ?? 'count';
        $aggregateField = $this->dataSource['aggregate_field'] ?? '*';

        if ($groupBy === 'created_at') {
            $query->selectRaw("DATE(created_at) as date, {$aggregateFunction}({$aggregateField}) as value")
                  ->groupBy('date')
                  ->orderBy('date');
        } else {
            $query->selectRaw("{$groupBy}, {$aggregateFunction}({$aggregateField}) as value")
                  ->groupBy($groupBy);
        }

        return $query->limit(100)->get()->toArray();
    }

    private function getAvailableModels(): array
    {
        return [
            'App\\Models\\Deal' => 'Deals',
            'App\\Models\\Contact' => 'Contacts',
            'App\\Models\\Company' => 'Companies',
            'App\\Models\\Task' => 'Tasks',
            'App\\Models\\Email' => 'Emails',
            'App\\Models\\Note' => 'Notes',
        ];
    }

    private function getModelFields($modelClass): array
    {
        $commonFields = [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];

        switch ($modelClass) {
            case 'App\\Models\\Deal':
                return array_merge($commonFields, [
                    'name' => 'Name',
                    'value' => 'Value',
                    'stage_id' => 'Stage',
                    'expected_close_date' => 'Expected Close Date',
                    'probability' => 'Probability',
                ]);
            case 'App\\Models\\Contact':
                return array_merge($commonFields, [
                    'first_name' => 'First Name',
                    'last_name' => 'Last Name',
                    'email' => 'Email',
                    'phone' => 'Phone',
                    'company_id' => 'Company',
                ]);
            case 'App\\Models\\Company':
                return array_merge($commonFields, [
                    'name' => 'Name',
                    'industry' => 'Industry',
                    'employee_count' => 'Employee Count',
                    'annual_revenue' => 'Annual Revenue',
                ]);
            case 'App\\Models\\Task':
                return array_merge($commonFields, [
                    'name' => 'Name',
                    'type' => 'Type',
                    'priority' => 'Priority',
                    'status' => 'Status',
                    'due_date' => 'Due Date',
                ]);
            default:
                return $commonFields;
        }
    }

    private function getDefaultChartConfig($chartType): array
    {
        $base = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['display' => true],
                'tooltip' => ['enabled' => true],
            ],
        ];

        switch ($chartType) {
            case 'line':
            case 'area':
                return array_merge($base, [
                    'scales' => [
                        'x' => ['type' => 'category'],
                        'y' => ['beginAtZero' => true],
                    ],
                    'elements' => [
                        'line' => ['tension' => 0.4],
                        'point' => ['radius' => 4],
                    ],
                ]);
            case 'bar':
                return array_merge($base, [
                    'scales' => [
                        'x' => ['type' => 'category'],
                        'y' => ['beginAtZero' => true],
                    ],
                    'barPercentage' => 0.8,
                ]);
            case 'pie':
            case 'donut':
                return array_merge($base, [
                    'cutout' => $chartType === 'donut' ? '50%' : '0%',
                ]);
            default:
                return $base;
        }
    }

    private function getDefaultStyling($chartType): array
    {
        return [
            'colors' => [
                '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6',
                '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1'
            ],
            'backgroundColor' => 'transparent',
            'borderColor' => '#e5e7eb',
            'borderWidth' => 1,
            'fontSize' => 12,
            'fontFamily' => 'system-ui, -apple-system, sans-serif',
        ];
    }

    private function initializeDefaults()
    {
        $this->dataSource = [
            'model' => null,
            'fields' => [],
            'group_by' => 'created_at',
            'aggregate' => 'count',
            'aggregate_field' => '*',
            'filters' => [],
        ];
        
        $this->chartConfig = $this->getDefaultChartConfig('line');
        $this->styling = $this->getDefaultStyling('line');
        $this->filters = [];
    }

    private function resetForm()
    {
        $this->chartName = '';
        $this->chartDescription = '';
        $this->chartType = 'line';
        $this->selectedModel = null;
        $this->isRealTime = false;
        $this->refreshInterval = 300;
        $this->isPublic = false;
        $this->initializeDefaults();
    }

    public function render()
    {
        $userCharts = CustomChart::where('user_id', auth()->id())
            ->orWhere('is_public', true)
            ->orWhere('tenant_id', auth()->user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.analytics.custom-chart-builder', [
            'userCharts' => $userCharts,
            'chartTypes' => $this->getChartTypes(),
        ]);
    }

    private function getChartTypes(): array
    {
        return [
            'line' => 'Line Chart',
            'bar' => 'Bar Chart',
            'pie' => 'Pie Chart',
            'donut' => 'Donut Chart',
            'scatter' => 'Scatter Plot',
            'bubble' => 'Bubble Chart',
            'area' => 'Area Chart',
            'radar' => 'Radar Chart',
            'heatmap' => 'Heat Map',
            'treemap' => 'Tree Map',
            'gauge' => 'Gauge Chart',
        ];
    }
}