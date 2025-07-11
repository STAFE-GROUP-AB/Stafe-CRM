<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Stalled Customers</h2>
            <p class="text-gray-600">Customers that haven't been contacted recently</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-sm text-gray-600">
                <span class="font-medium">{{ $this->stats['total_stalled'] }}</span> stalled customers
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Stalled</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_stalled']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Never Contacted</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['never_contacted']) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Avg Days Since Contact</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['avg_days_since_contact'] ?? 0) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search customers..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <!-- Sales Rep Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sales Rep</label>
                <select 
                    wire:model.live="selectedOwner"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">All Sales Reps</option>
                    @foreach($salesReps as $rep)
                        <option value="{{ $rep->id }}">{{ $rep->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Stalled Days Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Consider Stalled After</label>
                <select 
                    wire:model.live="stalledDays"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="7">7 days</option>
                    <option value="14">14 days</option>
                    <option value="30">30 days</option>
                    <option value="60">60 days</option>
                    <option value="90">90 days</option>
                </select>
            </div>

            <!-- Quick Actions -->
            <div class="flex items-end">
                <button 
                    wire:click="$refresh"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Stalled by Sales Rep Summary -->
    @if($stalledByRep->count() > 0)
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Stalled Customers by Sales Rep</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($stalledByRep as $rep)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-gray-700">
                                {{ substr($rep->owner->name ?? 'Unknown', 0, 1) }}
                            </span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $rep->owner->name ?? 'Unassigned' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $rep->count }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Stalled Customers List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Stalled Customers List
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Customers that haven't been contacted in the last {{ $stalledDays }} days
            </p>
        </div>
        
        @if($stalledCustomers->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($stalledCustomers as $customer)
                    <li class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ substr($customer->full_name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="flex items-center">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $customer->full_name }}
                                        </p>
                                        @if($customer->company)
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $customer->company->name }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $customer->email }}
                                        @if($customer->phone)
                                            • {{ $customer->phone }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $customer->owner->name ?? 'Unassigned' }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        @if($customer->last_contacted_at)
                                            Last contact: {{ $customer->last_contacted_at->diffForHumans() }}
                                        @else
                                            <span class="text-red-600">Never contacted</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button 
                                        wire:click="updateLastContacted({{ $customer->id }})"
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                    >
                                        Mark Contacted
                                    </button>
                                    <a 
                                        href="{{ route('contacts.index') }}"
                                        class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            
            <div class="px-6 py-4 bg-gray-50">
                {{ $stalledCustomers->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M34 18l2-2a2 2 0 0 0 0-2.83l-2-2a2 2 0 0 0-2.83 0l-2 2M34 18L22 6M34 18L22 30M22 6l-2 2M22 6L10 18M22 30l-2 2M22 30L10 18m12 12L10 18"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No stalled customers found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Great job! All customers have been contacted within the last {{ $stalledDays }} days.
                </p>
            </div>
        @endif
    </div>
</div>