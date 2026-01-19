<?php

namespace App\Livewire\Users;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Market;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class Update extends Component
{
    use Alert, WithLogging;

    public ?User $user;

    public ?string $password = null;

    public ?string $password_confirmation = null;

    /** @var array<int> */
    public array $roleIds = [];

    /** @var array<int> */
    public array $marketIds = [];

    public ?int $sellerOwnerId = null;

    public bool $modal = false;

    public function mount(User $user = null): void
    {
        $this->user = $user ?: new User([
            'is_active' => true,
        ]);

        $this->roleIds = $this->user->exists ? $this->user->roles()->pluck('roles.id')->all() : [];
        $this->marketIds = $this->user->exists ? $this->user->workerMarkets()->pluck('markets.id')->all() : [];
        $this->sellerOwnerId = $this->user->exists ? $this->user->seller_id : null;
    }

    public function render(): View
    {
        // Include market_worker for admin management.
        $roles = Role::query()
            ->whereIn('name', ['admin', 'buyer', 'seller', 'supplier', 'market_worker'])
            ->orderBy('name')
            ->get(['id', 'name', 'display_name']);

        $sellers = User::query()
            ->whereHas('roles', fn ($q) => $q->where('name', 'seller'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $markets = Market::query()
            ->orderBy('name')
            ->get(['id', 'name', 'user_id']);

        return view('livewire.users.update', [
            'roles' => $roles,
            'sellers' => $sellers,
            'markets' => $markets,
        ]);
    }

    #[On('load::user')]
    public function load(User $user): void
    {
        $this->user = $user;
        $this->roleIds = $user->roles()->pluck('roles.id')->all();
        $this->marketIds = $user->workerMarkets()->pluck('markets.id')->all();
        $this->sellerOwnerId = $user->seller_id;

        $this->reset(['password', 'password_confirmation']);

        $this->modal = true;
    }

    public function rules(): array
    {
        return [
            // Basic Information
            'user.name' => ['required', 'string', 'max:255'],
            'user.email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user->id)],

            // Roles (pivot)
            'roleIds' => ['required', 'array', 'min:1'],
            'roleIds.*' => ['integer', Rule::exists('roles', 'id')],

            // Market worker fields
            'sellerOwnerId' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'marketIds' => ['array'],
            'marketIds.*' => ['integer', Rule::exists('markets', 'id')],

            // Role flags (legacy)
            'user.is_buyer' => ['boolean'],
            'user.is_seller' => ['boolean'],
            'user.is_supplier' => ['boolean'],
            'user.is_active' => ['boolean'],

            // Buyer (Business Information)
            'user.company_name' => ['nullable', 'string', 'max:255'],
            'user.tax_id' => ['nullable', 'string', 'max:255'],
            'user.business_type' => ['nullable', 'string', 'max:255'],
            'user.business_description' => ['nullable', 'string', 'max:1000'],

            // Contact Information
            'user.phone' => ['nullable', 'string', 'max:255'],
            'user.mobile' => ['nullable', 'string', 'max:255'],
            'user.website' => ['nullable', 'url', 'max:255'],

            // Address
            'user.address_line1' => ['nullable', 'string', 'max:255'],
            'user.address_line2' => ['nullable', 'string', 'max:255'],
            'user.city' => ['nullable', 'string', 'max:255'],
            'user.state' => ['nullable', 'string', 'max:255'],
            'user.postal_code' => ['nullable', 'string', 'max:255'],
            'user.country' => ['nullable', 'string', 'max:255'],

            // Supplier Fields
            'user.supplier_code' => ['nullable', 'string', 'max:255', Rule::unique('users', 'supplier_code')->ignore($this->user->id)],
            'user.duns_number' => ['nullable', 'string', 'max:255'],
            'user.ariba_network_id' => ['nullable', 'string', 'max:255'],
            'user.currency' => ['nullable', 'string', 'max:10'],
            'user.credit_limit' => ['nullable', 'numeric', 'min:0'],
            'user.supplier_status' => ['nullable', 'in:pending,active,inactive,blocked'],

            // Seller Fields
            'user.commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'user.verified_seller' => ['boolean'],

            // Password
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],

            // Notes
            'user.notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function save(): void
    {
        if (!Auth::user()->hasPermission('edit_users')) {
            $this->error('You do not have permission to edit users.');
            return;
        }

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            $this->error($firstError);
            throw $e;
        }

        // If user is a market worker, validate seller->market ownership.
        $roleNamesSelected = Role::query()->whereIn('id', $this->roleIds)->pluck('name')->all();
        $isMarketWorker = in_array('market_worker', $roleNamesSelected, true);

        if ($isMarketWorker) {
            if (empty($this->sellerOwnerId)) {
                $this->error(__('Seller owner is required for market worker accounts.'));
                return;
            }

            if (!empty($this->marketIds)) {
                $ownedCount = Market::query()
                    ->where('user_id', $this->sellerOwnerId)
                    ->whereIn('id', $this->marketIds)
                    ->count();

                if ($ownedCount !== count($this->marketIds)) {
                    $this->error(__('Invalid market selection for the selected seller.'));
                    return;
                }
            }
        }

        $originalData = $this->user->getOriginal();

        if ($this->password !== null && !empty($this->password)) {
            $this->user->password = bcrypt($this->password);
        }

        // Assign worker owner (or clear if not worker)
        $this->user->seller_id = $isMarketWorker ? $this->sellerOwnerId : null;

        $this->user->save();

        // Pivot roles
        $this->user->roles()->sync($this->roleIds);

        // Worker market assignments
        if ($isMarketWorker) {
            $this->user->workerMarkets()->sync($this->marketIds);
        } else {
            $this->user->workerMarkets()->detach();
        }

        // Sync legacy flags
        $roleNames = $this->user->roles()->pluck('name')->all();
        $this->user->forceFill([
            'is_admin' => in_array('admin', $roleNames, true),
            'is_buyer' => in_array('buyer', $roleNames, true),
            'is_seller' => in_array('seller', $roleNames, true),
            'is_supplier' => in_array('supplier', $roleNames, true),
            'role' => in_array('admin', $roleNames, true) ? 'admin' : ($roleNames[0] ?? 'buyer'),
        ])->saveQuietly();

        // Supplier defaults
        if ($this->user->hasRole('supplier') && empty($this->user->supplier_status)) {
            $this->user->forceFill(['supplier_status' => 'pending'])->saveQuietly();
        }
        if (!$this->user->hasRole('supplier') && empty($this->user->supplier_status)) {
            $this->user->forceFill(['supplier_status' => 'inactive'])->saveQuietly();
        }

        if ($this->user->hasRole('supplier') && empty($this->user->supplier_code)) {
            $this->user->supplier_code = 'SUP-' . strtoupper(substr(uniqid(), -8));
            $this->user->saveQuietly();
        }

        $changes = [];
        foreach (['name', 'email', 'company_name', 'supplier_status', 'is_supplier', 'is_buyer', 'is_seller', 'is_active', 'seller_id'] as $field) {
            if ($this->user->wasChanged($field)) {
                $changes[$field] = ['old' => $originalData[$field] ?? null, 'new' => $this->user->$field];
            }
        }

        if ($this->password !== null && !empty($this->password)) {
            $changes['password'] = 'updated';
        }


        $this->logUpdate(User::class, $this->user->id, $changes);

        $this->dispatch('updated');

        $this->reset(['password', 'password_confirmation']);

        $this->success(__('User updated successfully.'));

        $this->modal = false;
    }
}
