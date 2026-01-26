<?php

namespace App\Observers;

use App\Events\TherapeuticSessionCompleted;
use App\Events\TherapeuticSessionStarted;
use App\Models\HumanEvent;
use App\Models\TherapeuticSession;

/**
 * Observer for TherapeuticSession model.
 * Tracks session lifecycle for HumanOps insights.
 */
class TherapeuticSessionObserver
{
    public function created(TherapeuticSession $session): void
    {
        event(new TherapeuticSessionStarted($session));

        $employee = $session->employee;
        $teams = $employee->teams;

        foreach ($teams as $team) {
            HumanEvent::create([
                'eventable_type' => TherapeuticSession::class,
                'eventable_id' => $session->id,
                'team_id' => $team->id,
                'event_type' => 'path_started',
                'description' => 'Team member started therapeutic path',
                'occurred_at' => $session->started_at,
                'metadata' => [
                    'situation_type' => $session->situation_type,
                    'intensity_before' => $session->intensity_before,
                ],
            ]);
        }
    }

    public function updated(TherapeuticSession $session): void
    {
        if ($session->isDirty('status') && $session->status === 'completed') {
            event(new TherapeuticSessionCompleted($session));

            $employee = $session->employee;
            $teams = $employee->teams;

            foreach ($teams as $team) {
                HumanEvent::create([
                    'eventable_type' => TherapeuticSession::class,
                    'eventable_id' => $session->id,
                    'team_id' => $team->id,
                    'event_type' => 'path_completed',
                    'description' => 'Team member completed therapeutic path',
                    'occurred_at' => $session->completed_at,
                    'metadata' => [
                        'situation_type' => $session->situation_type,
                        'intensity_before' => $session->intensity_before,
                        'intensity_after' => $session->intensity_after,
                        'intensity_shift' => $session->intensity_before - ($session->intensity_after ?? $session->intensity_before),
                    ],
                ]);
            }
        }
    }
}
