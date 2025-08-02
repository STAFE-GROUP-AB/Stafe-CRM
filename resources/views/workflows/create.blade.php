@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Create Workflow</h1>
        <p class="text-gray-600">Set up automated workflows with triggers and actions.</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('workflows.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Workflow Name</label>
                    <input type="text" name="name" id="name" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="{{ old('name') }}" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="trigger_type" class="block text-sm font-medium text-gray-700 mb-2">Trigger Type</label>
                    <select name="trigger_type" id="trigger_type" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select Trigger Type</option>
                        <option value="event" {{ old('trigger_type') === 'event' ? 'selected' : '' }}>Event-based</option>
                        <option value="schedule" {{ old('trigger_type') === 'schedule' ? 'selected' : '' }}>Scheduled</option>
                        <option value="manual" {{ old('trigger_type') === 'manual' ? 'selected' : '' }}>Manual</option>
                    </select>
                    @error('trigger_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Describe what this workflow does...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                           {{ old('is_active') ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">Activate workflow immediately</span>
                </label>
            </div>

            <div class="mt-8 flex justify-between">
                <a href="{{ route('workflows.index') }}" 
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Create Workflow
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-lg font-medium text-blue-900 mb-2">Next Steps</h3>
        <p class="text-blue-800">After creating the workflow, you'll be able to add workflow steps, configure triggers, and set up actions.</p>
    </div>
</div>
@endsection