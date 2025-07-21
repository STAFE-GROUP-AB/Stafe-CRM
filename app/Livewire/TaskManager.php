<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\User;
use App\Models\Contact;
use App\Models\Company;
use App\Models\Deal;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class TaskManager extends Component
{
    use WithPagination;

    // View state
    public $view = 'list'; // list, create, edit, show
    public $selectedTask = null;
    
    // Search and filters
    public $search = '';
    public $statusFilter = '';
    public $priorityFilter = '';
    public $typeFilter = '';
    public $assignedToFilter = '';
    public $overdueOnly = false;
    public $perPage = 10;
    
    // Form fields
    public $title = '';
    public $description = '';
    public $type = 'task';
    public $priority = 'medium';
    public $status = 'pending';
    public $due_date = '';
    public $completed_at = '';
    public $duration_minutes = '';
    public $location = '';
    public $attendees = [];
    public $taskable_type = '';
    public $taskable_id = '';
    public $assigned_to = '';
    public $custom_fields = [];
    
    // Related entity selection
    public $relatedEntityType = '';
    public $relatedEntityId = '';
    
    // Bulk operations
    public $selectAll = false;
    public $selectedIds = [];
    public $bulkAction = '';
    
    // Modal states
    public $showDeleteModal = false;
    public $showBulkDeleteModal = false;
    public $taskToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'assignedToFilter' => ['except' => ''],
        'overdueOnly' => ['except' => false],
        'perPage' => ['except' => 10],
    ];

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'type' => 'required|in:task,call,meeting,email,followup,deadline',
        'priority' => 'required|in:low,medium,high,urgent',
        'status' => 'required|in:pending,in_progress,completed,cancelled',
        'due_date' => 'nullable|date',
        'completed_at' => 'nullable|date',
        'duration_minutes' => 'nullable|integer|min:0',
        'location' => 'nullable|string|max:255',
        'assigned_to' => 'required|exists:users,id',
    ];

    public function mount()
    {
        $this->assigned_to = auth()->id();
        $this->due_date = Carbon::now()->addDay()->format('Y-m-d H:i');
        
        // Check if we should open create form
        if (request()->query('action') === 'create') {
            $this->create();
        }
    }

    public function render()
    {
        $data = [
            'tasks' => $this->getTasks(),
            'users' => User::select('id', 'name')->get(),
            'contacts' => Contact::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'companies' => Company::select('id', 'name')->orderBy('name')->get(),
            'deals' => Deal::select('id', 'name')->orderBy('name')->get(),
            'types' => $this->getTypes(),
            'priorities' => $this->getPriorities(),
            'statuses' => $this->getStatuses(),
        ];

        return view('livewire.task-manager', $data);
    }

    public function getTasks()
    {
        if ($this->view !== 'list') {
            return collect();
        }

        return Task::with(['assignedTo', 'createdBy', 'taskable'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('location', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->priorityFilter, function ($query) {
                $query->where('priority', $this->priorityFilter);
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->assignedToFilter, function ($query) {
                $query->where('assigned_to', $this->assignedToFilter);
            })
            ->when($this->overdueOnly, function ($query) {
                $query->overdue();
            })
            ->orderBy('due_date', 'asc')
            ->orderBy('priority', 'desc')
            ->paginate($this->perPage);
    }

    public function create()
    {
        $this->reset(['selectedTask']);
        $this->resetForm();
        $this->view = 'create';
    }

    public function store()
    {
        $this->validate();

        $data = $this->getFormData();
        $data['created_by'] = auth()->id();
        
        // Handle related entity
        if ($this->relatedEntityType && $this->relatedEntityId) {
            $data['taskable_type'] = $this->getTaskableType($this->relatedEntityType);
            $data['taskable_id'] = $this->relatedEntityId;
        }

        Task::create($data);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Task created successfully!'
        ]);

        $this->view = 'list';
        $this->resetForm();
    }

    public function show($id)
    {
        $this->selectedTask = Task::with(['assignedTo', 'createdBy', 'taskable'])
            ->findOrFail($id);
        $this->view = 'show';
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $this->selectedTask = $task;
        $this->fillForm($task);
        $this->view = 'edit';
    }

    public function update()
    {
        $this->validate();

        $data = $this->getFormData();
        
        // Handle related entity
        if ($this->relatedEntityType && $this->relatedEntityId) {
            $data['taskable_type'] = $this->getTaskableType($this->relatedEntityType);
            $data['taskable_id'] = $this->relatedEntityId;
        } else {
            $data['taskable_type'] = null;
            $data['taskable_id'] = null;
        }

        $this->selectedTask->update($data);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Task updated successfully!'
        ]);

        $this->view = 'show';
        $this->resetForm();
    }

    public function markAsComplete($id)
    {
        $task = Task::findOrFail($id);
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Task marked as complete!'
        ]);
    }

    public function markAsIncomplete($id)
    {
        $task = Task::findOrFail($id);
        $task->update([
            'status' => 'pending',
            'completed_at' => null,
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Task marked as incomplete!'
        ]);
    }

    public function confirmDelete($id)
    {
        $this->taskToDelete = Task::findOrFail($id);
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->taskToDelete) {
            $this->taskToDelete->delete();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Task deleted successfully!'
            ]);
        }

        $this->showDeleteModal = false;
        $this->taskToDelete = null;
        
        if ($this->view === 'show' || $this->view === 'edit') {
            $this->view = 'list';
        }
    }

    public function backToList()
    {
        $this->view = 'list';
        $this->reset(['selectedTask']);
        $this->resetForm();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->getTasks()->pluck('id')->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedSelectedIds()
    {
        $this->selectAll = count($this->selectedIds) === $this->getTasks()->count();
    }

    public function updatedRelatedEntityType($value)
    {
        $this->relatedEntityId = '';
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Please select at least one task.'
            ]);
            return;
        }

        switch ($this->bulkAction) {
            case 'delete':
                $this->showBulkDeleteModal = true;
                break;
            case 'complete':
                $this->bulkComplete();
                break;
            case 'change_status':
                // TODO: Implement bulk status change
                break;
            case 'reassign':
                // TODO: Implement bulk reassign
                break;
        }
    }

    public function bulkDelete()
    {
        Task::whereIn('id', $this->selectedIds)->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => count($this->selectedIds) . ' tasks deleted successfully!'
        ]);

        $this->showBulkDeleteModal = false;
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function bulkComplete()
    {
        Task::whereIn('id', $this->selectedIds)->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => count($this->selectedIds) . ' tasks marked as complete!'
        ]);

        $this->selectedIds = [];
        $this->selectAll = false;
    }

    private function resetForm()
    {
        $this->reset([
            'title', 'description', 'type', 'priority', 'status',
            'due_date', 'completed_at', 'duration_minutes', 'location',
            'attendees', 'taskable_type', 'taskable_id', 'custom_fields',
            'relatedEntityType', 'relatedEntityId'
        ]);
        
        $this->assigned_to = auth()->id();
        $this->type = 'task';
        $this->priority = 'medium';
        $this->status = 'pending';
        $this->due_date = Carbon::now()->addDay()->format('Y-m-d H:i');
        $this->resetValidation();
    }

    private function fillForm($task)
    {
        $this->title = $task->title;
        $this->description = $task->description ?? '';
        $this->type = $task->type ?? 'task';
        $this->priority = $task->priority ?? 'medium';
        $this->status = $task->status ?? 'pending';
        $this->due_date = $task->due_date?->format('Y-m-d H:i') ?? '';
        $this->completed_at = $task->completed_at?->format('Y-m-d H:i') ?? '';
        $this->duration_minutes = $task->duration_minutes ?? '';
        $this->location = $task->location ?? '';
        $this->attendees = $task->attendees ?? [];
        $this->assigned_to = $task->assigned_to;
        $this->custom_fields = $task->custom_fields ?? [];
        
        // Handle related entity
        if ($task->taskable_type && $task->taskable_id) {
            $this->relatedEntityType = $this->getEntityTypeFromTaskable($task->taskable_type);
            $this->relatedEntityId = $task->taskable_id;
        }
    }

    private function getFormData()
    {
        return [
            'title' => $this->title,
            'description' => $this->description ?: null,
            'type' => $this->type,
            'priority' => $this->priority,
            'status' => $this->status,
            'due_date' => $this->due_date ?: null,
            'completed_at' => $this->completed_at ?: null,
            'duration_minutes' => $this->duration_minutes ?: null,
            'location' => $this->location ?: null,
            'attendees' => $this->attendees,
            'assigned_to' => $this->assigned_to,
            'custom_fields' => $this->custom_fields,
        ];
    }

    private function getTaskableType($entityType)
    {
        return match($entityType) {
            'contact' => 'App\Models\Contact',
            'company' => 'App\Models\Company',
            'deal' => 'App\Models\Deal',
            default => null,
        };
    }

    private function getEntityTypeFromTaskable($taskableType)
    {
        return match($taskableType) {
            'App\Models\Contact' => 'contact',
            'App\Models\Company' => 'company',
            'App\Models\Deal' => 'deal',
            default => '',
        };
    }

    private function getTypes()
    {
        return [
            'task' => 'Task',
            'call' => 'Call',
            'meeting' => 'Meeting',
            'email' => 'Email',
            'followup' => 'Follow-up',
            'deadline' => 'Deadline',
        ];
    }

    private function getPriorities()
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];
    }

    private function getStatuses()
    {
        return [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }
}