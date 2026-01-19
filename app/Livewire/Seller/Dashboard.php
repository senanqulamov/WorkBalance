<?php

namespace App\Livewire\Seller;

use App\Models\Market;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public int $activeMarkets = 0;
    public int $productsListed = 0;
    public int $totalSales = 0;
    public float $totalRevenue = 0;
    public float $commissionEarned = 0;
    public int $pendingOrders = 0;
    public float $averageRating = 0;

    public function mount(): void
    {
        $seller = auth()->user();

        // Count markets where seller has products (using supplier_id as seller_id)
        $this->activeMarkets = Market::query()
            ->whereHas('products', function ($query) use ($seller) {
                $query->where('supplier_id', $seller->id);
            })
            ->count();

        // Count products listed by this seller (using supplier_id as seller_id)
        $this->productsListed = Product::query()
            ->where('supplier_id', $seller->id)
            ->count();

        // Count total sales (completed orders with seller's products)
        $this->totalSales = Order::query()
            ->whereHas('items', function ($query) use ($seller) {
                $query->whereHas('product', function ($q) use ($seller) {
                    $q->where('supplier_id', $seller->id);
                });
            })
            ->where('status', 'completed')
            ->count();

        // Calculate total revenue from seller's products
        $this->totalRevenue = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('products.supplier_id', $seller->id)
            ->where('orders.status', 'completed')
            ->sum(DB::raw('order_items.quantity * order_items.unit_price'));

        // Calculate commission earned (using commission_rate from user)
        $commissionRate = $seller->commission_rate ?? 10; // default 10%
        $this->commissionEarned = $this->totalRevenue * ($commissionRate / 100);

        // Count pending orders
        $this->pendingOrders = Order::query()
            ->whereHas('items', function ($query) use ($seller) {
                $query->whereHas('product', function ($q) use ($seller) {
                    $q->where('supplier_id', $seller->id);
                });
            })
            ->whereIn('status', ['pending', 'processing'])
            ->count();

        // Get average rating
        $this->averageRating = $seller->rating ?? 0;
    }

    public function render(): View
    {
        return view('livewire.seller.dashboard')
            ->layout('layouts.app');
    }
}
