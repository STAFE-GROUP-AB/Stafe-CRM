@extends('layouts.app')

@section('title', 'Visual Intelligence & Analytics')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Visual Intelligence & Analytics</h1>
                <p class="text-gray-600">Advanced visualization and analytics tools for data-driven insights</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('analytics.dashboards') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Dashboard Builder
                </a>
                <a href="{{ route('analytics.charts') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Chart Builder
                </a>
            </div>
        </div>
    </div>

    <!-- Analytics Feature Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Interactive Dashboards -->
        <a href="{{ route('analytics.dashboards') }}" class="block">
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-200 hover:border-blue-300">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 ml-3">Interactive Dashboards</h3>
                </div>
                <p class="text-gray-600 mb-4">Drag-and-drop dashboard builder with real-time data visualization and customizable widgets.</p>
                <div class="text-sm text-blue-600 font-medium">{{ $dashboards->count() }} dashboards available</div>
            </div>
        </a>

        <!-- Heat Map Analytics -->
        <a href="{{ route('analytics.heat-maps') }}" class="block">
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-200 hover:border-red-300">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 ml-3">Heat Map Analytics</h3>
                </div>
                <p class="text-gray-600 mb-4">Visual representation of sales activities, performance metrics, and geographic data patterns.</p>
                <div class="text-sm text-red-600 font-medium">{{ $heatMaps->count() }} heat maps created</div>
            </div>
        </a>

        <!-- Custom Chart Builder -->
        <a href="{{ route('analytics.charts') }}" class="block">
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-200 hover:border-green-300">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 ml-3">Custom Chart Builder</h3>
                </div>
                <p class="text-gray-600 mb-4">Advanced visualization tools for creating custom charts with multiple data sources and types.</p>
                <div class="text-sm text-green-600 font-medium">{{ $charts->count() }} custom charts</div>
            </div>
        </a>

        <!-- Relationship Networks -->
        <a href="{{ route('analytics.relationships') }}" class="block">
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-200 hover:border-purple-300">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 ml-3">Relationship Networks</h3>
                </div>
                <p class="text-gray-600 mb-4">Visual network maps of customer relationships, influence networks, and stakeholder analysis.</p>
                <div class="text-sm text-purple-600 font-medium">Network mapping</div>
            </div>
        </a>

        <!-- Pipeline Visualization -->
        <a href="{{ route('analytics.pipeline') }}" class="block">
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-200 hover:border-yellow-300">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 ml-3">Pipeline Visualization</h3>
                </div>
                <p class="text-gray-600 mb-4">Sankey diagrams, conversion funnel analysis, and multi-dimensional pipeline views.</p>
                <div class="text-sm text-yellow-600 font-medium">Advanced pipeline analytics</div>
            </div>
        </a>

        <!-- Forecasting Simulator -->
        <a href="{{ route('analytics.forecasting') }}" class="block">
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-200 hover:border-indigo-300">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 ml-3">Forecasting Simulator</h3>
                </div>
                <p class="text-gray-600 mb-4">What-if scenario modeling, forecasting simulators, and trend analysis tools.</p>
                <div class="text-sm text-indigo-600 font-medium">Predictive modeling</div>
            </div>
        </a>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Dashboards -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Dashboards</h3>
            <div class="space-y-3">
                @forelse($dashboards as $dashboard)
                <div class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $dashboard->name }}</h4>
                        <p class="text-sm text-gray-500">{{ $dashboard->description }}</p>
                        <p class="text-xs text-gray-400">{{ $dashboard->dashboardWidgets->count() }} widgets</p>
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ $dashboard->updated_at->diffForHumans() }}
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">No dashboards created yet</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Charts -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Charts</h3>
            <div class="space-y-3">
                @forelse($charts as $chart)
                <div class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $chart->name }}</h4>
                        <p class="text-sm text-gray-500">{{ $chart->description }}</p>
                        <p class="text-xs text-gray-400">{{ ucfirst($chart->chart_type) }} chart</p>
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ $chart->updated_at->diffForHumans() }}
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">No charts created yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Visual Intelligence & Analytics dashboard loaded');
    });
</script>
@endpush