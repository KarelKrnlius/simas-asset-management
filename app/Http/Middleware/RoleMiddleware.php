<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user()->load('role');
        
        // Check if user has any of the required roles
        if (!$user || !$user->role) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        // Check if user's role name matches any of the required roles
        $userRoleName = strtolower($user->role->name);
        $hasRole = false;
        
        foreach ($roles as $role) {
            if ($userRoleName === strtolower($role)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
