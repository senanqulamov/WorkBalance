<?php

namespace App\Livewire\HumanOps\Insights;

use Livewire\Component;

class Index extends Component
{
    public function render(): mixed
    {
        return view('livewire.humanops.insights.index')
            ->layout('layouts.app', ['title' => 'Insights']);
    }
}
