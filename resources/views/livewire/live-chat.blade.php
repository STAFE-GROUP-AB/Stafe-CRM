<div class="flex flex-col h-full">
    <!-- Chat Messages -->
    <div class="flex-1 overflow-y-auto p-4 space-y-4 chat-messages" id="chat-messages">
        @if(empty($messages))
            <div class="text-center text-gray-500 py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p class="mt-2 text-sm">Start a conversation!</p>
            </div>
        @else
            @foreach($messages as $message)
                <div class="flex {{ $message['sender_type'] === 'visitor' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs lg:max-w-md">
                        <!-- Message bubble -->
                        <div class="px-4 py-2 rounded-lg {{ $message['sender_type'] === 'visitor' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-900' }}">
                            <!-- Sender name (for agent/bot messages) -->
                            @if($message['sender_type'] !== 'visitor')
                                <div class="text-xs text-gray-600 mb-1">{{ $message['sender_name'] }}</div>
                            @endif
                            
                            <!-- Message content -->
                            <p class="text-sm">{{ $message['message'] }}</p>
                            
                            <!-- Timestamp -->
                            <div class="text-xs {{ $message['sender_type'] === 'visitor' ? 'text-blue-200' : 'text-gray-500' }} mt-1">
                                {{ \Carbon\Carbon::parse($message['created_at'])->format('g:i A') }}
                                
                                <!-- Read indicator -->
                                @if($message['sender_type'] === 'visitor' && $message['is_read'])
                                    <span class="ml-1">✓</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Sentiment indicator (for debugging) -->
                        @if(config('app.debug') && isset($message['sentiment']) && $message['sentiment'] !== 'neutral')
                            <div class="text-xs text-gray-400 mt-1">
                                Sentiment: {{ $message['sentiment'] }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif

        <!-- Typing indicator -->
        <div wire:loading wire:target="sendMessage" class="flex justify-start">
            <div class="max-w-xs lg:max-w-md">
                <div class="px-4 py-2 bg-gray-200 rounded-lg">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Session Status Bar (for agents) -->
    @if($isAgent && $session)
        <div class="border-t border-gray-200 px-4 py-2 bg-gray-50">
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center space-x-4">
                    <!-- Session status -->
                    <span class="flex items-center">
                        <span class="w-2 h-2 rounded-full mr-2 {{ $this->sessionStatus === 'bot-active' ? 'bg-blue-500' : ($this->sessionStatus === 'agent-active' ? 'bg-green-500' : 'bg-gray-400') }}"></span>
                        {{ ucfirst(str_replace('-', ' ', $this->sessionStatus)) }}
                    </span>

                    <!-- Visitor info -->
                    @if($session['visitor_name'] || $session['visitor_email'])
                        <span class="text-gray-600">
                            {{ $session['visitor_name'] ?? 'Visitor' }}
                            @if($session['visitor_email'])
                                ({{ $session['visitor_email'] }})
                            @endif
                        </span>
                    @endif

                    <!-- Lead qualification -->
                    @if($session['qualified_lead'])
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Qualified Lead
                            @if($session['lead_score'])
                                - {{ $session['lead_score'] }}%
                            @endif
                        </span>
                    @endif
                </div>

                <!-- Agent actions -->
                <div class="flex items-center space-x-2">
                    @if($canTakeOver)
                        <button wire:click="takeOverSession" class="text-blue-600 hover:text-blue-800 text-sm">
                            Take Over
                        </button>
                    @endif

                    @if($isActive)
                        <button wire:click="endSession" class="text-red-600 hover:text-red-800 text-sm">
                            End Session
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Message Input -->
    @if($isActive)
        <div class="border-t border-gray-200 p-4">
            <form wire:submit="sendMessage" class="flex space-x-3">
                <div class="flex-1">
                    <input 
                        type="text" 
                        wire:model="newMessage" 
                        wire:keydown.debounce.500ms="typing"
                        placeholder="{{ $isAgent ? 'Type your message...' : 'Type a message...' }}" 
                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        maxlength="1000"
                        {{ $isLoading ? 'disabled' : '' }}
                    >
                    @error('newMessage') 
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                    @enderror
                </div>
                <button 
                    type="submit" 
                    {{ $isLoading ? 'disabled' : '' }}
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                >
                    @if($isLoading)
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    @else
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                        </svg>
                    @endif
                    <span class="sr-only">Send message</span>
                </button>
            </form>
        </div>

        <!-- Visitor info collection (for non-agent users) -->
        @if(!$isAgent && $session && (!$session['visitor_name'] || !$session['visitor_email']))
            <div class="border-t border-gray-200 bg-blue-50 p-4">
                <div class="text-sm text-blue-800 mb-3">
                    <strong>Help us serve you better!</strong> Share your contact information:
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @if(!$session['visitor_name'])
                        <input 
                            type="text" 
                            placeholder="Your name"
                            class="border-blue-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm"
                            onchange="updateVisitorInfo('name', this.value)"
                        >
                    @endif
                    @if(!$session['visitor_email'])
                        <input 
                            type="email" 
                            placeholder="Your email"
                            class="border-blue-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm"
                            onchange="updateVisitorInfo('email', this.value)"
                        >
                    @endif
                </div>
            </div>
        @endif
    @else
        <div class="border-t border-gray-200 bg-gray-50 p-4 text-center text-gray-500 text-sm">
            @if($session && $session['status'] === 'completed')
                This conversation has ended. Thank you for chatting with us!
            @elseif($session && $session['status'] === 'waiting')
                Please wait while we connect you with an agent...
            @else
                Chat is not available at the moment.
            @endif
        </div>
    @endif
</div>

<script>
    // Auto-scroll to bottom when new messages arrive
    function scrollToBottom() {
        const chatContainer = document.getElementById('chat-messages');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    }

    // Update visitor information
    function updateVisitorInfo(field, value) {
        if (value && value.trim()) {
            @this.updateVisitorInfo({[field]: value.trim()});
        }
    }

    // Listen for Livewire events
    document.addEventListener('livewire:init', function () {
        Livewire.on('scroll-to-bottom', scrollToBottom);
        
        // Auto-scroll on initial load and message updates
        setTimeout(scrollToBottom, 100);
    });

    // Auto-scroll when messages update
    document.addEventListener('livewire:updated', function () {
        setTimeout(scrollToBottom, 100);
    });

    // Mark messages as read when chat is visible
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                @this.markMessagesAsRead();
            }
        });
    });

    const chatContainer = document.getElementById('chat-messages');
    if (chatContainer) {
        observer.observe(chatContainer);
    }
</script>