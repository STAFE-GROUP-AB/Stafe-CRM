<?php

namespace App\Mail;

use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GenericEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Email $email
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->email->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.generic',
            with: [
                'emailBody' => $this->email->body,
                'senderName' => $this->email->user?->name ?? 'Stafe CRM',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
