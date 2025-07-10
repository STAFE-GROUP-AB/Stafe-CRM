<div class="space-y-6">
    <!-- Dashboard Header -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Dashboard Builder</h2>
                <p class="text-gray-600">Create and manage interactive dashboards</p>
            </div>
            <div class="flex space-x-3">
                <button wire:click="$set('showWidgetModal', true)" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Widget
                </button>
                @if(!$isEditing)
                <button wire:click="createDashboard" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Create Dashboard
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Dashboard Configuration -->
    @if(!$isEditing)
    <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Dashboard Configuration</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="dashboardName" class="block text-sm font-medium text-gray-700">Dashboard Name</label>
                <input wire:model="dashboardName" type="text" id="dashboardName" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div>
                <label for="dashboardType" class="block text-sm font-medium text-gray-700">Type</label>
                <select wire:model="dashboardType" id="dashboardType" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="personal">Personal</option>
                    <option value="team">Team</option>
                    <option value="company">Company</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="dashboardDescription" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea wire:model="dashboardDescription" id="dashboardDescription" rows="3" 
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
            </div>
            <div class="md:col-span-2">
                <div class="flex items-center">
                    <input wire:model="isPublic" id="isPublic" type="checkbox" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="isPublic" class="ml-2 block text-sm text-gray-900">Make this dashboard public</label>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Dashboard Grid -->
    @if($isEditing && $dashboard)
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">{{ $dashboard->name }}</h3>
            <button wire:click="updateDashboard" 
                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                Save Changes
            </button>
        </div>
        
        <div class="grid grid-cols-12 gap-4 min-h-96" id="dashboard-grid">
            @foreach($widgets as $widget)
            <div class="dashboard-widget col-span-{{ $widget->position['w'] ?? 4 }} row-span-{{ $widget->position['h'] ?? 3 }} bg-gray-50 rounded-lg p-4 border-2 border-dashed border-gray-200"
                 data-widget-id="{{ $widget->id }}">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900">{{ $widget->title }}</h4>
                    <div class="flex space-x-1">
                        <button wire:click="deleteWidget({{ $widget->id }})" 
                                class="text-red-600 hover:text-red-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="text-sm text-gray-500">
                    {{ $widget->description }}
                </div>
                <div class="mt-2 text-xs text-gray-400">
                    Type: {{ ucfirst($widget->widget_type) }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Available Dashboards -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Available Dashboards</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($userDashboards as $userDashboard)
            <div class="border rounded-lg p-4 hover:border-blue-500 cursor-pointer transition-colors"
                 wire:click="mount({{ $userDashboard->id }})">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900">{{ $userDashboard->name }}</h4>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $userDashboard->type === 'personal' ? 'bg-blue-100 text-blue-800' : ($userDashboard->type === 'team' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                        {{ ucfirst($userDashboard->type) }}
                    </span>
                </div>
                <p class="text-sm text-gray-500">{{ $userDashboard->description }}</p>
                <div class="mt-2 text-xs text-gray-400">
                    {{ $userDashboard->dashboardWidgets()->count() }} widgets
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Available Widget Types -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Available Widget Types</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($availableWidgets as $type => $widget)
            <div class="border rounded-lg p-4 hover:border-blue-500 cursor-pointer transition-colors"
                 wire:click="addWidget('{{ $type }}')">
                <div class="flex items-center mb-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <!-- Dynamic icon based on widget type -->
                            @if($widget['icon'] === 'chart-bar')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            @elseif($widget['icon'] === 'calculator')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            @elseif($widget['icon'] === 'table')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0V4a2 2 0 012-2h14a2 2 0 012 2v16a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                            @elseif($widget['icon'] === 'fire')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                            @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            @endif
                        </svg>
                    </div>
                    <h4 class="font-medium text-gray-900">{{ $widget['name'] }}</h4>
                </div>
                <p class="text-sm text-gray-500">{{ $widget['description'] }}</p>
                <div class="mt-2 flex flex-wrap gap-1">
                    @foreach($widget['types'] as $subType)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ ucfirst($subType) }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Widget Creation Modal -->
    @if($showWidgetModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Widget</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Widget Type</label>
                        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option>Chart</option>
                            <option>Metric</option>
                            <option>Table</option>
                            <option>Heat Map</option>
                            <option>List</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button wire:click="$set('showWidgetModal', false)" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                        Add Widget
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dashboard grid drag and drop functionality would go here
        // Using libraries like Sortable.js or Muuri for grid layout
        console.log('Dashboard builder initialized');
    });
</script>
@endpush