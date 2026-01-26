<?php

namespace App\Events;

use App\Models\Request;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SlaReminderDue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The request instance.
     *
     * @var \App\Models\Request
     */
    public $request;

    /**
     * The number of days remaining until the deadline.
     *
     * @var int
     */
    public $daysRemaining;

    /**
     * The priority of the reminder (low, medium, high).
     *
     * @var string
     */
    public $priority;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Request  $request
     * @param  int  $daysRemaining
     * @param  string  $priority
     * @return void
     */
    public function __construct(Request $request, int $daysRemaining, string $priority = 'medium')
    {
        $this->request = $request;
        $this->daysRemaining = $daysRemaining;
        $this->priority = $priority;
    }
}
