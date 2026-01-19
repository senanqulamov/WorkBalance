<?php

namespace App\Http\Middleware;

use App\Models\Log;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogPageView
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Logging page views is helpful, but it shouldn't ever slow down the app.
        // We skip it entirely in local/testing, for non-GET requests, for non-success responses,
        // and for requests that are typically noise (Livewire/AJAX/assets).
        if (app()->environment(['local', 'testing'])) {
            return $response;
        }

        if (! Auth::check() || ! $response->isSuccessful() || $request->method() !== 'GET') {
            return $response;
        }

        // Skip logging for Livewire updates / AJAX requests
        if ($request->ajax() || $request->headers->has('X-Livewire')) {
            return $response;
        }

        // Skip common static/utility paths
        $path = $request->path();
        if (
            str_starts_with($path, 'livewire/') ||
            str_starts_with($path, 'storage/') ||
            str_starts_with($path, 'build/') ||
            str_starts_with($path, 'assets/')
        ) {
            return $response;
        }

        try {
            $route = $request->route();
            $routeName = $route ? $route->getName() : null;

            // If there's no named route, don't spam the logs with "unknown" entries.
            if (! $routeName) {
                return $response;
            }

            $payload = [
                'user_id' => Auth::id(),
                'type' => 'page_view',
                'action' => 'view.'.$routeName,
                'message' => "Viewed page: {$routeName}",
                'metadata' => [
                    'route' => $routeName,
                    'url' => $request->fullUrl(),
                    'method' => 'GET',
                    'referer' => $request->header('referer'),
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ];

            // If the app has a queue connection configured, do this asynchronously.
            // Falling back to sync keeps behaviour the same when the queue isn't running.
            if (config('queue.default') && config('queue.default') !== 'sync') {
                dispatch(function () use ($payload) {
                    Log::create($payload);
                })->afterResponse();
            } else {
                // afterResponse ensures we don't inflate TTFB even in sync mode
                dispatch(function () use ($payload) {
                    Log::create($payload);
                })->afterResponse();
            }
        } catch (\Throwable $e) {
            // Silent fail - don't break the app if logging fails
            logger()->error('Failed to log page view: '.$e->getMessage());
        }

        return $response;
    }
}
