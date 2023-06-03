<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyHosts
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Define the allowed hosts in an array
        $allowedHosts = [
            'feria888.com',
        ];

        // Check if the request's host is in the allowed hosts array
        if (!in_array($request->getHost(), $allowedHosts)) {
            return response('Unauthorized', 401);
        }

        return $next($request);
    }
}
