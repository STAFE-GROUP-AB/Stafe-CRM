@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">{{ $workflow->name }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('workflows.edit', $workflow) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Edit Workflow
                </a>
                <form action="{{ route('workflows.execute', $workflow) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        Execute Now
                    </button>
                </form>
            </div>
        </div>
        <p class="text-gray-600 mt-2">{{ $workflow->description }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Workflow Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Workflow Details</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Trigger Type</dt>
                        <dd class="text-sm text-gray-900">{{ ucfirst($workflow->trigger_type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd>
                            <span class="px-2 py-1 text-xs rounded-full {{ $workflow->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $workflow->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Steps</dt>
                        <dd class="text-sm text-gray-900">{{ $workflow->steps->count() }} steps</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Executions</dt>
                        <dd class="text-sm text-gray-900">{{ $workflow->instances->count() }} times</dd>
                    </div>
                </dl>
            </div>

            <!-- Workflow Steps -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Workflow Steps</h2>
                @if($workflow->steps->count() > 0)
                    <div class="space-y-4">
                        @foreach($workflow->steps as $step)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-medium text-gray-900">{{ $step->name }}</h3>
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($step->type) }}
                                    </span>
                                </div>
                                @if($step->description)
                                    <p class="text-sm text-gray-600 mt-2">{{ $step->description }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No steps configured yet.</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Recent Executions -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Executions</h2>
                @if($workflow->instances->count() > 0)
                    <div class="space-y-3">
                        @foreach($workflow->instances->take(5) as $instance)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-900">{{ $instance->created_at->diffForHumans() }}</span>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $instance->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($instance->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($instance->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No executions yet.</p>
                @endif
            </div>

            <!-- Actions -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>
                <div class="space-y-3">
                    <a href="{{ route('workflows.edit', $workflow) }}" 
                       class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Edit Workflow
                    </a>
                    <form action="{{ route('workflows.execute', $workflow) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="block w-full text-center bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                            Execute Now
                        </button>
                    </form>
                    <form action="{{ route('workflows.destroy', $workflow) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this workflow?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="block w-full text-center bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                            Delete Workflow
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection