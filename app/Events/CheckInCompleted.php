<?php

namespace App\Events;

use App\Models\EmotionalCheckIn;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when an employee completes a daily emotional check-in.
 * Triggers aggregation for HumanOps metrics.
 */
class CheckInCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public EmotionalCheckIn $checkIn
    ) {}
}
