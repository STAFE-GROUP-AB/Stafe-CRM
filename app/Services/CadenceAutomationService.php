<?php

namespace App\Services;

use App\Models\CadenceSequence;
use App\Models\SequenceEnrollment;
use App\Models\SequenceStep;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Task;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CadenceAutomationService
{
    /**
     * Process all active sequence enrollments
     */
    public function processEnrollments(): array
    {
        $processed = 0;
        $failed = 0;
        $errors = [];

        $dueEnrollments = SequenceEnrollment::active()
            ->whereHas('currentStep', function ($query) {
                $query->where('scheduled_at', '<=', now());
            })
            ->with(['sequence.steps', 'contact', 'currentStep'])
            ->get();

        foreach ($dueEnrollments as $enrollment) {
            try {
                $this->processEnrollmentStep($enrollment);
                $processed++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = [
                    'enrollment_id' => $enrollment->id,
                    'error' => $e->getMessage(),
                ];
                Log::error('Failed to process sequence enrollment', [
                    'enrollment_id' => $enrollment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'processed' => $processed,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }

    /**
     * Process a single enrollment step
     */
    protected function processEnrollmentStep(SequenceEnrollment $enrollment): void
    {
        $currentStep = $enrollment->currentStep;

        if (!$currentStep) {
            $this->completeEnrollment($enrollment);
            return;
        }

        // Execute the step action
        $success = $this->executeStepAction($enrollment, $currentStep);

        if ($success) {
            // Mark step as completed
            $enrollment->completeCurrentStep();

            // Move to next step
            $nextStep = $enrollment->getNextStep();

            if ($nextStep) {
                $this->scheduleNextStep($enrollment, $nextStep);
            } else {
                $this->completeEnrollment($enrollment);
            }
        } else {
            // Handle failure
            $enrollment->recordStepFailure($currentStep, 'Step execution failed');
        }
    }

    /**
     * Execute step action based on step type
     */
    protected function executeStepAction(SequenceEnrollment $enrollment, SequenceStep $step): bool
    {
        return match ($step->action_type) {
            'email' => $this->sendSequenceEmail($enrollment, $step),
            'task' => $this->createSequenceTask($enrollment, $step),
            'wait' => $this->processWaitStep($enrollment, $step),
            'sms' => $this->sendSequenceSms($enrollment, $step),
            'call' => $this->scheduleSequenceCall($enrollment, $step),
            default => false,
        };
    }

    /**
     * Send email for sequence step
     */
    protected function sendSequenceEmail(SequenceEnrollment $enrollment, SequenceStep $step): bool
    {
        try {
            $contact = $enrollment->contact;
            $config = $step->action_config;

            $subject = $this->personalizeContent($config['subject'] ?? 'Follow-up', $contact);
            $body = $this->personalizeContent($config['body'] ?? '', $contact);

            $email = Email::create([
                'direction' => 'outbound',
                'from_email' => auth()->user()->email ?? config('mail.from.address'),
                'from_name' => auth()->user()->name ?? config('mail.from.name'),
                'to_recipients' => [$contact->email],
                'subject' => $subject,
                'body_html' => $body,
                'status' => 'pending',
                'emailable_type' => Contact::class,
                'emailable_id' => $contact->id,
                'user_id' => $enrollment->user_id,
            ]);

            // Send email
            Mail::to($contact->email)->send(new \App\Mail\GenericEmail($email));

            $email->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            Log::info('Sequence email sent', [
                'enrollment_id' => $enrollment->id,
                'step_id' => $step->id,
                'email_id' => $email->id,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send sequence email', [
                'enrollment_id' => $enrollment->id,
                'step_id' => $step->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Create task for sequence step
     */
    protected function createSequenceTask(SequenceEnrollment $enrollment, SequenceStep $step): bool
    {
        try {
            $contact = $enrollment->contact;
            $config = $step->action_config;

            Task::create([
                'title' => $config['title'] ?? 'Follow-up with ' . $contact->first_name,
                'description' => $config['description'] ?? '',
                'type' => $config['task_type'] ?? 'call',
                'priority' => $config['priority'] ?? 'medium',
                'status' => 'pending',
                'due_date' => now()->addDays($config['due_in_days'] ?? 1),
                'assigned_to' => $enrollment->user_id,
                'taskable_type' => Contact::class,
                'taskable_id' => $contact->id,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to create sequence task', [
                'enrollment_id' => $enrollment->id,
                'step_id' => $step->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Process wait step
     */
    protected function processWaitStep(SequenceEnrollment $enrollment, SequenceStep $step): bool
    {
        // Wait steps are automatically processed by scheduling
        return true;
    }

    /**
     * Send SMS for sequence step
     */
    protected function sendSequenceSms(SequenceEnrollment $enrollment, SequenceStep $step): bool
    {
        try {
            $contact = $enrollment->contact;
            $config = $step->action_config;

            if (!$contact->mobile && !$contact->phone) {
                Log::warning('No phone number for SMS', [
                    'enrollment_id' => $enrollment->id,
                    'contact_id' => $contact->id,
                ]);
                return false;
            }

            $message = $this->personalizeContent($config['message'] ?? '', $contact);
            $phone = $contact->mobile ?? $contact->phone;

            // Use Twilio service to send SMS
            $twilioService = app(\App\Services\TwilioService::class);
            $twilioService->sendSms($phone, $message);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send sequence SMS', [
                'enrollment_id' => $enrollment->id,
                'step_id' => $step->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Schedule call for sequence step
     */
    protected function scheduleSequenceCall(SequenceEnrollment $enrollment, SequenceStep $step): bool
    {
        try {
            $contact = $enrollment->contact;
            $config = $step->action_config;

            Task::create([
                'title' => 'Call: ' . $contact->first_name . ' ' . $contact->last_name,
                'description' => $config['call_notes'] ?? 'Scheduled call from cadence sequence',
                'type' => 'call',
                'priority' => $config['priority'] ?? 'medium',
                'status' => 'pending',
                'due_date' => now()->addHours($config['schedule_in_hours'] ?? 24),
                'assigned_to' => $enrollment->user_id,
                'taskable_type' => Contact::class,
                'taskable_id' => $contact->id,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to schedule sequence call', [
                'enrollment_id' => $enrollment->id,
                'step_id' => $step->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Schedule next step
     */
    protected function scheduleNextStep(SequenceEnrollment $enrollment, SequenceStep $nextStep): void
    {
        $delay = $nextStep->delay_value ?? 1;
        $delayUnit = $nextStep->delay_unit ?? 'days';

        $scheduledAt = match ($delayUnit) {
            'minutes' => now()->addMinutes($delay),
            'hours' => now()->addHours($delay),
            'days' => now()->addDays($delay),
            'weeks' => now()->addWeeks($delay),
            default => now()->addDays($delay),
        };

        $enrollment->update([
            'current_step_id' => $nextStep->id,
            'current_step_scheduled_at' => $scheduledAt,
        ]);
    }

    /**
     * Complete enrollment
     */
    protected function completeEnrollment(SequenceEnrollment $enrollment): void
    {
        $enrollment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        Log::info('Sequence enrollment completed', [
            'enrollment_id' => $enrollment->id,
            'sequence_id' => $enrollment->sequence_id,
        ]);
    }

    /**
     * Enroll contact in sequence
     */
    public function enrollContact(Contact $contact, CadenceSequence $sequence, ?int $userId = null): SequenceEnrollment
    {
        // Check if contact is already enrolled
        $existing = SequenceEnrollment::where('contact_id', $contact->id)
            ->where('sequence_id', $sequence->id)
            ->whereIn('status', ['active', 'paused'])
            ->first();

        if ($existing) {
            return $existing;
        }

        // Create enrollment
        $enrollment = SequenceEnrollment::create([
            'sequence_id' => $sequence->id,
            'contact_id' => $contact->id,
            'user_id' => $userId ?? auth()->id(),
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        // Schedule first step
        $firstStep = $sequence->steps()->orderBy('step_order')->first();

        if ($firstStep) {
            $this->scheduleNextStep($enrollment, $firstStep);
        }

        return $enrollment;
    }

    /**
     * Pause enrollment
     */
    public function pauseEnrollment(SequenceEnrollment $enrollment, ?string $reason = null): void
    {
        $enrollment->update([
            'status' => 'paused',
            'paused_at' => now(),
            'pause_reason' => $reason,
        ]);
    }

    /**
     * Resume enrollment
     */
    public function resumeEnrollment(SequenceEnrollment $enrollment): void
    {
        $enrollment->update([
            'status' => 'active',
            'paused_at' => null,
            'pause_reason' => null,
        ]);

        // Reschedule current step if needed
        if ($enrollment->currentStep && !$enrollment->current_step_scheduled_at) {
            $this->scheduleNextStep($enrollment, $enrollment->currentStep);
        }
    }

    /**
     * Unenroll contact from sequence
     */
    public function unenrollContact(SequenceEnrollment $enrollment, string $reason): void
    {
        $enrollment->update([
            'status' => 'unenrolled',
            'unenrolled_at' => now(),
            'unenroll_reason' => $reason,
        ]);
    }

    /**
     * Personalize content with contact data
     */
    protected function personalizeContent(string $content, Contact $contact): string
    {
        $variables = [
            '{{first_name}}' => $contact->first_name,
            '{{last_name}}' => $contact->last_name,
            '{{full_name}}' => $contact->first_name . ' ' . $contact->last_name,
            '{{email}}' => $contact->email,
            '{{company}}' => $contact->company?->name ?? '',
            '{{title}}' => $contact->title ?? '',
            '{{city}}' => $contact->city ?? '',
        ];

        return str_replace(array_keys($variables), array_values($variables), $content);
    }

    /**
     * Get enrollment statistics
     */
    public function getEnrollmentStats(CadenceSequence $sequence): array
    {
        $enrollments = $sequence->enrollments;

        return [
            'total_enrollments' => $enrollments->count(),
            'active' => $enrollments->where('status', 'active')->count(),
            'completed' => $enrollments->where('status', 'completed')->count(),
            'paused' => $enrollments->where('status', 'paused')->count(),
            'unenrolled' => $enrollments->where('status', 'unenrolled')->count(),
            'completion_rate' => $enrollments->count() > 0 
                ? round(($enrollments->where('status', 'completed')->count() / $enrollments->count()) * 100, 2) 
                : 0,
        ];
    }
}
