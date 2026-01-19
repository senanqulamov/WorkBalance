<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark" x-data="tallstackui_darkTheme()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <tallstackui:script/>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-950 text-slate-200"
      x-cloak
      x-data="{ name: @js(auth()->user()->name) }"
      x-on:name-updated.window="name = $event.detail.name">

<x-dialog/>
<x-toast/>
<x-layout.global-loader />

@php
    // Prefer the current route prefix to keep UI theme stable.
    if (request()->routeIs('buyer.*')) {
        $currentRole = 'buyer';
    } elseif (request()->routeIs('seller.*')) {
        $currentRole = 'seller';
    } elseif (request()->routeIs('supplier.*')) {
        $currentRole = 'supplier';
    } else {
        // Admin/default area.
        $currentRole = 'admin';
    }

    // If we're not in a role-prefixed area, fall back to the authenticated user's pivot roles.
    // This layout renders on every authenticated page, so keep DB work minimal.
    if ($currentRole === 'admin' && auth()->check()) {
        $roleNames = cache()->remember(
            'ui:role_names:user:'.auth()->id(),
            now()->addMinutes(5),
            fn () => auth()->user()->roles()->pluck('name')->all(),
        );

        if (in_array('admin', $roleNames, true) || auth()->user()->isAdmin()) {
            $currentRole = 'admin';
        } elseif (in_array('seller', $roleNames, true) || auth()->user()->isSeller()) {
            $currentRole = 'seller';
        } elseif (in_array('supplier', $roleNames, true) || auth()->user()->isSupplier()) {
            $currentRole = 'supplier';
        } elseif (in_array('buyer', $roleNames, true) || auth()->user()->isBuyer()) {
            $currentRole = 'buyer';
        } else {
            $currentRole = auth()->user()->role ?? 'admin';
        }
    }
@endphp

<div class="flex min-h-screen bg-slate-950" x-data="{ sidebarExpanded: $persist(true).as('sidebar-expanded') }">
    {{-- Sidebar --}}
    <x-layout.role-sidebar :role="$currentRole"/>

    {{-- Main Content Area --}}
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300" :class="sidebarExpanded ? 'lg:pl-64' : 'lg:pl-20'">
        {{-- Header --}}
        <x-layout.role-header :role="$currentRole">
        <x-slot:right>
            <div x-data="{ open: false }" x-on:click.away="open = false" class="relative">
                <button x-on:click="open = !open"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl bg-slate-800/50 hover:bg-slate-700/50 transition group">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden lg:block text-left">
                        <div class="text-sm font-medium text-slate-200 group-hover:text-white transition" x-text="name"></div>
                        <div class="text-xs text-slate-400">{{ ucfirst($currentRole) }}</div>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 text-slate-400 group-hover:text-white transition"/>
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-64 rounded-xl bg-slate-800 border border-slate-700 shadow-2xl overflow-hidden z-50"
                     style="display: none;">

                    {{-- User Info Header --}}
                    <div class="px-4 py-3 border-b border-slate-700 bg-slate-700/30">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold text-slate-300 truncate" x-text="name"></div>
                                <div class="text-xs text-slate-400">{{ auth()->user()->email }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Menu Items --}}
                    <div class="py-1">
                        <a href="{{ route('user.profile') }}"
                           class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-200 hover:text-slate-800 transition text-slate-300">
                            <x-icon name="user" class="w-4 h-4"/>
                            <span class="text-sm font-medium">{{ __('Profile') }}</span>
                        </a>
                        @if(auth()->user()->isAdmin() )
                            <a href="{{ route('settings.index') }}"
                               class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-200 hover:text-slate-800 transition text-slate-300">
                                <x-icon name="cog-6-tooth" class="w-4 h-4"/>
                                <span class="text-sm font-medium">{{ __('Settings') }}</span>
                            </a>
                            <a href="{{ route('privacy.index') }}"
                               class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-200 hover:text-slate-800 transition text-slate-300">
                                <x-icon name="finger-print" class="w-4 h-4"/>
                                <span class="text-sm font-medium">{{ __('Privacy & Roles') }}</span>
                            </a>
                            <a href="{{ route('logs.index') }}"
                               class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-200 hover:text-slate-800 transition text-slate-300">
                                <x-icon name="clipboard-document-list" class="w-4 h-4"/>
                                <span class="text-sm font-medium">{{ __('Activity Log') }}</span>
                            </a>
                        @endif
                    </div>

                    {{-- Logout --}}
                    <div class="border-t border-slate-700">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-3 hover:bg-red-600/20 transition text-red-400 hover:text-red-300">
                                <x-icon name="arrow-left-on-rectangle" class="w-4 h-4"/>
                                <span class="text-sm font-medium">{{ __('Logout') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </x-slot:right>
    </x-layout.role-header>

        {{-- Main Content --}}
        <main class="flex-1 p-6 overflow-x-hidden">
            {{ $slot }}
        </main>
    </div>
</div>

{{-- Global Search --}}
@auth
    @livewire('search.global-search')
@endauth

@livewireScripts
@stack('scripts')
</body>
</html>
