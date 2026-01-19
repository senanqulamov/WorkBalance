<?php

namespace App\Livewire\Privacy\Roles;

use App\Enums\TableHeaders;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public Role $role;

    public $quantity = 10;

    public ?string $search = null;

    public array $sort = [
        'column' => 'name',
        'direction' => 'asc',
    ];

    public array $headers = [
        ['index' => 'id', 'label' => '#'],
        ['index' => 'name', 'label' => 'Name'],
        ['index' => 'email', 'label' => 'Email'],
        ['index' => 'created_at', 'label' => 'Joined'],
    ];

    public function mount(): void
    {
        $this->headers = TableHeaders::make($this->headers);
    }

    public function render(): View
    {
        return view('livewire.privacy.roles.show');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        if ($this->quantity == 'all') {
            $this->quantity = $this->role->users()->count();
        }

        return $this->role->users()
            ->when($this->search !== null, fn (Builder $query) => $query->whereAny(['name', 'email'], 'like', '%'.trim($this->search).'%'))
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }

    #[Computed]
    public function permissions()
    {
        return $this->role->permissions()->orderBy('group')->orderBy('name')->get();
    }

    #[Computed]
    public function permissionsByGroup()
    {
        return $this->permissions->groupBy('group');
    }
}
