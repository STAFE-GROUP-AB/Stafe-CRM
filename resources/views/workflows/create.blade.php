@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Workflow</h1>
        <p class="text-gray-600 dark:text-gray-400">Set up automated workflows with triggers and actions.</p>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form action="{{ route('workflows.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Workflow Name</label>
                    <input type="text" name="name" id="name"
                           class="w-full border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="{{ old('name') }}" required>
                    @error('name')
                        <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="trigger_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Trigger Type</label>
                    <select name="trigger_type" id="trigger_type"
                            class="w-full border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select Trigger Type</option>
                        <option value="event" {{ old('trigger_type') === 'event' ? 'selected' : '' }}>Event-based</option>
                        <option value="schedule" {{ old('trigger_type') === 'schedule' ? 'selected' : '' }}>Scheduled</option>
                        <option value="manual" {{ old('trigger_type') === 'manual' ? 'selected' : '' }}>Manual</option>
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
                          placeholder="Describe what this workflow does...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1"
                           class="rounded border-gray-300 dark:border-gray-700 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-700"
                           {{ old('is_active') ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Activate workflow immediately</span>
                </label>
            </div>

            <div class="mt-8 flex justify-between">
                <a href="{{ route('workflows.index') }}"
                   class="bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                    Create Workflow
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8 bg-amber-50 dark:bg-amber-900 border border-amber-200 dark:border-amber-700 rounded-lg p-4">
        <h3 class="text-lg font-medium text-amber-900 dark:text-amber-100 mb-2">Try the Visual Builder</h3>
        <p class="text-amber-800 dark:text-amber-200 mb-3">Want a more visual approach? Use our drag-and-drop workflow builder to create complex workflows easily.</p>
        <a href="{{ route('workflows.builder') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600 transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
            </svg>
            Open Visual Builder
        </a>
    </div>
</div>
@endsection