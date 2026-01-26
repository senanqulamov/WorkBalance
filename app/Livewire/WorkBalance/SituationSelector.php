<?php

namespace App\Livewire\WorkBalance;

use Livewire\Component;

class SituationSelector extends Component
{
    public function render(): mixed
    {
        return view('livewire.workbalance.situation-selector')
            ->layout('layouts.workbalance', ['title' => 'Choose Support']);
    }
}
