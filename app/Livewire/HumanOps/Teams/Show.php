<?php

namespace App\Livewire\HumanOps\Teams;

use Livewire\Component;

class Show extends Component
{
    public string $team;

    public function mount(string $team): void
    {
        $this->team = $team;
    }

    public function render(): mixed
    {
        return view('livewire.humanops.teams.show')
            ->layout('layouts.app', ['title' => 'Team Overview']);
    }
}
