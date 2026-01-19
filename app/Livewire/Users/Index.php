<?php

namespace App\Livewire\Users;

use App\Enums\TableHeaders;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public bool $slideA = false;

    public $quantity = 10;

    public ?string $search = null;

    public string $roleFilter = 'all';

    public array $sort = [
        'column' => 'created_at',
        'direction' => 'desc',
    ];

    public array $headers = [
        ['index' => 'id', 'label' => '#', 'sortable' => true],
        ['index' => 'name', 'label' => 'Name', 'sortable' => true],
        ['index' => 'email', 'label' => 'E-mail', 'sortable' => true],
        ['index' => 'created_at', 'label' => 'Created', 'sortable' => true],
        ['index' => 'action', 'label' => '', 'sortable' => false],
    ];

    public function mount(): void
    {
        $this->headers = TableHeaders::make($this->headers);
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.users.index');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        if ($this->quantity == 'all') {
            $this->quantity = User::count();
        }

        return User::query()
            ->with(['markets', 'roles:id,name,display_name'])
            ->whereNotIn('id', [Auth::id()])
            ->when($this->search !== null, fn (Builder $query) => $query->whereAny(['name', 'email'], 'like', '%'.trim($this->search).'%'))
            ->when($this->roleFilter === 'buyer', fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'buyer')))
            ->when($this->roleFilter === 'seller', fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'seller')))
            ->when($this->roleFilter === 'supplier', fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'supplier')))
            ->when($this->roleFilter === 'admin', fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'admin')))
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }
}
