<?php

namespace App\Events;

use App\Models\TherapeuticSession;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when an employee completes a therapeutic path session.
 * Triggers aggregation and tracks completion metrics.
 */
class TherapeuticSessionCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public TherapeuticSession $session
    ) {}
}
