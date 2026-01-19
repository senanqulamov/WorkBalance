<?php

namespace App\Livewire\Buyer\Markets;

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
        ['index' => 'seller', 'label' => 'Seller'],
        ['index' => 'created_at', 'label' => 'Created'],
        ['index' => 'products_count', 'label' => 'Products'],
    ];

    public function mount(): void
    {
        // Translate headers based on current locale
        $this->headers = TableHeaders::make($this->headers);
    }

    public function render(): View
    {
        return view('livewire.buyer.markets.index');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        if ($this->quantity == 'all') {
            $this->quantity = Market::count();
        }

        $query = Market::query()
            ->with('seller')
            ->withCount('products')
            ->when($this->search !== null, fn (Builder $query) => $query->whereAny(['name', 'location'], 'like', '%'.trim($this->search).'%'));

        if ($this->sort['column'] === 'seller') {
            $query->join('users', 'markets.user_id', '=', 'users.id')
                ->select('markets.*')
                ->orderBy('users.name', $this->sort['direction']);
        } else {
            $query->orderBy($this->sort['column'], $this->sort['direction']);
        }

        return $query->paginate($this->quantity)
            ->withQueryString();
    }
}
