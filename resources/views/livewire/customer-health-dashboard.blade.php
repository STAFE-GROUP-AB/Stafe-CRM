<div>
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kundhalsa Dashboard</h1>
            <p class="text-gray-600 mt-2">Overvaka och spara kunders halsopoang</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <button wire:click="generateMissingHealthScores" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Skapa saknade
            </button>
            <button wire:click="recalculateAllScores" class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-medium hover:bg-amber-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Berakna om alla
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
        <!-- Total Customers -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-gray-500 uppercase">Totalt</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['total_customers'] }}</p>
                </div>
            </div>
        </div>

        <!-- Excellent -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-gray-500 uppercase">Utmarkt</p>
                    <p class="text-xl font-bold text-green-600">{{ $stats['excellent'] }}</p>
                </div>
            </div>
        </div>

        <!-- Good -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-gray-500 uppercase">Bra</p>
                    <p class="text-xl font-bold text-blue-600">{{ $stats['good'] }}</p>
                </div>
            </div>
        </div>

        <!-- At Risk -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-gray-500 uppercase">I riskzonen</p>
                    <p class="text-xl font-bold text-yellow-600">{{ $stats['at_risk'] }}</p>
                </div>
            </div>
        </div>

        <!-- Critical -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-gray-500 uppercase">Kritisk</p>
                    <p class="text-xl font-bold text-red-600">{{ $stats['critical'] }}</p>
                </div>
            </div>
        </div>

        <!-- Average Score -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-amber-100 rounded-lg">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-gray-500 uppercase">Snitt</p>
                    <p class="text-xl font-bold text-amber-600">{{ number_format($stats['average_score'], 1) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Health Scores Table -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- Filters -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="flex-1">
                            <input type="text" wire:model.live.debounce.300ms="searchQuery" placeholder="Sok kund..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm">
                        </div>
                        <div class="flex gap-2">
                            <select wire:model.live="filterStatus" class="rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm">
                                <option value="">Alla statusar</option>
                                <option value="excellent">Utmarkt</option>
                                <option value="good">Bra</option>
                                <option value="at_risk">I riskzonen</option>
                                <option value="critical">Kritisk</option>
                            </select>
                            <select wire:model.live="filterTrend" class="rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm">
                                <option value="">Alla trender</option>
                                <option value="improving">Forbattras</option>
                                <option value="stable">Stabil</option>
                                <option value="declining">Forsemras</option>
                            </select>
                            @if($filterStatus || $filterTrend || $searchQuery)
                                <button wire:click="resetFilters" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kund
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('overall_score')">
                                    <div class="flex items-center">
                                        Poang
                                        @if($sortField === 'overall_score')
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($sortDirection === 'asc')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                @endif
                                            </svg>
                                        @endif
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Trend
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Atgarder
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($healthScores as $score)
                                <tr class="hover:bg-gray-50 cursor-pointer" wire:click="viewDetails({{ $score->id }})">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-600">
                                                        {{ substr($score->contact->first_name ?? 'U', 0, 1) }}{{ substr($score->contact->last_name ?? 'K', 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $score->contact->full_name ?? 'Okand' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $score->contact->email ?? '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="h-2 rounded-full {{ $score->health_status === 'excellent' ? 'bg-green-500' : ($score->health_status === 'good' ? 'bg-blue-500' : ($score->health_status === 'at_risk' ? 'bg-yellow-500' : 'bg-red-500')) }}" style="width: {{ $score->overall_score }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">{{ number_format($score->overall_score, 1) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $score->health_status === 'excellent' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $score->health_status === 'good' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $score->health_status === 'at_risk' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $score->health_status === 'critical' ? 'bg-red-100 text-red-800' : '' }}
                                        ">
                                            {{ $score->health_status === 'excellent' ? 'Utmarkt' : '' }}
                                            {{ $score->health_status === 'good' ? 'Bra' : '' }}
                                            {{ $score->health_status === 'at_risk' ? 'I riskzonen' : '' }}
                                            {{ $score->health_status === 'critical' ? 'Kritisk' : '' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center text-sm">
                                            @if($score->trend === 'improving')
                                                <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                                </svg>
                                                <span class="text-green-600">Forbattras</span>
                                            @elseif($score->trend === 'declining')
                                                <svg class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                                </svg>
                                                <span class="text-red-600">Forsemras</span>
                                            @else
                                                <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                                <span class="text-gray-500">Stabil</span>
                                            @endif
                                            @if($score->trend_percentage && abs($score->trend_percentage) > 0)
                                                <span class="ml-1 text-xs text-gray-400">({{ $score->trend_percentage > 0 ? '+' : '' }}{{ number_format($score->trend_percentage, 1) }}%)</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click.stop="recalculateScore({{ $score->id }})" class="text-amber-600 hover:text-amber-900" title="Berakna om">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                        <p class="text-lg font-medium">Inga halsopoang hittades</p>
                                        <p class="text-sm mt-1">Klicka pa "Skapa saknade" for att generera halsopoang for alla kunder.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($healthScores->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $healthScores->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Alerts & Details -->
        <div class="space-y-6">
            <!-- At Risk Customers Alert -->
            @if($atRiskContacts->isNotEmpty())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <h3 class="text-sm font-semibold text-red-800">Kunder som kraver uppmärksamhet</h3>
                    </div>
                    <ul class="space-y-2">
                        @foreach($atRiskContacts as $atRisk)
                            <li class="flex items-center justify-between text-sm">
                                <span class="text-red-700">{{ $atRisk->contact->full_name ?? 'Okand' }}</span>
                                <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded text-xs font-medium">{{ number_format($atRisk->overall_score, 0) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Declining Scores Alert -->
            @if($decliningScores->isNotEmpty())
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                        </svg>
                        <h3 class="text-sm font-semibold text-yellow-800">Forsemrade halsopoang</h3>
                    </div>
                    <ul class="space-y-2">
                        @foreach($decliningScores as $declining)
                            <li class="flex items-center justify-between text-sm">
                                <span class="text-yellow-700">{{ $declining->contact->full_name ?? 'Okand' }}</span>
                                <span class="text-red-600 text-xs">{{ $declining->trend_percentage > 0 ? '+' : '' }}{{ number_format($declining->trend_percentage, 1) }}%</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Trend Distribution -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Trendfordelning</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Forbattras</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['improving'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Stabil</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['stable'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Forsemras</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['declining'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Health Score Details Modal -->
    @if($healthScoreDetails)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeDetails"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $healthScoreDetails->contact->full_name ?? 'Okand' }}</h3>
                                <p class="text-sm text-gray-500">{{ $healthScoreDetails->contact->email ?? '' }}</p>
                            </div>
                            <button wire:click="closeDetails" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Score Overview -->
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <p class="text-3xl font-bold {{ $healthScoreDetails->health_status_color === 'green' ? 'text-green-600' : ($healthScoreDetails->health_status_color === 'blue' ? 'text-blue-600' : ($healthScoreDetails->health_status_color === 'yellow' ? 'text-yellow-600' : 'text-red-600')) }}">
                                    {{ number_format($healthScoreDetails->overall_score, 1) }}
                                </p>
                                <p class="text-xs text-gray-500 uppercase mt-1">Halsopoang</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $healthScoreDetails->health_status === 'excellent' ? 'Utmarkt' : ($healthScoreDetails->health_status === 'good' ? 'Bra' : ($healthScoreDetails->health_status === 'at_risk' ? 'I riskzonen' : 'Kritisk')) }}
                                </p>
                                <p class="text-xs text-gray-500 uppercase mt-1">Status</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <p class="text-lg font-semibold {{ $healthScoreDetails->trend === 'improving' ? 'text-green-600' : ($healthScoreDetails->trend === 'declining' ? 'text-red-600' : 'text-gray-600') }}">
                                    {{ $healthScoreDetails->formatted_score_change }}
                                </p>
                                <p class="text-xs text-gray-500 uppercase mt-1">Forandring</p>
                            </div>
                        </div>

                        <!-- Score Breakdown -->
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Poangfordelning</h4>
                            <div class="space-y-3">
                                @foreach($healthScoreDetails->formatted_breakdown as $factor)
                                    <div>
                                        <div class="flex items-center justify-between text-sm mb-1">
                                            <span class="text-gray-600">{{ $factor['label'] }}</span>
                                            <span class="font-medium text-gray-900">{{ $factor['score'] }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full {{ $factor['color'] === 'green' ? 'bg-green-500' : ($factor['color'] === 'yellow' ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $factor['score'] }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Risk Factors -->
                        @if(!empty($healthScoreDetails->risk_factors))
                            <div class="mb-6">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">Riskfaktorer</h4>
                                <ul class="space-y-2">
                                    @foreach($healthScoreDetails->risk_factors as $risk)
                                        <li class="flex items-start">
                                            <svg class="w-4 h-4 text-red-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            <span class="text-sm text-gray-600">{{ is_array($risk) ? ($risk['description'] ?? $risk['factor'] ?? '') : $risk }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Improvement Suggestions -->
                        @if(!empty($healthScoreDetails->improvement_suggestions))
                            <div class="mb-6">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">Rekommenderade atgarder</h4>
                                <ul class="space-y-2">
                                    @foreach($healthScoreDetails->improvement_suggestions as $suggestion)
                                        <li class="flex items-start">
                                            <svg class="w-4 h-4 text-amber-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ is_array($suggestion) ? ($suggestion['action'] ?? '') : $suggestion }}</p>
                                                @if(is_array($suggestion) && isset($suggestion['details']))
                                                    <p class="text-xs text-gray-500 mt-0.5">{{ $suggestion['details'] }}</p>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Score History Chart Placeholder -->
                        @if(!empty($scoreHistory))
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">Halsopoanghistorik (30 dagar)</h4>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-end justify-between h-24 space-x-1">
                                        @foreach($scoreHistory as $history)
                                            <div class="flex-1 flex flex-col items-center">
                                                <div class="w-full bg-amber-500 rounded-t" style="height: {{ $history['score'] }}%"></div>
                                                <span class="text-xs text-gray-400 mt-1 truncate w-full text-center">{{ $history['date'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="recalculateScore({{ $healthScoreDetails->id }})" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Berakna om
                        </button>
                        <button wire:click="closeDetails" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Stang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>