@extends('layouts.app')

@section('title', 'Forecasting Simulator')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Forecasting Simulator</h1>
                <p class="text-gray-600">What-if scenario modeling and predictive analytics for sales planning</p>
            </div>
            <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                New Simulation
            </button>
        </div>
    </div>

    <!-- Simulation Controls -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Simulation Parameters</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Forecast Period</label>
                <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option>Next Quarter</option>
                    <option>Next 6 Months</option>
                    <option>Next Year</option>
                    <option>Custom Range</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Scenario Type</label>
                <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option>Revenue Forecast</option>
                    <option>Pipeline Analysis</option>
                    <option>Performance Simulation</option>
                    <option>Market Impact</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Confidence Level</label>
                <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option>90%</option>
                    <option>95%</option>
                    <option>99%</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Scenario Results -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Forecast Chart -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Forecast Scenarios</h3>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h4 class="mt-2 text-sm font-medium text-gray-900">Forecast Visualization</h4>
                <p class="mt-1 text-sm text-gray-500">Interactive forecast chart will be displayed here</p>
            </div>
        </div>

        <!-- Scenario Comparison -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Scenario Comparison</h3>
            <div class="space-y-4">
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-green-900">Best Case Scenario</span>
                        <span class="text-lg font-bold text-green-700">$3.2M</span>
                    </div>
                    <div class="text-sm text-green-600">+35% growth, high market confidence</div>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-blue-900">Most Likely Scenario</span>
                        <span class="text-lg font-bold text-blue-700">$2.4M</span>
                    </div>
                    <div class="text-sm text-blue-600">+15% growth, current market conditions</div>
                </div>
                
                <div class="bg-red-50 p-4 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-red-900">Worst Case Scenario</span>
                        <span class="text-lg font-bold text-red-700">$1.8M</span>
                    </div>
                    <div class="text-sm text-red-600">-5% decline, market challenges</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Variables and Assumptions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Key Variables -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Key Variables</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium text-gray-700">Win Rate</label>
                        <span class="text-sm text-gray-500">30%</span>
                    </div>
                    <input type="range" min="10" max="80" value="30" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                </div>
                
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium text-gray-700">Average Deal Size</label>
                        <span class="text-sm text-gray-500">$45K</span>
                    </div>
                    <input type="range" min="20000" max="100000" value="45000" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                </div>
                
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium text-gray-700">Sales Cycle (days)</label>
                        <span class="text-sm text-gray-500">67</span>
                    </div>
                    <input type="range" min="30" max="180" value="67" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                </div>
                
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium text-gray-700">Lead Volume</label>
                        <span class="text-sm text-gray-500">120/month</span>
                    </div>
                    <input type="range" min="50" max="300" value="120" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                </div>
            </div>
        </div>

        <!-- Model Assumptions -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Model Assumptions</h3>
            <div class="space-y-3">
                <div class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-gray-700">Current team capacity maintained</span>
                </div>
                <div class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-gray-700">No major market disruptions</span>
                </div>
                <div class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-gray-700">Seasonal patterns consistent</span>
                </div>
                <div class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-yellow-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <span class="text-sm text-gray-700">Economic conditions stable</span>
                </div>
                <div class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span class="text-sm text-gray-700">Competitive pressure increasing</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Forecasting simulator page loaded');
        
        // Add interactivity to range sliders
        document.querySelectorAll('input[type="range"]').forEach(slider => {
            slider.addEventListener('input', function() {
                // Update the displayed value
                const label = this.parentNode.querySelector('.text-gray-500');
                if (label) {
                    let value = this.value;
                    if (this.min === '20000') {
                        value = '$' + (value / 1000) + 'K';
                    } else if (this.min === '50') {
                        value = value + '/month';
                    } else if (this.min === '30') {
                        value = value + ' days';
                    } else {
                        value = value + '%';
                    }
                    label.textContent = value;
                }
                
                // Trigger forecast recalculation
                console.log('Variable changed:', this.value);
            });
        });
    });
</script>
@endpush