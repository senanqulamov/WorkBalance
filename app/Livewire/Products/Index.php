<?php

namespace App\Livewire\Products;

use App\Enums\TableHeaders;
use App\Models\Product;
use App\Models\Category;
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

    public ?int $categoryFilter = null;

    public array $sort = [
        'column' => 'created_at',
        'direction' => 'desc',
    ];

    public array $headers = [
        ['index' => 'id', 'label' => '#'],
        ['index' => 'name', 'label' => 'Name'],
        ['index' => 'sku', 'label' => 'SKU'],
        ['index' => 'price', 'label' => 'Price'],
        ['index' => 'stock', 'label' => 'Stock'],
        ['index' => 'category', 'label' => 'Category'],
        ['index' => 'market', 'label' => 'Market'],
        ['index' => 'created_at', 'label' => 'Created'],
        ['index' => 'action', 'label' => 'Actions', 'sortable' => false],
    ];

    public function mount(): void
    {
        $this->headers = TableHeaders::make($this->headers);
    }

    public function render(): View
    {
        return view('livewire.products.index');
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('name')->get();
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        if ($this->quantity == 'all') {
            $this->quantity = Product::count();
        }
        return Product::query()
            ->with(['market', 'category'])
            ->when($this->search !== null, fn (Builder $query) => $query->where(function($q) {
                $q->where('name', 'like', '%'.trim($this->search).'%')
                  ->orWhere('sku', 'like', '%'.trim($this->search).'%')
                  ->orWhereHas('category', function($catQ) {
                      $catQ->where('name', 'like', '%'.trim($this->search).'%');
                  });
            }))
            ->when($this->categoryFilter !== null, fn (Builder $query) => $query->where('category_id', $this->categoryFilter))
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }
}
