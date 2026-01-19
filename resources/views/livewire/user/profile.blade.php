<div x-data="{ activeTab: 'profile' }" @updated="$dispatch('name-updated', { name: $event.detail.name })">
    <!-- Hero Header with Role Badge -->
    <div class="mb-8 relative overflow-hidden rounded-3xl bg-gradient-to-r
        @if($user->isAdmin()) from-red-600 via-orange-500 to-amber-600
        @elseif($user->isBuyer()) from-blue-600 via-blue-500 to-cyan-500
        @elseif($user->isSeller()) from-emerald-600 via-emerald-500 to-green-600
        @elseif($user->isSupplier()) from-purple-600 via-purple-500 to-indigo-600
        @else from-slate-600 via-slate-500 to-slate-600
        @endif
        text-white shadow-2xl">
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAxOGMzLjMxIDAgNiAyLjY5IDYgNnMtMi42OSA2LTYgNi02LTIuNjktNi02IDIuNjktNiA2LTZ6TTI0IDEyYzIuMjEgMCA0IDEuNzkgNCA0cy0xLjc5IDQtNCA0LTQtMS43OS00LTQgMS43OS00IDQtNHoiIGZpbGw9IiNmZmYiIG9wYWNpdHk9Ii4xIi8+PC9nPjwvc3ZnPg==')] opacity-10"></div>
        <div class="relative px-8 py-12">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <!-- Avatar -->
                <div class="relative group">
                    <div class="absolute -inset-1 bg-white/30 rounded-full blur-md group-hover:bg-white/40 transition"></div>
                    <div class="relative w-24 h-24 rounded-full bg-white/20 backdrop-blur-sm border-4 border-white/30 flex items-center justify-center shadow-2xl">
                        <span class="text-4xl font-black">{{ $user->initials() }}</span>
                    </div>
                </div>

                <!-- User Info -->
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <h1 class="text-3xl md:text-4xl font-black">{{ $user->name }}</h1>
                        @if($user->is_active)
                            <span class="px-3 py-1 bg-white/20 backdrop-blur-sm border border-white/30 rounded-full text-xs font-bold">{{ __('Active') }}</span>
                        @endif
                    </div>
                    <p class="text-lg text-white/90 mb-4">{{ $user->email }}</p>

                    <!-- Role Badges -->
                    <div class="flex flex-wrap gap-2">
                        @if($user->isAdmin())
                            <div class="flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <span class="text-sm font-bold">{{ __('Administrator') }}</span>
                            </div>
                        @endif
                        @if($user->isBuyer())
                            <div class="flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span class="text-sm font-bold">{{ __('Buyer') }}</span>
                            </div>
                        @endif
                        @if($user->isSeller())
                            <div class="flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                <span class="text-sm font-bold">{{ $user->verified_seller ? __('Verified Seller') : __('Seller') }}</span>
                            </div>
                        @endif
                        @if($user->isSupplier())
                            <div class="flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span class="text-sm font-bold">{{ __('Supplier') }} ({{ ucfirst($user->supplier_status ?? 'pending') }})</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Stats -->
                @if($user->isBuyer() || $user->isSeller() || $user->isSupplier())
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-4 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20">
                            <div class="text-2xl font-black mb-1">{{ $user->total_orders ?? 0 }}</div>
                            <div class="text-xs font-medium text-white/80">{{ __('Total Orders') }}</div>
                        </div>
                        <div class="text-center p-4 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20">
                            <div class="text-2xl font-black mb-1">{{ number_format($user->rating ?? 0, 1) }}</div>
                            <div class="text-xs font-medium text-white/80">{{ __('Rating') }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="mb-6">
        <div class="border-b border-slate-200 dark:border-slate-700">
            <nav class="-mb-px flex gap-2" aria-label="Tabs">
                <button @click="activeTab = 'profile'"
                        :class="activeTab === 'profile' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-semibold text-sm transition-colors">
                    {{ __('Profile Information') }}
                </button>
                <button @click="activeTab = 'business'"
                        :class="activeTab === 'business' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-semibold text-sm transition-colors">
                    {{ __('Business Info') }}
                </button>
                <button @click="activeTab = 'security'"
                        :class="activeTab === 'security' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-semibold text-sm transition-colors">
                    {{ __('Security') }}
                </button>
                @if($user->isSupplier() || $user->isSeller())
                    <button @click="activeTab = 'professional'"
                            :class="activeTab === 'professional' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-semibold text-sm transition-colors">
                        {{ __('Professional Details') }}
                    </button>
                @endif
            </nav>
        </div>
    </div>

    <!-- Form -->
    <form id="update-profile" wire:submit="save">
        <!-- Profile Information Tab -->
        <div x-show="activeTab === 'profile'" x-transition>
            <x-card>
                <x-slot:header>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-200">{{ __('Basic Information') }}</h3>
                            <p class="text-sm text-slate-400">{{ __('Update your personal details') }}</p>
                        </div>
                    </div>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input label="{{ __('Full Name') }} *" wire:model="user.name" required/>
                    </div>
                    <div>
                        <x-input label="{{ __('Email Address') }} *" value="{{ $user->email }}" disabled/>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('Contact support to change your email') }}</p>
                    </div>
                    <div>
                        <x-input label="{{ __('Phone') }}" wire:model="user.phone"/>
                    </div>
                    <div>
                        <x-input label="{{ __('Mobile') }}" wire:model="user.mobile"/>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Business Information Tab -->
        <div x-show="activeTab === 'business'" x-transition style="display: none;">
            <x-card>
                <x-slot:header>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-600 to-green-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-200">{{ __('Business Information') }}</h3>
                            <p class="text-sm text-slate-400">{{ __('Company and contact details') }}</p>
                        </div>
                    </div>
                </x-slot:header>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input label="{{ __('Company Name') }}" wire:model="user.company_name"/>
                        </div>
                        <div>
                            <x-input label="{{ __('Website') }}" wire:model="user.website" placeholder="https://example.com"/>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 dark:border-slate-700 pt-6">
                        <h4 class="text-base font-semibold text-slate-900 dark:text-white mb-4">{{ __('Address') }}</h4>
                        <div class="space-y-4">
                            <div>
                                <x-input label="{{ __('Address Line 1') }}" wire:model="user.address_line1"/>
                            </div>
                            <div>
                                <x-input label="{{ __('Address Line 2') }}" wire:model="user.address_line2"/>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <x-input label="{{ __('City') }}" wire:model="user.city"/>
                                </div>
                                <div>
                                    <x-input label="{{ __('State/Province') }}" wire:model="user.state"/>
                                </div>
                                <div>
                                    <x-input label="{{ __('Postal Code') }}" wire:model="user.postal_code"/>
                                </div>
                            </div>
                            <div>
                                <x-input label="{{ __('Country') }}" wire:model="user.country"/>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Security Tab -->
        <div x-show="activeTab === 'security'" x-transition style="display: none;">
            <x-card>
                <x-slot:header>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-red-600 to-orange-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-200">{{ __('Security Settings') }}</h3>
                            <p class="text-sm text-slate-400">{{ __('Update your password and security preferences') }}</p>
                        </div>
                    </div>
                </x-slot:header>

                <div class="space-y-6">
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-amber-800 dark:text-amber-200">
                                <p class="font-semibold mb-1">{{ __('Password Update') }}</p>
                                <p>{{ __('Leave the password fields empty if you don\'t want to change your current password.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-password :label="__('New Password')"
                                        :hint="__('Must be at least 8 characters')"
                                        wire:model="password"
                                        rules
                                        generator
                                        x-on:generate="$wire.set('password_confirmation', $event.detail.password)"/>
                        </div>
                        <div>
                            <x-password :label="__('Confirm New Password')" wire:model="password_confirmation" rules/>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Professional Details Tab (Supplier/Seller) -->
        @if($user->isSupplier() || $user->isSeller())
            <div x-show="activeTab === 'professional'" x-transition style="display: none;">
                <x-card>
                    <x-slot:header>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-200">{{ __('Professional Details') }}</h3>
                                <p class="text-sm text-slate-400">{{ __('Role-specific information and status') }}</p>
                            </div>
                        </div>
                    </x-slot:header>

                    <div class="space-y-6">
                        <!-- Status Overview -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center gap-3 mb-2">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-sm font-semibold text-blue-900 dark:text-blue-100">{{ __('Status') }}</span>
                                </div>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ ucfirst($user->supplier_status ?? 'Active') }}</p>
                            </div>

                            <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                <div class="flex items-center gap-3 mb-2">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                    <span class="text-sm font-semibold text-green-900 dark:text-green-100">{{ __('Rating') }}</span>
                                </div>
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($user->rating ?? 0, 1) }}/5.0</p>
                            </div>

                            <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                                <div class="flex items-center gap-3 mb-2">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    <span class="text-sm font-semibold text-purple-900 dark:text-purple-100">{{ __('Total Orders') }}</span>
                                </div>
                                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($user->total_orders ?? 0) }}</p>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                <span class="font-semibold text-slate-900 dark:text-white">{{ __('Note:') }}</span>
                                {{ __('Professional details are managed by administrators. Contact support to update your verification status or commission rates.') }}
                            </p>
                        </div>
                    </div>
                </x-card>
            </div>
        @endif

        <!-- Save Button (Sticky) -->
        <div class="sticky bottom-0 mt-6 py-4 bg-slate-600 px-6 rounded-b-3xl">
            <div class="flex items-center justify-between">
                <p class="text-sm text-slate-200">
                    {{ __('Make sure to save your changes before leaving') }}
                </p>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 hover:from-blue-700 hover:via-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 transition-all duration-200 transform hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ __('Save Changes') }}
                </button>
            </div>
        </div>
    </form>
</div>
