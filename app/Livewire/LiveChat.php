<?php

namespace App\Livewire;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Services\ChatBotService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LiveChat extends Component
{
    public $sessionId;
    public $session;
    public $messages = [];
    public $newMessage = '';
    public $isActive = false;
    public $isLoading = false;

    // Agent mode properties
    public $isAgent = false;
    public $canTakeOver = false;
    public $botActive = true;

    protected $rules = [
        'newMessage' => 'required|string|max:1000',
    ];

    protected $listeners = [
        'message-received' => 'handleMessageReceived',
        'session-assigned' => 'handleSessionAssigned',
        'take-over-session' => 'takeOverSession',
        'end-session' => 'endSession',
    ];

    public function mount($sessionId = null, $isAgent = false)
    {
        $this->sessionId = $sessionId;
        $this->isAgent = $isAgent;

        if ($sessionId) {
            $this->loadSession();
        } else if (!$isAgent) {
            // Start new visitor session
            $this->startNewSession();
        }
    }

    public function render()
    {
        return view('livewire.live-chat', [
            'messages' => $this->messages,
            'session' => $this->session,
        ]);
    }

    public function startNewSession()
    {
        try {
            $chatBotService = app(ChatBotService::class);
            
            $visitorData = [
                'page_url' => request()->headers->get('referer'),
                'browser_info' => [
                    'user_agent' => request()->headers->get('user-agent'),
                    'ip_address' => request()->ip(),
                ],
            ];

            $this->session = $chatBotService->startSession($visitorData);
            $this->sessionId = $this->session->session_id;
            $this->isActive = true;
            $this->botActive = $this->session->bot_active;

            $this->loadMessages();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to start chat session: ' . $e->getMessage());
        }
    }

    public function loadSession()
    {
        $chatBotService = app(ChatBotService::class);
        $this->session = $chatBotService->getSession($this->sessionId);

        if (!$this->session) {
            session()->flash('error', 'Chat session not found.');
            return;
        }

        $this->isActive = $this->session->isActive();
        $this->botActive = $this->session->isBotActive();
        $this->canTakeOver = $this->isAgent && $this->botActive;

        $this->loadMessages();
    }

    public function loadMessages()
    {
        if (!$this->session) {
            return;
        }

        $this->messages = $this->session->messages()
            ->orderBy('created_at')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender_type' => $message->sender_type,
                    'sender_name' => $message->sender_name,
                    'message' => $message->message,
                    'created_at' => $message->created_at,
                    'sentiment' => $message->sentiment,
                    'is_read' => $message->is_read,
                ];
            })->toArray();
    }

    public function sendMessage()
    {
        $this->validate();

        if (!$this->session || !$this->isActive) {
            session()->flash('error', 'Chat session is not active.');
            return;
        }

        $this->isLoading = true;

        try {
            $chatBotService = app(ChatBotService::class);

            if ($this->isAgent) {
                // Agent sending message
                $message = ChatMessage::create([
                    'chat_session_id' => $this->session->id,
                    'sender_type' => 'agent',
                    'user_id' => Auth::id(),
                    'message' => $this->newMessage,
                ]);

                // Mark session as having human agent
                if ($this->session->bot_active) {
                    $this->session->assignToAgent(Auth::user());
                    $this->botActive = false;
                    $this->canTakeOver = false;
                }

            } else {
                // Visitor sending message
                $message = $chatBotService->processMessage($this->session, $this->newMessage);
            }

            $this->newMessage = '';
            $this->loadMessages();

            // Dispatch real-time event
            $this->dispatch('message-sent', [
                'session_id' => $this->sessionId,
                'message_id' => $message->id,
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send message: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function takeOverSession()
    {
        if (!$this->isAgent || !$this->session || !$this->canTakeOver) {
            return;
        }

        try {
            $this->session->assignToAgent(Auth::user());
            $this->botActive = false;
            $this->canTakeOver = false;

            // Send takeover message
            ChatMessage::create([
                'chat_session_id' => $this->session->id,
                'sender_type' => 'agent',
                'user_id' => Auth::id(),
                'message' => 'Hi! I\'m ' . Auth::user()->name . ' and I\'ll be helping you from here. How can I assist you today?',
                'metadata' => [
                    'takeover' => true,
                    'agent_id' => Auth::id(),
                ],
            ]);

            $this->loadMessages();

            session()->flash('success', 'You have taken over this chat session.');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to take over session: ' . $e->getMessage());
        }
    }

    public function endSession()
    {
        if (!$this->session) {
            return;
        }

        try {
            $chatBotService = app(ChatBotService::class);
            $chatBotService->endSession($this->session);

            $this->isActive = false;
            $this->loadMessages();

            if ($this->isAgent) {
                return redirect()->route('communications.chat-sessions');
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to end session: ' . $e->getMessage());
        }
    }

    public function handleMessageReceived($data)
    {
        if ($data['session_id'] === $this->sessionId) {
            $this->loadMessages();
        }
    }

    public function handleSessionAssigned($data)
    {
        if ($data['session_id'] === $this->sessionId) {
            $this->loadSession();
        }
    }

    public function markMessagesAsRead()
    {
        if (!$this->session) {
            return;
        }

        // Mark unread messages as read
        $unreadMessages = $this->session->messages()
            ->where('is_read', false)
            ->where('sender_type', '!=', $this->isAgent ? 'agent' : 'visitor')
            ->get();

        foreach ($unreadMessages as $message) {
            $message->markAsRead();
        }

        $this->loadMessages();
    }

    public function updateVisitorInfo($data)
    {
        if (!$this->session || $this->isAgent) {
            return;
        }

        $updateData = [];

        if (isset($data['name']) && !$this->session->visitor_name) {
            $updateData['visitor_name'] = $data['name'];
        }

        if (isset($data['email']) && !$this->session->visitor_email) {
            $updateData['visitor_email'] = $data['email'];
        }

        if (isset($data['phone']) && !$this->session->visitor_phone) {
            $updateData['visitor_phone'] = $data['phone'];
        }

        if (!empty($updateData)) {
            $this->session->update($updateData);
        }
    }

    public function getUnreadCountProperty()
    {
        if (!$this->session) {
            return 0;
        }

        return $this->session->messages()
            ->where('is_read', false)
            ->where('sender_type', '!=', $this->isAgent ? 'agent' : 'visitor')
            ->count();
    }

    public function getSessionStatusProperty()
    {
        if (!$this->session) {
            return 'inactive';
        }

        if ($this->session->status === 'waiting') {
            return 'waiting-for-agent';
        }

        if ($this->session->status === 'completed') {
            return 'ended';
        }

        if ($this->session->bot_active) {
            return 'bot-active';
        }

        if ($this->session->hasAssignedAgent()) {
            return 'agent-active';
        }

        return 'active';
    }

    public function typing()
    {
        // Dispatch typing indicator event
        $this->dispatch('user-typing', [
            'session_id' => $this->sessionId,
            'user_type' => $this->isAgent ? 'agent' : 'visitor',
            'user_id' => $this->isAgent ? Auth::id() : null,
        ]);
    }

    public function scrollToBottom()
    {
        $this->dispatch('scroll-to-bottom');
    }
}