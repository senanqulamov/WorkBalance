<?php

namespace App\Livewire\Seller\Workers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public int $perPage = 10;

    public function render(): View
    {
        $seller = Auth::user();

        $workers = User::query()
            ->where('seller_id', $seller->id)
            ->whereHas('roles', fn ($q) => $q->where('name', 'market_worker'))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('livewire.seller.workers.index', [
            'workers' => $workers,
        ]);
    }
}
