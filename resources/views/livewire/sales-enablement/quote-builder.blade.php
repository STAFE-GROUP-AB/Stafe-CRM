<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Quote Builder
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Create and manage intelligent quotes with approval workflows
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <button wire:click="createQuote" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    New Quote
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <div>
                        <input wire:model.live="search" type="text" placeholder="Search quotes..." class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <select wire:model.live="statusFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Statuses</option>
                            <option value="draft">Draft</option>
                            <option value="sent">Sent</option>
                            <option value="viewed">Viewed</option>
                            <option value="accepted">Accepted</option>
                            <option value="rejected">Rejected</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>
                    <div>
                        <select wire:model.live="dealFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Deals</option>
                            @foreach($deals as $deal)
                                <option value="{{ $deal->id }}">{{ $deal->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quotes Grid -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @forelse($quotes as $quote)
                    <li>
                        <div class="px-4 py-4 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="flex items-center">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $quote->title }}
                                        </p>
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $quote->status === 'accepted' ? 'bg-green-100 text-green-800' : ($quote->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($quote->status) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500">
                                        <p>{{ $quote->quote_number }}</p>
                                        <span class="mx-2">•</span>
                                        <p>{{ $quote->company->name }}</p>
                                        <span class="mx-2">•</span>
                                        <p>${{ number_format($quote->total_amount, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button wire:click="viewQuote({{ $quote->id }})" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    View
                                </button>
                                <button wire:click="editQuote({{ $quote->id }})" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    Edit
                                </button>
                                @if($quote->isDraft())
                                    <button wire:click="sendQuote({{ $quote->id }})" class="text-green-600 hover:text-green-900 text-sm font-medium">
                                        Send
                                    </button>
                                @endif
                                <button wire:click="duplicateQuote({{ $quote->id }})" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    Duplicate
                                </button>
                                <button wire:click="deleteQuote({{ $quote->id }})" wire:confirm="Are you sure you want to delete this quote?" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-4 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No quotes found</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating your first quote.</p>
                        <div class="mt-6">
                            <button wire:click="createQuote" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                New Quote
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
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="saveQuote">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="space-y-4">
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                    <input wire:model="title" type="text" id="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="deal_id" class="block text-sm font-medium text-gray-700">Deal</label>
                                    <select wire:model="deal_id" wire:change="updatedDealId" id="deal_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Select Deal</option>
                                        @foreach($deals as $deal)
                                            <option value="{{ $deal->id }}">{{ $deal->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('deal_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="company_id" class="block text-sm font-medium text-gray-700">Company</label>
                                    <select wire:model="company_id" id="company_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Select Company</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('company_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                @if($this->contacts->isNotEmpty())
                                    <div>
                                        <label for="contact_id" class="block text-sm font-medium text-gray-700">Contact</label>
                                        <select wire:model="contact_id" id="contact_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="">Select Contact</option>
                                            @foreach($this->contacts as $contact)
                                                <option value="{{ $contact->id }}">{{ $contact->first_name }} {{ $contact->last_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('contact_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                @endif

                                <div>
                                    <label for="valid_until" class="block text-sm font-medium text-gray-700">Valid Until</label>
                                    <input wire:model="valid_until" type="date" id="valid_until" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('valid_until') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea wire:model="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                    @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ $editingQuote ? 'Update' : 'Create' }} Quote
                            </button>
                            <button wire:click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>