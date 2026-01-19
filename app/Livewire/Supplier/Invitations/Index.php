<?php

namespace App\Livewire\Supplier\Invitations;

use App\Enums\TableHeaders;
use App\Models\SupplierInvitation;
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
        ['index' => 'request_id', 'label' => 'RFQ #'],
        ['index' => 'title', 'label' => 'Title'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'deadline', 'label' => 'Deadline'],
        ['index' => 'invited_at', 'label' => TableHeaders::InvitedAt],
        ['index' => 'action', 'label' => 'Actions', 'sortable' => false],
    ];

    public function mount(): void
    {
        // Translate headers based on current locale
        $this->headers = TableHeaders::make($this->headers);
    }

    public function render(): View
    {
        return view('livewire.supplier.invitations.index')
            ->layout('layouts.app');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        if ($this->quantity == 'all') {
            $this->quantity = SupplierInvitation::where('supplier_id', auth()->id())->count();
        }

        return SupplierInvitation::query()
            ->with(['request', 'request.buyer'])
            ->where('supplier_id', auth()->id())
            ->when($this->search !== null, function (Builder $query) {
                $query->whereHas('request', function (Builder $q) {
                    $q->where('title', 'like', '%'.trim($this->search).'%')
                      ->orWhere('description', 'like', '%'.trim($this->search).'%');
                });
            })
            ->when($this->statusFilter !== null, fn (Builder $query) => $query->where('status', $this->statusFilter))
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }

    public function acceptInvitation($invitationId): void
    {
        $invitation = SupplierInvitation::where('id', $invitationId)
            ->where('supplier_id', auth()->id())
            ->firstOrFail();

        $invitation->update([
            'status' => 'accepted',
            'responded_at' => now(),
        ]);

        $this->dispatch('invitation-accepted');
    }

    public function declineInvitation($invitationId): void
    {
        $invitation = SupplierInvitation::where('id', $invitationId)
            ->where('supplier_id', auth()->id())
            ->firstOrFail();

        $invitation->update([
            'status' => 'declined',
            'responded_at' => now(),
        ]);

        $this->dispatch('invitation-declined');
    }
}
