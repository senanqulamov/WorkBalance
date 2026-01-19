<?php

namespace App\Events;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuoteStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Quote $quote;
    public ?string $oldStatus;
    public string $newStatus;
    public ?User $user;

    public function __construct(Quote $quote, ?string $oldStatus, string $newStatus, ?User $user = null)
    {
        $this->quote = $quote;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->user = $user;
    }
}
