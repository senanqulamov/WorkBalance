<?php

namespace App\Listeners;

use App\Events\QuoteSubmitted;
use App\Notifications\QuoteReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyBuyerOfQuoteSubmission implements ShouldQueue
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
    public function handle(QuoteSubmitted $event): void
    {
        // Notify the buyer
        $buyer = $event->request->buyer;
        if ($buyer) {
            $buyer->notify(new QuoteReceived($event->quote));
        }

        // If the request has an assigned user who is different from the buyer, notify them too
        if ($event->request->assigned_to && $event->request->assigned_to != $event->request->buyer_id) {
            $assignee = $event->request->assignedTo;
            if ($assignee) {
                $assignee->notify(new QuoteReceived($event->quote));
            }
        }

        // Update the supplier invitation status if it exists
        $invitation = $event->request->supplierInvitations()
            ->where('supplier_id', $event->supplier->id)
            ->first();

        if ($invitation && $invitation->status === 'pending') {
            $invitation->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);
        }
    }
}
