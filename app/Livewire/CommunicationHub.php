<?php

namespace App\Livewire;

use App\Models\Communication;
use App\Models\Contact;
use App\Models\User;
use App\Services\TwilioService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class CommunicationHub extends Component
{
    use WithPagination;

    public $activeTab = 'all';
    public $selectedCommunication = null;
    public $showCallModal = false;
    public $showSmsModal = false;
    
    // Call/SMS form fields
    public $toNumber = '';
    public $fromNumber = '';
    public $smsMessage = '';
    public $selectedContact = null;
    public $selectedContactId = null;

    // Filters
    public $typeFilter = '';
    public $directionFilter = '';
    public $statusFilter = '';
    public $dateRange = '';

    protected $queryString = [
        'activeTab' => ['except' => 'all'],
        'typeFilter' => ['except' => ''],
        'directionFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    protected $rules = [
        'toNumber' => 'required|string',
        'fromNumber' => 'required|string',
        'smsMessage' => 'required|string|max:1600',
    ];

    public function mount()
    {
        // Set default from number if available
        $this->fromNumber = config('services.twilio.phone_number', '');
    }

    public function render()
    {
        $communications = $this->getCommunications();
        $stats = $this->getCommunicationStats();
        $contacts = Contact::select('id', 'name', 'phone', 'email')
            ->orderBy('name')
            ->limit(100)
            ->get();

        return view('livewire.communication-hub', [
            'communications' => $communications,
            'stats' => $stats,
            'contacts' => $contacts,
        ]);
    }

    public function getCommunications()
    {
        $query = Communication::with(['communicable', 'user'])
            ->latest();

        // Apply tab filter
        switch ($this->activeTab) {
            case 'calls':
                $query->whereIn('type', ['call', 'video']);
                break;
            case 'messages':
                $query->whereIn('type', ['sms', 'whatsapp', 'chat']);
                break;
            case 'emails':
                $query->where('type', 'email');
                break;
            case 'social':
                $query->where('type', 'social');
                break;
        }

        // Apply additional filters
        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        if ($this->directionFilter) {
            $query->where('direction', $this->directionFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->dateRange) {
            $this->applyDateRangeFilter($query);
        }

        return $query->paginate(20);
    }

    public function getCommunicationStats()
    {
        $today = now()->startOfDay();

        return [
            'total_today' => Communication::where('created_at', '>=', $today)->count(),
            'calls_today' => Communication::where('created_at', '>=', $today)
                ->whereIn('type', ['call', 'video'])
                ->count(),
            'messages_today' => Communication::where('created_at', '>=', $today)
                ->whereIn('type', ['sms', 'whatsapp', 'chat'])
                ->count(),
            'pending_callbacks' => Communication::where('status', 'no-answer')
                ->where('direction', 'outbound')
                ->count(),
        ];
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function selectCommunication($communicationId)
    {
        $this->selectedCommunication = Communication::with(['communicable', 'user'])
            ->find($communicationId);
    }

    public function showCallDialog()
    {
        $this->showCallModal = true;
        $this->resetForm();
    }

    public function showSmsDialog()
    {
        $this->showSmsModal = true;
        $this->resetForm();
    }

    public function selectContact($contactId)
    {
        $contact = Contact::find($contactId);
        if ($contact) {
            $this->selectedContact = $contact;
            $this->selectedContactId = $contactId;
            $this->toNumber = $contact->phone ?? $contact->mobile ?? '';
        }
    }

    public function makeCall()
    {
        $this->validate([
            'toNumber' => 'required|string',
            'fromNumber' => 'required|string',
        ]);

        try {
            $twilioService = app(TwilioService::class);
            
            $communicable = $this->selectedContact;
            
            $communication = $twilioService->makeCall(
                $this->fromNumber,
                $this->toNumber,
                $communicable,
                Auth::id()
            );

            $this->dispatch('call-initiated', [
                'communication_id' => $communication->id,
                'to_number' => $this->toNumber,
            ]);

            $this->showCallModal = false;
            $this->resetForm();

            session()->flash('success', 'Call initiated successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to make call: ' . $e->getMessage());
        }
    }

    public function sendSms()
    {
        $this->validate([
            'toNumber' => 'required|string',
            'fromNumber' => 'required|string',
            'smsMessage' => 'required|string|max:1600',
        ]);

        try {
            $twilioService = app(TwilioService::class);
            
            $communicable = $this->selectedContact;
            
            $communication = $twilioService->sendSms(
                $this->fromNumber,
                $this->toNumber,
                $this->smsMessage,
                $communicable,
                Auth::id()
            );

            $this->dispatch('sms-sent', [
                'communication_id' => $communication->id,
                'to_number' => $this->toNumber,
            ]);

            $this->showSmsModal = false;
            $this->resetForm();

            session()->flash('success', 'SMS sent successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send SMS: ' . $e->getMessage());
        }
    }

    public function closeModals()
    {
        $this->showCallModal = false;
        $this->showSmsModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->toNumber = '';
        $this->smsMessage = '';
        $this->selectedContact = null;
        $this->selectedContactId = null;
        $this->fromNumber = config('services.twilio.phone_number', '');
    }

    public function applyDateRangeFilter($query)
    {
        switch ($this->dateRange) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'yesterday':
                $query->whereDate('created_at', yesterday());
                break;
            case 'week':
                $query->where('created_at', '>=', now()->startOfWeek());
                break;
            case 'month':
                $query->where('created_at', '>=', now()->startOfMonth());
                break;
        }
    }

    public function clearFilters()
    {
        $this->typeFilter = '';
        $this->directionFilter = '';
        $this->statusFilter = '';
        $this->dateRange = '';
        $this->resetPage();
    }

    public function refreshCommunications()
    {
        // This method can be called via JavaScript to refresh the list
        $this->render();
    }

    public function getTypeColorAttribute($type)
    {
        return match ($type) {
            'call' => 'bg-blue-100 text-blue-800',
            'video' => 'bg-purple-100 text-purple-800',
            'sms' => 'bg-green-100 text-green-800',
            'whatsapp' => 'bg-emerald-100 text-emerald-800',
            'email' => 'bg-yellow-100 text-yellow-800',
            'chat' => 'bg-indigo-100 text-indigo-800',
            'social' => 'bg-pink-100 text-pink-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusColorAttribute($status)
    {
        return match ($status) {
            'completed' => 'bg-green-100 text-green-800',
            'answered' => 'bg-blue-100 text-blue-800',
            'ringing' => 'bg-yellow-100 text-yellow-800',
            'initiated' => 'bg-indigo-100 text-indigo-800',
            'failed' => 'bg-red-100 text-red-800',
            'busy' => 'bg-orange-100 text-orange-800',
            'no-answer' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}