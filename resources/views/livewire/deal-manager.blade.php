<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Deals') }}
        </h2>
    </x-slot>

    @php
        $primaryColor = team_theme()->primary();
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

    @if($view === 'list')
        <!-- Deals List View -->
        <x-data-table>
            <!-- Filter Bar -->
            <x-filter-bar :columns="5">
                <!-- Search -->
                <div class="md:col-span-2">
                    <x-input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="{{ __('Search deals...') }}"
                        class="w-full"
                    />
                </div>

                <!-- Status Filter -->
                <div>
                    <select wire:model.live="statusFilter" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500 sm:text-sm">
                        <option value="">{{ __('All Statuses') }}</option>
                        @foreach($statuses as $key => $status)
                            <option value="{{ $key }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Stage Filter -->
                <div>
                    <select wire:model.live="stageFilter" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500 sm:text-sm">
                        <option value="">{{ __('All Stages') }}</option>
                        @foreach($pipelineStages as $stage)
                            <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Add Button / Bulk Actions -->
                <div class="flex items-center justify-end space-x-2">
                    @if(count($selectedIds) > 0)
                        <x-bulk-actions
                            :actions="['delete' => 'Delete Selected', 'export' => 'Export Selected']"
                            :selectedCount="count($selectedIds)"
                            :bulkAction="$bulkAction"
                        />
                    @else
                        <x-button wire:click="create">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            {{ __('New Deal') }}
                        </x-button>
                    @endif
                </div>
            </x-filter-bar>

            <!-- Table -->
            @if($deals->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left">
                                    <input
                                        type="checkbox"
                                        wire:model.live="selectAll"
                                        class="rounded border-gray-300 dark:border-gray-600 text-{{ $primaryColor }}-600 shadow-sm focus:ring-{{ $primaryColor }}-500 dark:bg-gray-700"
                                    >
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Deal') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Company/Contact') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Value') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Stage') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Close Date') }}</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($deals as $deal)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input
                                            type="checkbox"
                                            wire:model.live="selectedIds"
                                            value="{{ $deal->id }}"
                                            class="rounded border-gray-300 dark:border-gray-600 text-{{ $primaryColor }}-600 shadow-sm focus:ring-{{ $primaryColor }}-500 dark:bg-gray-700"
                                        >
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $deal->name }}</div>
                                            <x-status-badge :status="$deal->status" />
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">
                                            @if($deal->company)
                                                <div class="text-gray-900 dark:text-gray-100">{{ $deal->company->name }}</div>
                                            @endif
                                            @if($deal->contact)
                                                <div class="text-gray-500 dark:text-gray-400">{{ $deal->contact->name }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $deal->formatted_value }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $deal->probability }}% - {{ $deal->formatted_weighted_value }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($deal->pipelineStage)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                  style="background-color: {{ $deal->pipelineStage->color }}20; color: {{ $deal->pipelineStage->color }}">
                                                {{ $deal->pipelineStage->name }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $deal->expected_close_date?->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <x-table-actions :id="$deal->id" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $deals->links() }}
                </div>
            @else
                <x-empty-state
                    icon="currency"
                    :title="__('No deals found')"
                    :description="__('Get started by creating your first deal.')"
                    action="create"
                    :actionLabel="__('New Deal')"
                />
            @endif
        </x-data-table>

    @elseif($view === 'create' || $view === 'edit')
        <!-- Create/Edit Form -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <button wire:click="backToList" class="mr-4 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </button>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $view === 'create' ? __('Create Deal') : __('Edit Deal') }}
                    </h2>
                </div>
            </div>

            <form wire:submit.prevent="{{ $view === 'create' ? 'store' : 'update' }}" class="px-6 py-4 space-y-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Basic Information') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-label for="name" value="{{ __('Deal Name') }}" />
                            <x-input wire:model="name" id="name" type="text" class="mt-1 block w-full" required />
                            <x-input-error for="name" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="owner_id" value="{{ __('Owner') }}" />
                            <select wire:model="owner_id" id="owner_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500" required>
                                <option value="">{{ __('Select Owner') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="owner_id" class="mt-2" />
                        </div>
                        <div class="md:col-span-2">
                            <x-label for="description" value="{{ __('Description') }}" />
                            <textarea wire:model="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500"></textarea>
                            <x-input-error for="description" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Deal Value -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Deal Value') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-label for="value" value="{{ __('Value') }}" />
                            <x-input wire:model="value" id="value" type="number" step="0.01" class="mt-1 block w-full" required />
                            <x-input-error for="value" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="currency" value="{{ __('Currency') }}" />
                            <select wire:model="currency" id="currency" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500" required>
                                @foreach($currencies as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="currency" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="probability" value="{{ __('Probability (%)') }}" />
                            <x-input wire:model="probability" id="probability" type="number" min="0" max="100" class="mt-1 block w-full" required />
                            <x-input-error for="probability" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Pipeline & Status -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Pipeline & Status') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-label for="pipeline_stage_id" value="{{ __('Pipeline Stage') }}" />
                            <select wire:model.live="pipeline_stage_id" id="pipeline_stage_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500" required>
                                <option value="">{{ __('Select Stage') }}</option>
                                @foreach($pipelineStages as $stage)
                                    <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="pipeline_stage_id" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="status" value="{{ __('Status') }}" />
                            <select wire:model="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500" required>
                                @foreach($statuses as $key => $statusLabel)
                                    <option value="{{ $key }}">{{ $statusLabel }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="status" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="expected_close_date" value="{{ __('Expected Close Date') }}" />
                            <x-input wire:model="expected_close_date" id="expected_close_date" type="date" class="mt-1 block w-full" required />
                            <x-input-error for="expected_close_date" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="actual_close_date" value="{{ __('Actual Close Date') }}" />
                            <x-input wire:model="actual_close_date" id="actual_close_date" type="date" class="mt-1 block w-full" />
                            <x-input-error for="actual_close_date" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Relationships -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Relationships') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-label for="company_id" value="{{ __('Company') }}" />
                            <select wire:model="company_id" id="company_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500">
                                <option value="">{{ __('Select Company') }}</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="company_id" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="contact_id" value="{{ __('Contact') }}" />
                            <select wire:model="contact_id" id="contact_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500">
                                <option value="">{{ __('Select Contact') }}</option>
                                @foreach($contacts as $contact)
                                    <option value="{{ $contact->id }}">{{ $contact->first_name }} {{ $contact->last_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="contact_id" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <x-secondary-button wire:click="backToList" type="button">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                    <x-button type="submit">
                        {{ $view === 'create' ? __('Create Deal') : __('Update Deal') }}
                    </x-button>
                </div>
            </form>
        </div>

    @elseif($view === 'show' && $selectedDeal)
        <!-- Show View -->
        <div class="space-y-6">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <button wire:click="backToList" class="mr-4 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                            </button>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $selectedDeal->name }}</h2>
                                <div class="mt-1 flex items-center space-x-2">
                                    <x-status-badge :status="$selectedDeal->status" />
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $selectedDeal->formatted_value }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <x-secondary-button wire:click="edit({{ $selectedDeal->id }})">
                                {{ __('Edit') }}
                            </x-secondary-button>
                            <x-danger-button wire:click="confirmDelete({{ $selectedDeal->id }})">
                                {{ __('Delete') }}
                            </x-danger-button>
                        </div>
                    </div>
                </div>

                <!-- Deal Details -->
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Value') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedDeal->formatted_value }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Weighted Value') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedDeal->formatted_weighted_value }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Probability') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedDeal->probability }}%</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Pipeline Stage') }}</dt>
                            <dd class="mt-1">
                                @if($selectedDeal->pipelineStage)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                          style="background-color: {{ $selectedDeal->pipelineStage->color }}20; color: {{ $selectedDeal->pipelineStage->color }}">
                                        {{ $selectedDeal->pipelineStage->name }}
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Expected Close Date') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedDeal->expected_close_date?->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Owner') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedDeal->owner?->name }}</dd>
                        </div>
                    </dl>
                    @if($selectedDeal->description)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Description') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedDeal->description }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Relationships -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($selectedDeal->company)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Company') }}</h3>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $selectedDeal->company->name }}</p>
                    </div>
                @endif
                @if($selectedDeal->contact)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Contact') }}</h3>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $selectedDeal->contact->name }}</p>
                    </div>
                @endif
            </div>

            <!-- Sales Playbooks Section -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Sales Playbooks') }}</h3>
                        <x-button wire:click="openPlaybookModal" class="bg-{{ $primaryColor }}-600 hover:bg-{{ $primaryColor }}-700">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('Start Playbook') }}
                        </x-button>
                    </div>
                </div>

                <div class="p-6">
                    @if($selectedDeal->activePlaybookExecutions && $selectedDeal->activePlaybookExecutions->count() > 0)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('Active Playbooks') }}</h4>
                            <div class="space-y-3">
                                @foreach($selectedDeal->activePlaybookExecutions as $execution)
                                    <div class="flex items-center justify-between p-4 bg-{{ $primaryColor }}-50 dark:bg-{{ $primaryColor }}-900/20 border border-{{ $primaryColor }}-200 dark:border-{{ $primaryColor }}-800 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-full bg-{{ $primaryColor }}-100 dark:bg-{{ $primaryColor }}-900 flex items-center justify-center mr-4">
                                                <svg class="h-5 w-5 text-{{ $primaryColor }}-600 dark:text-{{ $primaryColor }}-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $execution->playbook->title }}</p>
                                                <div class="flex items-center mt-1">
                                                    <div class="w-32 h-2 bg-gray-200 dark:bg-gray-700 rounded-full mr-2">
                                                        <div class="h-full bg-{{ $primaryColor }}-500 rounded-full" style="width: {{ $execution->getProgressPercentage() }}%"></div>
                                                    </div>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $execution->getProgressPercentage() }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button wire:click="continuePlaybookExecution({{ $execution->id }})" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-{{ $primaryColor }}-700 dark:text-{{ $primaryColor }}-300 hover:text-{{ $primaryColor }}-900 dark:hover:text-{{ $primaryColor }}-100">
                                            {{ __('Continue') }}
                                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($selectedDeal->completedPlaybookExecutions && $selectedDeal->completedPlaybookExecutions->count() > 0)
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('Completed Playbooks') }}</h4>
                            <div class="space-y-2">
                                @foreach($selectedDeal->completedPlaybookExecutions->take(5) as $execution)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center mr-3">
                                                <svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $execution->playbook->title }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ __('Completed') }} {{ $execution->completed_at->diffForHumans() }}
                                                    @if($execution->outcome)
                                                        <span class="mx-1">-</span>
                                                        <span class="@if($execution->outcome === 'successful') text-green-600 @elseif($execution->outcome === 'partial') text-yellow-600 @else text-red-600 @endif">
                                                            {{ ucfirst($execution->outcome) }}
                                                        </span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        @if($execution->rating)
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="h-4 w-4 {{ $execution->rating >= $i ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if((!$selectedDeal->activePlaybookExecutions || $selectedDeal->activePlaybookExecutions->count() === 0) && (!$selectedDeal->completedPlaybookExecutions || $selectedDeal->completedPlaybookExecutions->count() === 0))
                        <div class="text-center py-6">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('No playbooks have been run for this deal yet.') }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ __('Start a playbook to guide your sales process.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <x-confirmation-modal wire:model="showDeleteModal">
        <x-slot name="title">
            {{ __('Delete Deal') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you want to delete') }} <strong>{{ $dealToDelete?->name }}</strong>? {{ __('This action cannot be undone.') }}
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showDeleteModal', false)">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ms-3" wire:click="delete">
                {{ __('Delete') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>

    <!-- Bulk Delete Confirmation Modal -->
    <x-confirmation-modal wire:model="showBulkDeleteModal">
        <x-slot name="title">
            {{ __('Delete Multiple Deals') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you want to delete') }} <strong>{{ count($selectedIds) }} {{ __('deals') }}</strong>? {{ __('This action cannot be undone.') }}
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showBulkDeleteModal', false)">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ms-3" wire:click="bulkDelete">
                {{ __('Delete All') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>

    <!-- Start Playbook Modal -->
    <x-dialog-modal wire:model="showPlaybookModal">
        <x-slot name="title">
            {{ __('Start a Sales Playbook') }}
        </x-slot>

        <x-slot name="content">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                {{ __('Select a playbook to guide your sales process for this deal.') }}
            </p>

            <div class="space-y-4">
                <div>
                    <x-label for="selectedPlaybookId" value="{{ __('Select Playbook') }}" />
                    <select wire:model="selectedPlaybookId" id="selectedPlaybookId" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500">
                        <option value="">{{ __('Choose a playbook...') }}</option>
                        @foreach($playbooks as $playbook)
                            <option value="{{ $playbook->id }}">
                                {{ $playbook->title }}
                                ({{ ucfirst(str_replace('_', ' ', $playbook->type)) }})
                                @if($playbook->estimated_duration) - {{ $playbook->estimated_duration }} @endif
                            </option>
                        @endforeach
                    </select>
                    <x-input-error for="selectedPlaybookId" class="mt-2" />
                </div>

                @if($playbooks->isEmpty())
                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <p class="text-sm text-yellow-700 dark:text-yellow-300">
                            {{ __('No published playbooks available.') }}
                            <a href="{{ route('sales-enablement.playbooks') }}" class="underline">{{ __('Create one') }}</a>
                        </p>
                    </div>
                @endif
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showPlaybookModal', false)">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button wire:click="startPlaybook" class="ms-3 bg-{{ $primaryColor }}-600 hover:bg-{{ $primaryColor }}-700" :disabled="!$playbooks->count()">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                </svg>
                {{ __('Start Playbook') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
