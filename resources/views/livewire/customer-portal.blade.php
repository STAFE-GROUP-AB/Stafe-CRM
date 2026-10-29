<div>
    <!-- Header with Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Totalt</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $stats['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Oppna</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $stats['open'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Vantar svar</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $stats['awaiting_response'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">SLA-brott</dt>
                            <dd class="text-lg font-medium {{ $stats['sla_breached'] > 0 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">{{ $stats['sla_breached'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">SLA-efterlevnad</dt>
                            <dd class="text-lg font-medium {{ $stats['sla_compliance'] >= 90 ? 'text-green-600' : ($stats['sla_compliance'] >= 70 ? 'text-yellow-600' : 'text-red-600') }}">{{ $stats['sla_compliance'] }}%</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Response Time Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Genomsnittlig forsta svar</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['avg_first_response'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Genomsnittlig losning</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['avg_resolution'] }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Hog prioritet</p>
                        <p class="text-2xl font-semibold {{ $stats['high_priority'] > 0 ? 'text-orange-600' : 'text-gray-900 dark:text-white' }}">{{ $stats['high_priority'] }}</p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-full">
                        <svg class="h-6 w-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
                <div class="flex flex-col sm:flex-row flex-wrap gap-2 sm:gap-4">
                    <div>
                        <input wire:model.live="search" type="text" placeholder="Sok arenden..."
                               class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                    </div>
                    <div>
                        <select wire:model.live="statusFilter" class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                            <option value="">Alla statusar</option>
                            <option value="open">Oppen</option>
                            <option value="in_progress">Pagaende</option>
                            <option value="waiting_customer">Vantar pa kund</option>
                            <option value="resolved">Lost</option>
                            <option value="closed">Stangd</option>
                        </select>
                    </div>
                    <div>
                        <select wire:model.live="priorityFilter" class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                            <option value="">Alla prioriteter</option>
                            <option value="low">Lag</option>
                            <option value="medium">Medium</option>
                            <option value="high">Hog</option>
                            <option value="urgent">Bradskande</option>
                        </select>
                    </div>
                    <div>
                        <select wire:model.live="slaFilter" class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                            <option value="">Alla SLA</option>
                            <option value="breached">SLA-brott</option>
                            <option value="due_soon">SLA snart</option>
                            <option value="awaiting_response">Vantar forsta svar</option>
                        </select>
                    </div>
                </div>
                <button wire:click="createTicket"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nytt arende
                </button>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($tickets as $ticket)
                <li class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" wire:click="viewTicket({{ $ticket->id }})">
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center flex-1 min-w-0">
                                <!-- SLA Indicator -->
                                <div class="flex-shrink-0 mr-4">
                                    @php
                                        $slaStatus = $ticket->sla_status;
                                        $slaColor = $ticket->sla_color;
                                    @endphp
                                    <div class="relative">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center
                                            @if($slaColor === 'green') bg-green-100 dark:bg-green-900
                                            @elseif($slaColor === 'yellow') bg-yellow-100 dark:bg-yellow-900
                                            @elseif($slaColor === 'red') bg-red-100 dark:bg-red-900
                                            @else bg-gray-100 dark:bg-gray-700 @endif">
                                            @if($slaStatus['overall'] === 'breached')
                                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                </svg>
                                            @elseif($slaStatus['overall'] === 'warning')
                                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @elseif($slaStatus['overall'] === 'met' || $slaStatus['overall'] === 'on_track')
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Ticket Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center flex-wrap gap-2">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $ticket->subject }}</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            @if($ticket->priority === 'urgent') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @elseif($ticket->priority === 'high') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                            @elseif($ticket->priority === 'medium') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @endif">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            @if($ticket->status === 'open') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @elseif($ticket->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif($ticket->status === 'waiting_customer') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @elseif($ticket->status === 'resolved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                    </div>
                                    <div class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <span>{{ $ticket->contact->first_name }} {{ $ticket->contact->last_name }}</span>
                                        <span class="mx-2">-</span>
                                        <span>{{ $ticket->created_at->format('d M Y H:i') }}</span>
                                        @if($ticket->assignedUser)
                                            <span class="mx-2">-</span>
                                            <span>{{ $ticket->assignedUser->name }}</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 truncate">{{ Str::limit($ticket->description, 80) }}</p>
                                </div>
                            </div>

                            <!-- SLA Times -->
                            <div class="ml-4 flex-shrink-0 text-right">
                                @if($ticket->is_sla_paused)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        SLA pausad
                                    </span>
                                @elseif($ticket->time_to_sla_due)
                                    <span class="text-xs font-medium
                                        @if($slaColor === 'red') text-red-600
                                        @elseif($slaColor === 'yellow') text-yellow-600
                                        @else text-gray-500 dark:text-gray-400 @endif">
                                        {{ $ticket->time_to_sla_due }}
                                    </span>
                                @endif

                                @if($ticket->formatted_first_response_time)
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Forsta svar: {{ $ticket->formatted_first_response_time }}
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="ml-4 flex items-center space-x-2" wire:click.stop>
                                <button wire:click="editTicket({{ $ticket->id }})"
                                        class="text-amber-600 hover:text-amber-900 dark:text-amber-400 dark:hover:text-amber-300 text-sm font-medium">
                                    Redigera
                                </button>
                                <button wire:click="deleteTicket({{ $ticket->id }})"
                                        onclick="return confirm('Ar du saker pa att du vill ta bort detta arende?')"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                                    Ta bort
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Inga arenden hittades</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kom igang genom att skapa ett nytt arende.</p>
                </li>
            @endforelse
        </ul>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $tickets->links() }}
    </div>

    <!-- Create/Edit Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
                </div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            {{ $editingTicket ? 'Redigera arende' : 'Skapa nytt arende' }}
                        </h3>

                        <form wire:submit="saveTicket">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kund</label>
                                    <select wire:model="selectedContact" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                                        <option value="">Valj kund</option>
                                        @foreach($contacts as $contact)
                                            <option value="{{ $contact->id }}">{{ $contact->first_name }} {{ $contact->last_name }} ({{ $contact->email }})</option>
                                        @endforeach
                                    </select>
                                    @error('selectedContact') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amne</label>
                                    <input wire:model="subject" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                                    @error('subject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Beskrivning</label>
                                    <textarea wire:model="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm"></textarea>
                                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioritet</label>
                                        <select wire:model="priority" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                                            <option value="low">Lag</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">Hog</option>
                                            <option value="urgent">Bradskande</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</label>
                                        <select wire:model="category" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                                            <option value="general">Allman</option>
                                            <option value="technical">Teknisk</option>
                                            <option value="billing">Fakturering</option>
                                            <option value="feature_request">Funktionsonskemål</option>
                                            <option value="bug_report">Buggrapport</option>
                                        </select>
                                    </div>
                                </div>

                                @if($editingTicket)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                        <select wire:model="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                                            <option value="open">Oppen</option>
                                            <option value="in_progress">Pagaende</option>
                                            <option value="waiting_customer">Vantar pa kund</option>
                                            <option value="resolved">Lost</option>
                                            <option value="closed">Stangd</option>
                                        </select>
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tilldela till</label>
                                    <select wire:model="selectedAssignee" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                                        <option value="">Ej tilldelad</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end space-x-3">
                                <button type="button" wire:click="closeModal"
                                        class="inline-flex justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                    Avbryt
                                </button>
                                <button type="submit"
                                        class="inline-flex justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                    {{ $editingTicket ? 'Uppdatera' : 'Skapa' }} arende
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Ticket Detail Modal -->
    @if($showDetailModal && $viewingTicket)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" wire:click="closeDetailModal">
                    <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
                </div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800">
                        <!-- Header -->
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $viewingTicket->subject }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Skapad {{ $viewingTicket->created_at->format('d M Y H:i') }} av {{ $viewingTicket->contact->first_name }} {{ $viewingTicket->contact->last_name }}
                                    </p>
                                </div>
                                <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-col lg:flex-row">
                            <!-- Main Content -->
                            <div class="flex-1 p-6 overflow-y-auto max-h-[60vh]">
                                <!-- Original Description -->
                                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $viewingTicket->description }}</p>
                                </div>

                                <!-- Responses -->
                                <div class="space-y-4">
                                    @foreach($viewingTicket->responses as $response)
                                        <div class="p-4 rounded-lg {{ $response->is_internal_note ? 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800' : ($response->isFromAgent() ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-gray-50 dark:bg-gray-700') }}">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center">
                                                    <span class="font-medium text-sm text-gray-900 dark:text-white">{{ $response->author_name }}</span>
                                                    @if($response->is_internal_note)
                                                        <span class="ml-2 text-xs text-yellow-600 dark:text-yellow-400">(Intern anteckning)</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                    <span>{{ $response->created_at->format('d M Y H:i') }}</span>
                                                    @if($response->formatted_response_time)
                                                        <span class="ml-2 px-2 py-0.5 bg-gray-200 dark:bg-gray-600 rounded">{{ $response->formatted_response_time }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $response->message }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Add Response -->
                                @if(!$viewingTicket->isResolved())
                                    <div class="mt-6">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lagg till svar</label>
                                        <textarea wire:model="newResponseMessage" rows="3" class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm" placeholder="Skriv ditt svar har..."></textarea>
                                        @error('newResponseMessage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                                        <div class="mt-3 flex items-center justify-between">
                                            <label class="flex items-center">
                                                <input type="checkbox" wire:model="isInternalNote" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Intern anteckning</span>
                                            </label>
                                            <button wire:click="addResponse" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                                Skicka svar
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Sidebar -->
                            <div class="w-full lg:w-80 p-6 bg-gray-50 dark:bg-gray-700/50 border-t lg:border-t-0 lg:border-l border-gray-200 dark:border-gray-700">
                                <!-- SLA Status -->
                                <div class="mb-6">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">SLA Status</h4>
                                    @php
                                        $slaStatus = $viewingTicket->sla_status;
                                    @endphp

                                    <!-- First Response SLA -->
                                    <div class="mb-4 p-3 rounded-lg
                                        @if($slaStatus['first_response']['status'] === 'met' || $slaStatus['first_response']['status'] === 'on_track') bg-green-100 dark:bg-green-900/30
                                        @elseif($slaStatus['first_response']['status'] === 'warning') bg-yellow-100 dark:bg-yellow-900/30
                                        @elseif($slaStatus['first_response']['status'] === 'breached') bg-red-100 dark:bg-red-900/30
                                        @else bg-gray-100 dark:bg-gray-600 @endif">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Forsta svar</span>
                                            <span class="text-xs font-medium
                                                @if($slaStatus['first_response']['status'] === 'met' || $slaStatus['first_response']['status'] === 'on_track') text-green-700 dark:text-green-400
                                                @elseif($slaStatus['first_response']['status'] === 'warning') text-yellow-700 dark:text-yellow-400
                                                @elseif($slaStatus['first_response']['status'] === 'breached') text-red-700 dark:text-red-400
                                                @else text-gray-600 dark:text-gray-400 @endif">
                                                @if($slaStatus['first_response']['status'] === 'met') Uppfylld
                                                @elseif($slaStatus['first_response']['status'] === 'on_track') Pa sparet
                                                @elseif($slaStatus['first_response']['status'] === 'warning') Varning
                                                @elseif($slaStatus['first_response']['status'] === 'breached') Brott
                                                @else Pausad @endif
                                            </span>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                            Mal: {{ $slaStatus['first_response']['formatted_target'] ?? '-' }}
                                            @if(isset($slaStatus['first_response']['formatted_actual']))
                                                | Faktisk: {{ $slaStatus['first_response']['formatted_actual'] }}
                                            @elseif(isset($slaStatus['first_response']['formatted_remaining']))
                                                | Aterstår: {{ $slaStatus['first_response']['formatted_remaining'] }}
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Resolution SLA -->
                                    <div class="p-3 rounded-lg
                                        @if($slaStatus['resolution']['status'] === 'met' || $slaStatus['resolution']['status'] === 'on_track') bg-green-100 dark:bg-green-900/30
                                        @elseif($slaStatus['resolution']['status'] === 'warning') bg-yellow-100 dark:bg-yellow-900/30
                                        @elseif($slaStatus['resolution']['status'] === 'breached') bg-red-100 dark:bg-red-900/30
                                        @else bg-gray-100 dark:bg-gray-600 @endif">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Losning</span>
                                            <span class="text-xs font-medium
                                                @if($slaStatus['resolution']['status'] === 'met' || $slaStatus['resolution']['status'] === 'on_track') text-green-700 dark:text-green-400
                                                @elseif($slaStatus['resolution']['status'] === 'warning') text-yellow-700 dark:text-yellow-400
                                                @elseif($slaStatus['resolution']['status'] === 'breached') text-red-700 dark:text-red-400
                                                @else text-gray-600 dark:text-gray-400 @endif">
                                                @if($slaStatus['resolution']['status'] === 'met') Uppfylld
                                                @elseif($slaStatus['resolution']['status'] === 'on_track') Pa sparet
                                                @elseif($slaStatus['resolution']['status'] === 'warning') Varning
                                                @elseif($slaStatus['resolution']['status'] === 'breached') Brott
                                                @else Pausad @endif
                                            </span>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                            Mal: {{ $slaStatus['resolution']['formatted_target'] ?? '-' }}
                                            @if(isset($slaStatus['resolution']['formatted_actual']))
                                                | Faktisk: {{ $slaStatus['resolution']['formatted_actual'] }}
                                            @elseif(isset($slaStatus['resolution']['formatted_remaining']))
                                                | Aterstår: {{ $slaStatus['resolution']['formatted_remaining'] }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Ticket Details -->
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Status</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $viewingTicket->status)) }}</p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Prioritet</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($viewingTicket->priority) }}</p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Kategori</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $viewingTicket->category)) }}</p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Tilldelad</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $viewingTicket->assignedUser?->name ?? 'Ej tilldelad' }}</p>
                                    </div>
                                    @if($viewingTicket->formatted_first_response_time)
                                        <div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Forsta svarstid</span>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $viewingTicket->formatted_first_response_time }}</p>
                                        </div>
                                    @endif
                                    @if($viewingTicket->formatted_resolution_time)
                                        <div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Losningstid</span>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $viewingTicket->formatted_resolution_time }}</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Actions -->
                                @if(!$viewingTicket->isResolved())
                                    <div class="mt-6">
                                        <button wire:click="resolveTicket" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Markera som lost
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
