<?php

namespace App\Livewire\Logs;

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
        ['index' => 'user_id', 'label' => 'User'],
        ['index' => 'ip_address', 'label' => 'IP Address'],
        ['index' => 'created_at', 'label' => 'Created'],
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
            $this->quantity = Log::count();
        }

        return Log::query()
            ->with('user')
            ->when($this->search !== null, fn (Builder $query) => $query->where(function ($q) {
                $q->whereAny(['type', 'action', 'message', 'ip_address'], 'like', '%'.trim($this->search).'%')
                    ->orWhereHas('user', function ($userQuery) {
                        $userQuery->where('name', 'like', '%'.trim($this->search).'%');
                    });
            }))
            ->when($this->typeFilter !== null, fn (Builder $query) => $query->where('type', $this->typeFilter))
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }

    #[Computed]
    public function logTypes(): array
    {
        return Log::query()
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

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }
}
