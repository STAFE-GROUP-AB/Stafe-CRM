<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Battle Cards</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Competitive intelligence to help your team win deals</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('sales-enablement.battle-card-analytics') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg flex items-center dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Analytics
                    </a>
                    <button wire:click="createCard" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Battle Card
                    </button>
                </div>
            </div>

            <!-- Flash Messages -->
            @if (session()->has('message'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-900 dark:border-green-700 dark:text-green-200" role="alert">
                    {{ session('message') }}
                </div>
            @endif

            <!-- Search and Filters -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="text" wire:model.live="search" placeholder="Search battle cards..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <select wire:model.live="threatFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Threat Levels</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                    <select wire:model.live="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Statuses</option>
                        <option value="draft">Draft</option>
                        <option value="active">Active</option>
                        <option value="archived">Archived</option>
                    </select>
                    <select wire:model.live="selectedDealId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Select Deal Context (optional)</option>
                        @foreach($availableDeals as $deal)
                            <option value="{{ $deal->id }}">{{ $deal->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Battle Cards Grid -->
            @if($battleCards->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
                    <div class="text-gray-400 mb-4">
                        <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No battle cards yet</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Create battle cards to help your team win against competitors.</p>
                    <button wire:click="createCard" class="bg-amber-600 hover:bg-amber-700 text-white font-medium py-2 px-4 rounded-lg">
                        Create Your First Battle Card
                    </button>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($battleCards as $card)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow duration-200 overflow-hidden">
                            <!-- Card Header with Threat Level -->
                            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between
                                @if($card->threat_level === 'critical') bg-red-50 dark:bg-red-900/20
                                @elseif($card->threat_level === 'high') bg-orange-50 dark:bg-orange-900/20
                                @elseif($card->threat_level === 'medium') bg-yellow-50 dark:bg-yellow-900/20
                                @else bg-green-50 dark:bg-green-900/20
                                @endif">
                                <span class="text-xs font-semibold uppercase
                                    @if($card->threat_level === 'critical') text-red-700 dark:text-red-400
                                    @elseif($card->threat_level === 'high') text-orange-700 dark:text-orange-400
                                    @elseif($card->threat_level === 'medium') text-yellow-700 dark:text-yellow-400
                                    @else text-green-700 dark:text-green-400
                                    @endif">
                                    {{ ucfirst($card->threat_level) }} Threat
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @if($card->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($card->status === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                    @else bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400
                                    @endif">
                                    {{ ucfirst($card->status) }}
                                </span>
                            </div>

                            <!-- Card Content -->
                            <div class="p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ $card->title }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">vs. {{ $card->competitor_name }}</p>
                                    </div>
                                    @if($card->win_rate > 0)
                                        <div class="ml-2 text-center">
                                            <div class="text-lg font-bold {{ $card->win_rate >= 50 ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                                {{ number_format($card->win_rate, 0) }}%
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Win Rate</div>
                                        </div>
                                    @endif
                                </div>

                                @if($card->overview)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">{{ Str::limit($card->overview, 100) }}</p>
                                @endif

                                <!-- Analytics Badges -->
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        {{ number_format($card->view_count) }} views
                                    </div>
                                    <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                        </svg>
                                        {{ number_format($card->usage_count) }} uses
                                    </div>
                                    @php
                                        $lastViewed = $card->getLastViewedAt();
                                    @endphp
                                    @if($lastViewed)
                                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400" title="Last viewed: {{ $lastViewed->format('M d, Y H:i') }}">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $lastViewed->diffForHumans() }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Effectiveness Badge -->
                                @php
                                    $effectiveness = $card->getEffectiveness();
                                @endphp
                                @if($effectiveness['total_deal_views'] > 0)
                                    <div class="mb-3 p-2 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-600 dark:text-gray-400">Deal Effectiveness</span>
                                            @if($effectiveness['effectiveness_rate'] !== null)
                                                <span class="font-semibold {{ $effectiveness['effectiveness_rate'] >= 50 ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                                    {{ $effectiveness['effectiveness_rate'] }}% win rate
                                                </span>
                                            @else
                                                <span class="text-gray-500 dark:text-gray-400">{{ $effectiveness['deals_open'] }} deals pending</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2 mt-1 text-xs">
                                            <span class="text-green-600 dark:text-green-400">{{ $effectiveness['deals_won'] }} won</span>
                                            <span class="text-gray-400">/</span>
                                            <span class="text-red-600 dark:text-red-400">{{ $effectiveness['deals_lost'] }} lost</span>
                                            <span class="text-gray-400">/</span>
                                            <span class="text-blue-600 dark:text-blue-400">{{ $effectiveness['deals_open'] }} open</span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Key Info Pills -->
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @if($card->our_strengths && count($card->our_strengths) > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300">
                                            {{ count($card->our_strengths) }} strengths
                                        </span>
                                    @endif
                                    @if($card->objection_handling && count($card->objection_handling) > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">
                                            {{ count($card->objection_handling) }} objections
                                        </span>
                                    @endif
                                    @if($card->winning_strategies && count($card->winning_strategies) > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300">
                                            {{ count($card->winning_strategies) }} strategies
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Card Actions -->
                            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                <button wire:click="viewCard({{ $card->id }})" class="text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 text-sm font-medium">
                                    View Details
                                </button>
                                <div class="flex items-center space-x-2">
                                    <button wire:click="shareCard({{ $card->id }}, 'copy')" class="p-1.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" title="Share">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="duplicateCard({{ $card->id }})" class="p-1.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" title="Duplicate">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="editCard({{ $card->id }})" class="p-1.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="deleteCard({{ $card->id }})" wire:confirm="Are you sure you want to delete this battle card?" class="p-1.5 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $battleCards->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- View Card Modal -->
    @if($selectedCard)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between
                        @if($selectedCard->threat_level === 'critical') bg-red-50 dark:bg-red-900/20
                        @elseif($selectedCard->threat_level === 'high') bg-orange-50 dark:bg-orange-900/20
                        @elseif($selectedCard->threat_level === 'medium') bg-yellow-50 dark:bg-yellow-900/20
                        @else bg-green-50 dark:bg-green-900/20
                        @endif">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $selectedCard->title }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">vs. {{ $selectedCard->competitor_name }}</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Analytics badges in modal header -->
                            <div class="flex items-center space-x-3 text-sm">
                                <span class="flex items-center text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ number_format($selectedCard->view_count) }}
                                </span>
                                @if($selectedCard->win_rate > 0)
                                    <span class="font-semibold {{ $selectedCard->win_rate >= 50 ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                        {{ number_format($selectedCard->win_rate, 0) }}% Win Rate
                                    </span>
                                @endif
                            </div>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                        @if($selectedCard->overview)
                            <div class="mb-6">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase mb-2">Overview</h4>
                                <p class="text-gray-600 dark:text-gray-400">{{ $selectedCard->overview }}</p>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Our Strengths -->
                            @if($selectedCard->our_strengths && count($selectedCard->our_strengths) > 0)
                                <div>
                                    <h4 class="text-sm font-semibold text-green-700 dark:text-green-400 uppercase mb-2">Our Strengths</h4>
                                    <ul class="space-y-1">
                                        @foreach($selectedCard->our_strengths as $strength)
                                            <li class="flex items-start">
                                                <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $strength }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Our Weaknesses -->
                            @if($selectedCard->our_weaknesses && count($selectedCard->our_weaknesses) > 0)
                                <div>
                                    <h4 class="text-sm font-semibold text-red-700 dark:text-red-400 uppercase mb-2">Our Weaknesses</h4>
                                    <ul class="space-y-1">
                                        @foreach($selectedCard->our_weaknesses as $weakness)
                                            <li class="flex items-start">
                                                <svg class="w-4 h-4 text-red-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $weakness }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Competitor Strengths -->
                            @if($selectedCard->competitor_strengths && count($selectedCard->competitor_strengths) > 0)
                                <div>
                                    <h4 class="text-sm font-semibold text-orange-700 dark:text-orange-400 uppercase mb-2">Competitor Strengths</h4>
                                    <ul class="space-y-1">
                                        @foreach($selectedCard->competitor_strengths as $strength)
                                            <li class="flex items-start">
                                                <svg class="w-4 h-4 text-orange-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $strength }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Competitor Weaknesses -->
                            @if($selectedCard->competitor_weaknesses && count($selectedCard->competitor_weaknesses) > 0)
                                <div>
                                    <h4 class="text-sm font-semibold text-blue-700 dark:text-blue-400 uppercase mb-2">Competitor Weaknesses</h4>
                                    <ul class="space-y-1">
                                        @foreach($selectedCard->competitor_weaknesses as $weakness)
                                            <li class="flex items-start">
                                                <svg class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $weakness }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <!-- Key Differentiators -->
                        @if($selectedCard->key_differentiators && count($selectedCard->key_differentiators) > 0)
                            <div class="mt-6">
                                <h4 class="text-sm font-semibold text-purple-700 dark:text-purple-400 uppercase mb-2">Key Differentiators</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($selectedCard->key_differentiators as $diff)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300">
                                            {{ $diff }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Objection Handling -->
                        @if($selectedCard->objection_handling && count($selectedCard->objection_handling) > 0)
                            <div class="mt-6">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase mb-2">Objection Handling</h4>
                                <div class="space-y-3">
                                    @foreach($selectedCard->objection_handling as $objection)
                                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                                                <span class="text-red-500">Q:</span> {{ $objection['objection'] ?? '' }}
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                <span class="text-green-500">A:</span> {{ $objection['response'] ?? '' }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Winning Strategies -->
                        @if($selectedCard->winning_strategies && count($selectedCard->winning_strategies) > 0)
                            <div class="mt-6">
                                <h4 class="text-sm font-semibold text-amber-700 dark:text-amber-400 uppercase mb-2">Winning Strategies</h4>
                                <ul class="space-y-2">
                                    @foreach($selectedCard->winning_strategies as $index => $strategy)
                                        <li class="flex items-start">
                                            <span class="flex-shrink-0 w-6 h-6 bg-amber-100 dark:bg-amber-900/50 rounded-full flex items-center justify-center text-amber-700 dark:text-amber-400 text-sm font-medium mr-2">
                                                {{ $index + 1 }}
                                            </span>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $strategy }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Sales Notes -->
                        @if($selectedCard->sales_notes)
                            <div class="mt-6">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase mb-2">Sales Notes</h4>
                                <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-3">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedCard->sales_notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Created by {{ $selectedCard->creator->name ?? 'Unknown' }} on {{ $selectedCard->created_at->format('M d, Y') }}
                        </div>
                        <div class="flex items-center space-x-3">
                            <button wire:click="shareCard({{ $selectedCard->id }}, 'copy')" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-gray-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                </svg>
                                Share
                            </button>
                            <button wire:click="editCard({{ $selectedCard->id }})" class="bg-amber-600 hover:bg-amber-700 text-white font-medium py-2 px-4 rounded-lg flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Create/Edit Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                    <form wire:submit="saveCard">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $editingCard ? 'Edit Battle Card' : 'Create Battle Card' }}
                            </h3>
                        </div>

                        <div class="px-6 py-4 max-h-[70vh] overflow-y-auto space-y-4">
                            <!-- Basic Info -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title *</label>
                                    <input type="text" wire:model="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Competitor Name *</label>
                                    <input type="text" wire:model="competitor_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @error('competitor_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Overview</label>
                                <textarea wire:model="overview" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Threat Level *</label>
                                    <select wire:model="threat_level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status *</label>
                                    <select wire:model="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="draft">Draft</option>
                                        <option value="active">Active</option>
                                        <option value="archived">Archived</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Our Strengths -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Our Strengths</label>
                                @foreach($our_strengths as $index => $strength)
                                    <div class="flex items-center space-x-2 mb-2">
                                        <input type="text" wire:model="our_strengths.{{ $index }}" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter a strength">
                                        @if(count($our_strengths) > 1)
                                            <button type="button" wire:click="removeStrength({{ $index }})" class="p-2 text-red-500 hover:text-red-700">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addStrength" class="text-amber-600 hover:text-amber-700 text-sm font-medium">+ Add Strength</button>
                            </div>

                            <!-- Our Weaknesses -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Our Weaknesses</label>
                                @foreach($our_weaknesses as $index => $weakness)
                                    <div class="flex items-center space-x-2 mb-2">
                                        <input type="text" wire:model="our_weaknesses.{{ $index }}" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter a weakness">
                                        @if(count($our_weaknesses) > 1)
                                            <button type="button" wire:click="removeWeakness({{ $index }})" class="p-2 text-red-500 hover:text-red-700">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addWeakness" class="text-amber-600 hover:text-amber-700 text-sm font-medium">+ Add Weakness</button>
                            </div>

                            <!-- Winning Strategies -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Winning Strategies</label>
                                @foreach($winning_strategies as $index => $strategy)
                                    <div class="flex items-center space-x-2 mb-2">
                                        <input type="text" wire:model="winning_strategies.{{ $index }}" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter a strategy">
                                        @if(count($winning_strategies) > 1)
                                            <button type="button" wire:click="removeStrategy({{ $index }})" class="p-2 text-red-500 hover:text-red-700">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addStrategy" class="text-amber-600 hover:text-amber-700 text-sm font-medium">+ Add Strategy</button>
                            </div>

                            <!-- Objection Handling -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Objection Handling</label>
                                @foreach($objection_handling as $index => $objection)
                                    <div class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg mb-2">
                                        <div class="mb-2">
                                            <input type="text" wire:model="objection_handling.{{ $index }}.objection" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Objection">
                                        </div>
                                        <div class="flex items-start space-x-2">
                                            <textarea wire:model="objection_handling.{{ $index }}.response" rows="2" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Response"></textarea>
                                            @if(count($objection_handling) > 1)
                                                <button type="button" wire:click="removeObjection({{ $index }})" class="p-2 text-red-500 hover:text-red-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addObjection" class="text-amber-600 hover:text-amber-700 text-sm font-medium">+ Add Objection</button>
                            </div>

                            <!-- Sales Notes -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sales Notes</label>
                                <textarea wire:model="sales_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Additional notes for the sales team"></textarea>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg">
                                {{ $editingCard ? 'Update' : 'Create' }} Battle Card
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
