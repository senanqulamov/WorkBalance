<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSupplier
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->hasRole('supplier')) {
            if ($request->user() && $request->user()->hasRole('admin')) {
                return $next($request);
                // abort(403,'Admin access to supplier area is not allowed.');
            }
            abort(403, 'Access denied. This area is for suppliers only.');
        }

        return $next($request);
    }
}
