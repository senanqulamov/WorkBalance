<?php

namespace App\Livewire\Seller\Orders;

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
        ['index' => 'markets', 'label' => 'Markets', 'sortable' => false],
        ['index' => 'total', 'label' => 'Total'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'created_at', 'label' => 'Created'],
        ['index' => 'action', 'sortable' => false],
    ];

    public function render(): View
    {
        return view('livewire.seller.orders.index');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $user = auth()->user();

        if ($this->quantity == 'all') {
            $this->quantity = $this->baseQuery($user->id)->count();
        }

        return $this->baseQuery($user->id)
            ->when($this->search !== null, function (Builder $query) {
                $term = '%'.trim($this->search).'%';

                $query->whereAny(['order_number', 'status'], 'like', $term)
                    ->orWhereHas('items.market', fn (Builder $q) => $q->where('name', 'like', $term));
            })
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }

    protected function baseQuery(int $sellerId): Builder
    {
        return Order::query()
            ->whereHas('items.market', function (Builder $q) use ($sellerId) {
                $q->where('user_id', $sellerId);
            })
            ->with(['items.market', 'items.product', 'user']);
    }
}
