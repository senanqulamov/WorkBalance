<?php

namespace App\Livewire\Seller\Markets;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Market;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    use Alert, WithLogging;

    public Market $market;

    public bool $modal = false;

    public function mount(): void
    {
        $this->market = new Market;
    }

    public function render(): View
    {
        return view('livewire.seller.markets.create');
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
        if (! Auth::user()->hasPermission('create_markets')) {
            $this->error('You do not have permission to create markets.');

            return;
        }

        $this->market->user_id = Auth::id();

        $this->validate();

        $this->market->save();

        $this->logCreate(Market::class, $this->market->id, ['name' => $this->market->name]);

        $this->dispatch('created');

        $this->reset();
        $this->market = new Market;

        $this->success();
    }
}
