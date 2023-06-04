<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class UserTypeAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Check if the user is authenticated and has the user type ID equal to 2
        if (!$user || $user->type_id !== 2) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
