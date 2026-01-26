<?php

namespace App\Observers;

use App\Events\CheckInCompleted;
use App\Models\EmotionalCheckIn;
use App\Models\HumanEvent;

/**
 * Observer for EmotionalCheckIn model.
 * Creates anonymized HumanEvents for HumanOps aggregation.
 */
class EmotionalCheckInObserver
{
    public function created(EmotionalCheckIn $checkIn): void
    {
        // Fire event for aggregation
        event(new CheckInCompleted($checkIn));

        // Create anonymized HumanEvent
        $employee = $checkIn->employee;
        $teams = $employee->teams;

        foreach ($teams as $team) {
            HumanEvent::create([
                'eventable_type' => EmotionalCheckIn::class,
                'eventable_id' => $checkIn->id,
                'team_id' => $team->id,
                'event_type' => 'check_in_completed',
                'description' => 'Team member completed daily check-in',
                'occurred_at' => $checkIn->checked_in_at,
                'metadata' => [
                    // NO personal data, only aggregatable signals
                    'mood_level' => $checkIn->mood_level,
                    'energy_level' => $checkIn->energy_level,
                    'stress_level' => $checkIn->stress_level,
                ],
            ]);
        }
    }
}
