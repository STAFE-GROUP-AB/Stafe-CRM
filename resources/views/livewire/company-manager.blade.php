<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Companies') }}
        </h2>
    </x-slot>

    @php
        $primaryColor = team_theme()->primary();
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

    @if($view === 'list')
        <!-- Companies List View -->
        <x-data-table>
            <!-- Filter Bar -->
            <x-filter-bar :columns="5">
                <!-- Search -->
                <div class="md:col-span-2">
                    <x-input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="{{ __('Search companies...') }}"
                        class="w-full"
                    />
                </div>

                <!-- Industry Filter -->
                <div>
                    <select wire:model.live="industryFilter" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500 sm:text-sm">
                        <option value="">{{ __('All Industries') }}</option>
                        @foreach($industries as $industry)
                            <option value="{{ $industry }}">{{ $industry }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Owner Filter -->
                <div>
                    <select wire:model.live="ownerFilter" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500 sm:text-sm">
                        <option value="">{{ __('All Owners') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
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
                            {{ __('Add Company') }}
                        </x-button>
                    @endif
                </div>
            </x-filter-bar>

            <!-- Table -->
            @if($companies->count() > 0)
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
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Company') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Industry') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Owner') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Contacts') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Revenue') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($companies as $company)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input
                                            type="checkbox"
                                            wire:model.live="selectedIds"
                                            value="{{ $company->id }}"
                                            class="rounded border-gray-300 dark:border-gray-600 text-{{ $primaryColor }}-600 shadow-sm focus:ring-{{ $primaryColor }}-500 dark:bg-gray-700"
                                        >
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($company->logo_url)
                                                <img src="{{ Storage::url($company->logo_url) }}" class="h-10 w-10 rounded-full mr-3" alt="{{ $company->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full mr-3 flex items-center justify-center bg-{{ $primaryColor }}-100 dark:bg-{{ $primaryColor }}-900">
                                                    <span class="text-{{ $primaryColor }}-600 dark:text-{{ $primaryColor }}-300 font-semibold">{{ substr($company->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $company->name }}</div>
                                                @if($company->email)
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $company->email }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($company->industry)
                                            <x-status-badge status="default">{{ $company->industry }}</x-status-badge>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($company->owner->name) }}&background=random" class="h-8 w-8 rounded-full mr-2" alt="{{ $company->owner->name }}">
                                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $company->owner->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            {{ $company->contacts->count() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        @if($company->annual_revenue)
                                            ${{ number_format($company->annual_revenue, 0) }}
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <x-table-actions :id="$company->id" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $companies->links() }}
                </div>
            @else
                <x-empty-state
                    icon="building"
                    :title="__('No companies found')"
                    :description="__('Get started by creating your first company.')"
                    action="create"
                    :actionLabel="__('Add Company')"
                />
            @endif
        </x-data-table>

    @elseif($view === 'show')
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
                            @if($selectedCompany->logo_url)
                                <img src="{{ Storage::url($selectedCompany->logo_url) }}" class="h-12 w-12 rounded-full mr-4" alt="{{ $selectedCompany->name }}">
                            @else
                                <div class="h-12 w-12 rounded-full mr-4 flex items-center justify-center bg-{{ $primaryColor }}-100 dark:bg-{{ $primaryColor }}-900">
                                    <span class="text-{{ $primaryColor }}-600 dark:text-{{ $primaryColor }}-300 font-semibold text-lg">{{ substr($selectedCompany->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $selectedCompany->name }}</h2>
                                @if($selectedCompany->industry)
                                    <x-status-badge status="default">{{ $selectedCompany->industry }}</x-status-badge>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <x-secondary-button wire:click="edit({{ $selectedCompany->id }})">
                                {{ __('Edit') }}
                            </x-secondary-button>
                            <x-danger-button wire:click="confirmDelete({{ $selectedCompany->id }})">
                                {{ __('Delete') }}
                            </x-danger-button>
                        </div>
                    </div>
                </div>

                <!-- Company Details -->
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Email') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedCompany->email ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Phone') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedCompany->phone ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Website') }}</dt>
                            <dd class="mt-1 text-sm">
                                @if($selectedCompany->website)
                                    <a href="{{ $selectedCompany->website }}" target="_blank" class="text-{{ $primaryColor }}-600 hover:text-{{ $primaryColor }}-900 dark:text-{{ $primaryColor }}-400 dark:hover:text-{{ $primaryColor }}-300">{{ $selectedCompany->website }}</a>
                                @else
                                    <span class="text-gray-900 dark:text-gray-100">—</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Owner') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 flex items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($selectedCompany->owner->name) }}&background=random" class="h-6 w-6 rounded-full mr-2" alt="{{ $selectedCompany->owner->name }}">
                                {{ $selectedCompany->owner->name }}
                            </dd>
                        </div>
                        @if($selectedCompany->annual_revenue)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Annual Revenue') }}</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">${{ number_format($selectedCompany->annual_revenue, 0) }}</dd>
                        </div>
                        @endif
                        @if($selectedCompany->number_of_employees)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Employees') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ number_format($selectedCompany->number_of_employees, 0) }}</dd>
                        </div>
                        @endif
                    </dl>
                    @if($selectedCompany->description)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Description') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedCompany->description }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Related Contacts -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Contacts') }} ({{ $selectedCompany->contacts->count() }})</h3>
                </div>
                <div class="px-6 py-4">
                    @if($selectedCompany->contacts->count() > 0)
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($selectedCompany->contacts as $contact)
                                <li class="py-3 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($contact->name) }}&background=random" class="h-8 w-8 rounded-full mr-3" alt="{{ $contact->name }}">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $contact->name }}</p>
                                            @if($contact->title)
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $contact->title }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right text-sm text-gray-500 dark:text-gray-400">
                                        @if($contact->email)
                                            <p>{{ $contact->email }}</p>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No contacts yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>

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
                        {{ $view === 'create' ? __('Create Company') : __('Edit Company') }}
                    </h2>
                </div>
            </div>

            <form wire:submit.prevent="{{ $view === 'create' ? 'store' : 'update' }}" class="px-6 py-4 space-y-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Basic Information') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-label for="name" value="{{ __('Company Name') }}" />
                            <x-input wire:model="name" id="name" type="text" class="mt-1 block w-full" required />
                            <x-input-error for="name" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="email" value="{{ __('Email') }}" />
                            <x-input wire:model="email" id="email" type="email" class="mt-1 block w-full" />
                            <x-input-error for="email" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="phone" value="{{ __('Phone') }}" />
                            <x-input wire:model="phone" id="phone" type="tel" class="mt-1 block w-full" />
                            <x-input-error for="phone" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="website" value="{{ __('Website') }}" />
                            <x-input wire:model="website" id="website" type="url" class="mt-1 block w-full" />
                            <x-input-error for="website" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="industry" value="{{ __('Industry') }}" />
                            <select wire:model="industry" id="industry" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500">
                                <option value="">{{ __('Select industry') }}</option>
                                @foreach($industries as $ind)
                                    <option value="{{ $ind }}">{{ $ind }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="industry" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Company Details -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Company Details') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-label for="owner_id" value="{{ __('Owner') }}" />
                            <select wire:model="owner_id" id="owner_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500" required>
                                <option value="">{{ __('Select owner') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="owner_id" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="company_size" value="{{ __('Company Size') }}" />
                            <select wire:model="company_size" id="company_size" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500">
                                <option value="">{{ __('Select size') }}</option>
                                @foreach($companySizes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-label for="annual_revenue" value="{{ __('Annual Revenue') }}" />
                            <x-input wire:model="annual_revenue" id="annual_revenue" type="number" class="mt-1 block w-full" />
                            <x-input-error for="annual_revenue" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="number_of_employees" value="{{ __('Number of Employees') }}" />
                            <x-input wire:model="number_of_employees" id="number_of_employees" type="number" class="mt-1 block w-full" />
                            <x-input-error for="number_of_employees" class="mt-2" />
                        </div>
                        <div class="md:col-span-2">
                            <x-label for="description" value="{{ __('Description') }}" />
                            <textarea wire:model="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500"></textarea>
                            <x-input-error for="description" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Address') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-label for="address" value="{{ __('Street Address') }}" />
                            <x-input wire:model="address" id="address" type="text" class="mt-1 block w-full" />
                            <x-input-error for="address" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="city" value="{{ __('City') }}" />
                            <x-input wire:model="city" id="city" type="text" class="mt-1 block w-full" />
                            <x-input-error for="city" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="state" value="{{ __('State/Province') }}" />
                            <x-input wire:model="state" id="state" type="text" class="mt-1 block w-full" />
                            <x-input-error for="state" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="postal_code" value="{{ __('Postal Code') }}" />
                            <x-input wire:model="postal_code" id="postal_code" type="text" class="mt-1 block w-full" />
                            <x-input-error for="postal_code" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="country" value="{{ __('Country') }}" />
                            <x-input wire:model="country" id="country" type="text" class="mt-1 block w-full" />
                            <x-input-error for="country" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Social Media') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-label for="linkedin_url" value="{{ __('LinkedIn') }}" />
                            <x-input wire:model="linkedin_url" id="linkedin_url" type="url" class="mt-1 block w-full" placeholder="https://linkedin.com/company/..." />
                            <x-input-error for="linkedin_url" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="twitter_url" value="{{ __('Twitter') }}" />
                            <x-input wire:model="twitter_url" id="twitter_url" type="url" class="mt-1 block w-full" placeholder="https://twitter.com/..." />
                            <x-input-error for="twitter_url" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="facebook_url" value="{{ __('Facebook') }}" />
                            <x-input wire:model="facebook_url" id="facebook_url" type="url" class="mt-1 block w-full" placeholder="https://facebook.com/..." />
                            <x-input-error for="facebook_url" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <x-secondary-button wire:click="backToList" type="button">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                    <x-button type="submit">
                        {{ $view === 'create' ? __('Create Company') : __('Update Company') }}
                    </x-button>
                </div>
            </form>
        </div>
    @endif

        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <x-confirmation-modal wire:model="showDeleteModal">
        <x-slot name="title">
            {{ __('Delete Company') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you want to delete') }} <strong>{{ $companyToDelete?->name }}</strong>? {{ __('This action cannot be undone.') }}
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
            {{ __('Delete Multiple Companies') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you want to delete') }} <strong>{{ count($selectedIds) }} {{ __('companies') }}</strong>? {{ __('This action cannot be undone.') }}
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
</div>
