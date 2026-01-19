<?php

namespace App\Livewire\Supplier;

use App\Models\Quote;
use App\Models\SupplierInvitation;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    public int $pendingInvitations = 0;
    public int $activeQuotes = 0;
    public int $wonQuotes = 0;
    public int $totalRevenue = 0;

    public function mount(): void
    {
        $supplier = auth()->user();

        // Get statistics
        $this->pendingInvitations = SupplierInvitation::query()
            ->where('supplier_id', $supplier->id)
            ->where('status', 'pending')
            ->count();

        $this->activeQuotes = Quote::query()
            ->where('supplier_id', $supplier->id)
            ->whereIn('status', ['draft', 'submitted'])
            ->count();

        $this->wonQuotes = Quote::query()
            ->where('supplier_id', $supplier->id)
            ->where('status', 'won')
            ->count();

        $this->totalRevenue = Quote::query()
            ->where('supplier_id', $supplier->id)
            ->where('status', 'won')
            ->sum('total_amount');
    }

    public function render(): View
    {
        return view('livewire.supplier.dashboard')
            ->layout('layouts.app');
    }
}
