<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class RequestLoginOtp extends Component
{
    public $email = '';

    public $invitation = null;

    protected $rules = [
        'email' => ['required', 'string', 'email', 'max:255'],
    ];

    public function mount(): void
    {
        $this->invitation = request()->query('invitation');
        $this->email = request()->query('email', '');
    }

    public function sendOtp(OtpService $otpService)
    {
        $this->validate();

        $user = User::where('email', $this->email)->first();

        if (! $user) {
            $this->addError('email', __('No account found with this email. Please register first.'));

            return;
        }

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
            'login',
            request()->ip(),
            request()->userAgent()
        );

        $otpService->sendOtp($this->email, $otp, 'login');

        session([
            'otp_email' => $this->email,
            'otp_invitation' => $this->invitation,
        ]);

        return redirect()->route('login.verify');
    }

    public function render()
    {
        return view('livewire.auth.request-login-otp')
            ->layout('layouts.guest');
    }
}
