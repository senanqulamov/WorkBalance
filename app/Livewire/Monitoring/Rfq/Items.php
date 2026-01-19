<?php

namespace App\Livewire\Monitoring\Rfq;

use App\Models\Request;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Items extends Component
{
    public ?Request $request = null;

    public bool $modal = false;

    public function render(): View
    {
        return view('livewire.monitoring.rfq.items');
    }

    #[On('monitoring::load::rfq_items')]
    public function load(int $rfq): void
    {
        $request = Request::with('items')->find($rfq);

        if (! $request) {
            $this->modal = false;
            $this->request = null;
            return;
        }

        $this->request = $request;
        $this->modal = true;
    }

    public function close(): void
    {
        $this->modal = false;
        $this->request = null;
    }
}
