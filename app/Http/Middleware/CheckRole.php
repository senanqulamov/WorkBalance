<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            abort(403, 'Unauthorized');
        }

        if (!$request->user()->hasRole($role)) {
            abort(403, 'You do not have the required role to access this resource.');
        }

        return $next($request);
    }
}
