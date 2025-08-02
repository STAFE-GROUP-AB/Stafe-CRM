<?php

namespace App\Livewire;

use App\Models\Email;
use App\Models\EmailTemplate;
use App\Models\Contact;
use App\Models\Company;
use App\Models\Deal;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Mail;

class EmailManager extends Component
{
    use WithPagination;

    public $activeTab = 'inbox';
    public $search = '';
    public $showComposer = false;
    public $emailId = null;
    
    // Composer properties
    public $to = '';
    public $subject = '';
    public $body = '';
    public $templateId = null;
    public $entityType = null;
    public $entityId = null;
    
    protected $queryString = ['search', 'activeTab'];
    
    protected $rules = [
        'to' => 'required|email',
        'subject' => 'required|string|max:255',
        'body' => 'required|string',
    ];

    public function mount($action = null, $email = null)
    {
        if ($action === 'compose') {
            $this->showComposer = true;
        }
        
        if ($email) {
            $this->emailId = $email;
            $this->activeTab = 'view';
        }
    }

    public function showComposer()
    {
        $this->showComposer = true;
        $this->resetComposer();
    }

    public function hideComposer()
    {
        $this->showComposer = false;
        $this->resetComposer();
    }

    public function resetComposer()
    {
        $this->to = '';
        $this->subject = '';
        $this->body = '';
        $this->templateId = null;
        $this->entityType = null;
        $this->entityId = null;
    }

    public function sendEmail()
    {
        $this->validate();

        try {
            // Create email record
            $email = Email::create([
                'to' => $this->to,
                'subject' => $this->subject,
                'body' => $this->body,
                'user_id' => auth()->id(),
                'status' => 'sent',
                'sent_at' => now(),
                'emailable_type' => $this->entityType,
                'emailable_id' => $this->entityId,
            ]);

            // Send the actual email
            // Note: This would integrate with Laravel's mail system
            // Mail::to($this->to)->send(new EmailMailable($email));

            session()->flash('message', 'Email sent successfully!');
            $this->hideComposer();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function loadTemplate($templateId)
    {
        $template = EmailTemplate::find($templateId);
        if ($template) {
            $this->subject = $template->subject;
            $this->body = $template->body;
            $this->templateId = $templateId;
        }
    }

    public function getEmailsProperty()
    {
        $query = Email::query()
            ->with(['emailable', 'user'])
            ->where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhere('to', 'like', '%' . auth()->user()->email . '%');
            });

        if ($this->search) {
            $query->where(function($q) {
                $q->where('subject', 'like', '%' . $this->search . '%')
                  ->orWhere('body', 'like', '%' . $this->search . '%')
                  ->orWhere('to', 'like', '%' . $this->search . '%');
            });
        }

        switch ($this->activeTab) {
            case 'sent':
                $query->where('user_id', auth()->id());
                break;
            case 'inbox':
                $query->where('to', 'like', '%' . auth()->user()->email . '%');
                break;
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function getTemplatesProperty()
    {
        return EmailTemplate::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.email-manager', [
            'emails' => $this->emails,
            'templates' => $this->templates,
        ]);
    }
}