@props(['role' => 'default'])

@php
    $roleConfig = [
        'buyer' => [
            'gradient' => 'from-blue-600 via-blue-500 to-indigo-600',
            'icon' => 'shopping-cart',
            'title' => __('Buyer Portal'),
            'accent' => 'blue',
        ],
        'seller' => [
            'gradient' => 'from-emerald-600 via-emerald-500 to-green-600',
            'icon' => 'shopping-bag',
            'title' => __('Seller Portal'),
            'accent' => 'emerald',
        ],
        'supplier' => [
            'gradient' => 'from-purple-600 via-purple-500 to-indigo-600',
            'icon' => 'building-office',
            'title' => __('Supplier Portal'),
            'accent' => 'purple',
        ],
        'admin' => [
            'gradient' => 'from-purple-600 via-purple-500 to-indigo-600',
            'icon' => 'shield-check',
            'title' => __('Admin Portal'),
            'accent' => 'purple',
        ],
    ];

    $config = $roleConfig[$role] ?? $roleConfig['admin'];

    $unread = 0;
    if (auth()->check()) {
        $unread = cache()->remember(
            'ui:unread_notifications_count:user:'.auth()->id(),
            now()->addSeconds(30),
            fn () => auth()->user()->unreadNotifications()->count(),
        );
    }
@endphp

<header class="sticky top-0 flex-shrink-0 backdrop-blur-xl bg-slate-950/90 border-b border-slate-800/50 shadow-2xl z-30">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Left Section --}}
            <div class="flex items-center gap-4">
                {{-- Mobile Menu Toggle --}}
                <button x-data x-on:click="$dispatch('sidebar-toggle')"
                        class="lg:hidden p-2 rounded-lg bg-slate-800/50 hover:bg-slate-700/50 transition">
                    <x-icon name="bars-3" class="w-5 h-5 text-slate-300"/>
                </button>

                {{-- Portal Badge --}}
                <div class="hidden md:flex items-center gap-3 px-4 py-2 rounded-xl bg-gradient-to-r {{ $config['gradient'] }} shadow-lg">
                    <x-icon name="{{ $config['icon'] }}" class="w-5 h-5 text-white"/>
                    <span class="text-sm font-bold text-white tracking-wide">{{ $config['title'] }}</span>
                </div>

                {{ $left ?? '' }}
            </div>

            {{-- Center Section - Search --}}
            <div class="hidden lg:flex flex-1 max-w-xl mx-8">
                <button x-data x-on:click="$dispatch('openSearch')"
                        class="relative w-full group">
                    <x-icon name="magnifying-glass" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-hover:text-slate-300 transition"/>
                    <div class="w-full pl-12 pr-24 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-sm text-slate-400 text-left group-hover:bg-slate-700/50 group-hover:border-slate-600/50 transition">
                        {{__('Search')}}...
                    </div>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-1 text-xs text-slate-500 group-hover:text-slate-400 transition">
                        <kbd class="px-2 py-1 rounded bg-slate-700/50 text-slate-400 font-mono">Ctrl</kbd>
                        <span>+</span>
                        <kbd class="px-2 py-1 rounded bg-slate-700/50 text-slate-400 font-mono">K</kbd>
                    </div>
                </button>
            </div>

            {{-- Right Section --}}
            <div class="flex items-center gap-3">
                {{-- Notifications --}}
                <a href="{{ route('notifications.index') }}" class="relative p-2.5 rounded-xl bg-slate-800/50 hover:bg-slate-700/50 transition group">
                    <x-icon name="bell" class="w-5 h-5 text-slate-300 group-hover:text-white transition"/>
                    @if($unread > 0)
                        <span class="absolute -top-1 -right-1 min-w-5 h-5 px-1 bg-{{ $config['accent'] }}-500 rounded-full text-xs font-bold text-white flex items-center justify-center shadow-lg">{{ $unread }}</span>
                    @endif
                </a>

                {{-- Theme Switcher --}}
                <div class="relative" x-data="{ theme: (localStorage.getItem('theme') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')) }">
                    <button x-on:click="(theme = theme === 'light' ? 'dark' : 'light'); window.setAppTheme(theme)"
                            class="p-2.5 rounded-xl bg-slate-800/50 hover:bg-slate-700/50 transition flex items-center justify-center">
                        <template x-if="theme === 'light'">
                            <x-icon name="moon" class="w-5 h-5 text-slate-300"/>
                        </template>
                        <template x-if="theme === 'dark'">
                            <x-icon name="sun" class="w-5 h-5 text-slate-300"/>
                        </template>
                    </button>
                    <div class="absolute -bottom-10 left-1/2 -translate-x-1/2 text-xs rounded-md bg-slate-800/50 px-2 py-1 text-slate-200 hidden" x-ref="tooltip" x-show="false">Toggle theme</div>
                </div>

                {{-- Language Switcher --}}
                <div class="hidden sm:block" x-data="{ open: false }" x-on:click.away="open = false">
                    <button x-on:click="open = !open"
                            class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-800/50 hover:bg-slate-700/50 transition">
                        <span class="text-xs font-medium text-slate-300">{{ strtoupper(app()->getLocale()) }}</span>
                        <x-icon name="language" class="w-4 h-4 text-slate-400"/>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-30 mt-2 w-48 rounded-xl bg-slate-800 border border-slate-700 shadow-2xl overflow-hidden z-50"
                         style="display: none;">
                        @foreach(['en' => 'English', 'de' => 'Deutsch', 'es' => 'Español', 'fr' => 'Français', 'tr' => 'Türkçe', 'az' => 'Azərbaycanca'] as $code => $name)
                            <a href="{{ route('lang.switch', $code) }}"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-slate-200 hover:text-slate-800 transition {{ app()->getLocale() === $code ? 'bg-slate-400 text-slate-800' : 'text-slate-300' }}">
                                <span class="text-sm font-medium">{{ $name }}</span>
                                @if(app()->getLocale() === $code)
                                    <x-icon name="check" class="w-4 h-4 text-{{ $config['accent'] }}-500 ml-auto"/>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- User Menu --}}
                {{ $right ?? '' }}
            </div>
        </div>
    </div>
</header>
