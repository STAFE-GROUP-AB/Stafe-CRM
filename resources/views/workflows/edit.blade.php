@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Workflow</h1>
            <p class="text-gray-600 dark:text-gray-400">Modify workflow settings and configuration.</p>
        </div>
        <a href="{{ route('workflows.builder.edit', $workflow) }}" class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600 transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
            </svg>
            Open Visual Builder
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form action="{{ route('workflows.update', $workflow) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Workflow Name</label>
                    <input type="text" name="name" id="name"
                           class="w-full border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="{{ old('name', $workflow->name) }}" required>
                    @error('name')
                        <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="trigger_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Trigger Type</label>
                    <select name="trigger_type" id="trigger_type"
                            class="w-full border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select Trigger Type</option>
                        <option value="event" {{ old('trigger_type', $workflow->trigger_type) === 'event' ? 'selected' : '' }}>Event-based</option>
                        <option value="schedule" {{ old('trigger_type', $workflow->trigger_type) === 'schedule' ? 'selected' : '' }}>Scheduled</option>
                        <option value="manual" {{ old('trigger_type', $workflow->trigger_type) === 'manual' ? 'selected' : '' }}>Manual</option>
                    </select>
                    @error('trigger_type')
                        <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Describe what this workflow does...">{{ old('description', $workflow->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1"
                           class="rounded border-gray-300 dark:border-gray-700 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-700"
                           {{ old('is_active', $workflow->is_active) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Workflow is active</span>
                </label>
            </div>

            <div class="mt-8 flex justify-between">
                <a href="{{ route('workflows.show', $workflow) }}"
                   class="bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                    Update Workflow
                </button>
            </div>
        </form>
    </div>

    <!-- Workflow Steps Section -->
    @if($workflow->steps->count() > 0)
        <div class="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Workflow Steps</h2>
            <div class="space-y-4">
                @foreach($workflow->steps as $step)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $step->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $step->description }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ ucfirst($step->type) }}
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                    Step {{ $step->order }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                <button type="button" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600">
                    Add Step
                </button>
            </div>
        </div>
    @endif
</div>
@endsection