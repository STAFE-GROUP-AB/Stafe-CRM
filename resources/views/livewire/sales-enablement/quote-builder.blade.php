<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 rounded-md bg-red-50 dark:bg-red-900/50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
                    Offerthantering
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Skapa och hantera offerter med godkannandearb	etsfloden
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <button wire:click="createQuote" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-800">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Ny offert
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <div>
                        <input wire:model.live="search" type="text" placeholder="Sok offerter..." class="block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                    </div>
                    <div>
                        <select wire:model.live="statusFilter" class="block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                            <option value="">Alla statusar</option>
                            <option value="draft">Utkast</option>
                            <option value="sent">Skickad</option>
                            <option value="viewed">Visad</option>
                            <option value="accepted">Accepterad</option>
                            <option value="rejected">Avvisad</option>
                            <option value="expired">Utgangen</option>
                        </select>
                    </div>
                    <div>
                        <select wire:model.live="dealFilter" class="block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                            <option value="">Alla affarer</option>
                            @foreach($deals as $deal)
                                <option value="{{ $deal->id }}">{{ $deal->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quotes Grid -->
        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($quotes as $quote)
                    <li class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <div class="px-4 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center flex-1 min-w-0">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-amber-100 dark:bg-amber-900 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1 min-w-0">
                                        <div class="flex items-center">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $quote->title }}
                                            </p>
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($quote->status === 'accepted') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                                                @elseif($quote->status === 'rejected') bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200
                                                @elseif($quote->status === 'sent') bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200
                                                @elseif($quote->status === 'expired') bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                                                @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                                @endif">
                                                {{ ucfirst($quote->status) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            <p>{{ $quote->quote_number }}</p>
                                            <span class="mx-2">|</span>
                                            <p>{{ $quote->company->name ?? 'N/A' }}</p>
                                            <span class="mx-2">|</span>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ number_format($quote->total_amount ?? 0, 2, ',', ' ') }} SEK</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-1 ml-4">
                                    <!-- Preview Button -->
                                    <a href="{{ route('sales-enablement.quotes.preview', $quote) }}" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500" title="Forhandsgranska">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <!-- Download PDF Button -->
                                    <a href="{{ route('sales-enablement.quotes.pdf', $quote) }}" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500" title="Ladda ner PDF">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </a>
                                    <!-- Email Button -->
                                    <button wire:click="openEmailModal({{ $quote->id }})" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500" title="Skicka via e-post">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                    <!-- Edit Button -->
                                    <button wire:click="editQuote({{ $quote->id }})" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500" title="Redigera">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <!-- Line Items Button -->
                                    <button wire:click="manageLineItems({{ $quote->id }})" class="inline-flex items-center px-2.5 py-1.5 border border-amber-300 dark:border-amber-600 shadow-sm text-xs font-medium rounded text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500" title="Hantera rader">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                    </button>
                                    <!-- Duplicate Button -->
                                    <button wire:click="duplicateQuote({{ $quote->id }})" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500" title="Duplicera">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                    <!-- Delete Button -->
                                    <button wire:click="deleteQuote({{ $quote->id }})" wire:confirm="Ar du saker pa att du vill ta bort denna offert?" class="inline-flex items-center px-2.5 py-1.5 border border-red-300 dark:border-red-600 shadow-sm text-xs font-medium rounded text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" title="Ta bort">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-4 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Inga offerter hittades</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Borja med att skapa din forsta offert.</p>
                        <div class="mt-6">
                            <button wire:click="createQuote" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-800">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Ny offert
                            </button>
                        </div>
                    </li>
                @endforelse
            </ul>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $quotes->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="saveQuote">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="space-y-4">
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                                    <input wire:model="title" type="text" id="title" class="mt-1 block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('title') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="deal_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deal</label>
                                    <select wire:model="deal_id" wire:change="updatedDealId" id="deal_id" class="mt-1 block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Select Deal</option>
                                        @foreach($deals as $deal)
                                            <option value="{{ $deal->id }}">{{ $deal->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('deal_id') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="company_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company</label>
                                    <select wire:model="company_id" id="company_id" class="mt-1 block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Select Company</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('company_id') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                </div>

                                @if($this->contacts->isNotEmpty())
                                    <div>
                                        <label for="contact_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contact</label>
                                        <select wire:model="contact_id" id="contact_id" class="mt-1 block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="">Select Contact</option>
                                            @foreach($this->contacts as $contact)
                                                <option value="{{ $contact->id }}">{{ $contact->first_name }} {{ $contact->last_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('contact_id') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                @endif

                                <div>
                                    <label for="valid_until" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valid Until</label>
                                    <input wire:model="valid_until" type="date" id="valid_until" class="mt-1 block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('valid_until') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                    <textarea wire:model="description" id="description" rows="3" class="mt-1 block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                    @error('description') <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ $editingQuote ? 'Update' : 'Create' }} Quote
                            </button>
                            <button wire:click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Line Items Management Modal -->
    @if($showLineItemsModal && $quoteForLineItems)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" wire:click="closeLineItemsModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-7xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-2xl font-semibold text-gray-900 dark:text-white" id="modal-title">
                                    Manage Line Items
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Quote: {{ $quoteForLineItems->quote_number }} - {{ $quoteForLineItems->title }}
                                </p>
                            </div>
                            <button
                                wire:click="closeLineItemsModal"
                                type="button"
                                class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500"
                            >
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="mt-4">
                            <livewire:sales-enablement.quote-line-item-manager :quote="$quoteForLineItems" :key="'line-items-'.$quoteForLineItems->id" />
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            wire:click="closeLineItemsModal"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-800 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Klar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Email Quote Modal -->
    @if($showEmailModal && $quoteForEmail)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" wire:click="closeEmailModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="sendEmailQuote">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="modal-title">
                                        Skicka offert via e-post
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $quoteForEmail->quote_number }} - {{ $quoteForEmail->title }}
                                    </p>
                                </div>
                                <button
                                    wire:click="closeEmailModal"
                                    type="button"
                                    class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500"
                                >
                                    <span class="sr-only">Stang</span>
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <!-- Quote Summary -->
                                <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-4 border border-amber-200 dark:border-amber-800">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-amber-800 dark:text-amber-200">{{ $quoteForEmail->company->name ?? 'N/A' }}</p>
                                            <p class="text-xs text-amber-600 dark:text-amber-400">{{ $quoteForEmail->contact ? $quoteForEmail->contact->first_name . ' ' . $quoteForEmail->contact->last_name : 'Ingen kontakt' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-amber-900 dark:text-amber-100">{{ number_format($quoteForEmail->total_amount ?? 0, 2, ',', ' ') }} SEK</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recipient Email -->
                                <div>
                                    <label for="emailRecipient" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Mottagarens e-post
                                    </label>
                                    <input
                                        wire:model="emailRecipient"
                                        type="email"
                                        id="emailRecipient"
                                        class="mt-1 block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm"
                                        placeholder="kund@example.com"
                                        required
                                    >
                                    @error('emailRecipient')
                                        <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Personal Message -->
                                <div>
                                    <label for="emailMessage" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Personligt meddelande (valfritt)
                                    </label>
                                    <textarea
                                        wire:model="emailMessage"
                                        id="emailMessage"
                                        rows="4"
                                        class="mt-1 block w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm"
                                        placeholder="Lagg till ett personligt meddelande till mottagaren..."
                                    ></textarea>
                                    @error('emailMessage')
                                        <span class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- What will be sent -->
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">E-postmeddelandet inkluderar:</h4>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                        <li class="flex items-center">
                                            <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Offertsammanfattning
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            PDF-bilaga med fullstandig offert
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Lank for att signera offerten
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button
                                type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-800 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Skicka offert
                            </button>
                            <button
                                wire:click="closeEmailModal"
                                type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-800 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Avbryt
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>