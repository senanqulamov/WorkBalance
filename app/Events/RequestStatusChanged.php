<?php

namespace App\Events;

use App\Enums\RequestStatus;
use App\Models\Request;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The request instance.
     *
     * @var \App\Models\Request
     */
    public $request;

    /**
     * The previous status.
     *
     * @var \App\Enums\RequestStatus|null
     */
    public $oldStatus;

    /**
     * The new status.
     *
     * @var \App\Enums\RequestStatus
     */
    public $newStatus;

    /**
     * The user who changed the status.
     *
     * @var \App\Models\User|null
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Request  $request
     * @param  \App\Enums\RequestStatus|null  $oldStatus
     * @param  \App\Enums\RequestStatus  $newStatus
     * @param  \App\Models\User|null  $user
     * @return void
     */
    public function __construct(Request $request, ?RequestStatus $oldStatus, RequestStatus $newStatus, ?User $user = null)
    {
        $this->request = $request;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->user = $user;
    }
}
