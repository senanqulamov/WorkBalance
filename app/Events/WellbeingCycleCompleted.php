<?php

namespace App\Events;

use App\Models\WellbeingCycle;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when an employee completes a wellbeing cycle.
 */
class WellbeingCycleCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public WellbeingCycle $cycle
    ) {}
}
