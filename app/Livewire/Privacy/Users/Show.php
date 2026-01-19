<?php

namespace App\Livewire\Privacy\Users;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public User $user;

    public function render(): View
    {
        return view('livewire.privacy.users.show');
    }

    #[Computed]
    public function roles()
    {
        return $this->user->roles()->orderBy('name')->get();
    }

    #[Computed]
    public function permissions()
    {
        return $this->user->permissions()->sortBy('group');
    }

    #[Computed]
    public function permissionsByGroup()
    {
        return $this->permissions->groupBy('group');
    }

    #[Computed]
    public function roleFlags()
    {
        return [
            'admin' => $this->user->is_admin,
            'buyer' => $this->user->is_buyer,
            'seller' => $this->user->is_seller,
            'supplier' => $this->user->is_supplier,
        ];
    }
}
