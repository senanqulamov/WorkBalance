<?php

namespace App\Livewire\Users;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    use Alert, WithLogging;

    public string $name = '';
    public string $email = '';

    public ?string $password = null;
    public ?string $password_confirmation = null;

    /** @var array<int> */
    public array $roleIds = [];

    public bool $modal = false;

    public function mount(): void
    {
        $buyerRole = Role::where('name', 'buyer')->first();
        if ($buyerRole) {
            $this->roleIds = [$buyerRole->id];
        }
    }

    public function render(): View
    {
        // Exclude market_worker (seller-owned accounts only)
        $roles = Role::query()
            ->whereIn('name', ['admin', 'buyer', 'seller', 'supplier'])
            ->orderBy('name')
            ->get(['id', 'name', 'display_name']);

        return view('livewire.users.create', [
            'roles' => $roles,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roleIds' => ['required', 'array', 'min:1'],
            'roleIds.*' => ['integer', Rule::exists('roles', 'id')],
        ];
    }

    public function save(): void
    {
        if (!Auth::user()->hasPermission('create_users')) {
            $this->error('You do not have permission to create users.');
            return;
        }

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            $this->error($firstError);
            throw $e;
        }

        $user = new User();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->password = Hash::make((string) $this->password);
        $user->email_verified_at = now();
        $user->is_active = true;

        // Legacy flags off by default (transition period)
        $user->is_admin = false;
        $user->is_buyer = false;
        $user->is_seller = false;
        $user->is_supplier = false;
        $user->role = 'buyer';

        $user->save();

        // Attach pivot roles
        $user->roles()->sync($this->roleIds);

        // Transition: keep booleans in sync for now (UI still uses them)
        $roleNames = $user->roles()->pluck('name')->all();
        $user->forceFill([
            'is_admin' => in_array('admin', $roleNames, true),
            'is_buyer' => in_array('buyer', $roleNames, true),
            'is_seller' => in_array('seller', $roleNames, true),
            'is_supplier' => in_array('supplier', $roleNames, true),
            'role' => in_array('admin', $roleNames, true) ? 'admin' : ($roleNames[0] ?? 'buyer'),
        ])->saveQuietly();

        // Supplier defaults (keep behavior)
        if ($user->hasRole('supplier') && empty($user->supplier_status)) {
            $user->forceFill(['supplier_status' => 'pending'])->saveQuietly();
        }
        if (!$user->hasRole('supplier') && empty($user->supplier_status)) {
            $user->forceFill(['supplier_status' => 'inactive'])->saveQuietly();
        }

        $this->logCreate(User::class, $user->id, [
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoles(),
        ]);

        // Notify + close modal + refresh list
        $this->dispatch('created');
        $this->success(__('User created successfully.'));

        // Reset form
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'modal']);

        // Re-set default buyer role for next use
        $buyerRole = Role::where('name', 'buyer')->first();
        if ($buyerRole) {
            $this->roleIds = [$buyerRole->id];
        }

        // Redirect to user details (no auto-open edit modal)
        $this->redirect(route('users.show', $user), navigate: true);
    }
}
