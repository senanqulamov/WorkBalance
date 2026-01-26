<?php

namespace App\Livewire\HumanOps;

use Livewire\Component;

class Dashboard extends Component
{
    public function render(): mixed
    {
        return view('livewire.humanops.dashboard')
            ->layout('layouts.app', ['title' => 'HumanOps Intelligence']);
    }
}
