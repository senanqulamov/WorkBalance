<?php

namespace App\Livewire\Seller\Products;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public Product $product;

    public int $ordersQuantity = 10;

    public function mount(Product $product): void
    {
        $this->product = $product->loadMissing('market');
    }

    public function render(): View
    {
        return view('livewire.seller.products.show');
    }

    #[Computed]
    public function orders(): LengthAwarePaginator
    {
        return $this->product->orders()
            ->with(['user:id,name,email', 'markets:id,name'])
            ->orderByDesc('orders.created_at')
            ->paginate($this->ordersQuantity, ['orders.*'], 'orders_page')
            ->withQueryString();
    }

    #[Computed]
    public function relatedMarkets(): Collection
    {
        return $this->product->orders()
            ->with('markets:id,name')
            ->get()
            ->pluck('markets')
            ->flatten()
            ->unique('id')
            ->values();
    }

    #[Computed]
    public function metrics(): array
    {
        $orders = $this->product->orderItems();
        $totalSold = (clone $orders)->sum('quantity');
        $revenue = (clone $orders)->sum('subtotal');

        return [
            'total_sold' => $totalSold,
            'revenue' => $revenue,
            'avg_price' => $totalSold ? $revenue / $totalSold : 0.0,
            'stock' => $this->product->stock,
            'markets_count' => $this->relatedMarkets()->count(),
        ];
    }
}
