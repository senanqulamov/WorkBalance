<div>
    <x-modal
        :title="__('Create Worker')"
        wire
        x-on:open="setTimeout(() => $refs.name.focus(), 250)"
        size="2xl"
        blur="xl"
        x-on:seller::workers::create::open.window="show = true"
    >
        <form id="seller-worker-create" wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="{{ __('Full Name') }} *" x-ref="name" wire:model="worker.name" required />
                <x-input label="{{ __('Email Address') }} *" wire:model="worker.email" required />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-password
                    label="{{ __('Password') }} *"
                    wire:model="password"
                    rules
                    generator
                    x-on:generate="$wire.set('password_confirmation', $event.detail.password)"
                    required
                />
                <x-password
                    label="{{ __('Confirm Password') }} *"
                    wire:model="password_confirmation"
                    rules
                    required
                />
            </div>

            <div>
                <x-select.styled
                    :label="__('Assign Markets')"
                    wire:model="marketIds"
                    :options="$markets"
                    select="label:name|value:id"
                    searchable
                    multiple
                    :placeholder="__('Select markets')"
                />
                <div class="text-xs text-gray-500 mt-1">{{ __('A worker can be assigned to multiple markets.') }}</div>
            </div>
        </form>

        <x-slot:footer>
            <x-button type="submit" form="seller-worker-create">
                @lang('Save')
            </x-button>
        </x-slot:footer>
    </x-modal>
</div>
