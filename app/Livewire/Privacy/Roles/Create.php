<?php

namespace App\Livewire\Privacy\Roles;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    use Alert, WithLogging;

    public Role $role;

    public bool $modal = false;

    public function mount(): void
    {
        $this->role = new Role;
    }

    public function render(): View
    {
        return view('livewire.privacy.roles.create');
    }

    public function rules(): array
    {
        return [
            'role.name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name',
                'alpha_dash',
            ],
            'role.display_name' => [
                'required',
                'string',
                'max:255',
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
            $this->error('You do not have permission to create roles.');
            return;
        }

        $this->validate();

        $this->role->is_system = false;
        $this->role->save();

        $this->logCreate(Role::class, $this->role->id, ['name' => $this->role->name]);

        $this->dispatch('created');

        $this->reset();
        $this->role = new Role;

        $this->success('Role created successfully');
    }
}
