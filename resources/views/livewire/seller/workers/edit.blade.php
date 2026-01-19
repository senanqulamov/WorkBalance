<div>
    <x-card>
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold">{{ __('Edit Worker') }}</h1>
            <x-button icon="arrow-left" href="{{ route('seller.workers.index') }}">{{ __('Back') }}</x-button>
        </div>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="{{ __('Name') }}" wire:model="name" required />
                <x-input label="{{ __('Email') }}" wire:model="email" required />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-password label="{{ __('New Password') }}" wire:model="password" rules />
                <x-password label="{{ __('Confirm Password') }}" wire:model="password_confirmation" rules />
            </div>

            <div>
                <x-select.native
                    label="{{ __('Assign Markets') }}"
                    wire:model="marketIds"
                    multiple
                    :options="$markets->map(fn($m) => ['label' => $m->name, 'value' => $m->id])->toArray()"
                    select="label:label|value:value"
                />
                <div class="text-xs text-gray-500 mt-1">{{ __('A worker can be assigned to multiple markets.') }}</div>
            </div>

            <div class="flex gap-2">
                <x-button type="submit" icon="check">{{ __('Save') }}</x-button>
                <x-button color="secondary" href="{{ route('seller.workers.index') }}">{{ __('Cancel') }}</x-button>
                <x-button color="red" icon="trash" wire:click.prevent="delete">{{ __('Delete') }}</x-button>
            </div>
        </form>
    </x-card>
</div>
