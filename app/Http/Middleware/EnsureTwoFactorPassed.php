<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorPassed
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Bypass 2FA check when running unit tests unless explicitly requested
            if (app()->runningUnitTests() && !$request->session()->has('test_enforce_2fa')) {
                return $next($request);
            }

            // Bypass 2FA check if globally disabled in system settings
            try {
                $global2fa = \App\Models\SystemSetting::where('key', 'two_factor_enabled')->value('value') ?? '0';
                if ($global2fa === '0') {
                    return $next($request);
                }
            } catch (\Exception $e) {
                // Silently ignore if table doesn't exist yet
            }

            // If the user's session doesn't have 2fa_verified set to true, redirect to verification page
            if (!$request->session()->get('2fa_verified', false)) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Verifikasi dua faktor diperlukan.'], 403);
                }

                return redirect()->route('two-factor.index')
                    ->with('error', 'Anda harus memverifikasi kode 2FA sebelum melanjutkan.');
            }
        }

        return $next($request);
    }
}
