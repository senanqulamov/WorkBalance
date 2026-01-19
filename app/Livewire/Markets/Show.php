<?php

namespace App\Livewire\Markets;

use App\Models\Market;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public Market $market;

    public int $ordersQuantity = 10;
    public int $productsQuantity = 10; // added

    public function mount(Market $market): void
    {
        $this->market = $market;
    }

    public function render(): View
    {
        return view('livewire.markets.show');
    }

    // Recent orders paginator
    public function getOrdersProperty()
    {
        return $this->market->orders()
            ->latest()
            ->paginate($this->ordersQuantity, ['*'], 'orders_page'); // explicit columns to satisfy analyzer
    }

    public function getProductsProperty()
    {
        return $this->market->products()
            ->latest()
            ->paginate($this->productsQuantity, ['*'], 'products_page');
    }

    // Aggregated metrics for display
    public function getMetricsProperty(): array
    {
        $orders = $this->market->orders();
        $count = $orders->count();
        $revenue = (float) $orders->sum('total');
        $avg = $count ? $revenue / $count : 0.0;
        $products = $this->market->products()->count();

        return [
            'orders_count' => $count,
            'revenue' => $revenue,
            'avg_order_value' => $avg,
            'products_count' => $products,
        ];
    }
}
