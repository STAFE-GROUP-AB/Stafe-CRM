<?php

namespace App\Livewire;

use App\Mail\QuoteMail;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Quote;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;

class QuoteBuilder extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $showLineItemsModal = false;
    public $showEmailModal = false;
    public $showPreviewModal = false;
    public $editingQuote = null;
    public $selectedQuote = null;
    public $quoteForLineItems = null;
    public $quoteForEmail = null;
    public $quoteForPreview = null;

    // Form fields
    public $title = '';
    public $description = '';
    public $deal_id = '';
    public $company_id = '';
    public $contact_id = '';
    public $valid_until = '';
    public $terms_and_conditions = [];

    // Email form fields
    public $emailRecipient = '';
    public $emailMessage = '';

    // Search and filters
    public $search = '';
    public $statusFilter = '';
    public $dealFilter = '';

    public function render()
    {
        $quotes = Quote::with(['deal', 'company', 'contact', 'createdBy'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('quote_number', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->dealFilter, function ($query) {
                $query->where('deal_id', $this->dealFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $deals = Deal::select('id', 'name')->get();
        $companies = Company::select('id', 'name')->get();

        return view('livewire.sales-enablement.quote-builder', [
            'quotes' => $quotes,
            'deals' => $deals,
            'companies' => $companies,
        ]);
    }

    public function createQuote()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function editQuote(Quote $quote)
    {
        $this->editingQuote = $quote;
        $this->title = $quote->title;
        $this->description = $quote->description;
        $this->deal_id = $quote->deal_id;
        $this->company_id = $quote->company_id;
        $this->contact_id = $quote->contact_id;
        $this->valid_until = $quote->valid_until?->format('Y-m-d');
        $this->terms_and_conditions = $quote->terms_and_conditions ?? [];
        $this->showCreateModal = true;
    }

    public function viewQuote(Quote $quote)
    {
        $this->selectedQuote = $quote;
    }

    public function saveQuote()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deal_id' => 'required|exists:deals,id',
            'company_id' => 'required|exists:companies,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'valid_until' => 'nullable|date|after:today',
        ]);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'deal_id' => $this->deal_id,
            'company_id' => $this->company_id,
            'contact_id' => $this->contact_id,
            'valid_until' => $this->valid_until,
            'terms_and_conditions' => $this->terms_and_conditions,
            'created_by' => auth()->id(),
        ];

        if ($this->editingQuote) {
            $this->editingQuote->update($data);
            $quote = $this->editingQuote;
            session()->flash('message', 'Quote updated successfully.');
        } else {
            $quote = Quote::create($data);
            session()->flash('message', 'Quote created successfully.');
        }

        $this->resetForm();
        $this->showCreateModal = false;

        $this->manageLineItems($quote);
    }

    public function manageLineItems(Quote $quote)
    {
        $this->quoteForLineItems = $quote->load('items');
        $this->showLineItemsModal = true;
    }

    public function closeLineItemsModal()
    {
        $this->showLineItemsModal = false;
        $this->quoteForLineItems = null;
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function deleteQuote(Quote $quote)
    {
        $quote->delete();
        session()->flash('message', 'Quote deleted successfully.');
    }

    public function sendQuote(Quote $quote)
    {
        $quote->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
        
        session()->flash('message', 'Quote sent successfully.');
    }

    public function duplicateQuote(Quote $quote)
    {
        $newQuote = $quote->replicate();
        $newQuote->quote_number = Quote::generateQuoteNumber();
        $newQuote->status = 'draft';
        $newQuote->sent_at = null;
        $newQuote->viewed_at = null;
        $newQuote->accepted_at = null;
        $newQuote->rejected_at = null;
        $newQuote->save();

        foreach ($quote->items as $item) {
            $newItem = $item->replicate();
            $newItem->quote_id = $newQuote->id;
            $newItem->save();
        }

        session()->flash('message', 'Offert duplicerad.');
    }

    public function openEmailModal(Quote $quote)
    {
        $this->quoteForEmail = $quote->load(['contact', 'company']);
        $this->emailRecipient = $quote->contact?->email ?? '';
        $this->emailMessage = '';
        $this->showEmailModal = true;
    }

    public function closeEmailModal()
    {
        $this->showEmailModal = false;
        $this->quoteForEmail = null;
        $this->emailRecipient = '';
        $this->emailMessage = '';
    }

    public function sendEmailQuote()
    {
        $this->validate([
            'emailRecipient' => ['required', 'email'],
            'emailMessage' => ['nullable', 'string', 'max:1000'],
        ]);

        if (!$this->quoteForEmail) {
            session()->flash('error', 'Ingen offert vald.');
            return;
        }

        $quote = $this->quoteForEmail->load(['items', 'company', 'contact', 'createdBy', 'signatures']);

        Mail::to($this->emailRecipient)
            ->send(new QuoteMail($quote, $this->emailMessage ?? ''));

        $quote->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $this->closeEmailModal();
        session()->flash('message', "Offert har skickats till {$this->emailRecipient}.");
    }

    public function openPreviewModal(Quote $quote)
    {
        $this->quoteForPreview = $quote->load(['items', 'company', 'contact', 'createdBy', 'signatures']);
        $this->showPreviewModal = true;
    }

    public function closePreviewModal()
    {
        $this->showPreviewModal = false;
        $this->quoteForPreview = null;
    }

    /**
     * Generate and download PDF for a quote
     */
    public function downloadPdf(Quote $quote)
    {
        $quote->load(['items', 'company', 'contact', 'createdBy', 'signatures']);
        
        $pdf = Pdf::loadView('sales-enablement.quote-pdf', ['quote' => $quote])
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);
        
        $filename = 'quote-' . $quote->quote_number . '-' . now()->format('Y-m-d') . '.pdf';
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Generate PDF preview (base64 encoded for iframe)
     */
    public function generatePdfPreview(Quote $quote)
    {
        $quote->load(['items', 'company', 'contact', 'createdBy', 'signatures']);
        
        $pdf = Pdf::loadView('sales-enablement.quote-pdf', ['quote' => $quote])
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);
        
        return base64_encode($pdf->output());
    }

    public function updatedDealId()
    {
        if ($this->deal_id) {
            $deal = Deal::with(['company', 'contact'])->find($this->deal_id);
            if ($deal) {
                $this->company_id = $deal->company_id;
                $this->contact_id = $deal->contact_id;
            }
        }
    }

    public function getContactsProperty()
    {
        if (!$this->company_id) {
            return collect();
        }
        
        return Contact::where('company_id', $this->company_id)
            ->select('id', 'first_name', 'last_name')
            ->get();
    }

    private function resetForm()
    {
        $this->editingQuote = null;
        $this->title = '';
        $this->description = '';
        $this->deal_id = '';
        $this->company_id = '';
        $this->contact_id = '';
        $this->valid_until = '';
        $this->terms_and_conditions = [];
    }
}