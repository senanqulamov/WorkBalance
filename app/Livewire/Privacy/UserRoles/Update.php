<?php

namespace App\Livewire\Privacy\UserRoles;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Update extends Component
{
    use Alert, WithLogging;

    public ?User $user = null;

    public array $selectedRoles = [];

    public bool $modal = false;

    public function render(): View
    {
        $roles = Role::orderBy('name')->get();
        return view('livewire.privacy.user-roles.update', compact('roles'));
    }

    #[On('load::user-roles')]
    public function load(User $user): void
    {
        $this->user = $user;
        $this->selectedRoles = $user->roles->pluck('id')->toArray();
        $this->modal = true;
    }

    public function save(): void
    {
        // Check permission
        if (!Auth::user()->hasPermission('manage_roles')) {
            $this->error('You do not have permission to manage user roles.');
            return;
        }

        if (!$this->user) {
            $this->error('User not found');
            return;
        }

        $this->user->roles()->sync($this->selectedRoles);

        // Update role flags based on assigned roles
        $roleNames = Role::whereIn('id', $this->selectedRoles)->pluck('name')->toArray();

        $this->user->update([
            'is_buyer' => in_array('buyer', $roleNames),
            'is_seller' => in_array('seller', $roleNames),
            'is_supplier' => in_array('supplier', $roleNames),
            'is_admin' => in_array('admin', $roleNames),
            'role' => in_array('admin', $roleNames) ? 'admin' : (in_array('buyer', $roleNames) ? 'buyer' : (in_array('seller', $roleNames) ? 'seller' : (in_array('supplier', $roleNames) ? 'supplier' : 'user'))),
        ]);

        $this->logUpdate(User::class, $this->user->id, ['roles' => 'Updated user roles']);

        $this->dispatch('updated');

        $this->reset('modal', 'selectedRoles');

        $this->success('User roles updated successfully');
    }
}
