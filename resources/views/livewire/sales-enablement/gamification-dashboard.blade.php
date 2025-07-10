<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Gamification Dashboard
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Track your achievements, points, and leaderboard rankings
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <button wire:click="checkAchievements" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Check for New Achievements
                </button>
            </div>
        </div>

        <!-- Points and Level Card -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg mb-6">
            <div class="px-6 py-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium">Your Progress</h3>
                        <div class="mt-2">
                            <span class="text-3xl font-bold">{{ $userPoints->total_points }}</span>
                            <span class="text-lg opacity-75">points</span>
                        </div>
                        <div class="mt-1">
                            <span class="text-lg font-medium">Level {{ $levelProgress['current_level'] }}</span>
                            <span class="text-sm opacity-75">• Rank #{{ $userRank }}</span>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="w-24 h-24 mx-auto bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <div class="mt-2 text-sm opacity-75">{{ $levelProgress['points_needed'] - $levelProgress['points_in_level'] }} to next level</div>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="bg-white bg-opacity-20 rounded-full h-2">
                        <div class="bg-white rounded-full h-2" style="width: {{ $levelProgress['progress_percentage'] }}%"></div>
                    </div>
                    <div class="flex justify-between text-sm mt-1 opacity-75">
                        <span>{{ $levelProgress['points_in_level'] }}</span>
                        <span>{{ $levelProgress['points_needed'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Achievements -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Recent Achievements</h3>
                    </div>
                    <div class="p-6">
                        @if($recentAchievements->isNotEmpty())
                            <div class="space-y-4">
                                @foreach($recentAchievements as $userAchievement)
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-full bg-{{ $userAchievement->achievement->getRarityColor() }}-100 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-{{ $userAchievement->achievement->getRarityColor() }}-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $userAchievement->achievement->name }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $userAchievement->points_earned }} points • {{ $userAchievement->earned_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No achievements yet</h3>
                                <p class="mt-1 text-sm text-gray-500">Start completing tasks to earn your first achievement!</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Available Achievements -->
                <div class="bg-white shadow rounded-lg mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Available Achievements</h3>
                    </div>
                    <div class="p-6">
                        @if($availableAchievements->isNotEmpty())
                            <div class="space-y-3">
                                @foreach($availableAchievements->take(3) as $achievement)
                                    <div class="border border-gray-200 rounded-lg p-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $achievement->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $achievement->description }}</p>
                                            </div>
                                            <div class="text-right">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $achievement->getRarityColor() }}-100 text-{{ $achievement->getRarityColor() }}-800">
                                                    {{ $achievement->points }} pts
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">All available achievements earned!</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Leaderboard -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Leaderboard</h3>
                            <div class="flex space-x-2">
                                <select wire:model.live="selectedLeaderboard" class="border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="points">Points</option>
                                    <option value="deals_closed">Deals Closed</option>
                                    <option value="revenue_generated">Revenue</option>
                                </select>
                                <select wire:model.live="selectedPeriod" class="border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="monthly">This Month</option>
                                    <option value="quarterly">This Quarter</option>
                                    <option value="yearly">This Year</option>
                                    <option value="all_time">All Time</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($leaderboard->isNotEmpty())
                            <div class="space-y-3">
                                @foreach($leaderboard as $entry)
                                    <div class="flex items-center justify-between p-3 {{ $entry['user']->id === auth()->id() ? 'bg-indigo-50 border border-indigo-200 rounded-lg' : '' }}">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $entry['rank'] <= 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }} text-sm font-medium">
                                                    {{ $entry['rank'] }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $entry['user']->name }}
                                                    @if($entry['user']->id === auth()->id())
                                                        <span class="text-indigo-600">(You)</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">
                                                @if($entry['type'] === 'points')
                                                    {{ number_format($entry['score']) }} points
                                                @elseif($entry['type'] === 'deals')
                                                    {{ $entry['score'] }} deals
                                                @elseif($entry['type'] === 'revenue')
                                                    ${{ number_format($entry['score'], 2) }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No leaderboard data</h3>
                                <p class="mt-1 text-sm text-gray-500">Start completing activities to see rankings!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Achievement Categories -->
        @if($achievementsByCategory->isNotEmpty())
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Achievement Categories</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    @foreach($achievementsByCategory as $category => $achievements)
                        <div class="bg-white shadow rounded-lg p-6 text-center">
                            <div class="w-12 h-12 mx-auto bg-indigo-100 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($category === 'sales')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    @elseif($category === 'activity')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    @elseif($category === 'social')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    @elseif($category === 'learning')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                    @endif
                                </svg>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 capitalize">{{ $category }}</h4>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $userAchievementCounts[$category] ?? 0 }} / {{ $achievements->count() }} earned
                            </p>
                            <div class="mt-2">
                                <div class="bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 rounded-full h-2" style="width: {{ $achievements->count() > 0 ? (($userAchievementCounts[$category] ?? 0) / $achievements->count()) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>