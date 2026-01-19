<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale')
            ?? $request->cookie('locale');

        $supported = array_keys(config('languages.supported', []));
        $fallback = config('languages.fallback', 'en');

        if ($locale && in_array($locale, $supported, true)) {
            app()->setLocale($locale);
        } elseif ($fallback) {
            app()->setLocale($fallback);
        }

        return $next($request);
    }
}
