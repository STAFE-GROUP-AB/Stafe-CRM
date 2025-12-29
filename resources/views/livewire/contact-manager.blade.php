<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Contacts') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

    @if($view === 'list')
        <!-- Filters and Search -->
        <x-data-table class="mb-6">
            <x-filter-bar>
                <!-- Search -->
                <div class="md:col-span-2">
                    <x-label for="search" value="{{ __('Search') }}" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <x-input wire:model.live.debounce.300ms="search" type="text" id="search" class="pl-10 w-full" placeholder="{{ __('Search contacts...') }}" />
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <x-label for="statusFilter" value="{{ __('Status') }}" />
                    <select wire:model.live="statusFilter" id="statusFilter" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                        <option value="">{{ __('All Statuses') }}</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Company Filter -->
                <div>
                    <x-label for="companyFilter" value="{{ __('Company') }}" />
                    <select wire:model.live="companyFilter" id="companyFilter" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                        <option value="">{{ __('All Companies') }}</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Owner Filter -->
                <div>
                    <x-label for="ownerFilter" value="{{ __('Owner') }}" />
                    <select wire:model.live="ownerFilter" id="ownerFilter" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                        <option value="">{{ __('All Owners') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Bulk Actions -->
                @if(count($selectedIds) > 0)
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ count($selectedIds) }} {{ __('selected') }}</span>
                        <select wire:model="bulkAction" class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">{{ __('Choose action...') }}</option>
                            <option value="delete">{{ __('Delete') }}</option>
                            <option value="export">{{ __('Export') }}</option>
                            <option value="change_status">{{ __('Change Status') }}</option>
                        </select>
                        <x-button wire:click="executeBulkAction" class="py-1.5">
                            {{ __('Apply') }}
                        </x-button>
                    </div>
                @endif
            </x-filter-bar>

            <!-- Contacts Table -->
            @if($contacts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left">
                                    <x-checkbox wire:model.live="selectAll" />
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Name') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Contact Info') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Company') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Status') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Owner') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Value') }}
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">{{ __('Actions') }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($contacts as $contact)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-checkbox wire:model.live="selectedIds" value="{{ $contact->id }}" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($contact->avatar_url)
                                                    <img class="h-10 w-10 rounded-full" src="{{ Storage::url($contact->avatar_url) }}" alt="{{ $contact->name }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ substr($contact->first_name, 0, 1) }}{{ substr($contact->last_name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <button wire:click="show({{ $contact->id }})" class="text-sm font-medium text-emerald-600 hover:text-emerald-900 dark:text-emerald-400 dark:hover:text-emerald-300">
                                                    {{ $contact->name }}
                                                </button>
                                                @if($contact->title)
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $contact->title }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $contact->email }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $contact->phone ?? $contact->mobile ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($contact->company)
                                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $contact->company->name }}</div>
                                            @if($contact->department)
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $contact->department }}</div>
                                            @endif
                                        @else
                                            <span class="text-sm text-gray-500 dark:text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-status-badge :status="$contact->status">
                                            {{ $statuses[$contact->status] ?? $contact->status }}
                                        </x-status-badge>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $contact->owner->name ?? __('Unassigned') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        @if($contact->lifetime_value)
                                            ${{ number_format($contact->lifetime_value, 0) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-table-actions :id="$contact->id" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                    {{ $contacts->links() }}
                </div>
            @else
                <x-empty-state
                    icon="users"
                    title="{{ __('No contacts found') }}"
                    description="{{ __('Get started by creating a new contact.') }}"
                    action="create"
                    actionLabel="{{ __('New Contact') }}"
                />
            @endif
        </x-data-table>

    @elseif($view === 'create' || $view === 'edit')
        <!-- Create/Edit Form -->
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <form wire:submit.prevent="{{ $view === 'create' ? 'store' : 'update' }}">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                            {{ $view === 'create' ? __('Create New Contact') : __('Edit Contact') }}
                        </h3>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Personal Information -->
                            <div class="sm:col-span-2">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('Personal Information') }}</h4>
                            </div>

                            <!-- First Name -->
                            <div>
                                <x-label for="first_name" value="{{ __('First Name') }}" />
                                <span class="text-red-500">*</span>
                                <x-input wire:model="first_name" type="text" id="first_name" class="mt-1 block w-full" />
                                <x-input-error for="first_name" class="mt-2" />
                            </div>

                            <!-- Last Name -->
                            <div>
                                <x-label for="last_name" value="{{ __('Last Name') }}" />
                                <span class="text-red-500">*</span>
                                <x-input wire:model="last_name" type="text" id="last_name" class="mt-1 block w-full" />
                                <x-input-error for="last_name" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div>
                                <x-label for="email" value="{{ __('Email') }}" />
                                <span class="text-red-500">*</span>
                                <x-input wire:model="email" type="email" id="email" class="mt-1 block w-full" />
                                <x-input-error for="email" class="mt-2" />
                            </div>

                            <!-- Phone -->
                            <div>
                                <x-label for="phone" value="{{ __('Phone') }}" />
                                <x-input wire:model="phone" type="tel" id="phone" class="mt-1 block w-full" />
                                <x-input-error for="phone" class="mt-2" />
                            </div>

                            <!-- Mobile -->
                            <div>
                                <x-label for="mobile" value="{{ __('Mobile') }}" />
                                <x-input wire:model="mobile" type="tel" id="mobile" class="mt-1 block w-full" />
                                <x-input-error for="mobile" class="mt-2" />
                            </div>

                            <!-- Birthday -->
                            <div>
                                <x-label for="birthday" value="{{ __('Birthday') }}" />
                                <x-input wire:model="birthday" type="date" id="birthday" class="mt-1 block w-full" />
                                <x-input-error for="birthday" class="mt-2" />
                            </div>

                            <!-- Avatar -->
                            <div class="sm:col-span-2">
                                <x-label for="avatar" value="{{ __('Profile Photo') }}" />
                                <div class="mt-1 flex items-center">
                                    @if($avatar)
                                        <img src="{{ $avatar->temporaryUrl() }}" class="h-12 w-12 rounded-full">
                                    @elseif($avatar_url && $view === 'edit')
                                        <img src="{{ Storage::url($avatar_url) }}" class="h-12 w-12 rounded-full">
                                    @else
                                        <span class="h-12 w-12 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </span>
                                    @endif
                                    <input wire:model="avatar" type="file" id="avatar" accept="image/*" class="ml-5 bg-white dark:bg-gray-700 py-2 px-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                </div>
                                <x-input-error for="avatar" class="mt-2" />
                            </div>

                            <!-- Professional Information -->
                            <div class="sm:col-span-2 mt-6">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('Professional Information') }}</h4>
                            </div>

                            <!-- Title -->
                            <div>
                                <x-label for="title" value="{{ __('Job Title') }}" />
                                <x-input wire:model="title" type="text" id="title" class="mt-1 block w-full" />
                                <x-input-error for="title" class="mt-2" />
                            </div>

                            <!-- Department -->
                            <div>
                                <x-label for="department" value="{{ __('Department') }}" />
                                <x-input wire:model="department" type="text" id="department" class="mt-1 block w-full" />
                                <x-input-error for="department" class="mt-2" />
                            </div>

                            <!-- Company -->
                            <div>
                                <x-label for="company_id" value="{{ __('Company') }}" />
                                <select wire:model="company_id" id="company_id" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    <option value="">{{ __('No company') }}</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error for="company_id" class="mt-2" />
                            </div>

                            <!-- Owner -->
                            <div>
                                <x-label for="owner_id" value="{{ __('Owner') }}" />
                                <span class="text-red-500">*</span>
                                <select wire:model="owner_id" id="owner_id" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    <option value="">{{ __('Select owner') }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error for="owner_id" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div>
                                <x-label for="status" value="{{ __('Status') }}" />
                                <span class="text-red-500">*</span>
                                <select wire:model="status" id="status" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error for="status" class="mt-2" />
                            </div>

                            <!-- Source -->
                            <div>
                                <x-label for="source" value="{{ __('Lead Source') }}" />
                                <select wire:model="source" id="source" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    <option value="">{{ __('Select source') }}</option>
                                    @foreach($sources as $src)
                                        <option value="{{ $src }}">{{ $src }}</option>
                                    @endforeach
                                </select>
                                <x-input-error for="source" class="mt-2" />
                            </div>

                            <!-- Lifetime Value -->
                            <div>
                                <x-label for="lifetime_value" value="{{ __('Lifetime Value') }}" />
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                    </div>
                                    <x-input wire:model="lifetime_value" type="number" id="lifetime_value" class="pl-7 w-full" />
                                </div>
                                <x-input-error for="lifetime_value" class="mt-2" />
                            </div>

                            <!-- Location Information -->
                            <div class="sm:col-span-2 mt-6">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('Location Information') }}</h4>
                            </div>

                            <!-- Address -->
                            <div class="sm:col-span-2">
                                <x-label for="address" value="{{ __('Address') }}" />
                                <x-input wire:model="address" type="text" id="address" class="mt-1 block w-full" />
                            </div>

                            <!-- City -->
                            <div>
                                <x-label for="city" value="{{ __('City') }}" />
                                <x-input wire:model="city" type="text" id="city" class="mt-1 block w-full" />
                            </div>

                            <!-- State -->
                            <div>
                                <x-label for="state" value="{{ __('State/Province') }}" />
                                <x-input wire:model="state" type="text" id="state" class="mt-1 block w-full" />
                            </div>

                            <!-- Postal Code -->
                            <div>
                                <x-label for="postal_code" value="{{ __('Postal Code') }}" />
                                <x-input wire:model="postal_code" type="text" id="postal_code" class="mt-1 block w-full" />
                            </div>

                            <!-- Country -->
                            <div>
                                <x-label for="country" value="{{ __('Country') }}" />
                                <x-input wire:model="country" type="text" id="country" class="mt-1 block w-full" />
                            </div>

                            <!-- Timezone -->
                            <div>
                                <x-label for="timezone" value="{{ __('Timezone') }}" />
                                <span class="text-red-500">*</span>
                                <select wire:model="timezone" id="timezone" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    @foreach($timezones as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error for="timezone" class="mt-2" />
                            </div>

                            <!-- Social Links -->
                            <div class="sm:col-span-2 mt-6">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('Social Links') }}</h4>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                    <div>
                                        <x-label for="linkedin_url" value="{{ __('LinkedIn') }}" />
                                        <x-input wire:model="linkedin_url" type="url" id="linkedin_url" placeholder="https://linkedin.com/in/..." class="mt-1 block w-full" />
                                    </div>
                                    <div>
                                        <x-label for="twitter_url" value="{{ __('Twitter') }}" />
                                        <x-input wire:model="twitter_url" type="url" id="twitter_url" placeholder="https://twitter.com/..." class="mt-1 block w-full" />
                                    </div>
                                    <div>
                                        <x-label for="facebook_url" value="{{ __('Facebook') }}" />
                                        <x-input wire:model="facebook_url" type="url" id="facebook_url" placeholder="https://facebook.com/..." class="mt-1 block w-full" />
                                    </div>
                                </div>
                            </div>

                            <!-- Bio -->
                            <div class="sm:col-span-2">
                                <x-label for="bio" value="{{ __('Bio') }}" />
                                <textarea wire:model="bio" id="bio" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 text-right sm:px-6 rounded-b-lg">
                        <x-secondary-button wire:click="backToList" type="button">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                        <x-button type="submit" class="ml-3">
                            {{ $view === 'create' ? __('Create Contact') : __('Update Contact') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>

    @elseif($view === 'show' && $selectedContact)
        <!-- Contact Details View -->
        <div class="max-w-7xl mx-auto">
            <!-- Back Button -->
            <div class="mb-4">
                <button wire:click="backToList" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg class="mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    {{ __('Back to Contacts') }}
                </button>
            </div>

            <!-- Contact Header -->
            <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div class="flex items-center">
                        @if($selectedContact->avatar_url)
                            <img src="{{ Storage::url($selectedContact->avatar_url) }}" alt="{{ $selectedContact->name }}" class="h-16 w-16 rounded-full mr-4">
                        @else
                            <div class="h-16 w-16 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center mr-4">
                                <span class="text-xl font-medium text-gray-600 dark:text-gray-300">{{ substr($selectedContact->first_name, 0, 1) }}{{ substr($selectedContact->last_name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">{{ $selectedContact->name }}</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                                {{ $selectedContact->title ?? '' }}
                                @if($selectedContact->company)
                                    {{ $selectedContact->title ? 'at' : '' }} {{ $selectedContact->company->name }}
                                @endif
                            </p>
                            <div class="mt-2">
                                <x-status-badge :status="$selectedContact->status">
                                    {{ $statuses[$selectedContact->status] ?? $selectedContact->status }}
                                </x-status-badge>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <x-secondary-button wire:click="edit({{ $selectedContact->id }})">
                            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            {{ __('Edit') }}
                        </x-secondary-button>
                        <x-danger-button wire:click="confirmDelete({{ $selectedContact->id }})">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            {{ __('Delete') }}
                        </x-danger-button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Contact Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">{{ __('Contact Information') }}</h3>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700">
                            <dl>
                                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Email') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                                        <a href="mailto:{{ $selectedContact->email }}" class="text-emerald-600 hover:text-emerald-900 dark:text-emerald-400 dark:hover:text-emerald-300">{{ $selectedContact->email }}</a>
                                    </dd>
                                </div>
                                <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Phone') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">{{ $selectedContact->phone ?? '-' }}</dd>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Mobile') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">{{ $selectedContact->mobile ?? '-' }}</dd>
                                </div>
                                @if($selectedContact->birthday)
                                    <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Birthday') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">{{ $selectedContact->birthday->format('F d, Y') }}</dd>
                                    </div>
                                @endif
                                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Timezone') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">{{ $timezones[$selectedContact->timezone] ?? $selectedContact->timezone }}</dd>
                                </div>
                                <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Address') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                                        @if($selectedContact->address || $selectedContact->city || $selectedContact->state || $selectedContact->postal_code || $selectedContact->country)
                                            {{ $selectedContact->address }}<br>
                                            {{ $selectedContact->city }}{{ $selectedContact->state ? ', ' . $selectedContact->state : '' }} {{ $selectedContact->postal_code }}<br>
                                            {{ $selectedContact->country }}
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                @if($selectedContact->bio)
                                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Bio') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">{{ $selectedContact->bio }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Related Deals -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">{{ __('Deals') }} ({{ $selectedContact->deals->count() }})</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            @if($selectedContact->deals->count() > 0)
                                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($selectedContact->deals->take(5) as $deal)
                                        <li class="py-4">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $deal->name }}</p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">${{ number_format($deal->value, 0) }} - {{ $deal->stage->name ?? 'No stage' }}</p>
                                                </div>
                                                <x-status-badge :status="$deal->status" />
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No deals associated with this contact yet.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Professional Information -->
                    @if($selectedContact->company || $selectedContact->title || $selectedContact->department)
                        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">{{ __('Professional Information') }}</h3>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-5 sm:px-6">
                                <dl class="space-y-3">
                                    @if($selectedContact->company)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Company') }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedContact->company->name }}</dd>
                                        </div>
                                    @endif
                                    @if($selectedContact->title)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Title') }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedContact->title }}</dd>
                                        </div>
                                    @endif
                                    @if($selectedContact->department)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Department') }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedContact->department }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    @endif

                    <!-- Value & Owner Information -->
                    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">{{ __('Value & Ownership') }}</h3>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-5 sm:px-6">
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Lifetime Value') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        @if($selectedContact->lifetime_value)
                                            ${{ number_format($selectedContact->lifetime_value, 0) }}
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Lead Source') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedContact->source ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Owner') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedContact->owner->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Created') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedContact->created_at->format('M d, Y') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Health Score -->
                    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">{{ __('Health Score') }}</h3>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-5 sm:px-6">
                            @php
                                $healthScore = $selectedContact->healthScore();
                                $healthColor = $healthScore >= 70 ? 'emerald' : ($healthScore >= 40 ? 'yellow' : 'red');
                            @endphp
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <span class="text-2xl font-bold text-{{ $healthColor }}-600">{{ round($healthScore) }}%</span>
                                        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ __('Health Score') }}</span>
                                    </div>
                                    <div class="mt-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                        <div class="bg-{{ $healthColor }}-600 h-2 rounded-full" style="width: {{ $healthScore }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <x-confirmation-modal wire:model="showDeleteModal">
        <x-slot name="title">
            {{ __('Delete Contact') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you want to delete this contact? This action cannot be undone.') }}
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showDeleteModal', false)" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="delete" wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>

    <!-- Bulk Delete Confirmation Modal -->
    <x-confirmation-modal wire:model="showBulkDeleteModal">
        <x-slot name="title">
            {{ __('Delete Contacts') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you want to delete :count contacts? This action cannot be undone.', ['count' => count($selectedIds)]) }}
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showBulkDeleteModal', false)" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="bulkDelete" wire:loading.attr="disabled">
                {{ __('Delete All') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>
