<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if user has any of the required roles
        if (!empty($roles) && !in_array($user->role, $roles)) {
            return response()->json(['error' => 'Insufficient permissions'], 403);
        }

        // Check if user's account is active
        if ($user->status !== 'active') {
            return response()->json(['error' => 'Account is not active'], 403);
        }

        // Check if company subscription is active (for non-app-admin users)
        if (!$user->isAppAdmin() && $user->company) {
            if (!$user->company->isSubscriptionActive()) {
                return response()->json(['error' => 'Company subscription is not active'], 403);
            }
        }

        return $next($request);
    }
}
