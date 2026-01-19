<div>
    <x-slide wire="modal" right size="2xl" blur="md">
        <x-slot name="title">{{ __('Update Permissions: :name', ['name' => $role?->display_name]) }}</x-slot>
        <form id="role-permissions-update-{{ $role?->id }}" wire:submit="save" class="space-y-6">

            <div class="space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    @lang('Select the permissions for this role.')
                </p>

                @foreach($this->permissionsByGroup as $group => $permissions)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                            <x-icon name="key" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                            {{ $group }}
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($permissions as $permission)
                                <label class="flex items-center gap-2 text-sm cursor-pointer p-2 hover:bg-gray-50 dark:hover:bg-gray-800 rounded">
                                    <x-checkbox
                                        wire:model="selectedPermissions"
                                        value="{{ $permission->id }}"
                                        id="permission-{{ $permission->id }}"
                                    />
                                    <span class="text-gray-900 dark:text-gray-100">
                                        {{ $permission->display_name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <x-button
                type="submit"
                form="role-permissions-update-{{ $role?->id }}"
                color="primary"
                loading="save"
                icon="check"
            >
                {{ __('Save Permissions') }}
            </x-button>
        </form>
    </x-slide>
</div>
