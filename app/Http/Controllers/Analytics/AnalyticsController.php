<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\Dashboard;
use App\Models\CustomChart;
use App\Models\AnalyticsHeatMap;
use App\Models\RelationshipNetwork;
use App\Models\PipelineVisualization;
use App\Models\ForecastSimulation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $dashboards = Dashboard::where('user_id', $user->id)
            ->orWhere('is_public', true)
            ->orWhere('tenant_id', $user->tenant_id)
            ->with('dashboardWidgets')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $charts = CustomChart::where('user_id', $user->id)
            ->orWhere('is_public', true)
            ->orWhere('tenant_id', $user->tenant_id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $heatMaps = AnalyticsHeatMap::where('user_id', $user->id)
            ->orWhere('tenant_id', $user->tenant_id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('analytics.index', compact('dashboards', 'charts', 'heatMaps'));
    }

    public function dashboards()
    {
        return view('analytics.dashboards');
    }

    public function heatMaps()
    {
        return view('analytics.heat-maps');
    }

    public function charts()
    {
        return view('analytics.charts');
    }

    public function relationships()
    {
        return view('analytics.relationships');
    }

    public function pipeline()
    {
        return view('analytics.pipeline');
    }

    public function forecasting()
    {
        return view('analytics.forecasting');
    }

    public function getDashboardData(Dashboard $dashboard)
    {
        if (!$dashboard->isAccessibleBy(Auth::user())) {
            abort(403);
        }

        $widgets = $dashboard->dashboardWidgets()->active()->get();
        $data = [];

        foreach ($widgets as $widget) {
            $data[$widget->id] = $this->getWidgetData($widget);
        }

        return response()->json($data);
    }

    public function getChartData(CustomChart $chart)
    {
        if (!$chart->isAccessibleBy(Auth::user())) {
            abort(403);
        }

        return response()->json([
            'config' => $chart->getChartConfig(),
            'data' => $chart->getChartData(),
        ]);
    }

    public function getHeatMapData(AnalyticsHeatMap $heatMap)
    {
        if ($heatMap->user_id !== Auth::id() && $heatMap->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        return response()->json([
            'data_points' => $heatMap->data_points,
            'configuration' => $heatMap->configuration,
            'color_scheme' => $heatMap->color_scheme,
        ]);
    }

    public function getRelationshipNetworkData(RelationshipNetwork $network)
    {
        if ($network->user_id !== Auth::id() && $network->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        return response()->json([
            'nodes' => $network->nodes,
            'edges' => $network->edges,
            'layout_config' => $network->layout_config,
            'visual_config' => $network->visual_config,
        ]);
    }

    public function getPipelineVisualizationData(PipelineVisualization $visualization)
    {
        if ($visualization->user_id !== Auth::id() && $visualization->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        return response()->json([
            'data_points' => $visualization->data_points,
            'pipeline_config' => $visualization->pipeline_config,
            'visual_config' => $visualization->visual_config,
            'conversion_rates' => $visualization->getConversionRates(),
        ]);
    }

    public function getForecastSimulationData(ForecastSimulation $simulation)
    {
        if ($simulation->user_id !== Auth::id() && $simulation->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        return response()->json([
            'base_scenario' => $simulation->base_scenario,
            'scenarios' => $simulation->scenarios,
            'results' => $simulation->results,
            'confidence_interval' => $simulation->getConfidenceInterval(),
            'best_case' => $simulation->getBestCaseScenario(),
            'worst_case' => $simulation->getWorstCaseScenario(),
        ]);
    }

    private function getWidgetData($widget)
    {
        $dataSource = $widget->data_source;
        $model = $dataSource['model'] ?? null;
        
        if (!$model || !class_exists($model)) {
            return [];
        }

        $query = $model::query();
        
        // Apply tenant scoping if applicable
        if (Auth::user()->tenant_id && method_exists($model, 'scopeForTenant')) {
            $query->forTenant(Auth::user()->tenant_id);
        }

        // Apply filters
        $filters = array_merge($dataSource['filters'] ?? [], $widget->filters ?? []);
        foreach ($filters as $filter) {
            $field = $filter['field'] ?? null;
            $operator = $filter['operator'] ?? '=';
            $value = $filter['value'] ?? null;
            
            if ($field && $value !== null) {
                $query->where($field, $operator, $value);
            }
        }

        // Apply grouping and aggregation based on widget type
        switch ($widget->widget_type) {
            case 'chart':
                return $this->getChartWidgetData($query, $dataSource);
            case 'metric':
                return $this->getMetricWidgetData($query, $dataSource);
            case 'table':
                return $this->getTableWidgetData($query, $dataSource);
            case 'heatmap':
                return $this->getHeatmapWidgetData($query, $dataSource);
            case 'list':
                return $this->getListWidgetData($query, $dataSource);
            default:
                return [];
        }
    }

    private function getChartWidgetData($query, $dataSource)
    {
        $groupBy = $dataSource['group_by'] ?? 'created_at';
        $aggregateFunction = $dataSource['aggregate'] ?? 'count';
        $aggregateField = $dataSource['aggregate_field'] ?? '*';

        if ($groupBy === 'created_at') {
            $query->selectRaw("DATE(created_at) as date, {$aggregateFunction}({$aggregateField}) as value")
                  ->groupBy('date')
                  ->orderBy('date');
        } else {
            $query->selectRaw("{$groupBy}, {$aggregateFunction}({$aggregateField}) as value")
                  ->groupBy($groupBy);
        }

        return $query->get()->toArray();
    }

    private function getMetricWidgetData($query, $dataSource)
    {
        $aggregateFunction = $dataSource['aggregate'] ?? 'count';
        $aggregateField = $dataSource['aggregate_field'] ?? '*';

        $result = $query->selectRaw("{$aggregateFunction}({$aggregateField}) as value")->first();
        
        return [
            'value' => $result->value ?? 0,
            'label' => $dataSource['label'] ?? 'Total',
        ];
    }

    private function getTableWidgetData($query, $dataSource)
    {
        $fields = $dataSource['fields'] ?? ['*'];
        $limit = $dataSource['limit'] ?? 10;

        return $query->select($fields)
                     ->orderBy('created_at', 'desc')
                     ->limit($limit)
                     ->get()
                     ->toArray();
    }

    private function getHeatmapWidgetData($query, $dataSource)
    {
        // Simplified heatmap data - would be more complex in practice
        return $query->selectRaw('DATE(created_at) as date, COUNT(*) as value')
                     ->groupBy('date')
                     ->orderBy('date')
                     ->get()
                     ->toArray();
    }

    private function getListWidgetData($query, $dataSource)
    {
        $fields = $dataSource['fields'] ?? ['*'];
        $limit = $dataSource['limit'] ?? 5;

        return $query->select($fields)
                     ->orderBy('created_at', 'desc')
                     ->limit($limit)
                     ->get()
                     ->toArray();
    }
}