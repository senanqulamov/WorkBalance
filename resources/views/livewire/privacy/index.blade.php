<div>
    <x-card>
        <x-heading-title title="{{ __('Privacy & Role Management') }}" text="" icon="shield-check" padding="p-5" hover="-"/>

        {{-- Tabs --}}
        <div class="mt-6 border-b border-gray-200 dark:border-gray-700">
            <nav class="flex space-x-8">
                <button
                    wire:click="$set('tab', 'users')"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition {{ $tab === 'users' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="users" class="w-5 h-5 inline mr-2" />
                    @lang('Users & Roles')
                </button>
                <button
                    wire:click="$set('tab', 'roles')"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition {{ $tab === 'roles' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="shield-check" class="w-5 h-5 inline mr-2" />
                    @lang('Roles & Permissions')
                </button>
                <button
                    wire:click="$set('tab', 'permissions')"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition {{ $tab === 'permissions' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="key" class="w-5 h-5 inline mr-2" />
                    @lang('Permission Groups')
                </button>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="mt-6">
            {{-- Tab 1: Users & Roles --}}
            @if($tab === 'users')
                <div>
                    {{-- Role filter --}}
                    <div class="mb-4 flex flex-wrap items-center gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                @lang('Filter by Role')
                            </label>
                            <select
                                wire:model.live="roleFilter"
                                class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100 text-sm"
                            >
                                <option value="">@lang('All roles')</option>
                                @foreach($this->roles as $role)
                                    <option value="{{ $role->id }}">
                                        {{ $role->display_name }} ({{ $role->users_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <x-table :$headers :$sort :rows="$this->rows" paginate :paginator="null" filter loading :quantity="[5, 10, 20, 'all']">
                        @interact('column_id', $row)
                        {{ $row->id }}
                        @endinteract

                        @interact('column_name', $row)
                        <a href="{{ route('privacy.users.show', $row) }}" class="text-blue-600 hover:underline">
                            <x-badge :text="$row->name" icon="user" position="left"/>
                        </a>
                        @if($row->is_admin)
                            <x-badge text="Admin" color="purple" sm />
                        @endif
                        @endinteract

                        @interact('column_email', $row)
                        {{ $row->email }}
                        @endinteract

                        @interact('column_roles', $row)
                        <div class="flex gap-1 flex-wrap">
                            @forelse($row->roles as $role)
                                <x-badge :text="$role->display_name" color="blue" sm />
                            @empty
                                <span class="text-gray-400">-</span>
                            @endforelse
                        </div>
                        @endinteract

                        @interact('column_created_at', $row)
                        {{ $row->created_at->diffForHumans() }}
                        @endinteract

                        @interact('column_action', $row)
                        <div class="flex gap-1">
                            @can('manage_roles')
                                <x-button.circle icon="pencil" wire:click="$dispatch('load::user-roles', { 'user' : '{{ $row->id }}'})"/>
                            @endcan
                        </div>
                        @endinteract
                    </x-table>
                </div>
            @endif

            {{-- Tab 2: Roles & Permissions --}}
            @if($tab === 'roles')
                <div>
                    <div class="mb-4">
                        <livewire:privacy.roles.create @created="$refresh"/>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($this->roles as $role)
                            <x-card>
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <a href="{{ route('privacy.roles.show', $role) }}" class="font-semibold text-gray-900 dark:text-gray-100 hover:text-purple-600 dark:hover:text-purple-400">
                                                <x-badge :text="$role->display_name" icon="cursor-arrow-ripple" position="left"/>
                                            </a>
                                            @if($role->is_system)
                                                <x-badge text="System" color="gray" sm />
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            {{ $role->description }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center gap-2">
                                        <x-icon name="users" class="w-4 h-4 text-gray-500" />
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}
                                        </span>
                                    </div>

                                    <div class="flex gap-1">
                                        <x-button.circle icon="key" color="purple" sm wire:click="$dispatch('load::role-permissions', { 'role' : '{{ $role->id }}'})"/>
                                        @if(!$role->is_system)
                                            <x-button.circle icon="pencil" sm wire:click="$dispatch('load::role', { 'role' : '{{ $role->id }}'})"/>
                                            <livewire:privacy.roles.delete :role="$role" :key="uniqid('', true)" @deleted="$refresh"/>
                                        @endif
                                    </div>
                                </div>
                            </x-card>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Tab 3: Permission Groups --}}
            @if($tab === 'permissions')
                <div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($this->permissionsByGroup as $group => $groupPermissions)
                            <x-card>
                                <div class="flex items-center gap-2 mb-3">
                                    <x-icon name="key" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $group }}
                                    </h4>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                    {{ $groupPermissions->count() }} {{ Str::plural('permission', $groupPermissions->count()) }}
                                </p>
                                <div class="space-y-2 border-t border-gray-200 dark:border-gray-700 pt-3">
                                    @foreach($groupPermissions as $permission)
                                        <div class="flex items-start gap-2">
                                            <x-icon name="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400 mt-0.5 flex-shrink-0" />
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $permission->display_name }}
                                                </div>
                                                @if($permission->description)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
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
                </div>
            @endif
        </div>
    </x-card>

    <livewire:privacy.user-roles.update @updated="$refresh"/>
    <livewire:privacy.role-permissions.update @updated="$refresh"/>
    <livewire:privacy.roles.update @updated="$refresh"/>
</div>
