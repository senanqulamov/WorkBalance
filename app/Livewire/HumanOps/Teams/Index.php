<?php

namespace App\Livewire\HumanOps\Teams;

use Livewire\Component;

class Index extends Component
{
    public function render(): mixed
    {
        return view('livewire.humanops.teams.index')
            ->layout('layouts.app', ['title' => 'Teams']);
    }
}
