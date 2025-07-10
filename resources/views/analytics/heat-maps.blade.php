@extends('layouts.app')

@section('title', 'Heat Map Analytics')

@section('content')
<div class="space-y-6">
    @livewire('analytics.heat-map-analytics')
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/d3@7"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Heat map analytics loaded');
        
        // Initialize heat map interactions
        document.querySelectorAll('.heat-cell').forEach(cell => {
            cell.addEventListener('mouseover', function() {
                // Show tooltip or highlight related cells
                console.log('Heat cell hovered:', this.dataset.value);
            });
        });
    });
</script>
@endpush