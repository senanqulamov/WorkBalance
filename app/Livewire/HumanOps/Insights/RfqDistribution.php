<?php

namespace App\Livewire\HumanOps\Charts;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class RfqDistribution extends Component
{
    public $data = [];
    public $labels = [];

    public function mount($data = [])
    {
        $this->data = $data;
        $this->labels = collect($data)->keys()->map(function($k) {
            return __(str_replace('_', ' ', ucfirst($k)));
        })->values()->toArray();
    }

    public function render(): View
    {
        return view('livewire.humanops.charts.rfq-distribution', [
            'data' => $this->data,
            'labels' => $this->labels,
        ]);
    }
}
