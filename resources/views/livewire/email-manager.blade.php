<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-sm">
        <!-- Header -->
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Email Management</h1>
                <button wire:click="showComposer" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Compose Email
                </button>
            </div>
        </div>

        <div class="flex">
            <!-- Sidebar -->
            <div class="w-64 border-r border-gray-200">
                <nav class="p-4 space-y-2">
                    <a wire:click="setActiveTab('inbox')" 
                       class="block px-3 py-2 rounded-md text-sm font-medium cursor-pointer {{ $activeTab === 'inbox' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        📥 Inbox
                    </a>
                    <a wire:click="setActiveTab('sent')" 
                       class="block px-3 py-2 rounded-md text-sm font-medium cursor-pointer {{ $activeTab === 'sent' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        📤 Sent
                    </a>
                    <a wire:click="setActiveTab('templates')" 
                       class="block px-3 py-2 rounded-md text-sm font-medium cursor-pointer {{ $activeTab === 'templates' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        📝 Templates
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="flex-1">
                @if($showComposer)
                    <!-- Email Composer -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">Compose New Email</h2>
                            <button wire:click="hideComposer" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="sendEmail" class="space-y-4">
                            <!-- Template Selection -->
                            @if($templates->count() > 0)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Use Template</label>
                                    <select wire:change="loadTemplate($event.target.value)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select a template...</option>
                                        @foreach($templates as $template)
                                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <!-- To Field -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">To</label>
                                <input type="email" wire:model="to" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="recipient@example.com">
                                @error('to') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Subject Field -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Subject</label>
                                <input type="text" wire:model="subject" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Email subject">
                                @error('subject') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Body Field -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Message</label>
                                <textarea wire:model="body" rows="8" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Type your message here..."></textarea>
                                @error('body') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Send Button -->
                            <div class="flex justify-end space-x-3">
                                <button type="button" wire:click="hideComposer" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Send Email
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Search Bar -->
                @if(!$showComposer)
                    <div class="p-4 border-b border-gray-200">
                        <input type="text" wire:model.live="search" placeholder="Search emails..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                @endif

                <!-- Email List / Templates -->
                <div class="p-4">
                    @if($activeTab === 'templates')
                        <!-- Templates View -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">Email Templates</h3>
                                <button class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm">
                                    Add Template
                                </button>
                            </div>
                            
                            @if($templates->count() > 0)
                                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                                    @foreach($templates as $template)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                            <h4 class="font-medium text-gray-900">{{ $template->name }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $template->subject }}</p>
                                            <p class="text-xs text-gray-500 mt-2">{{ Str::limit($template->body, 100) }}</p>
                                            <div class="mt-3 flex space-x-2">
                                                <button wire:click="loadTemplate({{ $template->id }}); showComposer()" class="text-blue-600 hover:text-blue-800 text-sm">
                                                    Use Template
                                                </button>
                                                <button class="text-gray-600 hover:text-gray-800 text-sm">
                                                    Edit
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <p class="text-gray-500">No email templates found. Create your first template to get started.</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Email List View -->
                        @if($emails->count() > 0)
                            <div class="space-y-1">
                                @foreach($emails as $email)
                                    <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-3">
                                                    <div class="flex-shrink-0">
                                                        @if($activeTab === 'sent')
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                Sent
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                Received
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ $email->subject }}</p>
                                                        <p class="text-sm text-gray-500">{{ $activeTab === 'sent' ? 'To: ' . $email->to : 'From: ' . ($email->user->name ?? 'Unknown') }}</p>
                                                    </div>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $email->created_at->format('M j, Y g:i A') }}
                                                </div>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-600">{{ Str::limit(strip_tags($email->body), 150) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6">
                                {{ $emails->links() }}
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No emails found</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    @if($activeTab === 'sent')
                                        You haven't sent any emails yet.
                                    @else
                                        Your inbox is empty.
                                    @endif
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" 
             class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
             class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg">
            {{ session('error') }}
        </div>
    @endif
</div>