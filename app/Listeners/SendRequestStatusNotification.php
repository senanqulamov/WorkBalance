<?php

namespace App\Listeners;

use App\Events\RequestStatusChanged;
use App\Notifications\RequestStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendRequestStatusNotification implements ShouldQueue
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
    public function handle(RequestStatusChanged $event): void
    {
        // Notify the buyer
        $buyer = $event->request->buyer;
        if ($buyer) {
            $buyer->notify(new RequestStatusUpdated(
                $event->request,
                $event->oldStatus,
                $event->newStatus
            ));
        }

        // If the request has an assigned user who is different from the buyer, notify them too
        if ($event->request->assigned_to && $event->request->assigned_to != $event->request->buyer_id) {
            $assignee = $event->request->assignedTo;
            if ($assignee) {
                $assignee->notify(new RequestStatusUpdated(
                    $event->request,
                    $event->oldStatus,
                    $event->newStatus
                ));
            }
        }

        // If the request is now open, notify all invited suppliers
        if ($event->newStatus->value === 'open') {
            $event->request->supplierInvitations->each(function ($invitation) use ($event) {
                $supplier = $invitation->supplier;
                if ($supplier) {
                    $supplier->notify(new RequestStatusUpdated(
                        $event->request,
                        $event->oldStatus,
                        $event->newStatus
                    ));
                }
            });
        }

        // If the request is now awarded, notify the winning supplier
        if ($event->newStatus->value === 'awarded') {
            // Find the accepted quote
            $acceptedQuote = $event->request->quotes()->where('status', 'accepted')->first();
            if ($acceptedQuote) {
                $supplier = $acceptedQuote->supplier;
                if ($supplier) {
                    $supplier->notify(new RequestStatusUpdated(
                        $event->request,
                        $event->oldStatus,
                        $event->newStatus
                    ));
                }
            }
        }
    }
}
