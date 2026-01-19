<?php

namespace App\Livewire\Supplier\Markets;

use App\Models\Market;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public Market $market;

    public function render(): View
    {
        $this->market->load(['products', 'seller']);

        return view('livewire.supplier.markets.show')
            ->layout('layouts.app');
    }
}
