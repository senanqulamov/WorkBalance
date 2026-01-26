<?php

namespace App\Livewire\HumanOps\Risk;

use Livewire\Component;

class Matrix extends Component
{
    public function render(): mixed
    {
        return view('livewire.humanops.risk.matrix')
            ->layout('layouts.app', ['title' => 'Risk Matrix']);
    }
}
