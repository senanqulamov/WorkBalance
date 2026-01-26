<?php

namespace App\Livewire\Markets;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Market;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Update extends Component
{
    use Alert, WithLogging;

    public ?Market $market;

    public bool $modal = false;

    public function render(): View
    {
        $sellers = User::where('is_seller', true)->orderBy('name')->get();
        return view('livewire.markets.update', compact('sellers'));
    }

    #[On('load::market')]
    public function load(Market $market): void
    {
        $this->market = $market;

        $this->modal = true;
    }

    public function rules(): array
    {
        return [
            'market.user_id' => [
                'required',
                'exists:users,id',
            ],
            'market.name' => [
                'required',
                'string',
                'max:255'
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
        // Check permission
        if (!Auth::user()->hasPermission('edit_markets')) {
            $this->error('You do not have permission to edit markets.');
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
