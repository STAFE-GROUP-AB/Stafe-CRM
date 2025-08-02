<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-sm">
        <!-- Header -->
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Advanced Search & Filtering</h1>
                @if($query)
                    <button wire:click="showSaveModal" 
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v4"/>
                        </svg>
                        Save Search
                    </button>
                @endif
            </div>
        </div>

        <div class="flex">
            <!-- Sidebar -->
            <div class="w-64 border-r border-gray-200">
                <nav class="p-4 space-y-2">
                    <a wire:click="setActiveTab('search')" 
                       class="block px-3 py-2 rounded-md text-sm font-medium cursor-pointer {{ $activeTab === 'search' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        🔍 Global Search
                    </a>
                    <a wire:click="setActiveTab('saved')" 
                       class="block px-3 py-2 rounded-md text-sm font-medium cursor-pointer {{ $activeTab === 'saved' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        💾 Saved Searches
                    </a>
                    <a wire:click="setActiveTab('filters')" 
                       class="block px-3 py-2 rounded-md text-sm font-medium cursor-pointer {{ $activeTab === 'filters' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        🎯 Advanced Filters
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="flex-1">
                @if($activeTab === 'search')
                    <!-- Search Interface -->
                    <div class="p-6">
                        <!-- Search Bar -->
                        <div class="mb-6">
                            <div class="relative">
                                <input type="text" wire:model.live.debounce.300ms="query" 
                                       placeholder="Search across all your CRM data..." 
                                       class="w-full pl-10 pr-4 py-3 text-lg rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Search Type Filter -->
                        <div class="flex items-center space-x-4 mb-6">
                            <label class="text-sm font-medium text-gray-700">Search in:</label>
                            <div class="flex space-x-2">
                                <button wire:click="$set('searchType', 'all')" 
                                        class="px-3 py-1 rounded-full text-sm {{ $searchType === 'all' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    All
                                </button>
                                <button wire:click="$set('searchType', 'contacts')" 
                                        class="px-3 py-1 rounded-full text-sm {{ $searchType === 'contacts' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    Contacts
                                </button>
                                <button wire:click="$set('searchType', 'companies')" 
                                        class="px-3 py-1 rounded-full text-sm {{ $searchType === 'companies' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    Companies
                                </button>
                                <button wire:click="$set('searchType', 'deals')" 
                                        class="px-3 py-1 rounded-full text-sm {{ $searchType === 'deals' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    Deals
                                </button>
                                <button wire:click="$set('searchType', 'tasks')" 
                                        class="px-3 py-1 rounded-full text-sm {{ $searchType === 'tasks' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    Tasks
                                </button>
                            </div>
                        </div>

                        <!-- Search Results -->
                        @if($totalResults > 0)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">Found {{ number_format($totalResults) }} {{ $totalResults === 1 ? 'result' : 'results' }} for "{{ $query }}"</p>
                            </div>

                            <div class="space-y-6">
                                <!-- Contacts Results -->
                                @if(isset($searchResults['contacts']) && $searchResults['contacts']->count() > 0)
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            Contacts ({{ $searchResults['contacts']->count() }})
                                        </h3>
                                        <div class="space-y-2">
                                            @foreach($searchResults['contacts'] as $contact)
                                                <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer">
                                                    <img class="h-10 w-10 rounded-full mr-4" 
                                                         src="https://ui-avatars.com/api/?name={{ urlencode($contact->name) }}&color=7F9CF5&background=EBF4FF" 
                                                         alt="{{ $contact->name }}">
                                                    <div class="flex-1">
                                                        <h4 class="text-sm font-medium text-gray-900">{{ $contact->name }}</h4>
                                                        <p class="text-sm text-gray-500">{{ $contact->email }}</p>
                                                        @if($contact->company)
                                                            <p class="text-xs text-gray-400">{{ $contact->company->name }}</p>
                                                        @endif
                                                    </div>
                                                    <a href="{{ route('contacts.show', $contact) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                                        View →
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Companies Results -->
                                @if(isset($searchResults['companies']) && $searchResults['companies']->count() > 0)
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            Companies ({{ $searchResults['companies']->count() }})
                                        </h3>
                                        <div class="space-y-2">
                                            @foreach($searchResults['companies'] as $company)
                                                <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer">
                                                    <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                                        <span class="text-green-600 font-medium text-sm">{{ substr($company->name, 0, 2) }}</span>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h4 class="text-sm font-medium text-gray-900">{{ $company->name }}</h4>
                                                        <p class="text-sm text-gray-500">{{ $company->industry }}</p>
                                                        <p class="text-xs text-gray-400">{{ $company->contacts->count() }} contacts</p>
                                                    </div>
                                                    <a href="{{ route('companies.show', $company) }}" class="text-green-600 hover:text-green-800 text-sm">
                                                        View →
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Deals Results -->
                                @if(isset($searchResults['deals']) && $searchResults['deals']->count() > 0)
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                            Deals ({{ $searchResults['deals']->count() }})
                                        </h3>
                                        <div class="space-y-2">
                                            @foreach($searchResults['deals'] as $deal)
                                                <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer">
                                                    <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h4 class="text-sm font-medium text-gray-900">{{ $deal->title }}</h4>
                                                        <p class="text-sm text-gray-500">${{ number_format($deal->value) }}</p>
                                                        @if($deal->company)
                                                            <p class="text-xs text-gray-400">{{ $deal->company->name }}</p>
                                                        @endif
                                                    </div>
                                                    <a href="{{ route('deals.show', $deal) }}" class="text-purple-600 hover:text-purple-800 text-sm">
                                                        View →
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Tasks Results -->
                                @if(isset($searchResults['tasks']) && $searchResults['tasks']->count() > 0)
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                            </svg>
                                            Tasks ({{ $searchResults['tasks']->count() }})
                                        </h3>
                                        <div class="space-y-2">
                                            @foreach($searchResults['tasks'] as $task)
                                                <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer">
                                                    <div class="h-10 w-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h4 class="text-sm font-medium text-gray-900">{{ $task->title }}</h4>
                                                        <p class="text-sm text-gray-500">{{ ucfirst($task->type) }} • {{ ucfirst($task->status) }}</p>
                                                        @if($task->due_date)
                                                            <p class="text-xs text-gray-400">Due: {{ $task->due_date->format('M j, Y') }}</p>
                                                        @endif
                                                    </div>
                                                    <a href="{{ route('tasks.show', $task) }}" class="text-yellow-600 hover:text-yellow-800 text-sm">
                                                        View →
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @elseif($query && strlen($query) >= 2)
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No results found</h3>
                                <p class="mt-1 text-sm text-gray-500">Try adjusting your search terms or search type.</p>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Search your CRM data</h3>
                                <p class="mt-1 text-sm text-gray-500">Enter at least 2 characters to start searching across contacts, companies, deals, and tasks.</p>
                            </div>
                        @endif
                    </div>

                @elseif($activeTab === 'saved')
                    <!-- Saved Searches -->
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Saved Searches</h2>
                        
                        @if($savedSearches->count() > 0)
                            <div class="space-y-4">
                                @foreach($savedSearches as $savedSearch)
                                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                        <div class="flex items-center justify-between mb-3">
                                            <h3 class="text-lg font-medium text-gray-900">{{ $savedSearch->name }}</h3>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ ucfirst($savedSearch->search_type) }}
                                            </span>
                                        </div>
                                        
                                        @if($savedSearch->description)
                                            <p class="text-sm text-gray-600 mb-4">{{ $savedSearch->description }}</p>
                                        @endif
                                        
                                        <div class="flex items-center justify-between mb-4">
                                            <p class="text-sm text-gray-700">Query: "{{ $savedSearch->query }}"</p>
                                            <span class="text-xs text-gray-500">{{ $savedSearch->created_at->diffForHumans() }}</span>
                                        </div>
                                        
                                        <div class="flex space-x-2">
                                            <button wire:click="loadSavedSearch({{ $savedSearch->id }})" 
                                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Load Search
                                            </button>
                                            <button wire:click="deleteSavedSearch({{ $savedSearch->id }})" 
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v4"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No saved searches</h3>
                                <p class="mt-1 text-sm text-gray-500">Create searches and save them for quick access later.</p>
                            </div>
                        @endif
                    </div>

                @elseif($activeTab === 'filters')
                    <!-- Advanced Filters -->
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Advanced Filters</h2>
                        
                        <form class="space-y-6">
                            <!-- Date Range -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date From</label>
                                    <input type="date" wire:model="filters.date_from" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date To</label>
                                    <input type="date" wire:model="filters.date_to" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select wire:model="filters.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-between">
                                <button type="button" wire:click="clearFilters" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                    Clear Filters
                                </button>
                                <button type="button" wire:click="performSearch" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Save Search Modal -->
    @if($showSaveModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Save Search</h3>
                        <button wire:click="hideSaveModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form wire:submit.prevent="saveSearch" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Search Name</label>
                            <input type="text" wire:model="savedSearchName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('savedSearchName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                            <textarea wire:model="savedSearchDescription" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            @error('savedSearchDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="bg-gray-50 p-3 rounded-md">
                            <p class="text-sm text-gray-600">Query: "{{ $query }}"</p>
                            <p class="text-sm text-gray-600">Type: {{ ucfirst($searchType) }}</p>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="hideSaveModal" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Save Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" 
             class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
             class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif
</div>