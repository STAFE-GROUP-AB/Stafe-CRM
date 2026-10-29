<?php

namespace App\Notifications;

use App\Models\CustomerHealthScore;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HealthScoreDroppedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public CustomerHealthScore $healthScore
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $contact = $this->healthScore->contact;
        $status = ucfirst($this->healthScore->health_status);
        $score = round($this->healthScore->overall_score, 1);
        $previousScore = $this->healthScore->previous_score
            ? round($this->healthScore->previous_score, 1)
            : null;

        $subject = $this->healthScore->health_status === 'critical'
            ? "URGENT: Customer {$contact->full_name} is in Critical Health Status"
            : "Alert: Customer {$contact->full_name} Health Score Has Dropped";

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting("Customer Health Alert")
            ->line("A customer requires your attention due to a significant health score change.");

        $mailMessage->line('');
        $mailMessage->line("**Customer:** {$contact->full_name}");
        $mailMessage->line("**Current Status:** {$status}");
        $mailMessage->line("**Current Score:** {$score}/100");

        if ($previousScore !== null) {
            $change = round($score - $previousScore, 1);
            $changeText = $change >= 0 ? "+{$change}" : $change;
            $mailMessage->line("**Previous Score:** {$previousScore}/100 ({$changeText})");
        }

        if (!empty($this->healthScore->risk_factors)) {
            $mailMessage->line('');
            $mailMessage->line("**Risk Factors Identified:**");
            foreach (array_slice($this->healthScore->risk_factors, 0, 3) as $risk) {
                $riskDesc = is_array($risk) ? ($risk['description'] ?? $risk['factor']) : $risk;
                $mailMessage->line("- {$riskDesc}");
            }
        }

        if (!empty($this->healthScore->improvement_suggestions)) {
            $mailMessage->line('');
            $mailMessage->line("**Recommended Actions:**");
            foreach (array_slice($this->healthScore->improvement_suggestions, 0, 3) as $suggestion) {
                $suggestionText = is_array($suggestion) ? ($suggestion['action'] ?? '') : $suggestion;
                if ($suggestionText) {
                    $mailMessage->line("- {$suggestionText}");
                }
            }
        }

        $mailMessage->line('');
        $mailMessage->action('View Customer Details', url("/contacts/{$contact->id}"));
        $mailMessage->line('');
        $mailMessage->line('Please take appropriate action to address the customer health concerns.');
        $mailMessage->salutation("Regards,\n" . config('app.name'));

        return $mailMessage;
    }

    public function toArray(object $notifiable): array
    {
        $contact = $this->healthScore->contact;

        return [
            'type' => 'health_score_dropped',
            'title' => "Customer Health Alert: {$contact->full_name}",
            'message' => "Health score dropped to {$this->healthScore->overall_score} ({$this->healthScore->health_status})",
            'contact_id' => $contact->id,
            'contact_name' => $contact->full_name,
            'health_status' => $this->healthScore->health_status,
            'current_score' => $this->healthScore->overall_score,
            'previous_score' => $this->healthScore->previous_score,
            'risk_factors' => $this->healthScore->risk_factors,
            'url' => "/contacts/{$contact->id}",
        ];
    }
}
