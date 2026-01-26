<?php

namespace App\Observers;

use App\Events\BurnoutThresholdCrossed;
use App\Events\StressTrendChanged;
use App\Models\HumanEvent;
use App\Models\TeamMetric;
use App\Notifications\BurnoutRiskAlert;
use App\Notifications\StressTrendUpdate;

/**
 * Observer for TeamMetric model.
 * Detects threshold crossings and notifies managers.
 */
class TeamMetricObserver
{
    public function created(TeamMetric $metric): void
    {
        // Check for burnout risk alerts
        if (in_array($metric->burnout_risk_level, ['elevated', 'high'])) {
            event(new BurnoutThresholdCrossed($metric, $metric->burnout_risk_level));

            // Notify team manager
            if ($metric->team->manager) {
                $metric->team->manager->notify(new BurnoutRiskAlert($metric));
            }

            // Create HumanEvent
            HumanEvent::create([
                'eventable_type' => TeamMetric::class,
                'eventable_id' => $metric->id,
                'team_id' => $metric->team_id,
                'event_type' => 'burnout_threshold_crossed',
                'description' => "Team burnout risk reached {$metric->burnout_risk_level} level",
                'occurred_at' => now(),
                'metadata' => [
                    'risk_level' => $metric->burnout_risk_level,
                    'cohort_size' => $metric->cohort_size,
                ],
            ]);
        }
    }

    public function updated(TeamMetric $metric): void
    {
        // Detect stress trend changes
        if ($metric->isDirty('stress_trend')) {
            $previousTrend = $metric->getOriginal('stress_trend');
            $newTrend = $metric->stress_trend;

            event(new StressTrendChanged($metric, $previousTrend, $newTrend));

            // Notify if trend is worsening
            if (in_array($newTrend, ['rising', 'volatile']) && $metric->team->manager) {
                $metric->team->manager->notify(new StressTrendUpdate($metric, $previousTrend));
            }

            // Create HumanEvent
            HumanEvent::create([
                'eventable_type' => TeamMetric::class,
                'eventable_id' => $metric->id,
                'team_id' => $metric->team_id,
                'event_type' => 'stress_trend_changed',
                'description' => "Team stress trend changed from {$previousTrend} to {$newTrend}",
                'occurred_at' => now(),
                'metadata' => [
                    'previous_trend' => $previousTrend,
                    'new_trend' => $newTrend,
                ],
            ]);
        }
    }
}
