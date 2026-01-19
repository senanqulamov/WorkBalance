<?php

namespace App\Livewire\Logs;

use App\Livewire\Traits\Alert;
use App\Models\Log;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class LogView extends Component
{
    use Alert;

    public bool $showDetailModal = false;

    public ?Log $selectedLog = null;

    public function render(): View
    {
        return view('livewire.logs.log-view');
    }

    #[On('load::log')]
    public function load(int $log): void
    {
        $this->selectedLog = Log::with('user')->find($log);
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedLog = null;
    }
}
