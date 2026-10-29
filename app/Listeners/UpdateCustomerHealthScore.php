<?php

namespace App\Listeners;

use App\Events\CommunicationLogged;
use App\Events\DealStatusChanged;
use App\Events\SurveyResponseSubmitted;
use App\Events\TicketCreated;
use App\Events\TicketResolved;
use App\Models\Contact;
use App\Services\CustomerHealthService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateCustomerHealthScore implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying.
     */
    public int $backoff = 60;

    public function __construct(
        protected CustomerHealthService $healthService
    ) {}

    /**
     * Handle ticket created event.
     */
    public function handleTicketCreated(TicketCreated $event): void
    {
        $this->updateHealthScore($event->ticket->contact_id, 'ticket_created');
    }

    /**
     * Handle ticket resolved event.
     */
    public function handleTicketResolved(TicketResolved $event): void
    {
        $this->updateHealthScore($event->ticket->contact_id, 'ticket_resolved');
    }

    /**
     * Handle deal status changed event.
     */
    public function handleDealStatusChanged(DealStatusChanged $event): void
    {
        $this->updateHealthScore($event->deal->contact_id, 'deal_status_changed');
    }

    /**
     * Handle survey response submitted event.
     */
    public function handleSurveyResponseSubmitted(SurveyResponseSubmitted $event): void
    {
        $this->updateHealthScore($event->response->contact_id, 'survey_submitted');
    }

    /**
     * Handle communication logged event.
     */
    public function handleCommunicationLogged(CommunicationLogged $event): void
    {
        $communication = $event->communication;

        if ($communication->communicable_type !== Contact::class) {
            return;
        }

        $this->updateHealthScore($communication->communicable_id, 'communication_logged');
    }

    /**
     * Update health score for a contact.
     */
    protected function updateHealthScore(?int $contactId, string $trigger): void
    {
        if (!$contactId) {
            return;
        }

        $contact = Contact::find($contactId);

        if (!$contact) {
            return;
        }

        try {
            $this->healthService->calculateForContact($contact, $trigger);
        } catch (\Exception $e) {
            Log::error("Failed to update health score for contact {$contactId}: {$e->getMessage()}");
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('UpdateCustomerHealthScore job failed: ' . $exception->getMessage());
    }
}
