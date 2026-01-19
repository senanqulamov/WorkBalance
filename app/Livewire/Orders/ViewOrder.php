<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Illuminate\Contracts\View\View as ViewContract;
use Livewire\Attributes\On;
use Livewire\Component;

class ViewOrder extends Component
{
    public bool $showDetailModal = false;

    public ?Order $selectedOrder = null;

    public function render(): ViewContract
    {
        return view('livewire.orders.view');
    }

    #[On('view::order')]
    public function showDetailModal(Order $order): void
    {
        $this->selectedOrder = $order->load(['items.product', 'items.market', 'user']);
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedOrder = null;
    }
}
