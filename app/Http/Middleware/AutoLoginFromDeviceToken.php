<?php

namespace App\Http\Middleware;

use App\Services\DeviceTokenService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AutoLoginFromDeviceToken
{
    public function __construct(protected DeviceTokenService $deviceService) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return $next($request);
        }

        $token = $request->cookie('device_token');

        if (! $token) {
            return $next($request);
        }

        $user = $this->deviceService->verifyDeviceToken($token);

        if ($user) {
            Auth::login($user);
            $user->update(['last_login_at' => now()]);

            Log::channel('stack')->info('Auto-login from device token', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
            ]);
        } else {
            Cookie::queue(Cookie::forget('device_token'));
        }

        return $next($request);
    }
}
