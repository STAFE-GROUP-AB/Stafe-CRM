<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Services\ChatBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ChatWidgetController extends Controller
{
    protected ChatBotService $chatBotService;

    public function __construct(ChatBotService $chatBotService)
    {
        $this->chatBotService = $chatBotService;
    }

    /**
     * Serve the chat widget embed script
     */
    public function embedScript(Request $request)
    {
        $script = $this->generateEmbedScript();

        return response($script)
            ->header('Content-Type', 'application/javascript')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Serve the chat widget HTML
     */
    public function widget(Request $request)
    {
        $pageUrl = $request->query('page_url');
        $referrer = $request->query('referrer');

        // Check if chat should be shown for this page
        if (!$this->chatBotService->shouldShowChatWidget($pageUrl)) {
            return response('Chat not available', 404);
        }

        return view('chat.widget', [
            'pageUrl' => $pageUrl,
            'referrer' => $referrer,
            'config' => $this->getChatWidgetConfig(),
        ]);
    }

    /**
     * Generate the JavaScript embed script
     */
    private function generateEmbedScript(): string
    {
        $widgetUrl = route('chat.widget');
        $baseUrl = config('app.url');

        return <<<JAVASCRIPT
(function() {
    // Chat Widget Configuration
    var chatConfig = {
        baseUrl: '{$baseUrl}',
        widgetUrl: '{$widgetUrl}',
        position: 'bottom-right',
        theme: 'default',
        autoOpen: false,
        welcomeMessage: true
    };

    // Create chat widget container
    function createChatWidget() {
        if (document.getElementById('stafe-chat-widget')) {
            return; // Widget already exists
        }

        // Create widget container
        var container = document.createElement('div');
        container.id = 'stafe-chat-widget';
        container.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 350px;
            height: 500px;
            z-index: 9999;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            display: none;
        `;

        // Create toggle button
        var toggleButton = document.createElement('div');
        toggleButton.id = 'stafe-chat-toggle';
        toggleButton.innerHTML = `
            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                <path d="M12 2C6.48 2 2 6.48 2 12c0 1.54.36 3.04 1.05 4.39L1 22l5.61-2.05C8.96 20.64 10.46 21 12 21c5.52 0 10-4.48 10-10S17.52 2 12 2z"/>
            </svg>
        `;
        toggleButton.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: #3b82f6;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            transition: transform 0.2s;
        `;

        // Create iframe for chat
        var iframe = document.createElement('iframe');
        iframe.src = chatConfig.widgetUrl + '?page_url=' + encodeURIComponent(window.location.href) + '&referrer=' + encodeURIComponent(document.referrer);
        iframe.style.cssText = `
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        `;

        container.appendChild(iframe);

        // Add event listeners
        toggleButton.addEventListener('click', toggleChat);
        toggleButton.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
        });
        toggleButton.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });

        // Add to page
        document.body.appendChild(container);
        document.body.appendChild(toggleButton);

        // Auto-open if configured
        if (chatConfig.autoOpen) {
            setTimeout(function() {
                toggleChat();
            }, 3000);
        }
    }

    // Toggle chat visibility
    function toggleChat() {
        var widget = document.getElementById('stafe-chat-widget');
        var toggle = document.getElementById('stafe-chat-toggle');
        
        if (widget.style.display === 'none' || !widget.style.display) {
            widget.style.display = 'block';
            toggle.innerHTML = `
                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
                </svg>
            `;
        } else {
            widget.style.display = 'none';
            toggle.innerHTML = `
                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                    <path d="M12 2C6.48 2 2 6.48 2 12c0 1.54.36 3.04 1.05 4.39L1 22l5.61-2.05C8.96 20.64 10.46 21 12 21c5.52 0 10-4.48 10-10S17.52 2 12 2z"/>
                </svg>
            `;
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', createChatWidget);
    } else {
        createChatWidget();
    }

    // Global functions
    window.StafeChat = {
        open: function() {
            var widget = document.getElementById('stafe-chat-widget');
            if (widget) widget.style.display = 'block';
        },
        close: function() {
            var widget = document.getElementById('stafe-chat-widget');
            if (widget) widget.style.display = 'none';
        },
        toggle: toggleChat
    };
})();
JAVASCRIPT;
    }

    /**
     * Get chat widget configuration
     */
    private function getChatWidgetConfig(): array
    {
        return [
            'company_name' => config('app.name'),
            'welcome_message' => 'Hi! How can we help you today?',
            'theme' => [
                'primary_color' => '#3b82f6',
                'secondary_color' => '#f3f4f6',
                'text_color' => '#374151',
            ],
            'features' => [
                'file_upload' => false,
                'emoji' => true,
                'typing_indicator' => true,
                'read_receipts' => true,
            ],
            'business_hours' => [
                'enabled' => true,
                'timezone' => config('app.timezone'),
                'hours' => [
                    'monday' => ['09:00', '17:00'],
                    'tuesday' => ['09:00', '17:00'],
                    'wednesday' => ['09:00', '17:00'],
                    'thursday' => ['09:00', '17:00'],
                    'friday' => ['09:00', '17:00'],
                    'saturday' => null,
                    'sunday' => null,
                ],
            ],
        ];
    }
}