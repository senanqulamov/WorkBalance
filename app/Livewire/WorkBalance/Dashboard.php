<?php

namespace App\Livewire\WorkBalance;

use Livewire\Component;

class Dashboard extends Component
{
    public function render(): mixed
    {
        return view('livewire.workbalance.dashboard')
            ->layout('layouts.workbalance', ['title' => 'WorkBalance']);
    }
}
