@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Integration Marketplace</h1>
        <a href="{{ route('integrations.connections.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
            My Connections
        </a>
    </div>

    @if($categories->count() > 0)
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Browse by Category</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($categories as $category)
                    <div class="bg-white p-4 rounded-lg shadow text-center">
                        <h3 class="font-medium text-gray-900">{{ $category->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $category->integrations->count() }} integrations</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg">
        @if($integrations->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @foreach($integrations as $integration)
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <span class="text-lg font-bold text-gray-600">{{ substr($integration->name, 0, 2) }}</span>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $integration->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $integration->category->name ?? 'Other' }}</p>
                            </div>
                        </div>
                        
                        <p class="text-gray-600 mb-4">{{ $integration->description }}</p>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">{{ $integration->install_count }} installs</span>
                            <a href="{{ route('integrations.show', $integration) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="px-6 py-4">
                {{ $integrations->links() }}
            </div>
        @else
            <div class="p-6 text-center">
                <p class="text-gray-500">No integrations available.</p>
            </div>
        @endif
    </div>
</div>
@endsection