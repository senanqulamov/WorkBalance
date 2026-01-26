<?php

namespace App\Livewire\Rfq;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    use Alert, WithLogging;

    public Request $request;

    public bool $canQuote = false;

    public ?string $statusValue = null;

    public array $availableStatuses = [
        'draft' => 'Draft',
        'open' => 'Open',
        'closed' => 'Closed',
        'awarded' => 'Awarded',
        'cancelled' => 'Cancelled',
    ];

    public function mount(Request $request): void
    {
        $this->request = $request->load([
            'buyer',
            'items',
            'quotes.supplier',
            'quotes.items.requestItem',
            'supplierInvitations.supplier',
        ]);

        $this->statusValue = $this->request->status;

        $this->logPageView('RFQ Show', [
            'request_id' => $this->request->id,
        ]);

        $user = Auth::user();

        // Check if user has already submitted a quote
        $hasSubmittedQuote = $user && $this->request->quotes()
            ->where('supplier_id', $user->id)
            ->exists();

        $this->canQuote = $user
            && $user->id !== $this->request->buyer_id
            && !$hasSubmittedQuote
            && $this->request->status === 'open'
            && ($this->request->deadline === null || $this->request->deadline->isFuture());
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

    public function render(): View
    {
        return view('livewire.rfq.show');
    }
}
