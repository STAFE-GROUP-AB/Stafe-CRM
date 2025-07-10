<div class="space-y-6">
    <!-- Heat Map Header -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Heat Map Analytics</h2>
                <p class="text-gray-600">Visualize sales activities, performance, and geographic data</p>
            </div>
            <button wire:click="$set('showCreateModal', true)" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Heat Map
            </button>
        </div>
    </div>

    <!-- Heat Map Display -->
    @if($selectedHeatMap)
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-medium text-gray-900">{{ $selectedHeatMap->name }}</h3>
                <p class="text-sm text-gray-500">{{ $selectedHeatMap->description }}</p>
            </div>
            <div class="flex space-x-2">
                <button wire:click="refreshHeatMap" 
                        class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
                <button wire:click="deleteHeatMap({{ $selectedHeatMap->id }})" 
                        class="inline-flex items-center px-3 py-1 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete
                </button>
            </div>
        </div>
        
        <!-- Heat Map Visualization -->
        <div class="border rounded-lg p-4 bg-gray-50">
            <div class="heat-map-container" id="heatmap-{{ $selectedHeatMap->id }}">
                @if($selectedHeatMap->type === 'sales_activity')
                    <!-- Sales Activity Heat Map -->
                    <div class="grid grid-cols-24 gap-1 mb-4">
                        <div class="col-span-1"></div>
                        @for($hour = 0; $hour < 24; $hour++)
                        <div class="text-xs text-gray-500 text-center">{{ $hour }}</div>
                        @endfor
                        
                        @for($day = 0; $day < 7; $day++)
                        <div class="text-xs text-gray-500 text-right pr-2">{{ ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][$day] }}</div>
                        @for($hour = 0; $hour < 24; $hour++)
                        @php
                            $value = collect($heatMapData)->where('y', $day)->where('x', $hour)->first()['value'] ?? 0;
                            $intensity = $selectedHeatMap->getIntensityLevel($value);
                        @endphp
                        <div class="w-4 h-4 rounded-sm heat-cell heat-{{ $intensity }}" 
                             title="Day {{ $day }}, Hour {{ $hour }}: {{ $value }} activities"
                             data-value="{{ $value }}">
                        </div>
                        @endfor
                        @endfor
                    </div>
                @elseif($selectedHeatMap->type === 'performance')
                    <!-- Performance Heat Map -->
                    <div class="space-y-2">
                        @foreach($heatMapData as $data)
                        <div class="flex items-center justify-between p-2 border rounded">
                            <div>
                                <span class="font-medium">{{ $data['user_name'] }}</span>
                                <span class="text-sm text-gray-500 ml-2">{{ $data['total_deals'] }} deals</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="text-sm text-gray-600">${{ number_format($data['total_value']) }}</div>
                                <div class="w-16 h-4 bg-gray-200 rounded">
                                    <div class="h-full bg-blue-600 rounded" style="width: {{ $data['win_rate'] }}%"></div>
                                </div>
                                <div class="text-sm text-gray-600">{{ number_format($data['win_rate'], 1) }}%</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @elseif($selectedHeatMap->type === 'geographic')
                    <!-- Geographic Heat Map -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($heatMapData as $data)
                        <div class="border rounded-lg p-4 text-center">
                            <div class="text-lg font-semibold text-gray-900">{{ $data['city'] }}</div>
                            <div class="text-2xl font-bold text-blue-600">{{ $data['count'] }}</div>
                            <div class="text-sm text-gray-500">contacts</div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <!-- Time-based Heat Map -->
                    <div class="space-y-2">
                        @foreach($heatMapData as $data)
                        <div class="flex items-center justify-between p-2 border rounded">
                            <div>
                                <span class="font-medium">{{ $data['date'] }}</span>
                                <span class="text-sm text-gray-500 ml-2">{{ ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][$data['day_of_week']] }}</span>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-sm">
                                    <span class="text-blue-600">{{ $data['deals'] }} deals</span>
                                    <span class="text-green-600 ml-2">{{ $data['contacts'] }} contacts</span>
                                    <span class="text-orange-600 ml-2">{{ $data['tasks'] }} tasks</span>
                                </div>
                                <div class="text-lg font-semibold text-gray-900">{{ $data['total_activity'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Heat Map Details -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-sm font-medium text-blue-900">Date Range</div>
                <div class="text-blue-700">{{ $selectedHeatMap->date_from->format('M j, Y') }} - {{ $selectedHeatMap->date_to->format('M j, Y') }}</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-sm font-medium text-green-900">Data Points</div>
                <div class="text-green-700">{{ count($heatMapData) }} entries</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="text-sm font-medium text-purple-900">Type</div>
                <div class="text-purple-700">{{ ucfirst(str_replace('_', ' ', $selectedHeatMap->type)) }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Available Heat Maps -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Available Heat Maps</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($userHeatMaps as $heatMap)
            <div class="border rounded-lg p-4 hover:border-blue-500 cursor-pointer transition-colors"
                 wire:click="loadHeatMap({{ $heatMap->id }})">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900">{{ $heatMap->name }}</h4>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ ucfirst(str_replace('_', ' ', $heatMap->type)) }}
                    </span>
                </div>
                <p class="text-sm text-gray-500">{{ $heatMap->description }}</p>
                <div class="mt-2 text-xs text-gray-400">
                    {{ $heatMap->date_from->format('M j') }} - {{ $heatMap->date_to->format('M j, Y') }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Create Heat Map Modal -->
    @if($showCreateModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Heat Map</h3>
                <div class="space-y-4">
                    <div>
                        <label for="heatMapName" class="block text-sm font-medium text-gray-700">Name</label>
                        <input wire:model="heatMapName" type="text" id="heatMapName" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="heatMapType" class="block text-sm font-medium text-gray-700">Type</label>
                        <select wire:model="heatMapType" id="heatMapType" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="sales_activity">Sales Activity</option>
                            <option value="performance">Performance</option>
                            <option value="geographic">Geographic</option>
                            <option value="time_based">Time-based</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="dateFrom" class="block text-sm font-medium text-gray-700">From Date</label>
                            <input wire:model="dateFrom" type="date" id="dateFrom" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="dateTo" class="block text-sm font-medium text-gray-700">To Date</label>
                            <input wire:model="dateTo" type="date" id="dateTo" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                    <div>
                        <label for="heatMapDescription" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea wire:model="heatMapDescription" id="heatMapDescription" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button wire:click="$set('showCreateModal', false)" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button wire:click="createHeatMap" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                        Create Heat Map
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    .heat-very-low { background-color: #f7fafc; }
    .heat-low { background-color: #e2e8f0; }
    .heat-medium { background-color: #cbd5e0; }
    .heat-high { background-color: #a0aec0; }
    .heat-very-high { background-color: #718096; }
    
    .heat-cell {
        transition: all 0.2s ease;
    }
    
    .heat-cell:hover {
        transform: scale(1.2);
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>
@endpush