<?php

namespace App\Events;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuoteUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Quote $quote;
    public array $changes;
    public ?User $user;

    public function __construct(Quote $quote, array $changes, ?User $user = null)
    {
        $this->quote = $quote;
        $this->changes = $changes;
        $this->user = $user;
    }
}
