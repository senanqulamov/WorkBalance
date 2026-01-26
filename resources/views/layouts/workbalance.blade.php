<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'WorkBalance' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    <tallstackui:script />
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 font-sans antialiased">
    <div class="relative pb-16">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900 via-slate-950 to-slate-950"></div>
        <div class="absolute inset-x-0 top-0 h-48 bg-gradient-to-r from-indigo-600/30 via-purple-600/20 to-cyan-500/25 blur-3xl"></div>

        <header class="relative z-10 border-b border-slate-800/70 bg-slate-900/70 backdrop-blur">
            <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-500 via-purple-500 to-cyan-400 shadow-lg flex items-center justify-center text-lg font-semibold">WB</div>
                    <div>
                        <p class="text-sm uppercase tracking-wide text-slate-400">WorkBalance</p>
                        <p class="text-base font-semibold text-slate-100">Private wellbeing space</p>
                    </div>
                </div>
                <nav class="hidden sm:flex items-center gap-4 text-sm text-slate-200">
                    <a href="{{ route('workbalance.dashboard') }}" class="hover:text-white">Home</a>
                    <a href="{{ route('workbalance.check-in') }}" class="hover:text-white">Check-in</a>
                    <a href="{{ route('workbalance.progress') }}" class="hover:text-white">Progress</a>
                    <a href="{{ route('workbalance.settings') }}" class="hover:text-white">Settings</a>
                </nav>
            </div>
        </header>

        <main class="relative z-10 max-w-5xl mx-auto px-4 py-10">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
