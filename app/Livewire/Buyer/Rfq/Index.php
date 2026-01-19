<?php

namespace App\Livewire\Buyer\Rfq;

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
        ['index' => 'deadline', 'label' => 'Deadline'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'items_count', 'label' => 'Items'],
        ['index' => 'quotes_count', 'label' => 'Quotes'],
        ['index' => 'created_at', 'label' => 'Created'],
        ['index' => 'action', 'sortable' => false],
    ];

    public function mount(): void
    {
        // Translate headers based on current locale
        $this->headers = TableHeaders::make($this->headers);
    }

    public function render(): View
    {
        return view('livewire.buyer.rfq.index');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $user = auth()->user();

        if ($this->quantity == 'all') {
            $this->quantity = Request::where('buyer_id', $user->id)->count();
        }

        return Request::query()
            ->where('buyer_id', $user->id)
            ->withCount(['items', 'quotes'])
            ->when($this->search !== null, fn (Builder $query) => $query->whereAny(['title', 'description'], 'like', '%'.trim($this->search).'%'))
            ->when($this->statusFilter !== null, fn (Builder $query) => $query->where('status', $this->statusFilter))
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }

    #[Computed]
    public function statuses(): array
    {
        return Request::query()
            ->where('buyer_id', auth()->id())
            ->select('status')
            ->distinct()
            ->pluck('status')
            ->toArray();
    }

    public function clearStatusFilter(): void
    {
        $this->statusFilter = null;
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }
}
