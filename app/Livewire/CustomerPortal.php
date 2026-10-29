<?php

namespace App\Livewire;

use App\Models\Contact;
use App\Models\CustomerTicket;
use App\Models\User;
use App\Services\TicketSlaService;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerPortal extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $showDetailModal = false;
    public $editingTicket = null;
    public $viewingTicket = null;
    public $selectedContact = null;
    public $selectedAssignee = null;
    public $subject = '';
    public $description = '';
    public $priority = 'medium';
    public $category = 'general';
    public $status = 'open';
    public $search = '';
    public $statusFilter = '';
    public $priorityFilter = '';
    public $slaFilter = '';

    public $newResponseMessage = '';
    public $isInternalNote = false;

    protected $rules = [
        'subject' => ['required', 'min:5', 'max:255'],
        'description' => ['required', 'min:10'],
        'priority' => ['required', 'in:low,medium,high,urgent'],
        'category' => ['required', 'in:technical,billing,general,feature_request,bug_report'],
        'status' => ['required', 'in:open,in_progress,waiting_customer,resolved,closed'],
        'selectedContact' => ['required', 'exists:contacts,id'],
        'selectedAssignee' => ['nullable', 'exists:users,id'],
    ];

    public function render()
    {
        $slaService = app(TicketSlaService::class);

        $tickets = CustomerTicket::with(['contact', 'assignedUser'])
            ->when($this->search, fn ($query) => $query->where('subject', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%")
            )
            ->when($this->statusFilter, fn ($query) => $query->where('status', $this->statusFilter)
            )
            ->when($this->priorityFilter, fn ($query) => $query->where('priority', $this->priorityFilter)
            )
            ->when($this->slaFilter === 'breached', fn ($query) => $query->slaBreached()
            )
            ->when($this->slaFilter === 'due_soon', fn ($query) => $query->slaDueSoon(60)
            )
            ->when($this->slaFilter === 'awaiting_response', fn ($query) => $query->awaitingFirstResponse()
            )
            ->latest()
            ->paginate(10);

        $contacts = Contact::select('id', 'first_name', 'last_name', 'email')->get();
        $users = User::select('id', 'name')->get();

        $stats = $this->calculateStats($slaService);

        return view('livewire.customer-portal', compact('tickets', 'contacts', 'users', 'stats'));
    }

    protected function calculateStats(TicketSlaService $slaService): array
    {
        $avgFirstResponse = CustomerTicket::whereNotNull('total_response_time_minutes')
            ->avg('total_response_time_minutes');

        $avgResolution = CustomerTicket::whereNotNull('total_resolution_time_minutes')
            ->avg('total_resolution_time_minutes');

        $slaBreachedCount = CustomerTicket::slaBreached()->count();
        $totalResolved = CustomerTicket::whereNotNull('resolved_at')->count();

        $slaCompliance = $totalResolved > 0
            ? round((($totalResolved - $slaBreachedCount) / $totalResolved) * 100, 1)
            : 100;

        return [
            'total' => CustomerTicket::count(),
            'open' => CustomerTicket::open()->count(),
            'high_priority' => CustomerTicket::highPriority()->count(),
            'awaiting_response' => CustomerTicket::awaitingFirstResponse()->count(),
            'sla_breached' => $slaBreachedCount,
            'sla_due_soon' => CustomerTicket::slaDueSoon(60)->count(),
            'avg_first_response' => $avgFirstResponse
                ? $slaService->formatMinutes((int) $avgFirstResponse)
                : '-',
            'avg_resolution' => $avgResolution
                ? $slaService->formatMinutes((int) $avgResolution)
                : '-',
            'sla_compliance' => $slaCompliance,
        ];
    }

    public function createTicket(): void
    {
        $this->showCreateModal = true;
        $this->reset(['subject', 'description', 'priority', 'category', 'selectedContact', 'selectedAssignee', 'editingTicket']);
        $this->status = 'open';
    }

    public function editTicket($ticketId): void
    {
        $ticket = CustomerTicket::findOrFail($ticketId);
        $this->editingTicket = $ticket;
        $this->subject = $ticket->subject;
        $this->description = $ticket->description;
        $this->priority = $ticket->priority;
        $this->category = $ticket->category;
        $this->status = $ticket->status;
        $this->selectedContact = $ticket->contact_id;
        $this->selectedAssignee = $ticket->assigned_to;
        $this->showCreateModal = true;
    }

    public function viewTicket($ticketId): void
    {
        $this->viewingTicket = CustomerTicket::with(['contact', 'assignedUser', 'responses.user', 'responses.contact'])
            ->findOrFail($ticketId);
        $this->showDetailModal = true;
        $this->newResponseMessage = '';
        $this->isInternalNote = false;
    }

    public function saveTicket(): void
    {
        $this->validate();

        $data = [
            'contact_id' => $this->selectedContact,
            'assigned_to' => $this->selectedAssignee,
            'subject' => $this->subject,
            'description' => $this->description,
            'priority' => $this->priority,
            'category' => $this->category,
            'status' => $this->status,
        ];

        $wasResolved = false;

        if ($this->editingTicket) {
            $oldStatus = $this->editingTicket->status;
            $this->editingTicket->update($data);

            if ($this->status === 'resolved' && $oldStatus !== 'resolved') {
                $this->editingTicket->resolve();
                $wasResolved = true;
            }

            $this->editingTicket->updateSlaDue();
            $this->dispatch('ticket-updated', ticketId: $this->editingTicket->id);
        } else {
            $ticket = CustomerTicket::create($data);
            $ticket->updateSlaDue();
            $this->dispatch('ticket-created');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function addResponse(): void
    {
        $this->validate([
            'newResponseMessage' => ['required', 'min:1'],
        ]);

        if (! $this->viewingTicket) {
            return;
        }

        $this->viewingTicket->addResponse([
            'user_id' => auth()->id(),
            'message' => $this->newResponseMessage,
            'response_type' => 'agent',
            'is_internal_note' => $this->isInternalNote,
        ]);

        $this->viewingTicket->refresh();
        $this->newResponseMessage = '';
        $this->isInternalNote = false;

        $this->dispatch('response-added');
    }

    public function resolveTicket(): void
    {
        if (! $this->viewingTicket) {
            return;
        }

        $this->viewingTicket->resolve();
        $this->viewingTicket->refresh();

        $this->dispatch('ticket-resolved');
    }

    public function deleteTicket($ticketId): void
    {
        CustomerTicket::findOrFail($ticketId)->delete();
        $this->dispatch('ticket-deleted');
        $this->resetPage();
    }

    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->editingTicket = null;
        $this->reset(['subject', 'description', 'priority', 'category', 'selectedContact', 'selectedAssignee']);
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->viewingTicket = null;
        $this->newResponseMessage = '';
        $this->isInternalNote = false;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPriorityFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSlaFilter(): void
    {
        $this->resetPage();
    }
}
