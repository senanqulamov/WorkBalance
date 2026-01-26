<?php

namespace App\Livewire\WorkBalance;

use Livewire\Component;

class Settings extends Component
{
    public function render(): mixed
    {
        return view('livewire.workbalance.settings')
            ->layout('layouts.workbalance', ['title' => 'Settings']);
    }
}
