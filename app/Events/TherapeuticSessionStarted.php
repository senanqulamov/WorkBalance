<?php

namespace App\Events;

use App\Models\TherapeuticSession;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when an employee starts a therapeutic path session.
 */
class TherapeuticSessionStarted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public TherapeuticSession $session
    ) {}
}
