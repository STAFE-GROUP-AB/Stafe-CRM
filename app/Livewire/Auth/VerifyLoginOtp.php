<?php

namespace App\Livewire\Auth;

use App\Models\TeamInvitation;
use App\Models\User;
use App\Services\DeviceTokenService;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Jetstream\Contracts\AddsTeamMembers;
use Livewire\Component;

class VerifyLoginOtp extends Component
{
    public $email = '';

    public $otp = '';

    public $remember_device = false;

    protected $rules = [
        'otp' => ['required', 'digits:6'],
    ];

    public function mount()
    {
        $this->email = session('otp_email');

        if (! $this->email) {
            return redirect()->route('login');
        }
    }

    public function verify(OtpService $otpService, DeviceTokenService $deviceService)
    {
        $this->validate();

        $key = 'otp-verify:'.$this->email;

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $this->addError('otp', __('Too many verification attempts. Please request a new code.'));

            return;
        }

        RateLimiter::hit($key, 600);

        if (! $otpService->verifyOtp($this->email, $this->otp, 'login')) {
            $this->addError('otp', __('Invalid or expired verification code.'));

            return;
        }

        $user = User::where('email', $this->email)->first();

        if (! $user) {
            $this->addError('email', __('User not found. Please register first.'));

            return redirect()->route('register');
        }

        $user->update([
            'last_login_at' => now(),
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);

        $invitationId = session('otp_invitation');
        if ($invitationId) {
            $invitation = TeamInvitation::find($invitationId);

            if ($invitation && $invitation->email === $user->email) {
                $invitedTeamId = $invitation->team->id;

                app(AddsTeamMembers::class)->add(
                    $invitation->team->owner,
                    $invitation->team,
                    $invitation->email,
                    $invitation->role
                );

                $invitation->delete();

                $user->forceFill([
                    'current_team_id' => $invitedTeamId,
                ])->save();

                $user->load('currentTeam');
            }
        }

        session()->regenerate();
        session()->forget(['otp_email', 'otp_invitation']);

        Auth::login($user);

        if ($this->remember_device) {
            $token = $deviceService->createDeviceToken($user, true);

            if ($token) {
                session(['pending_device_token' => $token]);
            }
        }

        return redirect()->intended(route('dashboard'));
    }

    public function resend(OtpService $otpService): void
    {
        $key = 'otp-resend:'.$this->email;

        if (RateLimiter::tooManyAttempts($key, 2)) {
            $seconds = RateLimiter::availableIn($key);
            session()->flash('error', __('Please wait :seconds seconds before requesting another code.', ['seconds' => $seconds]));

            return;
        }

        RateLimiter::hit($key, 600);

        $otp = $otpService->storeOtp(
            $this->email,
            'login',
            request()->ip(),
            request()->userAgent()
        );

        $otpService->sendOtp($this->email, $otp, 'login');

        session()->flash('message', __('A new verification code has been sent to your email.'));
    }

    public function render()
    {
        return view('livewire.auth.verify-login-otp')
            ->layout('layouts.guest');
    }
}
