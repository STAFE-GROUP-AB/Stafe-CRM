<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="flex justify-between items-center py-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Customer Experience Platform</h1>
                <p class="mt-1 text-sm text-gray-600">Manage customer relationships, feedback, and journey optimization</p>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button wire:click="setActiveTab('overview')" 
                        class="@if($activeTab === 'overview') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Overview
                </button>
                <button wire:click="setActiveTab('tickets')" 
                        class="@if($activeTab === 'tickets') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Customer Portal
                </button>
                <button wire:click="setActiveTab('knowledge')" 
                        class="@if($activeTab === 'knowledge') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Knowledge Base
                </button>
                <button wire:click="setActiveTab('surveys')" 
                        class="@if($activeTab === 'surveys') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Surveys
                </button>
                <button wire:click="setActiveTab('health')" 
                        class="@if($activeTab === 'health') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Health Scores
                </button>
                <button wire:click="setActiveTab('journeys')" 
                        class="@if($activeTab === 'journeys') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Journey Mapping
                </button>
                <button wire:click="setActiveTab('loyalty')" 
                        class="@if($activeTab === 'loyalty') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Loyalty Programs
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        @if($activeTab === 'overview')
            <!-- Overview Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Tickets Stats -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 16h6"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16v12H4z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Customer Tickets</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['tickets']['total'] }}</dd>
                                    <dd class="text-sm text-red-600">{{ $stats['tickets']['open'] }} open</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Knowledge Base Stats -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Knowledge Articles</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['knowledge_base']['published'] }}</dd>
                                    <dd class="text-sm text-green-600">{{ number_format($stats['knowledge_base']['total_views']) }} views</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Health Scores Stats -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Customer Health</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['health_scores']['avg_score'], 1) }}%</dd>
                                    <dd class="text-sm text-orange-600">{{ $stats['health_scores']['at_risk'] }} at risk</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts and Notifications -->
            @if(count($alertsAndNotifications) > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Attention Required</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($alertsAndNotifications as $alert)
                                    <li>{{ $alert['message'] }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Activity -->
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Activity</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Latest customer interactions and updates</p>
                </div>
                <ul class="divide-y divide-gray-200">
                    @foreach($recentActivity as $activity)
                        <li class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($activity['type'] === 'ticket')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Ticket
                                            </span>
                                        @elseif($activity['type'] === 'survey')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Survey
                                            </span>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">{{ $activity['message'] }}</p>
                                        <p class="text-sm text-gray-500">{{ $activity['contact'] }}</p>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $activity['time']->diffForHumans() }}
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

        @elseif($activeTab === 'tickets')
            @livewire('customer-portal')
        @elseif($activeTab === 'knowledge')
            @livewire('knowledge-base-manager')
        @elseif($activeTab === 'surveys')
            @livewire('survey-manager')
        @elseif($activeTab === 'health')
            @livewire('customer-health-dashboard')
        @elseif($activeTab === 'journeys')
            @livewire('journey-mapping-dashboard')
        @elseif($activeTab === 'loyalty')
            @livewire('loyalty-program-manager')
        @endif
    </div>
</div>