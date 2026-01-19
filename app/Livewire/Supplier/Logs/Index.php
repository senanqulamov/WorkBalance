<?php

namespace App\Livewire\Supplier\Logs;

use App\Enums\TableHeaders;
use App\Models\Log;
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
        'column' => 'created_at',
        'direction' => 'desc',
    ];

    public array $headers = [
        ['index' => 'id', 'label' => '#'],
        ['index' => 'type', 'label' => 'Type'],
        ['index' => 'action', 'label' => 'Action'],
        ['index' => 'message', 'label' => 'Message'],
        ['index' => 'created_at', 'label' => 'Created'],
    ];

    public function mount(): void
    {
        $this->headers = TableHeaders::make($this->headers);
    }

    public function render(): View
    {
        return view('livewire.supplier.logs.index')
            ->layout('layouts.app');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $user = auth()->user();

        if ($this->quantity == 'all') {
            $this->quantity = Log::where('user_id', $user->id)->count();
        }

        return Log::query()
            ->where('user_id', $user->id)
            ->when($this->search !== null, function (Builder $query) {
                $term = '%'.trim($this->search).'%';
                $query->whereAny(['type', 'action', 'message'], 'like', $term);
            })
            ->when($this->typeFilter !== null, fn (Builder $query) => $query->where('type', $this->typeFilter))
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }

    #[Computed]
    public function logTypes(): array
    {
        return Log::query()
            ->where('user_id', auth()->id())
            ->select('type')
            ->distinct()
            ->pluck('type')
            ->toArray();
    }

    public function clearTypeFilter(): void
    {
        $this->typeFilter = null;
        $this->resetPage();
    }
}
