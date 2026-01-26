<?php

namespace App\Livewire\WorkBalance;

use Livewire\Component;

class ReflectionSummary extends Component
{
    public function render(): mixed
    {
        return view('livewire.workbalance.reflection-summary')
            ->layout('layouts.workbalance', ['title' => 'Reflection Summary']);
    }
}
