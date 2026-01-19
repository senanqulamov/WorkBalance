<?php

namespace App\Livewire\Supplier\Orders;

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

    public function mount(Order $order): void
    {
        // Ensure supplier can only view their own orders
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order');
        }

        $this->order = $order->load(['items.product', 'items.market', 'seller']);
    }

    public function render(): View
    {
        return view('livewire.supplier.orders.show')
            ->layout('layouts.app');
    }

    #[Renderless]
    public function confirmDelete(): void
    {
        // Suppliers can only delete their own pending orders
        if ($this->order->user_id !== Auth::id()) {
            $this->error(__('Unauthorized action'));
            return;
        }

        if ($this->order->status !== Order::STATUS_PENDING) {
            $this->error(__('Only pending orders can be deleted'));
            return;
        }

        $this->question(
            __('Are you sure you want to delete this order? Stock will be restored.'),
            __('Delete Order?')
        )
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    public function delete(): void
    {
        $orderNumber = $this->order->order_number;

        // Restore stock for all items
        foreach ($this->order->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        $this->logDelete(Order::class, $this->order->id, [
            'order_number' => $orderNumber,
            'total' => $this->order->total,
        ]);

        $this->order->delete();

        $this->success(__('Order :number deleted successfully', ['number' => $orderNumber]));

        $this->redirect(route('supplier.orders.index'));
    }
}
