<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DeviceTokenService
{
    public function createDeviceToken(User $user, bool $rememberDevice = false): ?string
    {
        if (! $rememberDevice) {
            return null;
        }

        $token = Str::random(64);
        $tokenHash = hash('sha256', $token);
        $fingerprint = $this->generateDeviceFingerprint();

        DeviceToken::create([
            'user_id' => $user->id,
            'token_hash' => $tokenHash,
            'device_fingerprint' => $fingerprint,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'last_used_at' => now(),
            'expires_at' => now()->addDays(30),
        ]);

        Log::channel('stack')->info('Device token created', [
            'user_id' => $user->id,
            'ip' => request()->ip(),
        ]);

        return $token;
    }

    public function verifyDeviceToken(string $token): ?User
    {
        $tokenHash = hash('sha256', $token);

        $deviceToken = DeviceToken::where('token_hash', $tokenHash)
            ->where('expires_at', '>', now())
            ->first();

        if (! $deviceToken) {
            return null;
        }

        $currentFingerprint = $this->generateDeviceFingerprint();
        if ($deviceToken->device_fingerprint !== $currentFingerprint) {
            $deviceToken->delete();

            Log::channel('stack')->warning('Device fingerprint mismatch', [
                'user_id' => $deviceToken->user_id,
                'ip' => request()->ip(),
            ]);

            return null;
        }

        $deviceToken->update(['last_used_at' => now()]);

        return $deviceToken->user;
    }

    protected function generateDeviceFingerprint(): string
    {
        $userAgent = request()->userAgent() ?? 'unknown';

        return hash('sha256', $userAgent);
    }

    public function revokeDeviceToken(string $token): void
    {
        $tokenHash = hash('sha256', $token);
        DeviceToken::where('token_hash', $tokenHash)->delete();
    }

    public function revokeAllUserDevices(User $user): void
    {
        DeviceToken::where('user_id', $user->id)->delete();
    }

    public function cleanupExpiredDevices(): int
    {
        return DeviceToken::expired()->delete();
    }
}
