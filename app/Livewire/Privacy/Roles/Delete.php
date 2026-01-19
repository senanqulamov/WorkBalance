<?php

namespace App\Livewire\Privacy\Roles;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Delete extends Component
{
    use Alert, WithLogging;

    public Role $role;

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <x-button.circle icon="trash" color="red" wire:click="confirm" />
        </div>
        HTML;
    }

    #[Renderless]
    public function confirm(): void
    {
        if ($this->role->is_system) {
            $this->error('Cannot delete system roles');
            return;
        }

        if ($this->role->users()->count() > 0) {
            $this->error('Cannot delete role with assigned users. Please remove all users first.');
            return;
        }

        $this->question()
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    public function delete(): void
    {
        // Check permission
        if (!Auth::user()->hasPermission('manage_roles')) {
            $this->error('You do not have permission to delete roles.');
            return;
        }

        $roleData = [
            'name' => $this->role->name,
            'display_name' => $this->role->display_name
        ];
        $roleId = $this->role->id;

        $this->role->delete();

        $this->logDelete(Role::class, $roleId, $roleData);

        $this->dispatch('deleted');

        $this->success('Role deleted successfully');
    }
}
