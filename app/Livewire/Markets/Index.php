<?php

namespace App\Livewire\Markets;

use App\Enums\TableHeaders;
use App\Models\Market;
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
        ['index' => 'name', 'label' => 'Name'],
        ['index' => 'location', 'label' => 'Location'],
        ['index' => 'owner', 'label' => 'Owner', 'sortable' => false],
        ['index' => 'created_at', 'label' => 'Created'],
        ['index' => 'products_count', 'label' => 'Products'],
        ['index' => 'action', 'label' => 'Actions', 'sortable' => false],
    ];

    public function mount(): void
    {
        $this->headers = TableHeaders::make($this->headers);
    }

    public function render(): View
    {
        return view('livewire.markets.index');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        if ($this->quantity == 'all') {
            $this->quantity = Market::count();
        }

        return Market::query()
            ->with('seller')
            ->withCount('products')
            ->when($this->search !== null, fn (Builder $query) => $query->whereAny(['name', 'location'], 'like', '%'.trim($this->search).'%'))
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }
}
