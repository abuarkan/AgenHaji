<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCredentialsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Enforce verification only if enabled in system settings
        $enabled = \App\Models\SystemSetting::where('key', 'registration_verification_enabled')->value('value') ?? '0';
        if ($enabled !== '1') {
            return $next($request);
        }

        if (Auth::check()) {
            $user = Auth::user();

            // Only enforce verification for agent roles
            if ($user->role === 'agent') {
                $agent = $user->agent;

                // If agent profile exists and is not fully verified (email & whatsapp), redirect
                if ($agent && (!$agent->is_email_verified || !$agent->is_whatsapp_verified)) {
                    // Bypass during testing unless requested
                    if (app()->runningUnitTests() && !$request->session()->has('test_enforce_verification')) {
                        return $next($request);
                    }

                    // Check if current route is verify-credentials (to avoid infinite redirects)
                    if (!$request->is('verify-credentials') && 
                        !$request->is('verify-credentials/*') && 
                        !$request->is('logout')
                    ) {
                        return redirect()->route('verify-credentials.index')
                            ->with('warning', 'Anda harus menyelesaikan verifikasi email dan nomor WhatsApp sebelum dapat mengakses dashboard.');
                    }
                }
            }
        }

        return $next($request);
    }
}
