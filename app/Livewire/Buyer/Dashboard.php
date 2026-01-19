<?php

namespace App\Livewire\Buyer;

use App\Models\Order;
use App\Models\Quote;
use App\Models\Request as RfqRequest;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    public int $openRfqs = 0;
    public int $pendingQuotes = 0;
    public int $awardedContracts = 0;
    public float $totalSpend = 0;
    public int $activeSuppliers = 0;
    public int $completedOrders = 0;

    public function mount(): void
    {
        $buyer = auth()->user();

        // Get open RFQs created by this buyer
        $this->openRfqs = RfqRequest::query()
            ->where('buyer_id', $buyer->id)
            ->where('status', 'open')
            ->count();

        // Get pending quotes (submitted but not yet reviewed)
        $this->pendingQuotes = Quote::query()
            ->whereHas('request', function ($query) use ($buyer) {
                $query->where('buyer_id', $buyer->id);
            })
            ->where('status', 'submitted')
            ->count();

        // Get awarded/won quotes
        $this->awardedContracts = Quote::query()
            ->whereHas('request', function ($query) use ($buyer) {
                $query->where('buyer_id', $buyer->id);
            })
            ->whereIn('status', ['accepted', 'won'])
            ->count();

        // Calculate total spend from completed orders
        $this->totalSpend = Order::query()
            ->where('user_id', $buyer->id)
            ->where('status', 'completed')
            ->sum('total');

        // Count active suppliers (unique suppliers from quotes)
        $this->activeSuppliers = Quote::query()
            ->whereHas('request', function ($query) use ($buyer) {
                $query->where('buyer_id', $buyer->id);
            })
            ->distinct('supplier_id')
            ->count('supplier_id');

        // Count completed orders
        $this->completedOrders = Order::query()
            ->where('user_id', $buyer->id)
            ->where('status', 'completed')
            ->count();
    }

    public function render(): View
    {
        return view('livewire.buyer.dashboard')
            ->layout('layouts.app');
    }
}
