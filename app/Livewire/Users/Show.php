<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public User $user;

    public int $ordersQuantity = 10;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    #[On('supplier::updated')]
    public function refreshUser(): void
    {
        $this->user = $this->user->fresh();
    }

    public function render(): View
    {
        return view('livewire.users.show');
    }

    #[Computed]
    public function orders(): LengthAwarePaginator
    {
        return $this->user->orders()
            ->with(['markets:id,name'])
            ->orderByDesc('created_at')
            ->paginate($this->ordersQuantity, ['*'], 'orders_page');
    }

    #[Computed]
    public function marketsServed(): Collection
    {
        return $this->user->orders()
            ->with('markets:id,name')
            ->get()
            ->pluck('markets')
            ->flatten()
            ->unique('id')
            ->values();
    }

    #[Computed]
    public function suppliedProducts(): Collection
    {
        return $this->user->suppliedProducts()
            ->with('market:id,name')
            ->get();
    }

    #[Computed]
    public function metrics(): array
    {
        $ordersQuery = $this->user->orders();
        $ordersCount = (clone $ordersQuery)->count();
        $lifetimeValue = (clone $ordersQuery)->sum('total');
        $avgOrderValue = $ordersCount ? $lifetimeValue / $ordersCount : 0.0;

        return [
            'orders_count' => $ordersCount,
            'lifetime_value' => $lifetimeValue,
            'avg_order_value' => $avgOrderValue,
            'markets_count' => $this->marketsServed()->count(),
        ];
    }
}
