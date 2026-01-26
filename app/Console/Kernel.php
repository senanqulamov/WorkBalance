<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // WorkBalance: Aggregate team metrics daily at 2:00 AM
        $schedule->call(function () {
            $service = app(\App\Services\TeamMetricsAggregationService::class);
            foreach (\App\Models\Team::all() as $team) {
                try {
                    $service->aggregateTeamMetrics($team, now()->subDay());
                } catch (\Exception $e) {
                    \Log::warning("Failed to aggregate metrics for team {$team->id}: {$e->getMessage()}");
                }
            }
        })->dailyAt('02:00')->name('aggregate-team-metrics');

        // WorkBalance: Send daily check-in reminders at 9:00 AM
        $schedule->job(\App\Jobs\SendDailyCheckInRemindersJob::class)
            ->dailyAt('09:00')
            ->name('send-check-in-reminders');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
