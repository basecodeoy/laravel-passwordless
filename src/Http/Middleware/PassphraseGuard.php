<?php

declare(strict_types=1);

namespace BaseCodeOy\Passwordless\Http\Middleware;

use BaseCodeOy\Passwordless\Enums\AuthSession;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class PassphraseGuard
{
    public function handle(Request $request, Closure $next)
    {
        $passphrase = $request->session()->get(AuthSession::PASSPHRASE, null);

        if (null === $passphrase) {
            return $next($request);
        }

        if ($request->session()->get(AuthSession::PASSPHRASE_EXPIRATION) < Carbon::now()->timestamp) {
            $request->session()->forget(AuthSession::PASSPHRASE);
            $request->session()->forget(AuthSession::PASSPHRASE_EXPIRATION);

            Auth::logout();

            return redirect()->route('home');
        }

        if ($request->route()->named('login.*')) {
            return $next($request);
        }

        return redirect()->route('login.passwordless');
    }
}
