<?php

namespace App\Events;

use App\Models\Request;
use App\Models\SupplierInvitation;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupplierInvited
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The request instance.
     *
     * @var \App\Models\Request
     */
    public $request;

    /**
     * The supplier invitation instance.
     *
     * @var \App\Models\SupplierInvitation
     */
    public $invitation;

    /**
     * The supplier user instance.
     *
     * @var \App\Models\User
     */
    public $supplier;

    /**
     * The user who sent the invitation.
     *
     * @var \App\Models\User|null
     */
    public $sender;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\SupplierInvitation  $invitation
     * @param  \App\Models\User|null  $sender
     * @return void
     */
    public function __construct(SupplierInvitation $invitation, ?User $sender = null)
    {
        $this->invitation = $invitation;
        $this->request = $invitation->request;
        $this->supplier = $invitation->supplier;
        $this->sender = $sender;
    }
}
