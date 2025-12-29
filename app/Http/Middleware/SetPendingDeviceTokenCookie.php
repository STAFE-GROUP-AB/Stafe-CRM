<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetPendingDeviceTokenCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $token = session('pending_device_token');

        if ($token) {
            session()->forget('pending_device_token');

            $cookie = Cookie::make(
                'device_token',
                $token,
                43200,
                '/',
                config('session.domain'),
                config('session.secure'),
                true,
                false,
                config('session.same_site', 'lax')
            );

            $response->headers->setCookie($cookie);
        }

        return $response;
    }
}
