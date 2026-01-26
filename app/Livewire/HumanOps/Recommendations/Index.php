<?php

namespace App\Livewire\HumanOps\Recommendations;

use App\Models\Recommendation;
use App\Services\HumanOps\HumanOpsDataService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $filterPriority = 'all';
    public bool $showAcknowledged = false;

    public function mount(HumanOpsDataService $data): void
    {
        $data->logView('recommendations');
    }

    public function updatingFilterPriority(): void
    {
        $this->resetPage();
    }

    public function updatingShowAcknowledged(): void
    {
        $this->resetPage();
    }

    public function acknowledgeRecommendation(int $id): void
    {
        $recommendation = Recommendation::find($id);

        if ($recommendation && $recommendation->acknowledged_at === null) {
            $recommendation->update(['acknowledged_at' => now()]);
            session()->flash('message', 'Recommendation acknowledged.');
        }
    }

    public function getRecommendationsProperty()
    {
        $query = Recommendation::query()
            ->with('department')
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderByDesc('generated_at');

        if ($this->filterPriority !== 'all') {
            $query->where('priority', $this->filterPriority);
        }

        if (!$this->showAcknowledged) {
            $query->whereNull('acknowledged_at');
        }

        return $query->paginate(10);
    }

    public function render(): View
    {
        return view('livewire.humanops.recommendations.index', [
            'recommendations' => $this->recommendations,
        ]);
    }
}
