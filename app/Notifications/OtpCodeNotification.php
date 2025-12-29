<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $otp,
        public string $purpose,
        public string $userLocale = 'en'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $originalLocale = app()->getLocale();
        app()->setLocale($this->userLocale);

        $subject = match ($this->purpose) {
            'login' => __('Your Login Verification Code'),
            'registration' => __('Your Registration Verification Code'),
            default => __('Your Verification Code'),
        };

        $greeting = match ($this->purpose) {
            'login' => __('Login Verification'),
            'registration' => __('Registration Verification'),
            default => __('Email Verification'),
        };

        $purposeMessage = match ($this->purpose) {
            'login' => __('Use the code below to complete your login to :app.', ['app' => config('app.name')]),
            'registration' => __('Use the code below to verify your email and complete your registration.'),
            default => __('Use the code below to verify your email address.'),
        };

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($purposeMessage)
            ->line('')
            ->line(__('Your verification code is:'))
            ->line('')
            ->line('**'.$this->otp.'**')
            ->line('')
            ->line(__('This code expires in :minutes minutes.', ['minutes' => 10]))
            ->line('')
            ->line(__('For your security, do not share this code with anyone. Our team will never ask you for this code.'))
            ->line('')
            ->line(__('If you did not request this code, you can safely ignore this email.'))
            ->salutation(__('Regards,')."\n".config('app.name'));

        app()->setLocale($originalLocale);

        return $mailMessage;
    }
}
