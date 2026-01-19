<?php

namespace App\Livewire\Privacy\RolePermissions;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Update extends Component
{
    use Alert, WithLogging;

    public ?Role $role = null;

    public array $selectedPermissions = [];

    public bool $modal = false;

    public function render(): View
    {
        return view('livewire.privacy.role-permissions.update');
    }

    #[Computed]
    public function permissionsByGroup()
    {
        return Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');
    }

    #[On('load::role-permissions')]
    public function load(Role $role): void
    {
        if ($role->is_system && $role->name === 'admin') {
            $this->error('Cannot edit admin role permissions - admin has all permissions');
            return;
        }

        $this->role = $role;
        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
        $this->modal = true;
    }

    public function save(): void
    {
        // Check permission
        if (!Auth::user()->hasPermission('manage_permissions')) {
            $this->error('You do not have permission to manage permissions.');
            return;
        }

        if (!$this->role) {
            $this->error('Role not found');
            return;
        }

        $this->role->permissions()->sync($this->selectedPermissions);

        $this->logUpdate(Role::class, $this->role->id, ['permissions' => 'Updated role permissions']);

        $this->dispatch('updated');

        $this->reset('modal', 'selectedPermissions');

        $this->success('Role permissions updated successfully');
    }
}
