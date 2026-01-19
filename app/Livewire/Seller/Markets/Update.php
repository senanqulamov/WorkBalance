<?php

namespace App\Livewire\Seller\Markets;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Market;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Update extends Component
{
    use Alert, WithLogging;

    public ?Market $market = null;

    public bool $modal = false;

    public function render(): View
    {
        return view('livewire.seller.markets.update');
    }

    #[On('seller::load::market')]
    public function load(Market $market): void
    {
        if ($market->user_id !== Auth::id()) {
            $this->error('You are not allowed to edit this market.');

            return;
        }

        $this->market = $market;

        $this->modal = true;
    }

    public function rules(): array
    {
        return [
            'market.name' => [
                'required',
                'string',
                'max:255',
            ],
            'market.location' => [
                'nullable',
                'string',
                'max:255',
            ],
            'market.image_path' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function save(): void
    {
        if (! Auth::user()->hasPermission('edit_markets')) {
            $this->error('You do not have permission to edit markets.');

            return;
        }

        if (! $this->market || $this->market->user_id !== Auth::id()) {
            $this->error('You are not allowed to edit this market.');

            return;
        }

        $this->validate();

        $changes = $this->market->getDirty();
        $this->market->save();

        $this->logUpdate(Market::class, $this->market->id, $changes);

        $this->dispatch('updated');

        $this->resetExcept('market');

        $this->success();
    }
}
