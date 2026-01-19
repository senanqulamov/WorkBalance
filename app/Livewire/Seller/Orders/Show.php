<?php

namespace App\Livewire\Seller\Orders;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Show extends Component
{
    use Alert, WithLogging;

    public Order $order;
    public ?string $sellerNotes = null;

    public function mount(Order $order): void
    {
        $this->order = $order->load(['items.product', 'items.market', 'user']);

        // Check if this seller owns products in this order
        $this->authorize('view', $order);
    }

    #[Renderless]
    public function confirmAccept(): void
    {
        if (!$this->order->isPending()) {
            $this->error(__('Order is not in pending status'));
            return;
        }

        $this->question(
            __('Are you sure you want to accept this order?'),
            __('Accept Order?')
        )
            ->confirm(method: 'acceptOrder')
            ->cancel()
            ->send();
    }

    public function acceptOrder(): void
    {
        $oldStatus = $this->order->status;
        $this->order->accept($this->sellerNotes);

        $this->logUpdate(Order::class, $this->order->id, [
            'status' => [
                'old' => $oldStatus,
                'new' => $this->order->status,
            ],
            'action' => 'Order accepted by seller',
            'seller_notes' => $this->sellerNotes,
        ]);

        $this->order->refresh();
        $this->sellerNotes = null;

        $this->success(__('Order accepted successfully'));
    }

    #[Renderless]
    public function confirmReject(): void
    {
        if (!$this->order->isPending()) {
            $this->error(__('Order is not in pending status'));
            return;
        }

        if (!$this->sellerNotes) {
            $this->error(__('Please provide a reason for rejection'));
            return;
        }

        $this->question(
            __('Are you sure you want to reject this order? Stock will be restored.'),
            __('Reject Order?')
        )
            ->confirm(method: 'rejectOrder')
            ->cancel()
            ->send();
    }

    public function rejectOrder(): void
    {
        $oldStatus = $this->order->status;
        $this->order->reject($this->sellerNotes);

        // Restore stock for rejected orders
        foreach ($this->order->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        $this->logUpdate(Order::class, $this->order->id, [
            'status' => [
                'old' => $oldStatus,
                'new' => $this->order->status,
            ],
            'action' => 'Order rejected by seller',
            'seller_notes' => $this->sellerNotes,
        ]);

        $this->order->refresh();
        $this->sellerNotes = null;

        $this->success(__('Order rejected'));
    }

    public function render(): View
    {
        return view('livewire.seller.orders.show');
    }
}
