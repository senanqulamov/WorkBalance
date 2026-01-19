<?php

namespace App\Livewire\Orders;

use App\Enums\TableHeaders;
use App\Models\Order;
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

    public array $sort = [
        'column' => 'created_at',
        'direction' => 'desc',
    ];

    public array $headers = [
        ['index' => 'id', 'label' => '#'],
        ['index' => 'order_number', 'label' => 'Order Number'],
        ['index' => 'user', 'label' => 'User', 'sortable' => false],
        ['index' => 'markets', 'label' => 'Markets', 'sortable' => false],
        ['index' => 'total', 'label' => 'Total'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'created_at', 'label' => 'Created'],
        ['index' => 'action', 'label' => 'Actions', 'sortable' => false],
    ];

    public function mount(): void
    {
        $this->headers = TableHeaders::make($this->headers);
    }

    public function render(): View
    {
        return view('livewire.orders.index');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        if ($this->quantity == 'all') {
            $this->quantity = Order::count();
        }

        return Order::query()
            ->with(['user', 'items.market'])
            ->when($this->search !== null, fn (Builder $query) => $query->whereAny(['order_number', 'status'], 'like', '%'.trim($this->search).'%')
                ->orWhereHas('user', fn (Builder $q) => $q->where('name', 'like', '%'.trim($this->search).'%'))
                ->orWhereHas('items.market', fn (Builder $q) => $q->where('name', 'like', '%'.trim($this->search).'%'))
            )
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }
}
