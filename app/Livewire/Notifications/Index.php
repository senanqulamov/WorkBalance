<?php

namespace App\Livewire\Notifications;

use Illuminate\Contracts\View\View;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public int|string $quantity = 10;
    public ?string $type = null;

    public function render(): View
    {
        return view('livewire.notifications.index');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $user = Auth::user();
        $query = $user->notifications()->when($this->type, fn ($q) => $q->where('type', $this->type));

        if ($this->quantity === 'all') {
            $this->quantity = $query->count();
        }

        return $query->orderBy('created_at', 'desc')->paginate($this->quantity)->withQueryString();
    }

    #[Computed]
    public function types(): array
    {
        $user = Auth::user();
        return $user->notifications()->select('type')->distinct()->reorder()->pluck('type')->toArray();
    }

    public function markAllRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function markRead(string $id): void
    {
        $n = Auth::user()->notifications()->find($id);
        if ($n && !$n->read_at) {
            $n->markAsRead();
        }
    }
}
