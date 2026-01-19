<?php

namespace App\Livewire\Markets;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Market;
use App\Models\User;
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
        $sellers = User::where('is_seller', true)->orderBy('name')->get();
        return view('livewire.markets.create', compact('sellers'));
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
        // Check permission
        if (!Auth::user()->hasPermission('create_markets')) {
            $this->error('You do not have permission to create markets.');
            return;
        }

        // If user_id is not set, use current authenticated user
        if (empty($this->market->user_id)) {
            $this->market->user_id = Auth::id();
        }

        $this->validate();

        $this->market->save();

        $this->logCreate(Market::class, $this->market->id, ['name' => $this->market->name]);

        $this->dispatch('created');

        $this->reset();
        $this->market = new Market;

        $this->success();
    }
}
