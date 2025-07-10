<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Widget</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
    </style>
</head>
<body class="bg-white">
    <div id="chat-widget" class="h-screen flex flex-col">
        <!-- Chat Header -->
        <div class="bg-blue-600 text-white p-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <div class="font-semibold">{{ $config['company_name'] }}</div>
                    <div class="text-xs opacity-90">We typically reply instantly</div>
                </div>
            </div>
            <button onclick="parent.StafeChat.close()" class="text-white hover:bg-blue-700 p-1 rounded">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>

        <!-- Chat Content -->
        <div class="flex-1 flex flex-col">
            @livewire('live-chat', ['isAgent' => false])
        </div>

        <!-- Powered by -->
        <div class="text-center py-2 text-xs text-gray-500 border-t">
            Powered by {{ config('app.name') }}
        </div>
    </div>

    @livewireScripts
    <script>
        // Chat widget specific JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-scroll to bottom
            const chatContainer = document.querySelector('.chat-messages');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }

            // Handle message notifications to parent window
            Livewire.on('message-sent', function(data) {
                if (window.parent && window.parent.postMessage) {
                    window.parent.postMessage({
                        type: 'chat-message-sent',
                        data: data
                    }, '*');
                }
            });

            // Listen for visitor info updates from parent
            window.addEventListener('message', function(event) {
                if (event.data.type === 'visitor-info') {
                    Livewire.dispatch('update-visitor-info', event.data.info);
                }
            });
        });
    </script>
</body>
</html>