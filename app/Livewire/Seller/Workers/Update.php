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
use Livewire\Attributes\On;
use Livewire\Component;

class Update extends Component
{
    use Alert;

    public ?User $worker = null;

    public bool $modal = false;

    public ?string $password = null;

    public ?string $password_confirmation = null;

    /** @var array<int> */
    public array $marketIds = [];

    public function render(): View
    {
        $seller = Auth::user();

        $markets = Market::query()
            ->where('user_id', $seller->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.seller.workers.update', [
            'markets' => $markets,
        ]);
    }

    #[On('seller::workers::load')]
    public function load(User $worker): void
    {
        $seller = Auth::user();

        if ((int) $worker->seller_id !== (int) $seller->id) {
            $this->error(__('You are not allowed to edit this worker.'));
            return;
        }

        $this->worker = $worker;
        $this->marketIds = $worker->workerMarkets()->pluck('markets.id')->all();
        $this->modal = true;
    }

    public function rules(): array
    {
        return [
            'worker.name' => ['required', 'string', 'max:255'],
            'worker.email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->worker?->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'marketIds' => ['array'],
            'marketIds.*' => ['integer'],
        ];
    }

    public function save(): void
    {
        $seller = Auth::user();

        if (!$this->worker || (int) $this->worker->seller_id !== (int) $seller->id) {
            $this->error(__('You are not allowed to edit this worker.'));
            return;
        }

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

        if (!empty($this->password)) {
            $this->worker->password = Hash::make((string) $this->password);
        }

        $this->worker->save();

        $marketWorkerRole = Role::where('name', 'market_worker')->first();
        if ($marketWorkerRole) {
            $this->worker->roles()->syncWithoutDetaching([$marketWorkerRole->id]);
        }

        $this->worker->workerMarkets()->sync($this->marketIds);

        $this->dispatch('updated');

        $this->reset(['password', 'password_confirmation', 'marketIds', 'modal']);

        $this->success(__('Worker updated successfully.'));
    }

    public function delete(): void
    {
        $seller = Auth::user();

        if (!$this->worker || (int) $this->worker->seller_id !== (int) $seller->id) {
            $this->error(__('You are not allowed to delete this worker.'));
            return;
        }

        $this->worker->workerMarkets()->detach();
        $this->worker->roles()->detach();
        $this->worker->delete();

        $this->dispatch('deleted');

        $this->reset(['password', 'password_confirmation', 'marketIds', 'modal', 'worker']);

        $this->success(__('Worker deleted successfully.'));
    }
}
