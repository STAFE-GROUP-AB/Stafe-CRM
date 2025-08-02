@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $integration->name }}</h1>
        <p class="text-gray-600">{{ $integration->description }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Integration Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Overview -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Overview</h2>
                <div class="prose max-w-none">
                    <p>{{ $integration->description }}</p>
                    
                    @if($integration->features)
                        <h4>Features:</h4>
                        <ul>
                            @foreach($integration->features as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Configuration -->
            @if($userConnections->count() === 0)
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Installation</h2>
                    <form action="{{ route('integrations.install', $integration) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Connection Name</label>
                                <input type="text" name="name" id="name" 
                                       value="My {{ $integration->name }} Connection"
                                       class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Credentials</label>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <p class="text-sm text-gray-600 mb-3">
                                        Configure your {{ $integration->name }} credentials:
                                    </p>
                                    
                                    @if($integration->auth_type === 'api_key')
                                        <div>
                                            <label for="api_key" class="block text-sm font-medium text-gray-700">API Key</label>
                                            <input type="password" name="credentials[api_key]" id="api_key" 
                                                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2" required>
                                        </div>
                                    @elseif($integration->auth_type === 'oauth')
                                        <p class="text-sm text-gray-600">
                                            OAuth authentication will be configured after installation.
                                        </p>
                                    @else
                                        <div class="space-y-3">
                                            <div>
                                                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                                                <input type="text" name="credentials[username]" id="username" 
                                                       class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                                            </div>
                                            <div>
                                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                                <input type="password" name="credentials[password]" id="password" 
                                                       class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                                    Install Integration
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Integration Info -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Information</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="text-sm text-gray-900">{{ $integration->category->name ?? 'Other' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Version</dt>
                        <dd class="text-sm text-gray-900">{{ $integration->version ?? '1.0.0' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Authentication</dt>
                        <dd class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $integration->auth_type)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Installs</dt>
                        <dd class="text-sm text-gray-900">{{ number_format($integration->install_count) }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Your Connections -->
            @if($userConnections->count() > 0)
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Your Connections</h2>
                    <div class="space-y-3">
                        @foreach($userConnections as $connection)
                            <div class="border border-gray-200 rounded-lg p-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">{{ $connection->name }}</span>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $connection->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $connection->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('integrations.connections.show', $connection) }}" 
                                       class="text-sm text-blue-600 hover:text-blue-800">
                                        Manage Connection
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>
                <div class="space-y-3">
                    <a href="{{ route('integrations.index') }}" 
                       class="block w-full text-center bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        Back to Marketplace
                    </a>
                    <a href="{{ route('integrations.connections.index') }}" 
                       class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        View All Connections
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection