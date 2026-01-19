<?php

namespace App\Livewire\Supplier\Orders;

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

    public ?string $statusFilter = null;

    public array $sort = [
        'column' => 'created_at',
        'direction' => 'desc',
    ];

    public array $headers = [
        ['index' => 'id', 'label' => '#'],
        ['index' => 'buyer', 'label' => 'Buyer'],
        ['index' => 'total', 'label' => 'Total'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'items_count', 'label' => 'Items'],
        ['index' => 'created_at', 'label' => 'Date'],
        ['index' => 'action', 'label' => 'Actions', 'sortable' => false],
    ];

    public function mount(): void
    {
        $this->headers = TableHeaders::make($this->headers);
    }

    public function render(): View
    {
        return view('livewire.supplier.orders.index')
            ->layout('layouts.app');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $supplier = auth()->user();

        if ($this->quantity == 'all') {
            $this->quantity = Order::where('user_id', $supplier->id)->count();
        }

        // Show only orders created by this supplier
        return Order::query()
            ->with(['user'])
            ->withCount('items')
            ->where('user_id', $supplier->id) // Supplier's own orders
            ->when($this->search !== null, function (Builder $query) {
                $term = '%'.trim($this->search).'%';
                $query->where('id', 'like', $term)
                    ->orWhere('status', 'like', $term);
            })
            ->when($this->statusFilter !== null, fn (Builder $query) => $query->where('status', $this->statusFilter))
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }
}
