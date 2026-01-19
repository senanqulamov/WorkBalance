<div>
    <x-slide wire="modal" right size="lg" blur="md">
        <x-slot name="title">{{ __('Update Role: #:id', ['id' => $role?->id]) }}</x-slot>
        <form id="role-update-{{ $role?->id }}" wire:submit="save" class="space-y-6">

            <div class="grid grid-cols-1 gap-4">
                <x-input
                    label="{{ __('Role Name') }}"
                    wire:model.blur="role.name"
                    required
                    hint="{{ __('Lowercase, alphanumeric with dashes/underscores only') }}"
                />

                <x-input
                    label="{{ __('Display Name') }}"
                    wire:model.blur="role.display_name"
                    required
                    hint="{{ __('Human-readable name') }}"
                />

                <x-textarea
                    label="{{ __('Description') }}"
                    wire:model.blur="role.description"
                    hint="{{ __('Brief description of this role') }}"
                    rows="3"
                />
            </div>

            <x-button
                type="submit"
                form="role-update-{{ $role?->id }}"
                color="primary"
                loading="save"
                icon="check"
            >
                {{ __('Save Changes') }}
            </x-button>
        </form>
    </x-slide>
</div>
