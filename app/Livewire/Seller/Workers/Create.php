<?php

namespace App\Livewire\Seller\Workers;

use App\Livewire\Traits\Alert;
use App\Models\Market;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    use Alert;

    public User $worker;

    public ?string $password = null;

    public ?string $password_confirmation = null;

    public bool $modal = false;

    /** @var array<int> */
    public array $marketIds = [];

    public function mount(): void
    {
        $this->worker = new User([
            'is_active' => true,
        ]);
    }

    public function render(): View
    {
        $seller = Auth::user();

        $markets = Market::query()
            ->where('user_id', $seller->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.seller.workers.create', [
            'markets' => $markets,
        ]);
    }

    public function rules(): array
    {
        return [
            'worker.name' => ['required', 'string', 'max:255'],
            'worker.email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'marketIds' => ['array'],
            'marketIds.*' => ['integer'],
        ];
    }

    public function save(): void
    {
        $seller = Auth::user();

        $this->validate();

        if (!empty($this->marketIds)) {
            $ownedCount = Market::query()
                ->where('user_id', $seller->id)
                ->whereIn('id', $this->marketIds)
                ->count();

            if ($ownedCount !== count($this->marketIds)) {
                $this->error(__('Invalid market selection.'));
                return;
            }
        }

        $this->worker->password = Hash::make((string) $this->password);
        $this->worker->email_verified_at = now();
        $this->worker->is_active = true;
        $this->worker->seller_id = $seller->id;

        // Legacy flags false (transition)
        $this->worker->is_admin = false;
        $this->worker->is_buyer = false;
        $this->worker->is_seller = false;
        $this->worker->is_supplier = false;
        $this->worker->role = 'market_worker';

        $this->worker->save();

        $marketWorkerRole = Role::where('name', 'market_worker')->first();
        if ($marketWorkerRole) {
            $this->worker->roles()->syncWithoutDetaching([$marketWorkerRole->id]);
        }

        if (!empty($this->marketIds)) {
            $this->worker->workerMarkets()->sync($this->marketIds);
        }

        $this->dispatch('created');

        $this->reset(['password', 'password_confirmation', 'marketIds', 'modal']);
        $this->mount();

        $this->success(__('Worker created successfully.'));
    }
}
