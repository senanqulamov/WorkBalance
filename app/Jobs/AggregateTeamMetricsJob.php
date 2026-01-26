<?php

namespace App\Jobs;

use App\Models\Team;
use App\Services\TeamMetricsAggregationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Daily job to aggregate team wellbeing metrics.
 * Runs every night to process check-ins from the day.
 */
class AggregateTeamMetricsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ?\DateTime $date = null
    ) {
        $this->date = $this->date ?? new \DateTime();
    }

    public function handle(TeamMetricsAggregationService $service): void
    {
        $teams = Team::where('is_active', true)->get();

        foreach ($teams as $team) {
            try {
                $service->aggregateTeamMetrics($team, $this->date);
            } catch (\Exception $e) {
                // Log but don't fail - some teams may not meet privacy threshold
                logger()->warning("Could not aggregate metrics for team {$team->id}: {$e->getMessage()}");
            }
        }
    }
}
