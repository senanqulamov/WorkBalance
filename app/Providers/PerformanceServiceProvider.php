<?php

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class PerformanceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (! $this->app->environment('local')) {
            return;
        }

        $this->app['events']->listen(QueryExecuted::class, function (QueryExecuted $query) {
            $count = app()->bound('perf.query_count') ? app('perf.query_count') : 0;
            $time = app()->bound('perf.query_time_ms') ? app('perf.query_time_ms') : 0.0;

            $count++;
            $time += $query->time;

            app()->instance('perf.query_count', $count);
            app()->instance('perf.query_time_ms', $time);

            // Log slow queries only (keeps the log usable).
            if ($query->time >= 200) {
                Log::warning('Slow query detected', [
                    'time_ms' => $query->time,
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                ]);
            }
        });

        $this->app['events']->listen(RequestHandled::class, function (RequestHandled $event) {
            $durationMs = (microtime(true) - LARAVEL_START) * 1000;
            $queryCount = app()->bound('perf.query_count') ? app('perf.query_count') : 0;
            $queryTimeMs = app()->bound('perf.query_time_ms') ? round(app('perf.query_time_ms'), 2) : 0.0;

            Log::info('Request performance', [
                'method' => $event->request->method(),
                'path' => '/'.$event->request->path(),
                'status' => $event->response->getStatusCode(),
                'duration_ms' => round($durationMs, 2),
                'query_count' => $queryCount,
                'query_time_ms' => $queryTimeMs,
            ]);

            app()->forgetInstance('perf.query_count');
            app()->forgetInstance('perf.query_time_ms');
        });
    }
}
