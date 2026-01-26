<?php

namespace App\Listeners;

use App\Events\QuoteStatusChanged;
use App\Events\QuoteSubmitted;
use App\Events\QuoteUpdated;
use App\Events\RequestStatusChanged;
use App\Events\RfqUpdated;
use App\Events\SlaReminderDue;
use App\Events\SupplierInvited;
use App\Models\WorkflowEvent;

class RecordWorkflowEvent
{

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the RequestStatusChanged event.
     */
    public function handleRequestStatusChanged(RequestStatusChanged $event): void
    {
        WorkflowEvent::create([
            'eventable_type' => get_class($event->request),
            'eventable_id' => $event->request->id,
            'user_id' => $event->user?->id,
            'event_type' => 'status_changed',
            'from_state' => $event->oldStatus?->value,
            'to_state' => $event->newStatus->value,
            'description' => 'RFQ status changed from ' . ($event->oldStatus?->label() ?? 'None') . ' to ' . $event->newStatus->label(),
            'occurred_at' => now(),
            'metadata' => [
                'user_name' => $event->user?->name,
                'old_status' => $event->oldStatus?->value,
                'new_status' => $event->newStatus->value,
            ],
        ]);
    }

    /**
     * Handle the SupplierInvited event.
     */
    public function handleSupplierInvited(SupplierInvited $event): void
    {
        WorkflowEvent::create([
            'eventable_type' => get_class($event->request),
            'eventable_id' => $event->request->id,
            'user_id' => $event->sender?->id,
            'event_type' => 'supplier_invited',
            'from_state' => null,
            'to_state' => null,
            'description' => "Supplier {$event->supplier->name} invited to RFQ",
            'occurred_at' => now(),
            'metadata' => [
                'supplier_id' => $event->supplier->id,
                'supplier_name' => $event->supplier->name,
                'invitation_id' => $event->invitation->id,
                'sender_name' => $event->sender?->name,
            ],
        ]);
    }

    /**
     * Handle the QuoteSubmitted event.
     */
    public function handleQuoteSubmitted(QuoteSubmitted $event): void
    {
        WorkflowEvent::create([
            'eventable_type' => get_class($event->request),
            'eventable_id' => $event->request->id,
            'user_id' => $event->supplier->id,
            'event_type' => 'quote_submitted',
            'from_state' => null,
            'to_state' => null,
            'description' => "Quote submitted by supplier {$event->supplier->name}",
            'occurred_at' => now(),
            'metadata' => [
                'supplier_id' => $event->supplier->id,
                'supplier_name' => $event->supplier->name,
                'quote_id' => $event->quote->id,
                'quote_total' => $event->quote->total_price,
            ],
        ]);
    }

    /**
     * Handle the QuoteStatusChanged event.
     */
    public function handleQuoteStatusChanged(QuoteStatusChanged $event): void
    {
        WorkflowEvent::create([
            'eventable_type' => get_class($event->quote->request),
            'eventable_id' => $event->quote->request_id,
            'user_id' => $event->user?->id,
            'event_type' => 'quote_status_changed',
            'from_state' => $event->oldStatus,
            'to_state' => $event->newStatus,
            'description' => "Quote #{$event->quote->id} status changed from " . ($event->oldStatus ?? 'None') . " to {$event->newStatus}",
            'occurred_at' => now(),
            'metadata' => [
                'quote_id' => $event->quote->id,
                'supplier_name' => $event->quote->supplier?->name,
                'user_name' => $event->user?->name,
                'old_status' => $event->oldStatus,
                'new_status' => $event->newStatus,
            ],
        ]);
    }

    /**
     * Handle the QuoteUpdated event.
     */
    public function handleQuoteUpdated(QuoteUpdated $event): void
    {
        $changeDescription = [];
        foreach ($event->changes as $field => $values) {
            $changeDescription[] = "{$field}: {$values['old']} → {$values['new']}";
        }

        WorkflowEvent::create([
            'eventable_type' => get_class($event->quote->request),
            'eventable_id' => $event->quote->request_id,
            'user_id' => $event->user?->id,
            'event_type' => 'quote_updated',
            'from_state' => null,
            'to_state' => null,
            'description' => "Quote #{$event->quote->id} updated: " . implode(', ', $changeDescription),
            'occurred_at' => now(),
            'metadata' => [
                'quote_id' => $event->quote->id,
                'supplier_name' => $event->quote->supplier?->name,
                'user_name' => $event->user?->name,
                'changes' => $event->changes,
            ],
        ]);
    }

    /**
     * Handle the RfqUpdated event.
     */
    public function handleRfqUpdated(RfqUpdated $event): void
    {
        $changeDescription = [];
        foreach ($event->changes as $field => $values) {
            $changeDescription[] = "{$field}: {$values['old']} → {$values['new']}";
        }

        WorkflowEvent::create([
            'eventable_type' => get_class($event->request),
            'eventable_id' => $event->request->id,
            'user_id' => $event->user?->id,
            'event_type' => 'rfq_updated',
            'from_state' => null,
            'to_state' => null,
            'description' => "RFQ updated: " . implode(', ', $changeDescription),
            'occurred_at' => now(),
            'metadata' => [
                'user_name' => $event->user?->name,
                'changes' => $event->changes,
            ],
        ]);
    }

    /**
     * Handle the SlaReminderDue event.
     */
    public function handleSlaReminderDue(SlaReminderDue $event): void
    {
        WorkflowEvent::create([
            'eventable_type' => get_class($event->request),
            'eventable_id' => $event->request->id,
            'user_id' => null,
            'event_type' => 'sla_reminder',
            'from_state' => null,
            'to_state' => null,
            'description' => "SLA reminder: {$event->daysRemaining} days remaining until deadline",
            'occurred_at' => now(),
            'metadata' => [
                'days_remaining' => $event->daysRemaining,
                'priority' => $event->priority,
                'deadline' => $event->request->deadline,
            ],
        ]);
    }
}
