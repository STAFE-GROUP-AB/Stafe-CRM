<?php

namespace App\Livewire\Auth;

use App\Actions\Jetstream\CreateTeam;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Services\DeviceTokenService;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Jetstream\Contracts\AddsTeamMembers;
use Livewire\Component;

class VerifyRegistrationOtp extends Component
{
    public $name = '';

    public $email = '';

    public $otp = '';

    public $remember_device = false;

    protected $rules = [
        'otp' => ['required', 'digits:6'],
    ];

    public function mount()
    {
        $this->email = session('otp_email');
        $this->name = session('otp_name');

        if (! $this->email || ! $this->name) {
            return redirect()->route('register');
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

        if (! $otpService->verifyOtp($this->email, $this->otp, 'registration')) {
            $this->addError('otp', __('Invalid or expired verification code.'));

            return;
        }

        $invitationId = session('otp_invitation');
        $invitation = null;
        if ($invitationId) {
            $invitation = TeamInvitation::find($invitationId);
        }

        $termsAccepted = session('otp_terms_accepted');

        $createdTeam = null;

        $user = DB::transaction(function () use ($invitation, $termsAccepted, &$createdTeam) {
            $userData = [
                'name' => $this->name,
                'email' => $this->email,
                'email_verified_at' => now(),
                'last_login_at' => now(),
            ];

            $user = User::create($userData);

            if (! $invitation) {
                $createdTeam = app(CreateTeam::class)->create($user, ['name' => explode(' ', $user->name, 2)[0]."'s Team"]);
            }

            return $user;
        });

        $invitedTeamId = null;

        if ($invitation && $invitation->email === $user->email) {
            app(AddsTeamMembers::class)->add(
                $invitation->team->owner,
                $invitation->team,
                $invitation->email,
                $invitation->role
            );

            $invitedTeamId = $invitation->team->id;
            $invitation->delete();
        }

        $user->refresh();

        if ($createdTeam) {
            $user->forceFill(['current_team_id' => $createdTeam->id])->save();
        } elseif ($invitedTeamId) {
            $user->forceFill(['current_team_id' => $invitedTeamId])->save();
        }

        $user->refresh();
        $user->load(['ownedTeams', 'currentTeam']);

        session()->regenerate();
        session()->forget(['otp_email', 'otp_name', 'otp_invitation', 'otp_terms_accepted']);

        Auth::login($user);

        $redirectUrl = route('dashboard');

        if ($this->remember_device) {
            $token = $deviceService->createDeviceToken($user, true);

            if ($token) {
                session(['pending_device_token' => $token]);
            }
        }

        return redirect($redirectUrl);
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
            'registration',
            request()->ip(),
            request()->userAgent()
        );

        $otpService->sendOtp($this->email, $otp, 'registration');

        session()->flash('message', __('A new verification code has been sent to your email.'));
    }

    public function render()
    {
        return view('livewire.auth.verify-registration-otp')
            ->layout('layouts.guest');
    }
}
