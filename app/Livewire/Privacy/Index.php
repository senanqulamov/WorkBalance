<?php

namespace App\Livewire\Privacy;

use App\Enums\TableHeaders;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $tab = 'users';

    public $quantity = 10;

    public ?string $search = null;

    /**
     * Currently selected role filter; null means "all roles".
     */
    public ?int $roleFilter = null;

    public array $sort = [
        'column' => 'name',
        'direction' => 'asc',
    ];

    public array $headers = [
        ['index' => 'id', 'label' => '#'],
        ['index' => 'name', 'label' => 'Name'],
        ['index' => 'email', 'label' => 'Email'],
        ['index' => 'roles', 'label' => 'Roles', 'sortable' => false],
        ['index' => 'created_at', 'label' => 'Joined'],
        ['index' => 'action', 'label' => 'Actions', 'sortable' => false],
    ];

    public function mount(): void
    {
        $this->headers = TableHeaders::make($this->headers);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.privacy.index');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        if ($this->quantity == 'all') {
            $this->quantity = User::count();
        }

        return User::query()
            ->with('roles')
            ->when($this->roleFilter, function (Builder $query) {
                $query->whereHas('roles', function (Builder $roleQuery) {
                    $roleQuery->where('roles.id', $this->roleFilter);
                });
            })
            ->when($this->search !== null, fn (Builder $query) => $query->whereAny(['name', 'email'], 'like', '%'.trim($this->search).'%'))
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }

    #[Computed]
    public function roles()
    {
        return Role::withCount('users')->orderBy('name')->get();
    }

    #[Computed]
    public function permissions()
    {
        return Permission::orderBy('group')->orderBy('name')->get();
    }

    #[Computed]
    public function permissionsByGroup()
    {
        return $this->permissions->groupBy('group');
    }
}
