<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Battle Card Analytics</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track usage and effectiveness of your battle cards</p>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Days Filter -->
                    <select wire:model.live="daysFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="365">Last year</option>
                    </select>
                    <a href="{{ route('sales-enablement.battle-cards') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200">
                        Back to Battle Cards
                    </a>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ number_format($summaryStats['total_views']) }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Views</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($summaryStats['unique_users']) }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Unique Users</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($summaryStats['total_shares']) }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Shares</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($summaryStats['active_cards']) }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Active Cards</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($summaryStats['deals_with_views']) }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Deals with Views</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $summaryStats['avg_views_per_card'] }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Avg Views/Card</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Most Viewed Battle Cards -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Most Viewed Battle Cards</h3>
                    </div>
                    <div class="p-6">
                        @if($mostViewed->isEmpty())
                            <p class="text-gray-500 dark:text-gray-400 text-center py-4">No views recorded yet</p>
                        @else
                            <div class="space-y-4">
                                @foreach($mostViewed as $item)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0 w-8 h-8 bg-amber-100 dark:bg-amber-900 rounded-full flex items-center justify-center">
                                                <span class="text-amber-600 dark:text-amber-400 font-medium text-sm">{{ $loop->iteration }}</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->battleCard->title ?? 'Unknown' }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->battleCard->competitor_name ?? '' }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200">
                                                {{ number_format($item->view_count) }} views
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Battle Card Effectiveness -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Effectiveness Rate</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Deals won after viewing battle card</p>
                    </div>
                    <div class="p-6">
                        @if($effectiveness->isEmpty())
                            <p class="text-gray-500 dark:text-gray-400 text-center py-4">No deal data available yet</p>
                        @else
                            <div class="space-y-4">
                                @foreach($effectiveness->take(10) as $item)
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['battle_card']->title }}</p>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <span class="text-xs text-green-600 dark:text-green-400">{{ $item['effectiveness']['deals_won'] }} won</span>
                                                <span class="text-xs text-gray-400">/</span>
                                                <span class="text-xs text-red-600 dark:text-red-400">{{ $item['effectiveness']['deals_lost'] }} lost</span>
                                                <span class="text-xs text-gray-400">/</span>
                                                <span class="text-xs text-blue-600 dark:text-blue-400">{{ $item['effectiveness']['deals_open'] }} open</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            @if($item['effectiveness']['effectiveness_rate'] !== null)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item['effectiveness']['effectiveness_rate'] >= 50 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                                    {{ $item['effectiveness']['effectiveness_rate'] }}%
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-500 dark:text-gray-400">Pending</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- View Trends -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">View Trends</h3>
                    </div>
                    <div class="p-6">
                        @if($viewTrends->isEmpty())
                            <p class="text-gray-500 dark:text-gray-400 text-center py-4">No trend data available yet</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Views</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Users</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cards</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($viewTrends->reverse()->take(10) as $trend)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($trend->date)->format('M d, Y') }}</td>
                                                <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-white">{{ number_format($trend->view_count) }}</td>
                                                <td class="px-4 py-2 text-sm text-right text-gray-600 dark:text-gray-400">{{ number_format($trend->unique_users) }}</td>
                                                <td class="px-4 py-2 text-sm text-right text-gray-600 dark:text-gray-400">{{ number_format($trend->unique_cards) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Usage by Team Member -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Usage by Team Member</h3>
                    </div>
                    <div class="p-6">
                        @if($usageByUser->isEmpty())
                            <p class="text-gray-500 dark:text-gray-400 text-center py-4">No usage data available yet</p>
                        @else
                            <div class="space-y-4">
                                @foreach($usageByUser->take(10) as $usage)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0 w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                                <span class="text-gray-600 dark:text-gray-300 font-medium text-sm">{{ substr($usage->user->name ?? 'U', 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $usage->user->name ?? 'Unknown' }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $usage->user->email ?? '' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4 text-sm">
                                            <div class="text-center">
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $usage->view_count }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">views</div>
                                            </div>
                                            <div class="text-center">
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $usage->cards_viewed }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">cards</div>
                                            </div>
                                            <div class="text-center">
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $usage->shares_count }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">shares</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recently Viewed -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recently Viewed</h3>
                </div>
                <div class="p-6">
                    @if($recentlyViewed->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">No recent views</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Battle Card</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Viewed By</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Deal</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Context</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">When</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($recentlyViewed as $view)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $view->battleCard->title ?? 'Unknown' }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $view->battleCard->competitor_name ?? '' }}</p>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $view->user->name ?? 'Unknown' }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                                @if($view->deal)
                                                    {{ $view->deal->name }}
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                    @if($view->was_shared) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @elseif($view->context === 'deal_context') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                                    @endif">
                                                    @if($view->was_shared)
                                                        Shared via {{ $view->shared_via }}
                                                    @else
                                                        {{ ucfirst(str_replace('_', ' ', $view->context)) }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $view->viewed_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
