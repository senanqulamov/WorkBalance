<?php

namespace App\Livewire\Supplier\Rfq;

use App\Enums\TableHeaders;
use App\Models\Request;
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

    public ?string $statusFilter = null;

    public array $sort = [
        'column' => 'created_at',
        'direction' => 'desc',
    ];

    public array $headers = [
        ['index' => 'id', 'label' => '#'],
        ['index' => 'title', 'label' => 'Title'],
        ['index' => 'buyer', 'label' => 'Buyer'],
        ['index' => 'deadline', 'label' => 'Deadline'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'items_count', 'label' => 'Items'],
        ['index' => 'created_at', 'label' => 'Posted'],
        ['index' => 'action', 'label' => 'Actions', 'sortable' => false],
    ];

    public function mount(): void
    {
        $this->headers = TableHeaders::make($this->headers);
    }

    public function render(): View
    {
        return view('livewire.supplier.rfq.index')
            ->layout('layouts.app');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        if ($this->quantity == 'all') {
            $this->quantity = Request::count();
        }

        return Request::query()
            ->with(['buyer', 'items'])
            ->withCount(['items', 'quotes'])
            ->when($this->search !== null, fn (Builder $query) => $query->whereAny(['title', 'description'], 'like', '%'.trim($this->search).'%'))
            ->when($this->statusFilter !== null, fn (Builder $query) => $query->where('status', $this->statusFilter))
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }
}
