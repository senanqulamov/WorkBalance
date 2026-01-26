<?php

namespace App\Livewire\Logs;

use App\Enums\TableHeaders;
use App\Models\ActivitySignal;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $quantity = 10;

    public ?string $search = null;

    public ?string $typeFilter = null;

    public array $sort = [
        'column' => 'occurred_at',
        'direction' => 'desc',
    ];

    public array $headers = [
        ['index' => 'id', 'label' => '#'],
        ['index' => 'action_type', 'label' => 'Action Type'],
        ['index' => 'description', 'label' => 'Description'],
        ['index' => 'team_id', 'label' => 'Team'],
        ['index' => 'occurred_at', 'label' => 'Occurred'],
        ['index' => 'action_column', 'label' => 'Actions', 'sortable' => false],
    ];

    public function mount(): void
    {
        $this->headers = TableHeaders::make($this->headers);
    }

    public function render(): View
    {
        return view('livewire.logs.index');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        if ($this->quantity == 'all') {
            $this->quantity = ActivitySignal::count();
        }

        return ActivitySignal::query()
            ->with('team')
            ->when($this->search !== null, fn (Builder $query) => $query->where(function ($q) {
                $q->whereAny(['action_type', 'description'], 'like', '%'.trim($this->search).'%')
                    ->orWhereHas('team', function ($teamQuery) {
                        $teamQuery->where('name', 'like', '%'.trim($this->search).'%');
                    });
            }))
            ->when($this->typeFilter !== null, fn (Builder $query) => $query->where('action_type', $this->typeFilter))
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }

    #[Computed]
    public function actionTypes(): array
    {
        return ActivitySignal::query()
            ->select('action_type')
            ->distinct()
            ->pluck('action_type')
            ->toArray();
    }

    public function clearTypeFilter(): void
    {
        $this->typeFilter = null;
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }
}
