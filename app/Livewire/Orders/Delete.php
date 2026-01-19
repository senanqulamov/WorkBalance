<?php

namespace App\Livewire\Orders;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Delete extends Component
{
    use Alert, WithLogging;

    public Order $order;

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <x-button.circle icon="trash" color="red" wire:click="confirm" />
        </div>
        HTML;
    }

    #[Renderless]
    public function confirm(): void
    {
        // Check permission
        if (!Auth::user()->hasPermission('delete_orders')) {
            $this->error('You do not have permission to delete orders.');
            return;
        }

        $this->question()
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    public function delete(): void
    {
        // Check permission
        if (!Auth::user()->hasPermission('delete_orders')) {
            $this->error('You do not have permission to delete orders.');
            return;
        }

        $orderData = [
            'order_number' => $this->order->order_number,
            'total' => $this->order->total,
            'status' => $this->order->status,
        ];
        $orderId = $this->order->id;

        // Delete order items first
        $this->order->items()->delete();

        // Delete order
        $this->order->delete();

        $this->logDelete(Order::class, $orderId, $orderData);

        $this->dispatch('deleted');

        $this->success();
    }
}
