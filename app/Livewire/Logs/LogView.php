<?php

namespace App\Livewire\Logs;

use App\Livewire\Traits\Alert;
use App\Models\ActivitySignal;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class LogView extends Component
{
    use Alert;

    public bool $showDetailModal = false;

    public ?ActivitySignal $selectedSignal = null;

    public function render(): View
    {
        return view('livewire.logs.log-view');
    }

    #[On('load::activity-signal')]
    public function load(int $signalId): void
    {
        $this->selectedSignal = ActivitySignal::with('team')->find($signalId);
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedSignal = null;
    }
}
