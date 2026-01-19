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
                            {{ $user->name }}
                            @if($user->is_admin)
                                <x-badge text="Admin" color="purple" />
                            @endif
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $user->email }}
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <x-button icon="pencil" wire:click="$dispatch('load::user-roles', { 'user' : '{{ $user->id }}'})">
                        @lang('Manage Roles')
                    </x-button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- User Information --}}
            <div class="lg:col-span-1">
                <x-card>
                    <h3 class="text-lg font-semibold mb-4">
                        @lang('User Information')
                    </h3>

                    <div class="space-y-3">
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">ID</div>
                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $user->id }}</div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">Primary Role</div>
                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($user->role ?? 'user') }}</div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">Member Since</div>
                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $user->created_at->format('M d, Y') }}</div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-2">Role Flags</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($this->roleFlags as $flag => $value)
                                    <x-badge
                                        :text="ucfirst($flag)"
                                        :color="$value ? 'green' : 'gray'"
                                        sm
                                    />
                                @endforeach
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>

            {{-- Roles and Permissions --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Assigned Roles --}}
                <x-card>
                    <h3 class="text-lg font-semibold mb-4">
                        @lang('Assigned Roles')
                    </h3>

                    @if($this->roles->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($this->roles as $role)
                                <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <x-icon name="shield-check" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $role->display_name }}
                                            </div>
                                            @if($role->description)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $role->description }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @if($role->is_system)
                                        <x-badge text="System" color="gray" sm />
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            @lang('No roles assigned')
                        </div>
                    @endif
                </x-card>

                {{-- Effective Permissions --}}
                <x-card>
                    <h3 class="text-lg font-semibold mb-4">
                        @lang('Effective Permissions')
                    </h3>

                    @if($user->is_admin)
                        <div class="text-center py-8">
                            <x-icon name="shield-check" class="w-12 h-12 text-purple-600 dark:text-purple-400 mx-auto mb-3" />
                            <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                @lang('Full Access')
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                @lang('This user has admin privileges and full access to all features')
                            </div>
                        </div>
                    @elseif($this->permissionsByGroup->count() > 0)
                        <div class="space-y-4">
                            @foreach($this->permissionsByGroup as $group => $groupPermissions)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex items-center gap-2 mb-3">
                                        <x-icon name="key" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $group }}
                                        </h4>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        @foreach($groupPermissions as $permission)
                                            <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                                <x-icon name="check" class="w-3 h-3 text-green-600 dark:text-green-400 flex-shrink-0" />
                                                {{ $permission->display_name }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            @lang('No permissions granted')
                        </div>
                    @endif
                </x-card>
            </div>
        </div>
    </x-card>

    <livewire:privacy.user-roles.update @updated="$refresh"/>
</div>
