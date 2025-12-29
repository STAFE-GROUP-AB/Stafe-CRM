<?php

namespace App\Services;

use App\Models\OtpVerification;
use App\Notifications\OtpCodeNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class OtpService
{
    public function generateOtp(): string
    {
        return (string) random_int(100000, 999999);
    }

    public function storeOtp(string $identifier, string $purpose, string $ipAddress, ?string $userAgent = null): string
    {
        $otp = $this->generateOtp();
        $otpHash = Hash::make($otp);

        Log::channel('stack')->info('OTP generated', [
            'identifier' => $identifier,
            'purpose' => $purpose,
            'ip_address' => $ipAddress,
            'expires_at' => now()->addMinutes(10),
        ]);

        OtpVerification::where('identifier', $identifier)
            ->where('purpose', $purpose)
            ->delete();

        OtpVerification::create([
            'identifier' => $identifier,
            'otp_hash' => $otpHash,
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        return $otp;
    }

    public function verifyOtp(string $identifier, string $otp, string $purpose): bool
    {
        $record = OtpVerification::forIdentifier($identifier, $purpose)->first();

        if (! $record) {
            Log::channel('stack')->warning('OTP verification failed - not found', [
                'identifier' => $identifier,
                'purpose' => $purpose,
            ]);

            return false;
        }

        $record->increment('attempts');

        if ($record->attempts > 5) {
            $record->delete();
            Log::channel('stack')->warning('OTP deleted - too many attempts', [
                'identifier' => $identifier,
                'purpose' => $purpose,
            ]);

            return false;
        }

        $result = Hash::check($otp, $record->otp_hash);

        if ($result) {
            $record->update(['verified_at' => now()]);
            Log::channel('stack')->info('OTP verified successfully', [
                'identifier' => $identifier,
                'purpose' => $purpose,
            ]);
        } else {
            Log::channel('stack')->warning('OTP verification failed - invalid code', [
                'identifier' => $identifier,
                'purpose' => $purpose,
                'attempts' => $record->attempts,
            ]);
        }

        return $result;
    }

    public function sendOtp(string $email, string $otp, string $purpose, ?string $locale = null): void
    {
        $locale = $locale ?? app()->getLocale();

        Notification::route('mail', $email)
            ->notify(new OtpCodeNotification($otp, $purpose, $locale));

        Log::channel('stack')->info('OTP notification sent', [
            'email' => $email,
            'purpose' => $purpose,
            'locale' => $locale,
        ]);
    }

    public function cleanupExpiredOtps(): int
    {
        $deletedExpired = OtpVerification::expired()->delete();

        $deletedVerified = OtpVerification::verified()
            ->where('verified_at', '<', now()->subHour())
            ->delete();

        return $deletedExpired + $deletedVerified;
    }
}
