<div>
    <!-- Forest Theme Company Manager -->
    <style>
        /* Import forest theme variables */
        @import url('/css/forest-theme.css');
    </style>

    @if($view === 'list')
        <!-- List View with Forest Theme -->
        <div class="min-h-screen" style="background: var(--forest-bg-secondary);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold" style="color: var(--forest-primary);">Companies</h1>
                        <p class="mt-2 text-sm" style="color: var(--forest-text-secondary);">Manage your company accounts and relationships</p>
                    </div>
                    <a href="{{ route('companies.create') }}" class="forest-btn forest-btn-primary">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Company
                    </a>
                </div>

                <!-- Search and Filters -->
                <div class="forest-card mb-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label for="search" class="sr-only">Search</label>
                            <input wire:model.live="search" type="text" id="search" placeholder="Search companies..." class="forest-input">
                        </div>
                        <div>
                            <select wire:model.live="industryFilter" class="forest-select">
                                <option value="">All Industries</option>
                                @foreach($industries as $industry)
                                    <option value="{{ $industry }}">{{ $industry }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select wire:model.live="ownerFilter" class="forest-select">
                                <option value="">All Owners</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select wire:model.live="perPage" class="forest-select">
                                <option value="10">10 per page</option>
                                <option value="25">25 per page</option>
                                <option value="50">50 per page</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Companies Table -->
                <div class="forest-card overflow-hidden">
                    @if($companies->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y" style="border-color: var(--forest-border);">
                                <thead style="background: var(--forest-bg-tertiary);">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left">
                                            <input wire:model.live="selectAll" type="checkbox" class="h-4 w-4 rounded" style="border-color: var(--forest-border);">
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--forest-text-secondary);">
                                            Company
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--forest-text-secondary);">
                                            Contact Info
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--forest-text-secondary);">
                                            Industry
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--forest-text-secondary);">
                                            Owner
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--forest-text-secondary);">
                                            Created
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y" style="background: var(--forest-surface); border-color: var(--forest-border);">
                                    @foreach($companies as $company)
                                        <tr class="hover:bg-gray-50" style="hover:background: var(--forest-surface-hover);">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input wire:model.live="selectedIds" value="{{ $company->id }}" type="checkbox" class="h-4 w-4 rounded" style="border-color: var(--forest-border);">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        @if($company->logo_url)
                                                            <img class="h-10 w-10 rounded-full" src="{{ Storage::url($company->logo_url) }}" alt="{{ $company->name }}">
                                                        @else
                                                            <div class="h-10 w-10 rounded-full flex items-center justify-center" style="background: var(--forest-bg-tertiary);">
                                                                <span class="text-sm font-medium" style="color: var(--forest-text-secondary);">{{ substr($company->name, 0, 2) }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <a href="{{ route('companies.show', $company) }}" class="text-sm font-medium hover:underline" style="color: var(--forest-accent);">
                                                            {{ $company->name }}
                                                        </a>
                                                        @if($company->website)
                                                            <p class="text-sm" style="color: var(--forest-text-muted);">{{ $company->website }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm" style="color: var(--forest-text-primary);">{{ $company->email }}</div>
                                                <div class="text-sm" style="color: var(--forest-text-muted);">{{ $company->phone }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" style="background: var(--forest-bg-tertiary); color: var(--forest-text-secondary);">
                                                    {{ $company->industry ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: var(--forest-text-secondary);">
                                                {{ $company->owner?->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: var(--forest-text-muted);">
                                                {{ $company->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end space-x-2">
                                                    <a href="{{ route('companies.show', $company) }}" class="hover:underline" style="color: var(--forest-accent);">View</a>
                                                    <a href="{{ route('companies.edit', $company) }}" class="hover:underline" style="color: var(--forest-accent);">Edit</a>
                                                    <button wire:click="confirmDelete({{ $company->id }})" class="hover:underline" style="color: var(--forest-error);">Delete</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="px-6 py-4 border-t" style="border-color: var(--forest-border);">
                            {{ $companies->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 mb-4" style="color: var(--forest-text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <h3 class="text-lg font-medium mb-2" style="color: var(--forest-text-primary);">No companies found</h3>
                            <p class="text-sm mb-6" style="color: var(--forest-text-muted);">Get started by creating a new company.</p>
                            <a href="{{ route('companies.create') }}" class="forest-btn forest-btn-primary">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                New Company
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    @elseif($view === 'show' && $selectedCompany)
        <!-- Show View with Forest Theme -->
        <div class="min-h-screen" style="background: var(--forest-bg-secondary);">
            <!-- Page Header -->
            <div class="forest-page-header">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Breadcrumb -->
                    <nav class="forest-breadcrumb mb-4">
                        <a href="{{ route('companies.index') }}">Companies</a>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span>{{ $selectedCompany->name }}</span>
                    </nav>
                    
                    <div class="flex justify-between items-start">
                        <div class="flex items-center space-x-4">
                            @if($selectedCompany->logo_url)
                                <img src="{{ Storage::url($selectedCompany->logo_url) }}" alt="{{ $selectedCompany->name }}" class="h-16 w-16 rounded-lg object-cover">
                            @else
                                <div class="h-16 w-16 rounded-lg flex items-center justify-center" style="background: var(--forest-accent);">
                                    <span class="text-2xl font-bold text-white">{{ substr($selectedCompany->name, 0, 2) }}</span>
                                </div>
                            @endif
                            <div>
                                <h1 class="forest-page-title">{{ $selectedCompany->name }}</h1>
                                @if($selectedCompany->industry)
                                    <span class="forest-badge forest-badge-success">{{ $selectedCompany->industry }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('companies.edit', $selectedCompany) }}" class="forest-btn forest-btn-primary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </a>
                            <button wire:click="confirmDelete({{ $selectedCompany->id }})" class="forest-btn forest-btn-secondary" style="color: var(--forest-error); border-color: var(--forest-error);">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Company Details -->
                        <div class="forest-card">
                            <h2 class="forest-section-header">Company Details</h2>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium" style="color: var(--forest-text-muted);">Email</dt>
                                    <dd class="mt-1 text-sm" style="color: var(--forest-text-primary);">{{ $selectedCompany->email ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium" style="color: var(--forest-text-muted);">Phone</dt>
                                    <dd class="mt-1 text-sm" style="color: var(--forest-text-primary);">{{ $selectedCompany->phone ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium" style="color: var(--forest-text-muted);">Website</dt>
                                    <dd class="mt-1 text-sm">
                                        @if($selectedCompany->website)
                                            <a href="{{ $selectedCompany->website }}" target="_blank" class="hover:underline" style="color: var(--forest-accent);">
                                                {{ $selectedCompany->website }}
                                            </a>
                                        @else
                                            <span style="color: var(--forest-text-primary);">N/A</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium" style="color: var(--forest-text-muted);">Company Size</dt>
                                    <dd class="mt-1 text-sm" style="color: var(--forest-text-primary);">{{ $selectedCompany->company_size ?? 'N/A' }}</dd>
                                </div>
                                @if($selectedCompany->annual_revenue)
                                    <div>
                                        <dt class="text-sm font-medium" style="color: var(--forest-text-muted);">Annual Revenue</dt>
                                        <dd class="mt-1 text-sm" style="color: var(--forest-text-primary);">${{ number_format($selectedCompany->annual_revenue, 0) }}</dd>
                                    </div>
                                @endif
                                @if($selectedCompany->number_of_employees)
                                    <div>
                                        <dt class="text-sm font-medium" style="color: var(--forest-text-muted);">Employees</dt>
                                        <dd class="mt-1 text-sm" style="color: var(--forest-text-primary);">{{ number_format($selectedCompany->number_of_employees, 0) }}</dd>
                                    </div>
                                @endif
                                @if($selectedCompany->founded_year)
                                    <div>
                                        <dt class="text-sm font-medium" style="color: var(--forest-text-muted);">Founded</dt>
                                        <dd class="mt-1 text-sm" style="color: var(--forest-text-primary);">{{ $selectedCompany->founded_year }}</dd>
                                    </div>
                                @endif
                            </dl>
                            
                            @if($selectedCompany->description)
                                <div class="mt-6">
                                    <dt class="text-sm font-medium mb-2" style="color: var(--forest-text-muted);">Description</dt>
                                    <dd class="text-sm" style="color: var(--forest-text-primary);">{{ $selectedCompany->description }}</dd>
                                </div>
                            @endif
                        </div>

                        <!-- Address -->
                        @if($selectedCompany->address || $selectedCompany->city || $selectedCompany->state || $selectedCompany->postal_code || $selectedCompany->country)
                            <div class="forest-card">
                                <h2 class="forest-section-header">Address</h2>
                                <address class="text-sm not-italic" style="color: var(--forest-text-primary);">
                                    @if($selectedCompany->address)
                                        {{ $selectedCompany->address }}<br>
                                    @endif
                                    @if($selectedCompany->city || $selectedCompany->state || $selectedCompany->postal_code)
                                        {{ $selectedCompany->city }}@if($selectedCompany->city && $selectedCompany->state), @endif
                                        {{ $selectedCompany->state }} {{ $selectedCompany->postal_code }}<br>
                                    @endif
                                    @if($selectedCompany->country)
                                        {{ $selectedCompany->country }}
                                    @endif
                                </address>
                            </div>
                        @endif

                        <!-- Related Contacts -->
                        <div class="forest-card">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-semibold" style="color: var(--forest-text-primary);">Contacts</h2>
                                <a href="{{ route('contacts.create') }}?company_id={{ $selectedCompany->id }}" class="text-sm hover:underline" style="color: var(--forest-accent);">Add Contact</a>
                            </div>
                            @if($selectedCompany->contacts->count() > 0)
                                <div class="space-y-3">
                                    @foreach($selectedCompany->contacts->take(5) as $contact)
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                @if($contact->avatar_url)
                                                    <img src="{{ Storage::url($contact->avatar_url) }}" alt="{{ $contact->name }}" class="h-8 w-8 rounded-full">
                                                @else
                                                    <div class="h-8 w-8 rounded-full flex items-center justify-center" style="background: var(--forest-bg-tertiary);">
                                                        <span class="text-xs" style="color: var(--forest-text-muted);">{{ substr($contact->first_name, 0, 1) }}{{ substr($contact->last_name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <a href="{{ route('contacts.show', $contact) }}" class="text-sm font-medium hover:underline" style="color: var(--forest-accent);">
                                                        {{ $contact->name }}
                                                    </a>
                                                    <p class="text-xs" style="color: var(--forest-text-muted);">{{ $contact->title }}</p>
                                                </div>
                                            </div>
                                            <span class="text-xs" style="color: var(--forest-text-muted);">{{ $contact->email }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm" style="color: var(--forest-text-muted);">No contacts yet</p>
                            @endif
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Quick Stats -->
                        <div class="forest-card">
                            <h3 class="text-lg font-semibold mb-4" style="color: var(--forest-text-primary);">Quick Stats</h3>
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-sm" style="color: var(--forest-text-muted);">Open Deals</dt>
                                    <dd class="text-sm font-medium" style="color: var(--forest-text-primary);">{{ $selectedCompany->deals->where('status', 'open')->count() }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm" style="color: var(--forest-text-muted);">Total Deal Value</dt>
                                    <dd class="text-sm font-medium" style="color: var(--forest-text-primary);">${{ number_format($selectedCompany->deals->where('status', 'open')->sum('value'), 0) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm" style="color: var(--forest-text-muted);">Contacts</dt>
                                    <dd class="text-sm font-medium" style="color: var(--forest-text-primary);">{{ $selectedCompany->contacts->count() }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm" style="color: var(--forest-text-muted);">Tasks</dt>
                                    <dd class="text-sm font-medium" style="color: var(--forest-text-primary);">{{ $selectedCompany->tasks->count() }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Social Links -->
                        @if($selectedCompany->linkedin_url || $selectedCompany->twitter_url || $selectedCompany->facebook_url)
                            <div class="forest-card">
                                <h3 class="text-lg font-semibold mb-4" style="color: var(--forest-text-primary);">Social Media</h3>
                                <div class="space-y-2">
                                    @if($selectedCompany->linkedin_url)
                                        <a href="{{ $selectedCompany->linkedin_url }}" target="_blank" class="flex items-center text-sm hover:underline" style="color: var(--forest-accent);">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                            </svg>
                                            LinkedIn
                                        </a>
                                    @endif
                                    @if($selectedCompany->twitter_url)
                                        <a href="{{ $selectedCompany->twitter_url }}" target="_blank" class="flex items-center text-sm hover:underline" style="color: var(--forest-accent);">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>
                                            </svg>
                                            Twitter
                                        </a>
                                    @endif
                                    @if($selectedCompany->facebook_url)
                                        <a href="{{ $selectedCompany->facebook_url }}" target="_blank" class="flex items-center text-sm hover:underline" style="color: var(--forest-accent);">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                            Facebook
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Meta Information -->
                        <div class="forest-card">
                            <h3 class="text-lg font-semibold mb-4" style="color: var(--forest-text-primary);">Information</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm" style="color: var(--forest-text-muted);">Owner</dt>
                                    <dd class="text-sm mt-1" style="color: var(--forest-text-primary);">{{ $selectedCompany->owner?->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm" style="color: var(--forest-text-muted);">Created</dt>
                                    <dd class="text-sm mt-1" style="color: var(--forest-text-primary);">{{ $selectedCompany->created_at->format('M d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm" style="color: var(--forest-text-muted);">Last Updated</dt>
                                    <dd class="text-sm mt-1" style="color: var(--forest-text-primary);">{{ $selectedCompany->updated_at->format('M d, Y') }}</dd>
                                </div>
                                @if($selectedCompany->tax_id)
                                    <div>
                                        <dt class="text-sm" style="color: var(--forest-text-muted);">Tax ID</dt>
                                        <dd class="text-sm mt-1" style="color: var(--forest-text-primary);">{{ $selectedCompany->tax_id }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @elseif($view === 'create' || $view === 'edit')
        <!-- Create/Edit Form with Forest Theme -->
        <div class="min-h-screen" style="background: var(--forest-bg-secondary);">
            <!-- Page Header -->
            <div class="forest-page-header">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Breadcrumb -->
                    <nav class="forest-breadcrumb mb-4">
                        <a href="{{ route('companies.index') }}">Companies</a>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span>{{ $view === 'create' ? 'New Company' : 'Edit Company' }}</span>
                    </nav>
                    <h1 class="forest-page-title">{{ $view === 'create' ? 'Create New Company' : 'Edit Company' }}</h1>
                </div>
            </div>

            <!-- Form Container -->
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
                <form wire:submit.prevent="{{ $view === 'create' ? 'store' : 'update' }}">
                    <!-- Basic Information -->
                    <div class="forest-card mb-6">
                        <h2 class="forest-section-header">Basic Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Company Name -->
                            <div class="md:col-span-2 forest-form-group">
                                <label for="name" class="forest-label">
                                    Company Name <span style="color: var(--forest-error);">*</span>
                                </label>
                                <input 
                                    wire:model="name" 
                                    type="text" 
                                    id="name" 
                                    class="forest-input @error('name') error @enderror"
                                    placeholder="Enter company name"
                                >
                                @error('name') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>

                            <!-- Email -->
                            <div class="forest-form-group">
                                <label for="email" class="forest-label">Email Address</label>
                                <input 
                                    wire:model="email" 
                                    type="email" 
                                    id="email" 
                                    class="forest-input @error('email') error @enderror"
                                    placeholder="company@example.com"
                                >
                                @error('email') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>

                            <!-- Phone -->
                            <div class="forest-form-group">
                                <label for="phone" class="forest-label">Phone Number</label>
                                <input 
                                    wire:model="phone" 
                                    type="tel" 
                                    id="phone" 
                                    class="forest-input @error('phone') error @enderror"
                                    placeholder="+1 (555) 123-4567"
                                >
                                @error('phone') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>

                            <!-- Website -->
                            <div class="forest-form-group">
                                <label for="website" class="forest-label">Website</label>
                                <input 
                                    wire:model="website" 
                                    type="url" 
                                    id="website" 
                                    class="forest-input @error('website') error @enderror"
                                    placeholder="https://www.example.com"
                                >
                                @error('website') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>

                            <!-- Industry -->
                            <div class="forest-form-group">
                                <label for="industry" class="forest-label">Industry</label>
                                <select wire:model="industry" id="industry" class="forest-select @error('industry') error @enderror">
                                    <option value="">Select industry</option>
                                    @foreach($industries as $ind)
                                        <option value="{{ $ind }}">{{ $ind }}</option>
                                    @endforeach
                                </select>
                                @error('industry') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Company Details -->
                    <div class="forest-card mb-6">
                        <h2 class="forest-section-header">Company Details</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Company Size -->
                            <div class="forest-form-group">
                                <label for="company_size" class="forest-label">Company Size</label>
                                <select wire:model="company_size" id="company_size" class="forest-select">
                                    <option value="">Select size</option>
                                    @foreach($companySizes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Owner -->
                            <div class="forest-form-group">
                                <label for="owner_id" class="forest-label">
                                    Owner <span style="color: var(--forest-error);">*</span>
                                </label>
                                <select wire:model="owner_id" id="owner_id" class="forest-select @error('owner_id') error @enderror">
                                    <option value="">Select owner</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('owner_id') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>

                            <!-- Annual Revenue -->
                            <div class="forest-form-group">
                                <label for="annual_revenue" class="forest-label">Annual Revenue</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2" style="color: var(--forest-text-muted);">$</span>
                                    <input 
                                        wire:model="annual_revenue" 
                                        type="number" 
                                        id="annual_revenue" 
                                        class="forest-input pl-8 @error('annual_revenue') error @enderror"
                                        placeholder="0"
                                    >
                                </div>
                                @error('annual_revenue') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>

                            <!-- Number of Employees -->
                            <div class="forest-form-group">
                                <label for="number_of_employees" class="forest-label">Number of Employees</label>
                                <input 
                                    wire:model="number_of_employees" 
                                    type="number" 
                                    id="number_of_employees" 
                                    class="forest-input @error('number_of_employees') error @enderror"
                                    placeholder="0"
                                >
                                @error('number_of_employees') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>

                            <!-- Founded Year -->
                            <div class="forest-form-group">
                                <label for="founded_year" class="forest-label">Founded Year</label>
                                <input 
                                    wire:model="founded_year" 
                                    type="number" 
                                    id="founded_year" 
                                    class="forest-input @error('founded_year') error @enderror"
                                    placeholder="2000"
                                    min="1800" 
                                    max="2100"
                                >
                                @error('founded_year') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>

                            <!-- Tax ID -->
                            <div class="forest-form-group">
                                <label for="tax_id" class="forest-label">Tax ID</label>
                                <input 
                                    wire:model="tax_id" 
                                    type="text" 
                                    id="tax_id" 
                                    class="forest-input @error('tax_id') error @enderror"
                                    placeholder="XX-XXXXXXX"
                                >
                                @error('tax_id') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2 forest-form-group">
                                <label for="description" class="forest-label">Description</label>
                                <textarea 
                                    wire:model="description" 
                                    id="description" 
                                    rows="4" 
                                    class="forest-textarea @error('description') error @enderror"
                                    placeholder="Provide a brief description of the company..."
                                ></textarea>
                                @error('description') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="forest-card mb-6">
                        <h2 class="forest-section-header">Address Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Address -->
                            <div class="md:col-span-2 forest-form-group">
                                <label for="address" class="forest-label">Street Address</label>
                                <input 
                                    wire:model="address" 
                                    type="text" 
                                    id="address" 
                                    class="forest-input @error('address') error @enderror"
                                    placeholder="123 Main Street"
                                >
                                @error('address') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>

                            <!-- City -->
                            <div class="forest-form-group">
                                <label for="city" class="forest-label">City</label>
                                <input 
                                    wire:model="city" 
                                    type="text" 
                                    id="city" 
                                    class="forest-input @error('city') error @enderror"
                                    placeholder="New York"
                                >
                                @error('city') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>

                            <!-- State -->
                            <div class="forest-form-group">
                                <label for="state" class="forest-label">State/Province</label>
                                <input 
                                    wire:model="state" 
                                    type="text" 
                                    id="state" 
                                    class="forest-input @error('state') error @enderror"
                                    placeholder="NY"
                                >
                                @error('state') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>

                            <!-- Postal Code -->
                            <div class="forest-form-group">
                                <label for="postal_code" class="forest-label">Postal Code</label>
                                <input 
                                    wire:model="postal_code" 
                                    type="text" 
                                    id="postal_code" 
                                    class="forest-input @error('postal_code') error @enderror"
                                    placeholder="10001"
                                >
                                @error('postal_code') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>

                            <!-- Country -->
                            <div class="forest-form-group">
                                <label for="country" class="forest-label">Country</label>
                                <input 
                                    wire:model="country" 
                                    type="text" 
                                    id="country" 
                                    class="forest-input @error('country') error @enderror"
                                    placeholder="United States"
                                >
                                @error('country') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Social Media & Logo -->
                    <div class="forest-card mb-6">
                        <h2 class="forest-section-header">Social Media & Branding</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- LinkedIn -->
                            <div class="forest-form-group">
                                <label for="linkedin_url" class="forest-label">LinkedIn URL</label>
                                <input 
                                    wire:model="linkedin_url" 
                                    type="url" 
                                    id="linkedin_url" 
                                    class="forest-input @error('linkedin_url') error @enderror"
                                    placeholder="https://linkedin.com/company/..."
                                >
                                @error('linkedin_url') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>

                            <!-- Twitter -->
                            <div class="forest-form-group">
                                <label for="twitter_url" class="forest-label">Twitter URL</label>
                                <input 
                                    wire:model="twitter_url" 
                                    type="url" 
                                    id="twitter_url" 
                                    class="forest-input @error('twitter_url') error @enderror"
                                    placeholder="https://twitter.com/..."
                                >
                                @error('twitter_url') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>

                            <!-- Facebook -->
                            <div class="forest-form-group">
                                <label for="facebook_url" class="forest-label">Facebook URL</label>
                                <input 
                                    wire:model="facebook_url" 
                                    type="url" 
                                    id="facebook_url" 
                                    class="forest-input @error('facebook_url') error @enderror"
                                    placeholder="https://facebook.com/..."
                                >
                                @error('facebook_url') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>

                            <!-- Logo Upload -->
                            <div class="forest-form-group">
                                <label class="forest-label">Company Logo</label>
                                <div class="forest-file-upload">
                                    <input type="file" wire:model="logo" id="logo" class="hidden" accept="image/*">
                                    <label for="logo" class="cursor-pointer">
                                        <svg class="mx-auto h-12 w-12 mb-3" style="color: var(--forest-text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <span class="forest-hint">Click to upload or drag and drop</span>
                                        <span class="forest-hint">PNG, JPG, GIF up to 2MB</span>
                                    </label>
                                </div>
                                @if ($logo)
                                    <div class="mt-2">
                                        <img src="{{ $logo->temporaryUrl() }}" class="h-20 w-20 object-cover rounded-lg">
                                    </div>
                                @elseif($logo_url && $view === 'edit')
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($logo_url) }}" class="h-20 w-20 object-cover rounded-lg">
                                    </div>
                                @endif
                                @error('logo') <p class="forest-error">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('companies.index') }}" class="forest-btn forest-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="forest-btn forest-btn-primary">
                            <svg wire:loading class="forest-loading mr-2" viewBox="0 0 20 20"></svg>
                            {{ $view === 'create' ? 'Create Company' : 'Update Company' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if($showDeleteModal && $companyToDelete)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showDeleteModal', false)"></div>
                
                <span class="hidden sm:inline-block sm:h-screen sm:align-middle">&#8203;</span>
                
                <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10" style="background: rgba(146, 43, 33, 0.1);">
                                <svg class="h-6 w-6" style="color: var(--forest-error);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg font-medium leading-6" style="color: var(--forest-text-primary);">Delete Company</h3>
                                <div class="mt-2">
                                    <p class="text-sm" style="color: var(--forest-text-muted);">
                                        Are you sure you want to delete <strong>{{ $companyToDelete->name }}</strong>? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6" style="background: var(--forest-bg-secondary);">
                        <button wire:click="delete" type="button" class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto" style="background: var(--forest-error);">
                            Delete
                        </button>
                        <button wire:click="$set('showDeleteModal', false)" type="button" class="mt-3 inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm ring-1 ring-inset sm:mt-0 sm:w-auto" style="background: var(--forest-surface); color: var(--forest-text-primary); border-color: var(--forest-border);">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>