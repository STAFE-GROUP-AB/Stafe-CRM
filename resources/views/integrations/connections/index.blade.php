@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">API Connections</h1>
        <a href="{{ route('integrations.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
            Browse Integrations
        </a>
    </div>

    <div class="bg-white shadow rounded-lg">
        @if($connections->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @foreach($connections as $connection)
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                    <span class="text-sm font-bold text-gray-600">
                                        {{ substr($connection->integration->name, 0, 2) }}
                                    </span>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $connection->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $connection->integration->name }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $connection->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $connection->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        
                        <div class="text-sm text-gray-600 mb-4">
                            <p>Last sync: {{ $connection->last_sync_at ? $connection->last_sync_at->diffForHumans() : 'Never' }}</p>
                            <p>Created: {{ $connection->created_at->diffForHumans() }}</p>
                        </div>
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('integrations.connections.show', $connection) }}" 
                               class="flex-1 text-center bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">
                                Manage
                            </a>
                            <form action="{{ route('integrations.connections.test', $connection) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" 
                                        class="w-full bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700">
                                    Test
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="px-6 py-4">
                {{ $connections->links() }}
            </div>
        @else
            <div class="p-6 text-center">
                <div class="max-w-md mx-auto">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No connections yet</h3>
                    <p class="text-gray-500 mb-4">Connect with third-party services to automate your workflows.</p>
                    <a href="{{ route('integrations.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        Browse available integrations
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection