<?php

namespace App\Livewire\Users;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Delete extends Component
{
    use Alert, WithLogging;

    public User $user;

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
        // Check permission
        if (!Auth::check() || !Auth::user()->hasPermission('delete_users')) {
            $this->error('You do not have permission to delete users.');
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
        if (!Auth::check() || !Auth::user()->hasPermission('delete_users')) {
            $this->error('You do not have permission to delete users.');
            return;
        }

        // Store user data before deletion
        $userData = [
            'name' => $this->user->name,
            'email' => $this->user->email,
        ];
        $userId = $this->user->id;
        $this->user->delete();

        // Log the deletion
        $this->logDelete(User::class, $userId, $userData);


        $this->dispatch('deleted');

        $this->success();
    }
}
