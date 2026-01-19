<?php

namespace App\Listeners;

use App\Events\SupplierInvited;
use App\Notifications\SupplierInvitation as SupplierInvitationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSupplierInvitationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SupplierInvited $event): void
    {
        // Notify the supplier
        $supplier = $event->supplier;
        if ($supplier) {
            $supplier->notify(new SupplierInvitationNotification(
                $event->invitation,
                $event->sender
            ));
        }

        // Also notify the buyer that the invitation has been sent
        $buyer = $event->request->buyer;
        if ($buyer && $buyer->id !== $event->sender?->id) {
            $buyer->notify(new SupplierInvitationNotification(
                $event->invitation,
                $event->sender
            ));
        }

        // Update the invitation to mark it as sent
        $event->invitation->update([
            'sent_at' => now(),
        ]);
    }
}
