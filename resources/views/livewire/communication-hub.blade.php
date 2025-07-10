<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Communication Hub</h1>
            <p class="text-gray-600">Manage all your communications in one place</p>
        </div>
        <div class="flex space-x-3">
            <button wire:click="showCallDialog" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                </svg>
                Make Call
            </button>
            <button wire:click="showSmsDialog" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"></path>
                </svg>
                Send SMS
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600">Total Today</h2>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_today'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600">Calls Today</h2>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['calls_today'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600">Messages Today</h2>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['messages_today'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600">Pending Callbacks</h2>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_callbacks'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs and Filters -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button wire:click="setActiveTab('all')" class="@if($activeTab === 'all') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    All Communications
                </button>
                <button wire:click="setActiveTab('calls')" class="@if($activeTab === 'calls') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Calls & Video
                </button>
                <button wire:click="setActiveTab('messages')" class="@if($activeTab === 'messages') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Messages
                </button>
                <button wire:click="setActiveTab('emails')" class="@if($activeTab === 'emails') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Emails
                </button>
                <button wire:click="setActiveTab('social')" class="@if($activeTab === 'social') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Social Media
                </button>
            </nav>
        </div>

        <!-- Filters -->
        <div class="p-6 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select wire:model.live="typeFilter" class="w-full border-gray-300 rounded-md">
                        <option value="">All Types</option>
                        <option value="call">Calls</option>
                        <option value="video">Video</option>
                        <option value="sms">SMS</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="email">Email</option>
                        <option value="chat">Chat</option>
                        <option value="social">Social</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Direction</label>
                    <select wire:model.live="directionFilter" class="w-full border-gray-300 rounded-md">
                        <option value="">All Directions</option>
                        <option value="inbound">Inbound</option>
                        <option value="outbound">Outbound</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model.live="statusFilter" class="w-full border-gray-300 rounded-md">
                        <option value="">All Statuses</option>
                        <option value="completed">Completed</option>
                        <option value="answered">Answered</option>
                        <option value="failed">Failed</option>
                        <option value="no-answer">No Answer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                    <select wire:model.live="dateRange" class="w-full border-gray-300 rounded-md">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                    </select>
                </div>
            </div>
            @if($typeFilter || $directionFilter || $statusFilter || $dateRange)
                <div class="mt-4">
                    <button wire:click="clearFilters" class="text-sm text-blue-600 hover:text-blue-800">
                        Clear all filters
                    </button>
                </div>
            @endif
        </div>

        <!-- Communications List -->
        <div class="overflow-hidden">
            @if($communications->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($communications as $communication)
                        <div wire:click="selectCommunication({{ $communication->id }})" class="p-6 hover:bg-gray-50 cursor-pointer">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 flex-1">
                                    <!-- Type Badge -->
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getTypeColorAttribute($communication->type) }}">
                                        {{ ucfirst($communication->type) }}
                                    </span>

                                    <!-- Direction -->
                                    <div class="flex items-center text-sm text-gray-500">
                                        @if($communication->direction === 'inbound')
                                            <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                        {{ ucfirst($communication->direction) }}
                                    </div>

                                    <!-- Contact/Number -->
                                    <div class="flex-1">
                                        @if($communication->communicable)
                                            <p class="text-sm font-medium text-gray-900">{{ $communication->communicable->name ?? $communication->communicable->company_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $communication->from_number ?? $communication->to_number }}</p>
                                        @else
                                            <p class="text-sm text-gray-900">{{ $communication->from_number ?? $communication->to_number ?? 'Unknown' }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4">
                                    <!-- Status -->
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColorAttribute($communication->status) }}">
                                        {{ ucfirst(str_replace('-', ' ', $communication->status)) }}
                                    </span>

                                    <!-- Duration -->
                                    @if($communication->duration_seconds)
                                        <span class="text-sm text-gray-500">{{ $communication->formatted_duration }}</span>
                                    @endif

                                    <!-- Date -->
                                    <span class="text-sm text-gray-500">{{ $communication->created_at->format('M j, Y g:i A') }}</span>
                                </div>
                            </div>

                            <!-- Content Preview -->
                            @if($communication->content)
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600 truncate">{{ $communication->content }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $communications->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No communications found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by making a call or sending a message.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Call Modal -->
    @if($showCallModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModals"></div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="makeCall">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Make a Call</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Contact (Optional)</label>
                                    <select wire:model.live="selectedContactId" class="w-full border-gray-300 rounded-md">
                                        <option value="">Choose a contact...</option>
                                        @foreach($contacts as $contact)
                                            <option value="{{ $contact->id }}">{{ $contact->name }} - {{ $contact->phone ?? $contact->email }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">From Number</label>
                                    <input type="text" wire:model="fromNumber" class="w-full border-gray-300 rounded-md" placeholder="+1234567890">
                                    @error('fromNumber') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">To Number</label>
                                    <input type="text" wire:model="toNumber" class="w-full border-gray-300 rounded-md" placeholder="+1234567890">
                                    @error('toNumber') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Make Call
                            </button>
                            <button type="button" wire:click="closeModals" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- SMS Modal -->
    @if($showSmsModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModals"></div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="sendSms">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Send SMS</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Contact (Optional)</label>
                                    <select wire:model.live="selectedContactId" class="w-full border-gray-300 rounded-md">
                                        <option value="">Choose a contact...</option>
                                        @foreach($contacts as $contact)
                                            <option value="{{ $contact->id }}">{{ $contact->name }} - {{ $contact->phone ?? $contact->email }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">From Number</label>
                                    <input type="text" wire:model="fromNumber" class="w-full border-gray-300 rounded-md" placeholder="+1234567890">
                                    @error('fromNumber') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">To Number</label>
                                    <input type="text" wire:model="toNumber" class="w-full border-gray-300 rounded-md" placeholder="+1234567890">
                                    @error('toNumber') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                                    <textarea wire:model="smsMessage" rows="4" class="w-full border-gray-300 rounded-md" placeholder="Type your message here..."></textarea>
                                    @error('smsMessage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    <p class="text-xs text-gray-500 mt-1">{{ strlen($smsMessage) }}/1600 characters</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Send SMS
                            </button>
                            <button type="button" wire:click="closeModals" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>