@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $connection->name }}</h1>
        <p class="text-gray-600">{{ $connection->integration->name }} connection</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Connection Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Status -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Connection Status</h2>
                <div class="flex items-center justify-between">
                    <div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $connection->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $connection->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <p class="text-sm text-gray-600 mt-2">
                            Last sync: {{ $connection->last_sync_at ? $connection->last_sync_at->diffForHumans() : 'Never' }}
                        </p>
                    </div>
                    <div class="flex space-x-2">
                        <form action="{{ route('integrations.connections.test', $connection) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                Test Connection
                            </button>
                        </form>
                        <form action="{{ route('integrations.connections.sync', $connection) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Sync Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Configuration -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Configuration</h2>
                <form action="{{ route('integrations.connections.update', $connection) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Connection Name</label>
                            <input type="text" name="name" id="name" 
                                   value="{{ $connection->name }}"
                                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" 
                                       {{ $connection->is_active ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                        </div>

                        <!-- Credentials Section -->
                        <div class="border-t pt-4">
                            <h3 class="text-md font-medium text-gray-900 mb-3">Credentials</h3>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <p class="text-sm text-gray-600 mb-3">
                                    Update your {{ $connection->integration->name }} credentials if needed.
                                </p>
                                
                                @if($connection->integration->auth_type === 'api_key')
                                    <div>
                                        <label for="api_key" class="block text-sm font-medium text-gray-700">API Key</label>
                                        <input type="password" name="credentials[api_key]" id="api_key" 
                                               placeholder="Enter new API key (leave blank to keep current)"
                                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('integrations.connections.index') }}" 
                               class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Update Connection
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Integration Info -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Integration Info</h2>
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                        <span class="text-lg font-bold text-gray-600">
                            {{ substr($connection->integration->name, 0, 2) }}
                        </span>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">{{ $connection->integration->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $connection->integration->category->name ?? 'Other' }}</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600">{{ $connection->integration->description }}</p>
            </div>

            <!-- Sync Stats -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Sync Statistics</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="text-sm text-gray-900">{{ $connection->created_at->diffForHumans() }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Sync</dt>
                        <dd class="text-sm text-gray-900">
                            {{ $connection->last_sync_at ? $connection->last_sync_at->diffForHumans() : 'Never' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sync Status</dt>
                        <dd class="text-sm text-gray-900">{{ $connection->sync_status ?? 'Unknown' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-red-900 mb-4">Danger Zone</h2>
                <p class="text-sm text-gray-600 mb-4">
                    Removing this connection will permanently delete all associated data and cannot be undone.
                </p>
                <form action="{{ route('integrations.connections.destroy', $connection) }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to remove this connection? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Remove Connection
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection