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

class Edit extends Component
{
    use Alert;

    public User $worker;

    public string $name = '';
    public string $email = '';
    public ?string $password = null;
    public ?string $password_confirmation = null;

    /** @var array<int> */
    public array $marketIds = [];

    public function mount(User $worker): void
    {
        $seller = Auth::user();

        // Only allow editing own worker accounts
        if ((int) $worker->seller_id !== (int) $seller->id) {
            abort(403);
        }

        $this->worker = $worker;
        $this->name = $worker->name;
        $this->email = $worker->email;
        $this->marketIds = $worker->workerMarkets()->pluck('markets.id')->all();
    }

    public function render(): View
    {
        $seller = Auth::user();

        $markets = Market::query()
            ->where('user_id', $seller->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.seller.workers.edit', [
            'markets' => $markets,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->worker->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'marketIds' => ['array'],
            'marketIds.*' => ['integer'],
        ];
    }

    public function save(): void
    {
        $seller = Auth::user();

        $this->validate();

        // Ensure selected markets belong to this seller
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

        $this->worker->name = $this->name;
        $this->worker->email = $this->email;

        if (!empty($this->password)) {
            $this->worker->password = Hash::make($this->password);
        }

        // Keep ownership stable
        $this->worker->seller_id = $seller->id;

        $this->worker->save();

        // Ensure market_worker role stays attached
        $marketWorkerRole = Role::where('name', 'market_worker')->first();
        if ($marketWorkerRole) {
            $this->worker->roles()->syncWithoutDetaching([$marketWorkerRole->id]);
        }

        $this->worker->workerMarkets()->sync($this->marketIds);

        $this->redirect(route('seller.workers.index'), navigate: true);
    }

    public function delete(): void
    {
        $seller = Auth::user();

        if ((int) $this->worker->seller_id !== (int) $seller->id) {
            abort(403);
        }

        $this->worker->workerMarkets()->detach();
        $this->worker->roles()->detach();
        $this->worker->delete();

        $this->redirect(route('seller.workers.index'), navigate: true);
    }
}
