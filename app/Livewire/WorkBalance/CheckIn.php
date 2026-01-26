<?php

namespace App\Livewire\WorkBalance;

use Livewire\Component;

class CheckIn extends Component
{
    public function render(): mixed
    {
        return view('livewire.workbalance.check-in')
            ->layout('layouts.workbalance', ['title' => 'Daily Check-in']);
    }
}
