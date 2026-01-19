<?php

namespace App\Livewire\Seller\Markets;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Market;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Delete extends Component
{
    use Alert, WithLogging;

    public Market $market;

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <x-button.circle icon="trash" color="red" wire:click="confirm" />
        </div>
        HTML;
    }

    #[Renderless]
    public function confirm(): void
    {
        if (! Auth::user()->hasPermission('delete_markets')) {
            $this->error('You do not have permission to delete markets.');

            return;
        }

        if ($this->market->user_id !== Auth::id()) {
            $this->error('You are not allowed to delete this market.');

            return;
        }

        $this->question()
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    public function delete(): void
    {
        if (! Auth::user()->hasPermission('delete_markets')) {
            $this->error('You do not have permission to delete markets.');

            return;
        }

        if ($this->market->user_id !== Auth::id()) {
            $this->error('You are not allowed to delete this market.');

            return;
        }

        $marketData = ['name' => $this->market->name, 'location' => $this->market->location];
        $marketId = $this->market->id;

        $this->market->delete();

        $this->logDelete(Market::class, $marketId, $marketData);

        $this->dispatch('deleted');

        $this->success();
    }
}
