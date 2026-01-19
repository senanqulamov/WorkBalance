<div>
    <x-modal
        :title="__('Update Worker')"
        wire
        size="2xl"
        blur="xl"
    >
        <form id="seller-worker-update" wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="{{ __('Full Name') }} *" wire:model="worker.name" required />
                <x-input label="{{ __('Email Address') }} *" wire:model="worker.email" required />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-password
                    label="{{ __('New Password') }}"
                    wire:model="password"
                    rules
                    generator
                    x-on:generate="$wire.set('password_confirmation', $event.detail.password)"
                    hint="{{ __('Leave empty to keep current password') }}"
                />
                <x-password
                    label="{{ __('Confirm Password') }}"
                    wire:model="password_confirmation"
                    rules
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
            </div>
        </form>

        <x-slot:footer>
            <div class="flex items-center justify-between w-full">
                <x-button color="red" wire:click="delete">
                    @lang('Delete')
                </x-button>

                <x-button type="submit" form="seller-worker-update">
                    @lang('Save')
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
