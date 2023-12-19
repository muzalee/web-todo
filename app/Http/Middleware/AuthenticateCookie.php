<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;

class AuthenticateCookie
{
    public function handle(Request $request, Closure $next)
    {
        $cookieToken = $_COOKIE['access_token'] ?? null;

        if (! $cookieToken) {
            return redirect()->route('auth.login');
        }

        $accessToken = Passport::token()->where('id', $cookieToken)->first();

        if (!$accessToken || $accessToken->expires_at <= now()) {
            return redirect()->route('auth.login');
        }

        return $next($request);
    }
}
