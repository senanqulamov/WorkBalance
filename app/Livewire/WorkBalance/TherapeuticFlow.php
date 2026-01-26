<?php

namespace App\Livewire\WorkBalance;

use Livewire\Component;

class TherapeuticFlow extends Component
{
    public function render(): mixed
    {
        return view('livewire.workbalance.therapeutic-flow')
            ->layout('layouts.workbalance', ['title' => 'Therapeutic Flow']);
    }
}
