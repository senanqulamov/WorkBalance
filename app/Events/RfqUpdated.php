<?php

namespace App\Events;

use App\Models\Request;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RfqUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Request $request;
    public array $changes;
    public ?User $user;

    public function __construct(Request $request, array $changes, ?User $user = null)
    {
        $this->request = $request;
        $this->changes = $changes;
        $this->user = $user;
    }
}
