<?php

namespace App\Livewire\Supplier\Messages;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public function render(): View
    {
        return view('livewire.supplier.messages.index')
            ->layout('layouts.app');
    }
}
