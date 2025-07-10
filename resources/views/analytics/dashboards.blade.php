@extends('layouts.app')

@section('title', 'Dashboard Builder')

@section('content')
<div class="space-y-6">
    @livewire('analytics.dashboard-builder')
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize dashboard grid if it exists
        const dashboardGrid = document.getElementById('dashboard-grid');
        if (dashboardGrid) {
            new Sortable(dashboardGrid, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function(evt) {
                    // Handle widget position updates
                    console.log('Widget moved', evt);
                }
            });
        }
    });
</script>
@endpush