@extends('layouts.app')

@section('title', 'Custom Chart Builder')

@section('content')
<div class="space-y-6">
    @livewire('analytics.custom-chart-builder')
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Custom chart builder loaded');
        
        // Chart.js configuration and utilities
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;
        
        window.createChart = function(canvasId, config) {
            const ctx = document.getElementById(canvasId);
            if (ctx) {
                return new Chart(ctx, config);
            }
        };
    });
</script>
@endpush