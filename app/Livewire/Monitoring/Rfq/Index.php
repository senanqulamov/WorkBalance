<?php

namespace App\Livewire\Monitoring\Rfq;

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
    public array $metrics = [];

    public function mount(): void
    {
        $this->headers = TableHeaders::make($this->headers);
        $this->loadMetrics();
    }

    public function loadMetrics(): void
    {
        $this->metrics = [
            'open' => Request::where('status', 'open')->count(),
            'draft' => Request::where('status', 'draft')->count(),
            'closed' => Request::where('status', 'closed')->count(),
            'due_3_days' => Request::where('status', 'open')->whereBetween('deadline', [now(), now()->addDays(3)])->count(),
            'overdue' => Request::where('status', 'open')->where('deadline', '<', now())->count(),
        ];
    }

    public function render(): View
    {
        return view('livewire.monitoring.rfq.index');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        if ($this->quantity == 'all') {
            $this->quantity = Request::count();
        }
        return Request::query()
            ->withCount(['items', 'quotes'])
            ->when($this->search !== null, fn (Builder $query) => $query->where(function($q) {
                $q->where('title', 'like', '%'.trim($this->search).'%')
                  ->orWhere('id', $this->search)
                  ->orWhere('status', 'like', '%'.trim($this->search).'%');
            }))
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }
}
