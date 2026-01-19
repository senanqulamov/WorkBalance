<div class="space-y-6">
    {{-- Hero Section with Gradient Background --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-grid-white/[0.05] bg-[size:20px_20px]"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>

        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-3 rounded-xl bg-purple-500/20 backdrop-blur-sm border border-purple-400/30">
                            <x-icon name="chart-bar" class="w-8 h-8 text-purple-300"/>
                        </div>
                        <div>
                            <h1 class="text-3xl lg:text-4xl font-bold text-white tracking-tight">
                                {{ __('Admin Dashboard') }}
                            </h1>
                            <p class="text-purple-200/80 text-sm mt-1">
                                {{ now()->format('l, F j, Y') }}
                            </p>
                        </div>
                    </div>
                    <p class="text-slate-300 text-base max-w-2xl">
                        {{ __('Monitor system-wide activities, manage procurement operations, and analyze performance metrics in real-time.') }}
                    </p>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:min-w-[500px]">
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 hover:bg-white/15 transition-all duration-300">
                        <div class="flex items-center gap-2 mb-2">
                            <x-icon name="document-text" class="w-4 h-4 text-blue-300"/>
                            <p class="text-xs font-medium text-slate-300 uppercase tracking-wider">{{ __('Total RFQs') }}</p>
                        </div>
                        <p class="text-3xl font-bold text-white">{{ number_format($rfqStats['totalRfqs'] ?? 0) }}</p>
                        @if(($rfqStats['rfqsChange'] ?? 0) != 0)
                            <p class="text-xs mt-1 {{ ($rfqStats['rfqsChange'] ?? 0) > 0 ? 'text-green-300' : 'text-red-300' }}">
                                {{ ($rfqStats['rfqsChange'] ?? 0) > 0 ? '↑' : '↓' }} {{ abs($rfqStats['rfqsChange'] ?? 0) }}%
                            </p>
                        @endif
                    </div>

                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 hover:bg-white/15 transition-all duration-300">
                        <div class="flex items-center gap-2 mb-2">
                            <x-icon name="clock" class="w-4 h-4 text-amber-300"/>
                            <p class="text-xs font-medium text-slate-300 uppercase tracking-wider">{{ __('Open RFQs') }}</p>
                        </div>
                        <p class="text-3xl font-bold text-white">{{ $rfqStats['openRfqs'] ?? 0 }}</p>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 hover:bg-white/15 transition-all duration-300">
                        <div class="flex items-center gap-2 mb-2">
                            <x-icon name="document-check" class="w-4 h-4 text-green-300"/>
                            <p class="text-xs font-medium text-slate-300 uppercase tracking-wider">{{ __('Quotes') }}</p>
                        </div>
                        <p class="text-3xl font-bold text-white">{{ $rfqStats['totalQuotes'] ?? 0 }}</p>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 hover:bg-white/15 transition-all duration-300">
                        <div class="flex items-center gap-2 mb-2">
                            <x-icon name="users" class="w-4 h-4 text-purple-300"/>
                            <p class="text-xs font-medium text-slate-300 uppercase tracking-wider">{{ __('Suppliers') }}</p>
                        </div>
                        <p class="text-3xl font-bold text-white">{{ $rfqStats['totalSuppliers'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions Bar --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Quick Actions') }}</h3>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <a href="{{ route('monitoring.rfq.index') }}" class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-800 hover:shadow-lg transition-all duration-300">
                <div class="p-3 rounded-full bg-blue-500 mb-3 group-hover:scale-110 transition-transform">
                    <x-icon name="document-text" class="w-6 h-6 text-white"/>
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white text-center">{{ __('View RFQs') }}</span>
            </a>

            <a href="{{ route('monitoring.rfq.index') }}" class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border border-green-200 dark:border-green-800 hover:shadow-lg transition-all duration-300">
                <div class="p-3 rounded-full bg-green-500 mb-3 group-hover:scale-110 transition-transform">
                    <x-icon name="document-plus" class="w-6 h-6 text-white"/>
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white text-center">{{ __('Create RFQ') }}</span>
            </a>

            <a href="{{ route('users.index') }}" class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border border-purple-200 dark:border-purple-800 hover:shadow-lg transition-all duration-300">
                <div class="p-3 rounded-full bg-purple-500 mb-3 group-hover:scale-110 transition-transform">
                    <x-icon name="users" class="w-6 h-6 text-white"/>
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white text-center">{{ __('Manage Users') }}</span>
            </a>

            <a href="{{ route('settings.index') }}" class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 border border-amber-200 dark:border-amber-800 hover:shadow-lg transition-all duration-300">
                <div class="p-3 rounded-full bg-amber-500 mb-3 group-hover:scale-110 transition-transform">
                    <x-icon name="cog-6-tooth" class="w-6 h-6 text-white"/>
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white text-center">{{ __('Settings') }}</span>
            </a>

            <a href="{{ route('logs.index') }}" class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border border-red-200 dark:border-red-800 hover:shadow-lg transition-all duration-300">
                <div class="p-3 rounded-full bg-red-500 mb-3 group-hover:scale-110 transition-transform">
                    <x-icon name="document-chart-bar" class="w-6 h-6 text-white"/>
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white text-center">{{ __('View Logs') }}</span>
            </a>

            <a href="{{ route('privacy.index') }}" class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 border border-indigo-200 dark:border-indigo-800 hover:shadow-lg transition-all duration-300">
                <div class="p-3 rounded-full bg-indigo-500 mb-3 group-hover:scale-110 transition-transform">
                    <x-icon name="shield-check" class="w-6 h-6 text-white"/>
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white text-center">{{ __('Permissions') }}</span>
            </a>
        </div>
    </div>

    {{-- KPI Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        {{-- Open RFQs Card --}}
        <div
            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-50/10 via-blue-50/5 to-transparent dark:from-blue-500/20 dark:via-blue-500/10 backdrop-blur-sm border border-blue-200/50 dark:border-blue-500/30 hover:border-blue-400/60 dark:hover:border-blue-400/50 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/20 dark:hover:shadow-blue-500/30">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-400/0 to-blue-600/0 group-hover:from-blue-400/5 group-hover:to-blue-600/5 transition-all duration-500"></div>
            <div class="relative p-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1.5">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">{{ __('Open RFQs') }}</p>
                            @if(($rfqStats['openRfqs'] ?? 0) > 0)
                                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded-full bg-blue-500 text-white animate-pulse">{{ $rfqStats['openRfqs'] }}</span>
                            @endif
                        </div>
                        <p class="text-2xl font-bold bg-gradient-to-br from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                            {{ $rfqStats['openRfqs'] ?? 0 }}
                        </p>
                    </div>
                    <div
                        class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-400 dark:to-blue-500 flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="folder-open" class="w-6 h-6 text-white"/>
                    </div>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400">
                    {{ __('Active requests awaiting quotes') }}
                </p>
            </div>
        </div>

        {{-- Pending Quotes Card --}}
        <div
            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent dark:from-amber-500/20 dark:via-amber-500/10 backdrop-blur-sm border border-amber-200/50 dark:border-amber-500/30 hover:border-amber-400/60 dark:hover:border-amber-400/50 transition-all duration-300 hover:shadow-lg hover:shadow-amber-500/20 dark:hover:shadow-amber-500/30">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-400/0 to-amber-600/0 group-hover:from-amber-400/5 group-hover:to-amber-600/5 transition-all duration-500"></div>
            <div class="relative p-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1.5">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">{{ __('Pending Quotes') }}</p>
                            @if(($rfqStats['pendingQuotes'] ?? 0) > 0)
                                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded-full bg-amber-500 text-white animate-pulse">{{ $rfqStats['pendingQuotes'] }}</span>
                            @endif
                        </div>
                        <p class="text-2xl font-bold bg-gradient-to-br from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                            {{ $rfqStats['pendingQuotes'] ?? 0 }}
                        </p>
                    </div>
                    <div
                        class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 dark:from-amber-400 dark:to-amber-500 flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="clock" class="w-6 h-6 text-white"/>
                    </div>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400">
                    @if(($rfqStats['quotesChange'] ?? 0) > 0)
                        <span class="text-green-600">↑ {{ abs($rfqStats['quotesChange']) }}%</span>
                    @elseif(($rfqStats['quotesChange'] ?? 0) < 0)
                        <span class="text-red-600">↓ {{ abs($rfqStats['quotesChange']) }}%</span>
                    @else
                        <span class="text-gray-600">—</span>
                    @endif
                    {{ __(' vs last period') }}
                </p>
            </div>
        </div>

        {{-- Awarded Contracts Card --}}
        <div
            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-500/10 via-green-500/5 to-transparent dark:from-green-500/20 dark:via-green-500/10 backdrop-blur-sm border border-green-200/50 dark:border-green-500/30 hover:border-green-400/60 dark:hover:border-green-400/50 transition-all duration-300 hover:shadow-lg hover:shadow-green-500/20 dark:hover:shadow-green-500/30">
            <div class="absolute inset-0 bg-gradient-to-br from-green-400/0 to-green-600/0 group-hover:from-green-400/5 group-hover:to-green-600/5 transition-all duration-500"></div>
            <div class="relative p-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1.5">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-green-600 dark:text-green-400">{{ __('Awarded') }}</p>
                        </div>
                        <p class="text-2xl font-bold bg-gradient-to-br from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                            {{ $rfqStats['awardedRfqs'] ?? 0 }}
                        </p>
                    </div>
                    <div
                        class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 dark:from-green-400 dark:to-green-500 flex items-center justify-center shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="check-badge" class="w-6 h-6 text-white"/>
                    </div>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400">
                    {{ __('Contracts successfully awarded') }}
                </p>
            </div>
        </div>

        {{-- Workflow Events Card --}}
        <div
            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-500/10 via-purple-500/5 to-transparent dark:from-purple-500/20 dark:via-purple-500/10 backdrop-blur-sm border border-purple-200/50 dark:border-purple-500/30 hover:border-purple-400/60 dark:hover:border-purple-400/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/20 dark:hover:shadow-purple-500/30">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-400/0 to-purple-600/0 group-hover:from-purple-400/5 group-hover:to-purple-600/5 transition-all duration-500"></div>
            <div class="relative p-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1.5">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">{{ __('Events Today') }}</p>
                            @if(($rfqStats['eventsToday'] ?? 0) > 0)
                                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded-full bg-purple-500 text-white">{{ $rfqStats['eventsToday'] }}</span>
                            @endif
                        </div>
                        <p class="text-2xl font-bold bg-gradient-to-br from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                            {{ $rfqStats['eventsToday'] ?? 0 }}
                        </p>
                    </div>
                    <div
                        class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-400 dark:to-purple-500 flex items-center justify-center shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="bolt" class="w-6 h-6 text-white"/>
                    </div>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400">
                    {{ __('Total: ') }} {{ number_format($rfqStats['totalWorkflowEvents'] ?? 0) }}
                </p>
            </div>
        </div>
    </div>

    {{-- System Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
        @foreach($stats as $key => $stat)
            <x-card class="hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border-0">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">{{ $stat['label'] }}</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ is_numeric($stat['count']) ? number_format($stat['count']) : $stat['count'] }}
                        </p>
                        @if($stat['change'] != 0)
                            <p class="mt-2 flex items-center text-sm {{ $stat['change'] > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                @if($stat['change'] > 0)
                                    <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                @endif
                                <span class="font-semibold">{{ abs($stat['change']) }}%</span>
                            </p>
                        @endif
                    </div>
                    <div class="ml-4 p-4 rounded-2xl bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900/30">
                        <x-icon name="{{ $stat['icon'] }}" class="h-8 w-8 text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400"/>
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>

    {{-- Role Dashboards --}}
    @if(auth()->user()->isAdmin() || auth()->user()->isBuyer() || auth()->user()->isSeller() || auth()->user()->isSupplier())
        <div>
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600">
                    <x-icon name="squares-2x2" class="w-5 h-5 text-white"/>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('Role Dashboards') }}
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @if(auth()->user()->isAdmin() || auth()->user()->isBuyer())
                    <a href="{{ route('buyer.dashboard') }}" class="group block">
                        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                            <div class="relative z-10">
                                <div class="p-3 rounded-xl bg-white/20 backdrop-blur-sm w-fit mb-4">
                                    <x-icon name="shopping-cart" class="w-8 h-8 text-white"/>
                                </div>
                                <h3 class="text-xl font-bold text-white mb-2">{{ __('Buyer Dashboard') }}</h3>
                                <p class="text-white/80 text-sm">{{ __('Manage RFQs, review quotes, and track procurement activities.') }}</p>
                                <div class="mt-4 flex items-center text-white font-medium">
                                    {{ __('Go to Dashboard') }}
                                    <x-icon name="arrow-right" class="w-4 h-4 ml-2 group-hover:translate-x-2 transition-transform"/>
                                </div>
                            </div>
                        </div>
                    </a>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->isSeller())
                    <a href="{{ route('seller.dashboard') }}" class="group block">
                        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-500 to-green-600 p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                            <div class="relative z-10">
                                <div class="p-3 rounded-xl bg-white/20 backdrop-blur-sm w-fit mb-4">
                                    <x-icon name="shopping-bag" class="w-8 h-8 text-white"/>
                                </div>
                                <h3 class="text-xl font-bold text-white mb-2">{{ __('Seller Dashboard') }}</h3>
                                <p class="text-white/80 text-sm">{{ __('Manage products, view orders, and track sales performance.') }}</p>
                                <div class="mt-4 flex items-center text-white font-medium">
                                    {{ __('Go to Dashboard') }}
                                    <x-icon name="arrow-right" class="w-4 h-4 ml-2 group-hover:translate-x-2 transition-transform"/>
                                </div>
                            </div>
                        </div>
                    </a>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->isSupplier())
                    <a href="{{ route('supplier.dashboard') }}" class="group block">
                        <div
                            class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                            <div class="relative z-10">
                                <div class="p-3 rounded-xl bg-white/20 backdrop-blur-sm w-fit mb-4">
                                    <x-icon name="building-office" class="w-8 h-8 text-white"/>
                                </div>
                                <h3 class="text-xl font-bold text-white mb-2">{{ __('Supplier Dashboard') }}</h3>
                                <p class="text-white/80 text-sm">{{ __('View invitations, submit quotes, and manage supplier activities.') }}</p>
                                <div class="mt-4 flex items-center text-white font-medium">
                                    {{ __('Go to Dashboard') }}
                                    <x-icon name="arrow-right" class="w-4 h-4 ml-2 group-hover:translate-x-2 transition-transform"/>
                                </div>
                            </div>
                        </div>
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Sales Chart - Large --}}
        <div class="lg:col-span-2">
            <x-card class="h-full">
                <x-slot name="header">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Sales Overview') }}</h3>
                        <x-badge color="green" light>
                            <svg class="w-3 h-3 mr-1.5 animate-pulse" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3"/>
                            </svg>
                            {{ __('Last 30 Days') }}
                        </x-badge>
                    </div>
                </x-slot>
                <div class="relative h-80 bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                    <canvas id="salesChart"></canvas>
                </div>
            </x-card>
        </div>

        {{-- Orders by Status --}}
        <div>
            <x-card class="h-full">
                <x-slot name="header">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Orders Status') }}</h3>
                        <x-badge color="blue" light>
                            <svg class="w-3 h-3 mr-1.5 animate-pulse" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3"/>
                            </svg>
                        </x-badge>
                    </div>
                </x-slot>
                <div class="relative h-80 flex items-center justify-center bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                    <canvas id="ordersStatusChart"></canvas>
                </div>
            </x-card>
        </div>
    </div>

    {{-- Second Row --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- User Activity Chart --}}
        <div class="lg:col-span-2">
            <x-card class="h-full">
                <x-slot name="header">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('User Activity') }}</h3>
                        <x-badge color="purple" light>
                            <svg class="w-3 h-3 mr-1.5 animate-pulse" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3"/>
                            </svg>
                            {{ __('Page Views') }}
                        </x-badge>
                    </div>
                </x-slot>
                <div class="relative h-64 bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                    <canvas id="userActivityChart"></canvas>
                </div>
            </x-card>
        </div>

        {{-- System Health --}}
        <div>
            <x-card class="h-full">
                <x-slot name="header">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('System Health') }}</h3>
                </x-slot>
                <div class="space-y-6">
                    {{-- Health Score --}}
                    <div class="text-center">
                        <div class="relative inline-flex h-32 w-32">
                            <svg class="h-full w-full transform -rotate-90">
                                <circle class="text-gray-200 dark:text-gray-700" stroke-width="8" stroke="currentColor" fill="transparent" r="56" cx="64" cy="64"/>
                                <circle
                                    class="transition-all duration-1000 ease-out {{ $systemHealth['status'] === 'excellent' ? 'text-green-500' : ($systemHealth['status'] === 'good' ? 'text-blue-500' : 'text-yellow-500') }}"
                                    stroke-width="8" stroke-dasharray="{{ 2 * pi() * 56 }}" stroke-dashoffset="{{ 2 * pi() * 56 * (1 - $systemHealth['score'] / 100) }}" stroke-linecap="round"
                                    stroke="currentColor" fill="transparent" r="56" cx="64" cy="64"/>
                            </svg>
                            <span class="absolute inset-0 flex items-center justify-center text-3xl font-bold text-gray-900 dark:text-white">{{ $systemHealth['score'] }}%</span>
                        </div>
                        <p class="mt-2 text-sm font-medium capitalize {{ $systemHealth['status'] === 'excellent' ? 'text-green-500' : ($systemHealth['status'] === 'good' ? 'text-blue-500' : 'text-yellow-500') }}">
                            {{ ucfirst($systemHealth['status']) }}
                        </p>
                    </div>

                    {{-- Health Metrics --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('Active Users Today') }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $systemHealth['activeUsersToday'] }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('Logs Today') }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($systemHealth['logsToday']) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('Errors Today') }}</span>
                            <span class="font-semibold {{ $systemHealth['errorLogsToday'] > 0 ? 'text-red-500' : 'text-green-500' }}">
                                {{ $systemHealth['errorLogsToday'] }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('Total Logs') }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($systemHealth['totalLogs']) }}</span>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    {{-- RFQ & Quotes Overview --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- RFQs by Status --}}
        <div>
            <x-card class="h-full">
                <x-slot name="header">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('RFQs by Status') }}</h3>
                        <x-badge color="blue" light>{{ $rfqStats['totalRfqs'] ?? 0 }}</x-badge>
                    </div>
                </x-slot>
                <div class="relative h-64 flex items-center justify-center bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                    <canvas id="rfqsByStatusChart"></canvas>
                </div>
            </x-card>
        </div>

        {{-- Quotes by Status --}}
        <div>
            <x-card class="h-full">
                <x-slot name="header">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Quotes by Status') }}</h3>
                        <x-badge color="amber" light>{{ $rfqStats['totalQuotes'] ?? 0 }}</x-badge>
                    </div>
                </x-slot>
                <div class="relative h-64 flex items-center justify-center bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                    <canvas id="quotesByStatusChart"></canvas>
                </div>
            </x-card>
        </div>

        {{-- Supplier Activity --}}
        <div>
            <x-card class="h-full">
                <x-slot name="header">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Supplier Activity') }}</h3>
                </x-slot>
                <div class="space-y-4">
                    <div class="p-4 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center">
                                    <x-icon name="users" class="w-5 h-5 text-white"/>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Total Suppliers') }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $rfqStats['totalSuppliers'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center">
                                    <x-icon name="check-circle" class="w-5 h-5 text-white"/>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Active Suppliers') }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $rfqStats['activeSuppliers'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-amber-500 flex items-center justify-center">
                                    <x-icon name="document-check" class="w-5 h-5 text-white"/>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Accepted Quotes') }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $rfqStats['acceptedQuotes'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    {{-- Recent RFQs & Quotes --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Recent RFQs --}}
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Recent RFQs') }}</h3>
            </x-slot>
            <div class="space-y-3">
                @forelse($recentRfqs as $rfq)
                    <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 hover:shadow-md transition-shadow">
                        <div class="flex-1">
                            <p class="font-semibold text-sm text-gray-900 dark:text-white">{{ $rfq['title'] }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $rfq['buyer'] }} • {{ $rfq['items_count'] }} {{ __('items') }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ __('Deadline:') }} {{ $rfq['deadline'] }}</p>
                        </div>
                        <div class="text-right ml-4">
                            <x-badge :color="$rfq['status'] === 'open' ? 'green' : ($rfq['status'] === 'draft' ? 'gray' : ($rfq['status'] === 'awarded' ? 'blue' : 'red'))" light>
                                {{ ucfirst($rfq['status']) }}
                            </x-badge>
                            <p class="text-xs text-gray-500 mt-1">{{ $rfq['created_at'] }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">{{ __('No recent RFQs') }}</p>
                @endforelse
            </div>
        </x-card>

        {{-- Recent Quotes --}}
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Recent Quotes') }}</h3>
            </x-slot>
            <div class="space-y-3">
                @forelse($recentQuotes as $quote)
                    <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 hover:shadow-md transition-shadow">
                        <div class="flex-1">
                            <p class="font-semibold text-sm text-gray-900 dark:text-white">{{ $quote['request_title'] }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('By:') }} {{ $quote['supplier'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $quote['submitted_at'] }}</p>
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-sm font-bold text-green-600 dark:text-green-400">${{ number_format($quote['total_price'], 2) }}</p>
                            <x-badge :color="$quote['status'] === 'accepted' ? 'green' : ($quote['status'] === 'pending' ? 'amber' : 'red')" light>
                                {{ ucfirst($quote['status']) }}
                            </x-badge>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">{{ __('No recent quotes') }}</p>
                @endforelse
            </div>
        </x-card>
    </div>

    {{-- Workflow Activity --}}
    <div>
        <x-card>
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Workflow Activity') }}</h3>
                    <x-badge color="purple" light>{{ __('Recent Events') }}</x-badge>
                </div>
            </x-slot>
            <div class="space-y-3">
                @forelse($workflowActivity as $event)
                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <div class="flex-shrink-0">
                            <div
                                class="h-8 w-8 rounded-full {{ $event['event_type'] === 'status_changed' ? 'bg-blue-100 dark:bg-blue-900' : ($event['event_type'] === 'quote_submitted' ? 'bg-green-100 dark:bg-green-900' : ($event['event_type'] === 'supplier_invited' ? 'bg-purple-100 dark:bg-purple-900' : 'bg-amber-100 dark:bg-amber-900')) }} flex items-center justify-center">
                                @if($event['event_type'] === 'status_changed')
                                    <x-icon name="arrow-path" class="h-4 w-4 text-blue-600 dark:text-blue-400"/>
                                @elseif($event['event_type'] === 'quote_submitted')
                                    <x-icon name="document-check" class="h-4 w-4 text-green-600 dark:text-green-400"/>
                                @elseif($event['event_type'] === 'supplier_invited')
                                    <x-icon name="user-plus" class="h-4 w-4 text-purple-600 dark:text-purple-400"/>
                                @else
                                    <x-icon name="bell" class="h-4 w-4 text-amber-600 dark:text-amber-400"/>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 dark:text-white">
                                <span class="font-semibold">{{ $event['user'] }}</span>
                                <span class="text-gray-600 dark:text-gray-400"> - {{ $event['description'] }}</span>
                            </p>
                            <div class="flex items-center gap-2 mt-1">
                                <x-badge
                                    :color="$event['event_type'] === 'status_changed' ? 'blue' : ($event['event_type'] === 'quote_submitted' ? 'green' : ($event['event_type'] === 'supplier_invited' ? 'purple' : 'amber'))"
                                    light>
                                    {{ ucfirst(str_replace('_', ' ', $event['event_type'])) }}
                                </x-badge>
                                <p class="text-xs text-gray-500">{{ $event['occurred_at'] }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">{{ __('No workflow events') }}</p>
                @endforelse
            </div>
        </x-card>
    </div>

    {{-- Third Row --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Top Products --}}
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Top Products') }}</h3>
            </x-slot>
            <div class="space-y-4">
                @forelse($topProducts as $product)
                    <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 hover:shadow-md transition-shadow">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $product['name'] }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $product['orders'] }} {{ __('orders') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-green-600 dark:text-green-400">${{ number_format($product['revenue'], 2) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">{{ __('No products data available') }}</p>
                @endforelse
            </div>
        </x-card>

        {{-- Recent Orders --}}
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Recent Orders') }}</h3>
            </x-slot>
            <div class="space-y-3">
                @forelse($recentOrders as $order)
                    <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 hover:shadow-md transition-shadow">
                        <div class="flex-1">
                            <p class="font-semibold text-sm text-gray-900 dark:text-white">{{ $order['order_number'] }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $order['user'] }} • {{ $order['product'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $order['created_at'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">${{ number_format($order['total'], 2) }}</p>
                            <x-badge :color="$order['status'] === 'completed' ? 'green' : ($order['status'] === 'processing' ? 'blue' : 'red')" light>
                                {{ ucfirst($order['status']) }}
                            </x-badge>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">{{ __('No recent orders') }}</p>
                @endforelse
            </div>
        </x-card>
    </div>

    {{-- Recent Activity --}}
    <div>
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Recent Activity') }}</h3>
            </x-slot>
            <div class="space-y-3">
                @forelse($recentActivity as $activity)
                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <div class="flex-shrink-0">
                            <div
                                class="h-8 w-8 rounded-full {{ $activity['type'] === 'create' ? 'bg-green-100 dark:bg-green-900' : ($activity['type'] === 'update' ? 'bg-blue-100 dark:bg-blue-900' : 'bg-red-100 dark:bg-red-900') }} flex items-center justify-center">
                                @if($activity['type'] === 'create')
                                    <x-icon name="plus" class="h-4 w-4 text-green-600 dark:text-green-400"/>
                                @elseif($activity['type'] === 'update')
                                    <x-icon name="pencil" class="h-4 w-4 text-blue-600 dark:text-blue-400"/>
                                @else
                                    <x-icon name="trash" class="h-4 w-4 text-red-600 dark:text-red-400"/>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 dark:text-white">
                                <span class="font-semibold">{{ $activity['user'] }}</span>
                                <span class="text-gray-600 dark:text-gray-400"> {{ $activity['message'] }}</span>
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $activity['created_at'] }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">{{ __('No recent activity') }}</p>
                @endforelse
            </div>
        </x-card>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Chart.js default configuration
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;

        // Check if dark mode
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
        const textColor = isDark ? 'rgba(255, 255, 255, 0.7)' : '#000000';

        // Sales Chart
        const salesData = @json($salesByDay);
        const salesChart = new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: salesData.map(item => new Date(item.date).toLocaleDateString('en-US', {month: 'short', day: 'numeric'})),
                datasets: [{
                    label: 'Revenue',
                    data: salesData.map(item => item.total),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(34, 197, 94)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }, {
                    label: 'Orders',
                    data: salesData.map(item => item.count * 10),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                plugins: {
                    legend: {display: true, position: 'top', labels: {color: textColor, font: {size: 12, weight: '600'}}},
                    tooltip: {backgroundColor: 'rgba(0, 0, 0, 0.8)', padding: 12, borderColor: 'rgba(255, 255, 255, 0.2)', borderWidth: 1, titleFont: {size: 14, weight: 'bold'}, bodyFont: {size: 13}}
                },
                scales: {
                    x: {grid: {color: gridColor}, ticks: {color: textColor}},
                    y: {grid: {color: gridColor}, ticks: {color: textColor}}
                }
            }
        });

        // Orders Status Chart
        const ordersStatusData = @json($ordersByStatus);
        const ordersStatusChart = new Chart(document.getElementById('ordersStatusChart'), {
            type: 'doughnut',
            data: {
                labels: ordersStatusData.map(item => item.label),
                datasets: [{
                    data: ordersStatusData.map(item => item.value),
                    backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(34, 197, 94, 0.8)', 'rgba(239, 68, 68, 0.8)', 'rgba(251, 191, 36, 0.8)', 'rgba(168, 85, 247, 0.8)'],
                    borderColor: ['rgb(59, 130, 246)', 'rgb(34, 197, 94)', 'rgb(239, 68, 68)', 'rgb(251, 191, 36)', 'rgb(168, 85, 247)'],
                    borderWidth: 2,
                    hoverOffset: 10
                }]
            },
            options: {
                plugins: {
                    legend: {display: true, position: 'bottom', labels: {color: textColor, padding: 15, font: {size: 12, weight: '600'}}},
                    tooltip: {backgroundColor: 'rgba(0, 0, 0, 0.8)', padding: 12, borderColor: 'rgba(255, 255, 255, 0.2)', borderWidth: 1}
                },
                cutout: '70%'
            }
        });

        // User Activity Chart
        const page_views_text = '{{ __("Page Views") }}';
        const userActivityData = @json($userActivity);
        const userActivityChart = new Chart(document.getElementById('userActivityChart'), {
            type: 'bar',
            data: {
                labels: userActivityData.map(item => new Date(item.date).toLocaleDateString('en-US', {month: 'short', day: 'numeric'})),
                datasets: [{
                    label: page_views_text,
                    data: userActivityData.map(item => item.count),
                    backgroundColor: 'rgba(168, 85, 247, 0.8)',
                    borderColor: 'rgb(168, 85, 247)',
                    borderWidth: 2,
                    borderRadius: 8,
                    hoverBackgroundColor: 'rgba(168, 85, 247, 1)'
                }]
            },
            options: {
                plugins: {
                    legend: {display: false},
                    tooltip: {backgroundColor: 'rgba(0, 0, 0, 0.8)', padding: 12, borderColor: 'rgba(255, 255, 255, 0.2)', borderWidth: 1}
                },
                scales: {
                    x: {grid: {display: false}, ticks: {color: textColor}},
                    y: {grid: {color: gridColor}, ticks: {color: textColor}}
                }
            }
        });

        // RFQs by Status Chart
        const rfqsByStatusData = @json($rfqsByStatus);
        if (rfqsByStatusData && rfqsByStatusData.length > 0) {
            const rfqsByStatusChart = new Chart(document.getElementById('rfqsByStatusChart'), {
                type: 'doughnut',
                data: {
                    labels: rfqsByStatusData.map(item => item.label),
                    datasets: [{
                        data: rfqsByStatusData.map(item => item.value),
                        backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(16, 185, 129, 0.8)', 'rgba(239, 68, 68, 0.8)', 'rgba(245, 158, 11, 0.8)', 'rgba(107, 114, 128, 0.8)'],
                        borderColor: ['rgb(59, 130, 246)', 'rgb(16, 185, 129)', 'rgb(239, 68, 68)', 'rgb(245, 158, 11)', 'rgb(107, 114, 128)'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {position: 'bottom', labels: {color: textColor, padding: 15}},
                        tooltip: {backgroundColor: 'rgba(0, 0, 0, 0.8)', padding: 12, borderColor: 'rgba(255, 255, 255, 0.2)', borderWidth: 1}
                    }
                }
            });
        }

        // Quotes by Status Chart
        const quotesByStatusData = @json($quotesByStatus);
        if (quotesByStatusData && quotesByStatusData.length > 0) {
            const quotesByStatusChart = new Chart(document.getElementById('quotesByStatusChart'), {
                type: 'doughnut',
                data: {
                    labels: quotesByStatusData.map(item => item.label),
                    datasets: [{
                        data: quotesByStatusData.map(item => item.value),
                        backgroundColor: ['rgba(245, 158, 11, 0.8)', 'rgba(16, 185, 129, 0.8)', 'rgba(239, 68, 68, 0.8)', 'rgba(107, 114, 128, 0.8)'],
                        borderColor: ['rgb(245, 158, 11)', 'rgb(16, 185, 129)', 'rgb(239, 68, 68)', 'rgb(107, 114, 128)'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {position: 'bottom', labels: {color: textColor, padding: 15}},
                        tooltip: {backgroundColor: 'rgba(0, 0, 0, 0.8)', padding: 12, borderColor: 'rgba(255, 255, 255, 0.2)', borderWidth: 1}
                    }
                }
            });
        }
    </script>
@endpush
