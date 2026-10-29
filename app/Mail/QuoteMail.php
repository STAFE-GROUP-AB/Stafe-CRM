<?php

namespace App\Mail;

use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Quote $quote,
        public string $personalMessage = ''
    ) {}

    public function envelope(): Envelope
    {
        $companyName = \App\Models\SystemSetting::get('app_name', config('app.name', 'Stafe CRM'));

        return new Envelope(
            subject: "Offert {$this->quote->quote_number} - {$this->quote->title}",
            replyTo: auth()->user()?->email ?? config('mail.from.address'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quote',
            with: [
                'quote' => $this->quote,
                'personalMessage' => $this->personalMessage,
                'senderName' => auth()->user()?->name ?? 'Stafe CRM',
                'companyName' => \App\Models\SystemSetting::get('app_name', config('app.name', 'Stafe CRM')),
                'signUrl' => route('sales-enablement.quotes.sign', $this->quote),
                'previewUrl' => route('sales-enablement.quotes.preview', $this->quote),
            ],
        );
    }

    public function attachments(): array
    {
        $this->quote->load(['items', 'company', 'contact', 'createdBy', 'signatures']);

        $pdf = Pdf::loadView('sales-enablement.quote-pdf', ['quote' => $this->quote]);

        return [
            Attachment::fromData(fn () => $pdf->output(), "offert-{$this->quote->quote_number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
