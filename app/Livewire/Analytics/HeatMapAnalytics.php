<?php

namespace App\Livewire\Analytics;

use App\Models\AnalyticsHeatMap;
use App\Models\Deal;
use App\Models\Contact;
use App\Models\Task;
use Livewire\Component;
use Carbon\Carbon;

class HeatMapAnalytics extends Component
{
    public $heatMap;
    public $heatMapName = '';
    public $heatMapDescription = '';
    public $heatMapType = 'sales_activity';
    public $dateFrom;
    public $dateTo;
    public $showCreateModal = false;
    public $selectedHeatMap = null;
    public $heatMapData = [];

    public function mount()
    {
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function createHeatMap()
    {
        $this->validate([
            'heatMapName' => 'required|string|max:255',
            'heatMapDescription' => 'nullable|string',
            'heatMapType' => 'required|in:sales_activity,performance,geographic,time_based',
            'dateFrom' => 'required|date',
            'dateTo' => 'required|date|after:dateFrom',
        ]);

        $heatMapData = $this->generateHeatMapData();

        $this->heatMap = AnalyticsHeatMap::create([
            'name' => $this->heatMapName,
            'description' => $this->heatMapDescription,
            'type' => $this->heatMapType,
            'configuration' => $this->getHeatMapConfiguration(),
            'data_points' => $heatMapData,
            'color_scheme' => $this->getColorScheme(),
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'user_id' => auth()->id(),
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        $this->showCreateModal = false;
        $this->resetForm();
        $this->dispatch('heatmap-created');
    }

    public function loadHeatMap($heatMapId)
    {
        $this->selectedHeatMap = AnalyticsHeatMap::findOrFail($heatMapId);
        $this->heatMapData = $this->selectedHeatMap->data_points;
    }

    public function deleteHeatMap($heatMapId)
    {
        AnalyticsHeatMap::findOrFail($heatMapId)->delete();
        $this->selectedHeatMap = null;
        $this->heatMapData = [];
        $this->dispatch('heatmap-deleted');
    }

    public function refreshHeatMap()
    {
        if ($this->selectedHeatMap) {
            $newData = $this->generateHeatMapData();
            $this->selectedHeatMap->update(['data_points' => $newData]);
            $this->heatMapData = $newData;
            $this->dispatch('heatmap-refreshed');
        }
    }

    private function generateHeatMapData(): array
    {
        switch ($this->heatMapType) {
            case 'sales_activity':
                return $this->generateSalesActivityData();
            case 'performance':
                return $this->generatePerformanceData();
            case 'geographic':
                return $this->generateGeographicData();
            case 'time_based':
                return $this->generateTimeBasedData();
            default:
                return [];
        }
    }

    private function generateSalesActivityData(): array
    {
        $data = [];
        $startDate = Carbon::parse($this->dateFrom);
        $endDate = Carbon::parse($this->dateTo);

        // Generate activity data by day and hour
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            for ($hour = 0; $hour < 24; $hour++) {
                $dayOfWeek = $date->dayOfWeek;
                $key = $date->format('Y-m-d') . '_' . $hour;
                
                // Count activities for this day/hour
                $activities = Task::where('created_at', '>=', $date->copy()->hour($hour))
                    ->where('created_at', '<', $date->copy()->hour($hour + 1))
                    ->count();

                $data[] = [
                    'x' => $hour,
                    'y' => $dayOfWeek,
                    'value' => $activities,
                    'date' => $date->format('Y-m-d'),
                    'hour' => $hour,
                    'day_name' => $date->format('l'),
                ];
            }
        }

        return $data;
    }

    private function generatePerformanceData(): array
    {
        $data = [];
        $users = auth()->user()->tenant->users ?? [];

        foreach ($users as $user) {
            $deals = Deal::where('user_id', $user->id)
                ->whereBetween('created_at', [$this->dateFrom, $this->dateTo])
                ->get();

            $totalValue = $deals->sum('value');
            $wonDeals = $deals->where('stage.is_won', true)->count();
            $totalDeals = $deals->count();

            $data[] = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'total_value' => $totalValue,
                'won_deals' => $wonDeals,
                'total_deals' => $totalDeals,
                'win_rate' => $totalDeals > 0 ? ($wonDeals / $totalDeals) * 100 : 0,
                'value' => $totalValue, // For heat map intensity
            ];
        }

        return $data;
    }

    private function generateGeographicData(): array
    {
        $data = [];
        $contacts = Contact::whereBetween('created_at', [$this->dateFrom, $this->dateTo])
            ->whereNotNull('city')
            ->get();

        $cityCounts = $contacts->groupBy('city')->map(function ($group) {
            return $group->count();
        });

        foreach ($cityCounts as $city => $count) {
            $data[] = [
                'city' => $city,
                'count' => $count,
                'value' => $count,
            ];
        }

        return $data;
    }

    private function generateTimeBasedData(): array
    {
        $data = [];
        $startDate = Carbon::parse($this->dateFrom);
        $endDate = Carbon::parse($this->dateTo);

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $dayDeals = Deal::whereDate('created_at', $date)->count();
            $dayContacts = Contact::whereDate('created_at', $date)->count();
            $dayTasks = Task::whereDate('created_at', $date)->count();

            $totalActivity = $dayDeals + $dayContacts + $dayTasks;

            $data[] = [
                'date' => $date->format('Y-m-d'),
                'day_of_week' => $date->dayOfWeek,
                'deals' => $dayDeals,
                'contacts' => $dayContacts,
                'tasks' => $dayTasks,
                'total_activity' => $totalActivity,
                'value' => $totalActivity,
            ];
        }

        return $data;
    }

    private function getHeatMapConfiguration(): array
    {
        return [
            'type' => $this->heatMapType,
            'date_range' => [
                'from' => $this->dateFrom,
                'to' => $this->dateTo,
            ],
            'granularity' => 'day',
            'aggregation' => 'count',
        ];
    }

    private function getColorScheme(): array
    {
        return [
            'colors' => ['#f7fafc', '#e2e8f0', '#cbd5e0', '#a0aec0', '#718096'],
            'min_color' => '#f7fafc',
            'max_color' => '#718096',
            'opacity' => 0.8,
        ];
    }

    private function resetForm()
    {
        $this->heatMapName = '';
        $this->heatMapDescription = '';
        $this->heatMapType = 'sales_activity';
    }

    public function render()
    {
        $userHeatMaps = AnalyticsHeatMap::where('user_id', auth()->id())
            ->orWhere('tenant_id', auth()->user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.analytics.heat-map-analytics', [
            'userHeatMaps' => $userHeatMaps,
        ]);
    }
}