<?php

namespace App\Livewire\Rfq;

use App\Enums\TableHeaders;
use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use Alert, WithLogging, WithPagination;

    public string $status = 'all';

    public ?string $search = null;

    public $quantity = 10;

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
        ['index' => 'action', 'label' => 'Actions', 'sortable' => false],
    ];

    protected $queryString = [
        'status' => ['except' => 'all'],
        'search' => ['except' => ''],
        'quantity' => ['except' => 10],
        'sort' => ['except' => ['column' => 'created_at', 'direction' => 'desc']],
    ];

    public function mount(): void
    {
        $this->headers = TableHeaders::make($this->headers);
        $this->logPageView('RFQ Index');
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingQuantity(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.rfq.index');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        if ($this->quantity === 'all') {
            $this->quantity = Request::query()
                ->count();
        }

        $query = Request::query()
            ->withCount(['items', 'quotes']);

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if ($this->search) {
            $search = trim($this->search);
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sortableColumn = $this->sort['column'];
        $direction = $this->sort['direction'];

        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortableColumn, $direction);

        return $query
            ->paginate($this->quantity)
            ->withQueryString();
    }
}
