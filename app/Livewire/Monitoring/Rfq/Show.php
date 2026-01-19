<?php

namespace App\Livewire\Monitoring\Rfq;

use App\Enums\TableHeaders;
use App\Events\SupplierInvited;
use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Quote;
use App\Models\Request;
use App\Models\SupplierInvitation;
use App\Models\User;
use App\Models\WorkflowEvent;
use App\Notifications\QuoteStatusChanged;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class Show extends Component
{
    use Alert, WithLogging, WithPagination;

    public Request $request;

    public ?string $statusValue = null;

    public bool $showInviteModal = false;

    public array $selectedSuppliers = [];

    public array $availableStatuses = [
        'draft' => 'Draft',
        'open' => 'Open',
        'closed' => 'Closed',
        'awarded' => 'Awarded',
        'cancelled' => 'Cancelled',
    ];

    /**
     * Generic table props used by <x-table> (for supplier invitations table).
     */
    public int $quantity = 5;

    public array $sort = [
        'column' => 'status',
        'direction' => 'desc',
    ];

    public array $headers = [
        ['index' => 'supplier', 'label' => TableHeaders::Supplier],
        ['index' => 'status', 'label' => TableHeaders::Status],
        ['index' => 'sent_at', 'label' => TableHeaders::SentAt],
        ['index' => 'responded_at', 'label' => TableHeaders::RespondedAt],
        ['index' => 'action', 'label' => TableHeaders::Action, 'sortable' => false],
    ];

    #[Computed]
    public function invitationRows()
    {
        if ($this->quantity === 'all') {
            $this->quantity = SupplierInvitation::where('request_id', $this->request->id)->count();
        }

        return SupplierInvitation::query()
            ->with('supplier')
            ->where('request_id', $this->request->id)
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }

    public function mount(Request $request): void
    {
        $this->headers = TableHeaders::make($this->headers);

        $this->request = $request->load([
            'buyer',
            'items.product',
            'quotes.supplier',
            'quotes.items.requestItem',
            'supplierInvitations.supplier',
        ]);

        // Ensure the buyer owns this RFQ
        if (Auth::user()->role != 'admin') {
            abort(403, __('Only admin users can view this page.'));
        }

        // Initialize status value
        $this->statusValue = $this->request->status;

        $this->logPageView('Buyer RFQ Show', [
            'request_id' => $this->request->id,
        ]);
    }

    public function updatedStatusValue($value): void
    {
        if (!$value || !array_key_exists($value, $this->availableStatuses)) {
            $this->error(__('Invalid status selected.'));
            $this->statusValue = $this->request->status; // Reset to current status
            return;
        }

        $oldStatus = $this->request->status;

        // Don't update if it's the same status
        if ($oldStatus === $value) {
            return;
        }

        $this->request->status = $value;
        $this->request->save();

        // Observer will fire RequestStatusChanged event automatically

        $this->logUpdate(
            Request::class,
            $this->request->id,
            [
                'status' => [
                    'old' => $oldStatus,
                    'new' => $value,
                ],
            ]
        );


        $this->success(__('RFQ status updated successfully.'));

        // Refresh the request
        $this->request->refresh();
    }

    public function openInviteModal(): void
    {
        $this->showInviteModal = true;
        $this->selectedSuppliers = [];
    }

    public function closeInviteModal(): void
    {
        $this->showInviteModal = false;
        $this->selectedSuppliers = [];
    }

    public function inviteSuppliers(): void
    {
        if (empty($this->selectedSuppliers)) {
            $this->error(__('Please select at least one supplier to invite.'));
            return;
        }

        $user = Auth::user();
        $invitedCount = 0;
        $alreadyInvitedCount = 0;
        $invitationIds = [];

        foreach ($this->selectedSuppliers as $supplierId) {
            // Check if supplier is already invited
            $existingInvitation = SupplierInvitation::where('request_id', $this->request->id)
                ->where('supplier_id', $supplierId)
                ->first();

            if ($existingInvitation) {
                $alreadyInvitedCount++;
                continue;
            }

            // Create invitation
            $invitation = SupplierInvitation::create([
                'request_id' => $this->request->id,
                'supplier_id' => $supplierId,
                'status' => 'pending',
                'sent_at' => now(),
            ]);

            $invitationIds[] = $invitation->id;

            // Dispatch event to send notification
            $supplier = User::find($supplierId);
            if ($supplier) {
                event(new SupplierInvited($invitation, $user));
                $invitedCount++;
            }
        }

        if ($invitedCount > 0) {
            $this->logCreate(
                SupplierInvitation::class,
                $invitationIds[0],
                [
                    'request_id' => $this->request->id,
                    'suppliers_invited' => $invitedCount,
                    'already_invited' => $alreadyInvitedCount,
                    'invitation_ids' => $invitationIds,
                    'selected_supplier_ids' => $this->selectedSuppliers,
                ]
            );

            // Record workflow event for admin inviting suppliers
            $admin = Auth::user();
            try {
                WorkflowEvent::create([
                    'eventable_type' => get_class($this->request),
                    'eventable_id' => $this->request->id,
                    'user_id' => $admin?->id,
                    'event_type' => 'suppliers_invited',
                    'from_state' => null,
                    'to_state' => null,
                    'description' => 'Admin ' . ($admin?->name ?? 'system') . ' invited ' . $invitedCount . ' supplier(s)',
                    'occurred_at' => now(),
                    'metadata' => [
                        'admin_id' => $admin?->id,
                        'admin_name' => $admin?->name,
                        'invitation_ids' => $invitationIds,
                        'selected_supplier_ids' => $this->selectedSuppliers,
                    ],
                ]);
            } catch (\Throwable $e) {
                $this->logException($e);
            }
        }

        // Refresh the request with invitations
        $this->request->load('supplierInvitations.supplier');

        $this->closeInviteModal();

        if ($invitedCount > 0) {
            $message = $invitedCount === 1
                ? __('1 supplier invited successfully.')
                : __(':count suppliers invited successfully.', ['count' => $invitedCount]);

            if ($alreadyInvitedCount > 0) {
                $message .= ' ' . __(':count already invited.', ['count' => $alreadyInvitedCount]);
            }

            $this->success($message);
        } else {
            $this->warning(__('All selected suppliers have already been invited.'));
        }
    }

    public function deleteInvitation(int $invitationId): void
    {
        $invitation = SupplierInvitation::where('id', $invitationId)
            ->where('request_id', $this->request->id)
            ->first();

        if (! $invitation) {
            $this->error(__('Invitation not found.'));

            return;
        }

        // Optional: prevent deleting invitations that already have quotes
        if ($invitation->quotes()->exists()) {
            $this->warning(__('Cannot delete an invitation that already has quotes.'));

            return;
        }

        $invitationId = $invitation->id;
        $supplierName = $invitation->supplier?->name;

        $invitation->delete();

        // Record workflow event for admin deleting invitation
        $admin = Auth::user();
        try {
            WorkflowEvent::create([
                'eventable_type' => get_class($this->request),
                'eventable_id' => $this->request->id,
                'user_id' => $admin?->id,
                'event_type' => 'invitation_deleted',
                'from_state' => null,
                'to_state' => null,
                'description' => 'Admin ' . ($admin?->name ?? 'system') . ' deleted invitation for supplier ' . ($supplierName ?? 'unknown'),
                'occurred_at' => now(),
                'metadata' => [
                    'admin_id' => $admin?->id,
                    'admin_name' => $admin?->name,
                    'invitation_id' => $invitationId,
                    'supplier_name' => $supplierName,
                ],
            ]);
        } catch (\Throwable $e) {
            $this->logException($e);
        }

        $this->logDelete(
            SupplierInvitation::class,
            $invitationId,
            [
                'request_id' => $this->request->id,
                'supplier_name' => $supplierName,
            ]
        );

        // Reload invitations relation
        $this->request->load('supplierInvitations.supplier');

        $this->success(__('Invitation deleted successfully.'));
    }

    public function getAvailableSuppliersProperty()
    {
        $invitedSupplierIds = $this->request->supplierInvitations->pluck('supplier_id')->toArray();

        // Return suppliers that haven't been invited yet
        return User::activeSuppliers()
            ->whereNotIn('id', $invitedSupplierIds)
            ->orderBy('name')
            ->get();
    }

    #[Renderless]
    public function confirmAcceptQuote(int $quoteId): void
    {
        $quote = Quote::where('id', $quoteId)
            ->where('request_id', $this->request->id)
            ->first();

        if (!$quote) {
            $this->error(__('Quote not found.'));
            return;
        }

        $this->question(
            __('Are you sure you want to accept this quote? All other quotes will be automatically rejected.'),
            __('Accept Quote?')
        )
            ->confirm(method: 'acceptQuote', params: ['quoteId' => $quoteId])
            ->cancel()
            ->send();
    }

    public function acceptQuote($params): void
    {
        $quoteId = is_array($params) && isset($params['quoteId']) ? (int)$params['quoteId'] : (int)$params;
        $quote = Quote::where('id', $quoteId)
            ->where('request_id', $this->request->id)
            ->first();

        if (!$quote) {
            $this->error(__('Quote not found.'));
            return;
        }

        $oldStatus = $quote->status;

        // Update quote status to accepted
        $quote->status = 'accepted';
        $quote->save();

        // Optionally, reject all other quotes for this RFQ
        Quote::where('request_id', $this->request->id)
            ->where('id', '!=', $quoteId)
            ->where('status', '!=', 'rejected')
            ->update(['status' => 'rejected']);

        // Update RFQ status to awarded
        $this->request->status = 'awarded';
        $this->request->save();
        $this->statusValue = 'awarded';

        // Send notification to supplier
        if ($quote->supplier) {
            $quote->supplier->notify(new QuoteStatusChanged($quote, $oldStatus, 'accepted'));
        }

        // Notify other suppliers their quotes were rejected
        $otherQuotes = Quote::where('request_id', $this->request->id)
            ->where('id', '!=', $quoteId)
            ->with('supplier')
            ->get();

        foreach ($otherQuotes as $otherQuote) {
            if ($otherQuote->supplier) {
                $otherQuote->supplier->notify(new QuoteStatusChanged($otherQuote, $otherQuote->status, 'rejected'));
            }
        }

        $this->logUpdate(
            Quote::class,
            $quote->id,
            [
                'status' => [
                    'old' => $oldStatus,
                    'new' => 'accepted',
                ],
                'action' => 'Quote accepted by buyer',
            ]
        );

        // Record workflow event for admin accepting quote
        $admin = Auth::user();
        try {
            WorkflowEvent::create([
                'eventable_type' => get_class($this->request),
                'eventable_id' => $this->request->id,
                'user_id' => $admin?->id,
                'event_type' => 'quote_accepted',
                'from_state' => null,
                'to_state' => null,
                'description' => 'Admin ' . ($admin?->name ?? 'system') . ' accepted quote #' . $quote->id . ' from supplier ' . ($quote->supplier?->name ?? 'unknown'),
                'occurred_at' => now(),
                'metadata' => [
                    'admin_id' => $admin?->id,
                    'admin_name' => $admin?->name,
                    'quote_id' => $quote->id,
                    'quote_total' => $quote->total_price ?? null,
                    'supplier_id' => $quote->supplier?->id ?? null,
                ],
            ]);

            // Also record workflow events for each rejected quote
            foreach ($otherQuotes as $otherQuote) {
                WorkflowEvent::create([
                    'eventable_type' => get_class($this->request),
                    'eventable_id' => $this->request->id,
                    'user_id' => $admin?->id,
                    'event_type' => 'quote_rejected',
                    'from_state' => null,
                    'to_state' => null,
                    'description' => 'Admin ' . ($admin?->name ?? 'system') . ' rejected quote #' . $otherQuote->id . ' from supplier ' . ($otherQuote->supplier?->name ?? 'unknown'),
                    'occurred_at' => now(),
                    'metadata' => [
                        'admin_id' => $admin?->id,
                        'admin_name' => $admin?->name,
                        'quote_id' => $otherQuote->id,
                        'supplier_id' => $otherQuote->supplier?->id ?? null,
                    ],
                ]);
            }
        } catch (\Throwable $e) {
            $this->logException($e);
        }

        // Refresh the request
        $this->request->refresh()->load([
            'items.product',
            'quotes.supplier',
            'quotes.items.requestItem.product',
        ]);

        $this->success(__('Quote accepted successfully. Other quotes have been rejected.'));
    }

    #[Renderless]
    public function confirmRejectQuote(int $quoteId): void
    {
        $quote = Quote::where('id', $quoteId)
            ->where('request_id', $this->request->id)
            ->first();

        if (!$quote) {
            $this->error(__('Quote not found.'));
            return;
        }

        $this->question(
            __('Are you sure you want to reject this quote?'),
            __('Reject Quote?')
        )
            ->confirm(method: 'rejectQuote', params: ['quoteId' => $quoteId])
            ->cancel()
            ->send();
    }

    public function rejectQuote($params): void
    {
        $quoteId = is_array($params) && isset($params['quoteId']) ? (int)$params['quoteId'] : (int)$params;
        $quote = Quote::where('id', $quoteId)
            ->where('request_id', $this->request->id)
            ->first();

        if (!$quote) {
            $this->error(__('Quote not found.'));
            return;
        }

        $oldStatus = $quote->status;

        // Update quote status to rejected
        $quote->status = 'rejected';
        $quote->save();

        // Send notification to supplier
        if ($quote->supplier) {
            $quote->supplier->notify(new QuoteStatusChanged($quote, $oldStatus, 'rejected'));
        }

        $this->logUpdate(
            Quote::class,
            $quote->id,
            [
                'status' => [
                    'old' => $oldStatus,
                    'new' => 'rejected',
                ],
                'action' => 'Quote rejected by buyer',
            ]
        );

        // Record workflow event for admin rejecting quote
        $admin = Auth::user();
        try {
            WorkflowEvent::create([
                'eventable_type' => get_class($this->request),
                'eventable_id' => $this->request->id,
                'user_id' => $admin?->id,
                'event_type' => 'quote_rejected',
                'from_state' => null,
                'to_state' => null,
                'description' => 'Admin ' . ($admin?->name ?? 'system') . ' rejected quote #' . $quote->id . ' from supplier ' . ($quote->supplier?->name ?? 'unknown'),
                'occurred_at' => now(),
                'metadata' => [
                    'admin_id' => $admin?->id,
                    'admin_name' => $admin?->name,
                    'quote_id' => $quote->id,
                    'supplier_id' => $quote->supplier?->id ?? null,
                ],
            ]);
        } catch (\Throwable $e) {
            $this->logException($e);
        }

        // Refresh the request
        $this->request->refresh()->load([
            'items.product',
            'quotes.supplier',
            'quotes.items.requestItem.product',
        ]);

        $this->success(__('Quote rejected successfully.'));
    }

    public function render(): View
    {
        return view('livewire.monitoring.rfq.show');
    }
}
