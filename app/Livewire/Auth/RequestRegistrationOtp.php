<?php

namespace App\Livewire\Auth;

use App\Services\OtpService;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Jetstream\Jetstream;
use Livewire\Component;

class RequestRegistrationOtp extends Component
{
    public $name = '';

    public $email = '';

    public $invitation = null;

    public $terms = false;

    protected function rules()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ];

        if (Jetstream::hasTermsAndPrivacyPolicyFeature()) {
            $rules['terms'] = ['accepted', 'required'];
        }

        return $rules;
    }

    public function mount(): void
    {
        $this->invitation = request()->query('invitation');
        $this->email = request()->query('email', '');
    }

    public function sendOtp(OtpService $otpService)
    {
        $this->validate();

        $key = 'otp-request:'.$this->email;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = (int) ceil($seconds / 60);
            $this->addError('email', __('Too many OTP requests. Please try again in :minutes minutes.', ['minutes' => $minutes]));

            return;
        }

        RateLimiter::hit($key, 3600);

        $otp = $otpService->storeOtp(
            $this->email,
            'registration',
            request()->ip(),
            request()->userAgent()
        );

        $otpService->sendOtp($this->email, $otp, 'registration');

        session([
            'otp_email' => $this->email,
            'otp_name' => $this->name,
            'otp_invitation' => $this->invitation,
            'otp_terms_accepted' => $this->terms,
        ]);

        return redirect()->route('register.verify');
    }

    public function render()
    {
        return view('livewire.auth.request-registration-otp')
            ->layout('layouts.guest');
    }
}
