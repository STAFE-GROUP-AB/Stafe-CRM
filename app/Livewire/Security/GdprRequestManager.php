<?php

namespace App\Livewire\Security;

use App\Models\GdprDataRequest;
use App\Services\Security\GdprComplianceService;
use Livewire\Component;
use Livewire\WithPagination;

class GdprRequestManager extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $type = '';
    public $requesterEmail = '';
    public $description = '';
    public $selectedRequest = null;
    public $processingNotes = '';

    protected $rules = [
        'type' => 'required|in:access,portability,rectification,erasure,restriction',
        'requesterEmail' => 'required|email',
        'description' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        //
    }

    public function render()
    {
        $requests = GdprDataRequest::with(['requestable', 'processedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $pendingRequests = GdprDataRequest::pending()->count();
        $overdueRequests = GdprDataRequest::overdue()->count();

        return view('livewire.security.gdpr-request-manager', [
            'requests' => $requests,
            'pendingRequests' => $pendingRequests,
            'overdueRequests' => $overdueRequests,
        ]);
    }

    public function createRequest()
    {
        $this->validate();

        // This would typically find the requestable entity based on email or other criteria
        // For now, we'll assume it's related to a Contact
        $contact = \App\Models\Contact::where('email', $this->requesterEmail)->first();
        
        if (!$contact) {
            $this->addError('requesterEmail', 'No record found for this email address.');
            return;
        }

        $gdprService = app(GdprComplianceService::class);
        
        $gdprService->createDataRequest(
            $this->type,
            $this->requesterEmail,
            $contact,
            $this->description
        );

        $this->reset(['type', 'requesterEmail', 'description']);
        $this->showCreateModal = false;
        
        session()->flash('message', 'GDPR data request created successfully.');
    }

    public function processRequest($requestId, $action)
    {
        $request = GdprDataRequest::find($requestId);
        
        if (!$request) {
            return;
        }

        $gdprService = app(GdprComplianceService::class);

        switch ($action) {
            case 'approve':
                if ($request->type === 'portability') {
                    $data = $gdprService->exportPersonalData($request->requestable);
                    $request->update(['exported_data' => $data]);
                }
                $request->complete();
                break;
                
            case 'reject':
                $request->reject($this->processingNotes);
                break;
        }

        session()->flash('message', 'Request processed successfully.');
    }

    public function exportData($requestId)
    {
        $request = GdprDataRequest::find($requestId);
        
        if (!$request || $request->type !== 'portability') {
            return;
        }

        $gdprService = app(GdprComplianceService::class);
        $data = $gdprService->exportPersonalData($request->requestable);
        
        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT);
        }, "gdpr_export_{$request->id}.json", [
            'Content-Type' => 'application/json',
        ]);
    }
}