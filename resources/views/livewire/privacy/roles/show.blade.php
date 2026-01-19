<div>
    <x-card>
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('privacy.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <x-icon name="arrow-left" class="w-6 h-6" />
                    </a>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                            {{ $role->display_name }}
                            @if($role->is_system)
                                <x-badge text="System" color="gray" />
                            @endif
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $role->description }}
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <x-button icon="key" color="purple" wire:click="$dispatch('load::role-permissions', { 'role' : '{{ $role->id }}'})">
                        @lang('Manage Permissions')
                    </x-button>
                    @if(!$role->is_system)
                        <x-button icon="pencil" wire:click="$dispatch('load::role', { 'role' : '{{ $role->id }}'})">
                            @lang('Edit Role')
                        </x-button>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-8">
            {{-- Permissions Section --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">
                    @lang('Permissions')
                </h3>

                @if($this->permissionsByGroup->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($this->permissionsByGroup as $group => $groupPermissions)
                            <x-card>
                                <div class="flex items-center gap-2 mb-3">
                                    <x-icon name="key" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $group }}
                                    </h4>
                                </div>
                                <div class="space-y-2">
                                    @foreach($groupPermissions as $permission)
                                        <div class="flex items-start gap-2">
                                            <x-icon name="check" class="w-4 h-4 text-green-600 dark:text-green-400 mt-0.5 flex-shrink-0" />
                                            <div>
                                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $permission->display_name }}
                                                </div>
                                                @if($permission->description)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $permission->description }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </x-card>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        @lang('No permissions assigned to this role')
                    </div>
                @endif
            </div>

            {{-- Users Section --}}
            <div>
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold">
                        @lang('Users with this Role')
                    </h3>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $role->users_count ?? $role->users()->count() }} {{ Str::plural('user', $role->users_count ?? $role->users()->count()) }}
                    </div>
                </div>

                <x-table :$headers :$sort :rows="$this->rows" paginate :paginator="null" filter loading :quantity="[5, 10, 20, 'all']">
                    @interact('column_id', $row)
                    {{ $row->id }}
                    @endinteract

                    @interact('column_name', $row)
                    <div class="flex items-center gap-2">
                        <a href="{{ route('privacy.users.show', $row) }}" class="text-blue-600 hover:underline">
                            <x-badge :text="$row->name" icon="user" position="left"/>
                        </a>
                        @if($row->is_admin)
                            <x-badge text="Admin" color="purple" sm />
                        @endif
                    </div>
                    @endinteract

                    @interact('column_email', $row)
                    {{ $row->email }}
                    @endinteract

                    @interact('column_created_at', $row)
                    {{ $row->created_at->diffForHumans() }}
                    @endinteract
                </x-table>
            </div>
        </div>
    </x-card>

    <livewire:privacy.role-permissions.update @updated="$refresh"/>
    <livewire:privacy.roles.update @updated="$refresh"/>
</div>
