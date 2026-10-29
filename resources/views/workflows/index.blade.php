@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Automation Workflows</h1>
        <div class="flex space-x-3">
            <a href="{{ route('workflows.create') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 transition-colors duration-200">
                Quick Create
            </a>
            <a href="{{ route('workflows.builder') }}" class="bg-amber-600 text-white px-4 py-2 rounded-md hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600 transition-colors duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
                </svg>
                Visual Builder
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        @if($workflows->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Trigger</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Steps</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($workflows as $workflow)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $workflow->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $workflow->description }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ ucfirst($workflow->trigger_type) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $workflow->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                        {{ $workflow->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $workflow->steps->count() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('workflows.show', $workflow) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">View</a>
                                    <a href="{{ route('workflows.builder.edit', $workflow) }}" class="text-amber-600 hover:text-amber-900 dark:text-amber-400 dark:hover:text-amber-300 mr-3">Builder</a>
                                    <a href="{{ route('workflows.edit', $workflow) }}" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-3">Edit</a>
                                    <form action="{{ route('workflows.destroy', $workflow) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4">
                {{ $workflows->links() }}
            </div>
        @else
            <div class="p-6 text-center">
                <p class="text-gray-500 dark:text-gray-400">No workflows found. <a href="{{ route('workflows.create') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">Create your first workflow</a>.</p>
            </div>
        @endif
    </div>
</div>
@endsection