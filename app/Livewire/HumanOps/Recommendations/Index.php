<?php

namespace App\Livewire\HumanOps\Recommendations;

use Livewire\Component;

class Index extends Component
{
    public function render(): mixed
    {
        return view('livewire.humanops.recommendations.index')
            ->layout('layouts.app', ['title' => 'Recommendations']);
    }
}
