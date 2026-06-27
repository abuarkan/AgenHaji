<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use Symfony\Component\HttpFoundation\Response;

class RoleHierarchyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = Auth::user();

        // Check if the user's role matches any of the allowed roles
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Log unauthorized access attempt for security auditing
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'unauthorized_access_attempt',
            'model_type' => null,
            'model_id' => null,
            'ip_address' => $request->ip(),
            'before_payload' => [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'required_roles' => $roles,
                'user_role' => $user->role,
            ],
            'after_payload' => null
        ]);

        return response()->json([
            'message' => 'Forbidden: You do not have the required role to access this resource.'
        ], 403);
    }
}
