<?php

namespace App\Livewire\Supplier\Quotes;

use App\Enums\TableHeaders;
use App\Models\Quote;
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
        ['index' => 'request', 'label' => 'RFQ'],
        ['index' => 'total_amount', 'label' => 'Total Amount'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'valid_until', 'label' => 'Valid Until'],
        ['index' => 'submitted_at', 'label' => 'Submitted'],
        ['index' => 'action', 'label' => 'Actions', 'sortable' => false],
    ];

    public function mount(): void
    {
        $this->headers = TableHeaders::make($this->headers);
    }

    public function render(): View
    {
        return view('livewire.supplier.quotes.index')
            ->layout('layouts.app');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        if ($this->quantity == 'all') {
            $this->quantity = Quote::where('supplier_id', auth()->id())->count();
        }

        return Quote::query()
            ->with(['request', 'request.buyer'])
            ->where('supplier_id', auth()->id())
            ->when($this->search !== null, function (Builder $query) {
                $query->whereHas('request', function (Builder $q) {
                    $q->where('title', 'like', '%'.trim($this->search).'%');
                });
            })
            ->when($this->statusFilter !== null, fn (Builder $query) => $query->where('status', $this->statusFilter))
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }
}
