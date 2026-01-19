<?php

namespace App\Listeners;

use App\Events\SlaReminderDue;
use App\Notifications\SlaReminder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSlaReminderNotification implements ShouldQueue
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
    public function handle(SlaReminderDue $event): void
    {
        // Notify the buyer
        $buyer = $event->request->buyer;
        if ($buyer) {
            $buyer->notify(new SlaReminder(
                $event->request,
                $event->daysRemaining,
                $event->priority
            ));
        }

        // If the request has an assigned user who is different from the buyer, notify them too
        if ($event->request->assigned_to && $event->request->assigned_to != $event->request->buyer_id) {
            $assignee = $event->request->assignedTo;
            if ($assignee) {
                $assignee->notify(new SlaReminder(
                    $event->request,
                    $event->daysRemaining,
                    $event->priority
                ));
            }
        }

        // If the request is in open status, also notify suppliers who have been invited but haven't submitted quotes yet
        if ($event->request->status === 'open') {
            $event->request->supplierInvitations()
                ->where('status', 'pending')
                ->get()
                ->each(function ($invitation) use ($event) {
                    $supplier = $invitation->supplier;
                    if ($supplier) {
                        $supplier->notify(new SlaReminder(
                            $event->request,
                            $event->daysRemaining,
                            $event->priority
                        ));
                    }
                });
        }
    }
}
