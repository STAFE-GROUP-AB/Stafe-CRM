@extends('layouts.app')

@section('title', 'Pipeline Visualization')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pipeline Visualization</h1>
                <p class="text-gray-600">Sankey diagrams, conversion funnel analysis, and multi-dimensional pipeline views</p>
            </div>
            <div class="flex space-x-3">
                <select class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option>Sankey Diagram</option>
                    <option>Funnel Chart</option>
                    <option>Flow Diagram</option>
                    <option>Timeline View</option>
                </select>
                <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Create Visualization
                </button>
            </div>
        </div>
    </div>

    <!-- Pipeline Visualization -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Pipeline Flow Visualization</h3>
            <p class="mt-1 text-sm text-gray-500">Interactive pipeline diagram will be displayed here</p>
        </div>
    </div>

    <!-- Pipeline Metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Conversion Rates -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Stage Conversion Rates</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Lead → Qualified</span>
                    <div class="flex items-center">
                        <div class="w-24 h-2 bg-gray-200 rounded mr-2">
                            <div class="h-2 bg-blue-600 rounded" style="width: 75%"></div>
                        </div>
                        <span class="text-sm font-medium">75%</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Qualified → Proposal</span>
                    <div class="flex items-center">
                        <div class="w-24 h-2 bg-gray-200 rounded mr-2">
                            <div class="h-2 bg-green-600 rounded" style="width: 60%"></div>
                        </div>
                        <span class="text-sm font-medium">60%</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Proposal → Negotiation</span>
                    <div class="flex items-center">
                        <div class="w-24 h-2 bg-gray-200 rounded mr-2">
                            <div class="h-2 bg-yellow-600 rounded" style="width: 45%"></div>
                        </div>
                        <span class="text-sm font-medium">45%</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Negotiation → Closed Won</span>
                    <div class="flex items-center">
                        <div class="w-24 h-2 bg-gray-200 rounded mr-2">
                            <div class="h-2 bg-purple-600 rounded" style="width: 30%"></div>
                        </div>
                        <span class="text-sm font-medium">30%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pipeline Health -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Pipeline Health</h3>
            <div class="space-y-4">
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-sm font-medium text-green-900">Total Pipeline Value</div>
                    <div class="text-2xl font-bold text-green-700">$2.4M</div>
                    <div class="text-sm text-green-600">+15% from last quarter</div>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-sm font-medium text-blue-900">Average Deal Size</div>
                    <div class="text-2xl font-bold text-blue-700">$45K</div>
                    <div class="text-sm text-blue-600">Industry average: $38K</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="text-sm font-medium text-purple-900">Average Sales Cycle</div>
                    <div class="text-2xl font-bold text-purple-700">67 days</div>
                    <div class="text-sm text-purple-600">-8 days improvement</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/d3@7"></script>
<script src="https://cdn.jsdelivr.net/npm/d3-sankey@0.12.3/dist/d3-sankey.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Pipeline visualization page loaded');
        // Sankey diagram and other pipeline visualizations will be implemented here
    });
</script>
@endpush