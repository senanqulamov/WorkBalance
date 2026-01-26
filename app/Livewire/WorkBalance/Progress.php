<?php

namespace App\Livewire\WorkBalance;

use Livewire\Component;

class Progress extends Component
{
    public function render(): mixed
    {
        return view('livewire.workbalance.progress')
            ->layout('layouts.workbalance', ['title' => 'Progress']);
    }
}
