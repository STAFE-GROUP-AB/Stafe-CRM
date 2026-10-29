<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsHeatMap;
use App\Models\CustomChart;
use App\Models\Dashboard;
use App\Models\Deal;
use App\Models\ForecastSimulation;
use App\Models\PipelineStage;
use App\Models\PipelineVisualization;
use App\Models\RelationshipNetwork;
use App\Models\User;
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
        $owners = User::select('id', 'name')
            ->whereHas('deals')
            ->orderBy('name')
            ->get();

        return view('analytics.pipeline', compact('owners'));
    }

    /**
     * Get Sankey diagram data for pipeline visualization.
     */
    public function getSankeyData(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $ownerId = $request->input('owner_id');
        $minValue = $request->input('min_value', 0);
        $dataMode = $request->input('data_mode', 'value');

        $stages = PipelineStage::active()
            ->ordered()
            ->get();

        if ($stages->isEmpty()) {
            return response()->json([
                'nodes' => [],
                'links' => [],
                'stats' => [
                    'totalDeals' => 0,
                    'totalValue' => 0,
                    'wonValue' => 0,
                    'lostValue' => 0,
                    'conversionRate' => 0,
                ],
            ]);
        }

        $query = Deal::query()
            ->with('pipelineStage');

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }

        if ($ownerId) {
            $query->where('owner_id', $ownerId);
        }

        if ($minValue > 0) {
            $query->where('value', '>=', $minValue);
        }

        $deals = $query->get();

        $nodes = [];
        $nodeIndex = [];
        $index = 0;

        foreach ($stages as $stage) {
            if ($stage->is_closed && $stage->is_won) {
                $nodes[] = [
                    'name' => $stage->name,
                    'id' => $stage->id,
                    'type' => 'won',
                    'color' => $stage->color ?? '#22c55e',
                ];
            } elseif ($stage->is_closed && ! $stage->is_won) {
                $nodes[] = [
                    'name' => $stage->name,
                    'id' => $stage->id,
                    'type' => 'lost',
                    'color' => $stage->color ?? '#ef4444',
                ];
            } else {
                $nodes[] = [
                    'name' => $stage->name,
                    'id' => $stage->id,
                    'type' => 'open',
                    'color' => $stage->color ?? '#3b82f6',
                ];
            }
            $nodeIndex[$stage->id] = $index++;
        }

        $links = [];
        $stageDeals = [];
        $stageValues = [];

        foreach ($deals as $deal) {
            $stageId = $deal->pipeline_stage_id;
            if (! isset($stageDeals[$stageId])) {
                $stageDeals[$stageId] = 0;
                $stageValues[$stageId] = 0;
            }
            $stageDeals[$stageId]++;
            $stageValues[$stageId] += $deal->value;
        }

        $orderedStages = $stages->sortBy('order')->values();

        for ($i = 0; $i < count($orderedStages) - 1; $i++) {
            $currentStage = $orderedStages[$i];
            $nextStage = $orderedStages[$i + 1];

            $currentStageId = $currentStage->id;
            $nextStageId = $nextStage->id;

            if (! isset($nodeIndex[$currentStageId]) || ! isset($nodeIndex[$nextStageId])) {
                continue;
            }

            $flowDeals = $deals->filter(function ($deal) use ($orderedStages, $i) {
                $stageOrder = $orderedStages->pluck('id')->search($deal->pipeline_stage_id);

                return $stageOrder >= $i + 1;
            });

            $flowValue = $dataMode === 'value' ?
                $flowDeals->sum('value') :
                $flowDeals->count();

            if ($flowValue > 0) {
                $links[] = [
                    'source' => $nodeIndex[$currentStageId],
                    'target' => $nodeIndex[$nextStageId],
                    'value' => max(1, $flowValue),
                    'deals' => $flowDeals->count(),
                    'totalValue' => $flowDeals->sum('value'),
                ];
            }
        }

        $wonDeals = $deals->where('status', 'won');
        $lostDeals = $deals->where('status', 'lost');
        $totalDeals = $deals->count();
        $wonCount = $wonDeals->count();

        $stats = [
            'totalDeals' => $totalDeals,
            'totalValue' => $deals->sum('value'),
            'wonValue' => $wonDeals->sum('value'),
            'lostValue' => $lostDeals->sum('value'),
            'conversionRate' => $totalDeals > 0 ? round(($wonCount / $totalDeals) * 100, 1) : 0,
            'averageDealValue' => $totalDeals > 0 ? round($deals->sum('value') / $totalDeals, 2) : 0,
        ];

        $stageStats = [];
        foreach ($stages as $stage) {
            $stageId = $stage->id;
            $stageStats[$stage->name] = [
                'count' => $stageDeals[$stageId] ?? 0,
                'value' => $stageValues[$stageId] ?? 0,
            ];
        }

        return response()->json([
            'nodes' => $nodes,
            'links' => $links,
            'stats' => $stats,
            'stageStats' => $stageStats,
        ]);
    }

    public function forecasting()
    {
        return view('analytics.forecasting');
    }

    public function getDashboardData(Dashboard $dashboard)
    {
        if (! $dashboard->isAccessibleBy(Auth::user())) {
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
        if (! $chart->isAccessibleBy(Auth::user())) {
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

        if (! $model || ! class_exists($model)) {
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
