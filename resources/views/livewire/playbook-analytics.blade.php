<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Playbook Analytics') }}
        </h2>
    </x-slot>

    @php
        $primaryColor = team_theme()->primary();
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Date Range Filter -->
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Performance Overview') }}</h3>
                <select wire:model.live="dateRange" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500">
                    <option value="7">{{ __('Last 7 days') }}</option>
                    <option value="30">{{ __('Last 30 days') }}</option>
                    <option value="90">{{ __('Last 90 days') }}</option>
                    <option value="365">{{ __('Last year') }}</option>
                </select>
            </div>

            <!-- Global Analytics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Executions') }}</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $globalAnalytics['total_executions'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Completion Rate') }}</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $globalAnalytics['completion_rate'] }}%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-{{ $primaryColor }}-100 dark:bg-{{ $primaryColor }}-900">
                            <svg class="h-6 w-6 text-{{ $primaryColor }}-600 dark:text-{{ $primaryColor }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Success Rate') }}</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $globalAnalytics['success_rate'] }}%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                            <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Avg. Completion Time') }}</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $globalAnalytics['avg_completion_time'] }} min</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Completed') }}</p>
                            <p class="text-xl font-semibold text-green-600 dark:text-green-400">{{ $globalAnalytics['completed_executions'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('In Progress') }}</p>
                            <p class="text-xl font-semibold text-blue-600 dark:text-blue-400">{{ $globalAnalytics['in_progress_executions'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Abandoned') }}</p>
                            <p class="text-xl font-semibold text-red-600 dark:text-red-400">{{ $globalAnalytics['abandoned_executions'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="h-6 w-6 {{ $globalAnalytics['avg_rating'] >= $i ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Avg. Rating') }}</p>
                            <p class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $globalAnalytics['avg_rating'] }}/5</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Successful Outcomes') }}</p>
                    <div class="flex items-center">
                        <div class="flex-1 h-3 bg-gray-200 dark:bg-gray-700 rounded-full">
                            <div class="h-full bg-green-500 rounded-full" style="width: {{ $globalAnalytics['success_rate'] }}%"></div>
                        </div>
                        <span class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $globalAnalytics['successful_executions'] }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Top Performing Playbooks -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Top Performing Playbooks') }}</h3>
                    </div>
                    <div class="p-6">
                        @if(count($topPerformers) > 0)
                            <div class="space-y-4">
                                @foreach($topPerformers as $playbook)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700" wire:click="selectPlaybook({{ $playbook['id'] }})">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $playbook['title'] }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $playbook['usage_count'] }} {{ __('uses') }}</p>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <span class="px-2 py-1 text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded">
                                                {{ number_format($playbook['success_rate'], 1) }}% {{ __('success') }}
                                            </span>
                                            @if($playbook['average_rating'] > 0)
                                                <div class="flex items-center">
                                                    <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                    <span class="ml-1 text-sm text-gray-600 dark:text-gray-400">{{ number_format($playbook['average_rating'], 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No playbook data available yet.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Most Skipped Steps -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Most Skipped Steps') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Steps that may need review or optimization') }}</p>
                    </div>
                    <div class="p-6">
                        @if(count($mostSkippedSteps) > 0)
                            <div class="space-y-3">
                                @foreach($mostSkippedSteps as $step)
                                    <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $step['step_title'] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $step['playbook_title'] }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded">
                                            {{ $step['skip_count'] }} {{ __('skips') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('No steps have been skipped yet.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Playbook Selector for Detailed Analytics -->
            @if($playbooks->count() > 0)
                <div class="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Playbook Details') }}</h3>
                            @if($selectedPlaybookId)
                                <button wire:click="clearSelection" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                    {{ __('Clear selection') }}
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="p-6">
                        @if($selectedPlaybookId && $selectedPlaybookAnalytics)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Executions') }}</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $selectedPlaybookAnalytics['total_executions'] }}</p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Avg. Completion Time') }}</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($selectedPlaybookAnalytics['average_completion_time_minutes'], 1) }} min</p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Deal Success Rate') }}</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($selectedPlaybookAnalytics['success_rate_with_completed_playbooks'], 1) }}%</p>
                                </div>
                            </div>

                            @if($selectedPlaybookAnalytics['step_completion_rates']->count() > 0)
                                <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Step Performance') }}</h4>
                                <div class="space-y-3">
                                    @foreach($selectedPlaybookAnalytics['step_completion_rates'] as $stepData)
                                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $stepData['title'] }}</span>
                                                <div class="flex items-center space-x-4 text-sm">
                                                    <span class="text-green-600 dark:text-green-400">{{ $stepData['completion_rate'] }}% {{ __('completed') }}</span>
                                                    @if($stepData['skip_rate'] > 0)
                                                        <span class="text-yellow-600 dark:text-yellow-400">{{ $stepData['skip_rate'] }}% {{ __('skipped') }}</span>
                                                    @endif
                                                    @if($stepData['average_duration_seconds'] > 0)
                                                        <span class="text-gray-500 dark:text-gray-400">~{{ round($stepData['average_duration_seconds'] / 60, 1) }} min</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="h-2 bg-gray-200 dark:bg-gray-600 rounded-full">
                                                <div class="h-full bg-green-500 rounded-full" style="width: {{ $stepData['completion_rate'] }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Select a playbook from the list above to see detailed analytics.') }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
