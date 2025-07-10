@extends('layouts.app')

@section('title', 'Relationship Networks')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Relationship Networks</h1>
                <p class="text-gray-600">Visual network maps of customer relationships and stakeholder analysis</p>
            </div>
            <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Network
            </button>
        </div>
    </div>

    <!-- Network Visualization -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Relationship Network Mapping</h3>
            <p class="mt-1 text-sm text-gray-500">Interactive network visualization will be displayed here</p>
        </div>
    </div>

    <!-- Network Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">Total Nodes</div>
            <div class="text-2xl font-bold text-gray-900">156</div>
            <div class="text-xs text-green-600">+12% from last month</div>
        </div>
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">Connections</div>
            <div class="text-2xl font-bold text-gray-900">423</div>
            <div class="text-xs text-green-600">+8% from last month</div>
        </div>
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">Network Density</div>
            <div class="text-2xl font-bold text-gray-900">0.73</div>
            <div class="text-xs text-blue-600">High connectivity</div>
        </div>
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">Key Influencers</div>
            <div class="text-2xl font-bold text-gray-900">23</div>
            <div class="text-xs text-purple-600">Central nodes</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/vis-network@9.1.2/dist/vis-network.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Relationship networks page loaded');
        // Network visualization code will be implemented here
    });
</script>
@endpush