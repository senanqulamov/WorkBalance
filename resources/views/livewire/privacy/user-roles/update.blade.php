<div>
    <x-slide wire="modal" right size="lg" blur="md">
        <x-slot name="title">{{ __('Update User Roles: :name', ['name' => $user?->name]) }}</x-slot>
        <form id="user-roles-update-{{ $user?->id }}" wire:submit="save" class="space-y-6">

            <div class="space-y-3">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    @lang('Select the roles for this user. Users can have multiple roles.')
                </p>

                @foreach($roles as $role)
                    <label class="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <x-checkbox
                            wire:model="selectedRoles"
                            value="{{ $role->id }}"
                            id="role-{{ $role->id }}"
                        />
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $role->display_name }}
                                </span>
                                @if($role->is_system)
                                    <x-badge text="System" color="gray" sm />
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $role->description }}
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            <x-button
                type="submit"
                form="user-roles-update-{{ $user?->id }}"
                color="primary"
                loading="save"
                icon="check"
            >
                {{ __('Save Roles') }}
            </x-button>
        </form>
    </x-slide>
</div>
