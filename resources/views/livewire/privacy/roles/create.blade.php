<div>
    <x-button :text="__('Create New Role')" wire:click="$toggle('modal')" icon="plus" sm />

    <x-modal :title="__('Create New Role')" wire x-on:open="setTimeout(() => $refs.name.focus(), 250)" size="md" blur="xl">
        <form id="role-create" wire:submit="save" class="space-y-4">
            <div>
                <x-input
                    label="{{ __('Role Name') }} *"
                    x-ref="name"
                    wire:model="role.name"
                    required
                    hint="{{ __('Lowercase, alphanumeric with dashes/underscores only (e.g., custom_role)') }}"
                />
            </div>

            <div>
                <x-input
                    label="{{ __('Display Name') }} *"
                    wire:model="role.display_name"
                    required
                    hint="{{ __('Human-readable name (e.g., Custom Role)') }}"
                />
            </div>

            <div>
                <x-textarea
                    label="{{ __('Description') }}"
                    wire:model="role.description"
                    hint="{{ __('Brief description of this role') }}"
                    rows="3"
                />
            </div>
        </form>
        <x-slot:footer>
            <x-button type="submit" form="role-create" loading="save">
                @lang('Save')
            </x-button>
        </x-slot:footer>
    </x-modal>
</div>
