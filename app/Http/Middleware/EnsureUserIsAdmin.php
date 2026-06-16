<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is an admin
        if (auth()->check() && auth()->user()->is_admin == 1) {
            return $next($request);
        }

        // Redirect unauthorized users to dashboard
        return redirect('/my-dashboard')->with('error', 'Unauthorized access.');
    }
}