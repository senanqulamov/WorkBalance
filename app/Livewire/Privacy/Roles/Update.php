<?php

namespace App\Livewire\Privacy\Roles;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class Update extends Component
{
    use Alert, WithLogging;

    public ?Role $role = null;

    public bool $modal = false;

    public function render(): View
    {
        return view('livewire.privacy.roles.update');
    }

    #[On('load::role')]
    public function load(Role $role): void
    {
        if ($role->is_system) {
            $this->error('Cannot edit system roles');
            return;
        }

        $this->role = $role;
        $this->modal = true;
    }

    public function rules(): array
    {
        return [
            'role.name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($this->role->id),
                'alpha_dash',
            ],
            'role.display_name' => [
                'required',
                'string',
                'max:255'
            ],
            'role.description' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    public function save(): void
    {
        // Check permission
        if (!Auth::user()->hasPermission('manage_roles')) {
            $this->error('You do not have permission to edit roles.');
            return;
        }

        if (!$this->role) {
            $this->error('Role not found');
            return;
        }

        $this->validate();

        $changes = $this->role->getDirty();
        $this->role->save();

        $this->logUpdate(Role::class, $this->role->id, $changes);

        $this->dispatch('updated');

        $this->resetExcept('role');

        $this->success('Role updated successfully');
    }
}
