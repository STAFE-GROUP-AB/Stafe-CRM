<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CustomerTicket;
use App\Models\Contact;
use App\Models\User;

class CustomerPortal extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $editingTicket = null;
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

    protected $rules = [
        'subject' => 'required|min:5|max:255',
        'description' => 'required|min:10',
        'priority' => 'required|in:low,medium,high,urgent',
        'category' => 'required|in:technical,billing,general,feature_request,bug_report',
        'status' => 'required|in:open,in_progress,waiting_customer,resolved,closed',
        'selectedContact' => 'required|exists:contacts,id',
        'selectedAssignee' => 'nullable|exists:users,id'
    ];

    public function render()
    {
        $tickets = CustomerTicket::with(['contact', 'assignedUser'])
            ->when($this->search, fn($query) => 
                $query->where('subject', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
            )
            ->when($this->statusFilter, fn($query) => 
                $query->where('status', $this->statusFilter)
            )
            ->when($this->priorityFilter, fn($query) => 
                $query->where('priority', $this->priorityFilter)
            )
            ->latest()
            ->paginate(10);

        $contacts = Contact::select('id', 'first_name', 'last_name', 'email')->get();
        $users = User::select('id', 'name')->get();

        $stats = [
            'total' => CustomerTicket::count(),
            'open' => CustomerTicket::open()->count(),
            'high_priority' => CustomerTicket::highPriority()->count(),
            'avg_response_time' => '2.4 hours' // This would be calculated
        ];

        return view('livewire.customer-portal', compact('tickets', 'contacts', 'users', 'stats'));
    }

    public function createTicket()
    {
        $this->showCreateModal = true;
        $this->reset(['subject', 'description', 'priority', 'category', 'selectedContact', 'selectedAssignee']);
    }

    public function editTicket($ticketId)
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

    public function saveTicket()
    {
        $this->validate();

        $data = [
            'contact_id' => $this->selectedContact,
            'assigned_to' => $this->selectedAssignee,
            'subject' => $this->subject,
            'description' => $this->description,
            'priority' => $this->priority,
            'category' => $this->category,
            'status' => $this->status
        ];

        if ($this->editingTicket) {
            $this->editingTicket->update($data);
            $this->dispatch('ticket-updated', ticketId: $this->editingTicket->id);
        } else {
            CustomerTicket::create($data);
            $this->dispatch('ticket-created');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function deleteTicket($ticketId)
    {
        CustomerTicket::findOrFail($ticketId)->delete();
        $this->dispatch('ticket-deleted');
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->editingTicket = null;
        $this->reset(['subject', 'description', 'priority', 'category', 'selectedContact', 'selectedAssignee']);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedPriorityFilter()
    {
        $this->resetPage();
    }
}