<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public Order $order;

    public function mount(Order $order): void
    {
        $this->order = $order->load(['items.product', 'items.market', 'user']);
    }

    public function render(): View
    {
        return view('livewire.orders.show');
    }
}
