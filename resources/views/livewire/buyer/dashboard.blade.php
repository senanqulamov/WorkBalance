<div class="space-y-6">
    {{-- Hero / Summary --}}
    <x-card class="bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-500 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <x-icon name="shopping-cart" class="w-7 h-7" />
                    <h1 class="text-2xl md:text-3xl font-semibold tracking-tight">
                        {{ __('Buyer Dashboard') }}
                    </h1>
                </div>
                <p class="text-sm md:text-base text-blue-50/90 max-w-xl">
                    {{ __('Manage your procurement activities, RFQs, and supplier relationships in one unified view.') }}
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 w-full md:w-auto">
                <div class="bg-blue-900/30 rounded-lg px-3 py-2 border border-blue-400/40">
                    <p class="text-[10px] uppercase tracking-wide text-blue-100/70 mb-1">{{ __('Total Spend') }}</p>
                    <p class="text-lg font-semibold leading-tight">
                        {{ '$' . number_format($totalSpend, 2) }}
                    </p>
                </div>
                <div class="bg-blue-900/30 rounded-lg px-3 py-2 border border-blue-400/40">
                    <p class="text-[10px] uppercase tracking-wide text-blue-100/70 mb-1">{{ __('Open RFQs') }}</p>
                    <p class="text-lg font-semibold leading-tight">{{ $openRfqs }}</p>
                </div>
                <div class="bg-blue-900/30 rounded-lg px-3 py-2 border border-blue-400/40">
                    <p class="text-[10px] uppercase tracking-wide text-blue-100/70 mb-1">{{ __('Active Suppliers') }}</p>
                    <p class="text-lg font-semibold leading-tight">{{ $activeSuppliers }}</p>
                </div>
                <div class="bg-blue-900/30 rounded-lg px-3 py-2 border border-blue-400/40">
                    <p class="text-[10px] uppercase tracking-wide text-blue-100/70 mb-1">{{ __('Completed') }}</p>
                    <p class="text-lg font-semibold leading-tight">{{ $completedOrders }}</p>
                </div>
            </div>
        </div>
    </x-card>

    {{-- Buyer nav --}}


    {{-- KPI grid - 2026 Modern Design --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
        {{-- Open RFQs Card --}}
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500/10 via-blue-500/5 to-transparent dark:from-blue-500/20 dark:via-blue-500/10 backdrop-blur-sm border border-blue-200/50 dark:border-blue-500/30 hover:border-blue-400/60 dark:hover:border-blue-400/50 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/20 dark:hover:shadow-blue-500/30">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-400/0 to-blue-600/0 group-hover:from-blue-400/5 group-hover:to-blue-600/5 transition-all duration-500"></div>
            <div class="relative p-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1.5">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">{{ __('Open RFQs') }}</p>
                            @if($openRfqs > 0)
                                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded-full bg-blue-500 text-white">{{ $openRfqs }}</span>
                            @endif
                        </div>
                        <p class="text-2xl font-bold bg-gradient-to-br from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                            {{ $openRfqs }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-400 dark:to-blue-500 flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="document-text" class="w-6 h-6 text-white" />
                    </div>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400">
                    {{ __('Active requests awaiting supplier quotes.') }}
                </p>
            </div>
        </div>

        {{-- Pending Quotes Card --}}
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent dark:from-amber-500/20 dark:via-amber-500/10 backdrop-blur-sm border border-amber-200/50 dark:border-amber-500/30 hover:border-amber-400/60 dark:hover:border-amber-400/50 transition-all duration-300 hover:shadow-lg hover:shadow-amber-500/20 dark:hover:shadow-amber-500/30">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-400/0 to-amber-600/0 group-hover:from-amber-400/5 group-hover:to-amber-600/5 transition-all duration-500"></div>
            <div class="relative p-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1.5">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">{{ __('Pending Quotes') }}</p>
                            @if($pendingQuotes > 0)
                                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded-full bg-amber-500 text-white animate-pulse">{{ $pendingQuotes }}</span>
                            @endif
                        </div>
                        <p class="text-2xl font-bold bg-gradient-to-br from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                            {{ $pendingQuotes }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 dark:from-amber-400 dark:to-amber-500 flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="clock" class="w-6 h-6 text-white" />
                    </div>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400">
                    {{ __('Quotes received and awaiting your review.') }}
                </p>
            </div>
        </div>

        {{-- Awarded Contracts Card --}}
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-500/10 via-green-500/5 to-transparent dark:from-green-500/20 dark:via-green-500/10 backdrop-blur-sm border border-green-200/50 dark:border-green-500/30 hover:border-green-400/60 dark:hover:border-green-400/50 transition-all duration-300 hover:shadow-lg hover:shadow-green-500/20 dark:hover:shadow-green-500/30">
            <div class="absolute inset-0 bg-gradient-to-br from-green-400/0 to-green-600/0 group-hover:from-green-400/5 group-hover:to-green-600/5 transition-all duration-500"></div>
            <div class="relative p-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1.5">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-green-600 dark:text-green-400">{{ __('Awarded Contracts') }}</p>
                        </div>
                        <p class="text-2xl font-bold bg-gradient-to-br from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                            {{ $awardedContracts }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 dark:from-green-400 dark:to-green-500 flex items-center justify-center shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="check-circle" class="w-6 h-6 text-white" />
                    </div>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400">
                    {{ __('Successfully awarded quotes and contracts.') }}
                </p>
            </div>
        </div>

        {{-- Active Suppliers Card --}}
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-500/10 via-purple-500/5 to-transparent dark:from-purple-500/20 dark:via-purple-500/10 backdrop-blur-sm border border-purple-200/50 dark:border-purple-500/30 hover:border-purple-400/60 dark:hover:border-purple-400/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/20 dark:hover:shadow-purple-500/30">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-400/0 to-purple-600/0 group-hover:from-purple-400/5 group-hover:to-purple-600/5 transition-all duration-500"></div>
            <div class="relative p-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1.5">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">{{ __('Active Suppliers') }}</p>
                        </div>
                        <p class="text-2xl font-bold bg-gradient-to-br from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                            {{ $activeSuppliers }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-400 dark:to-purple-500 flex items-center justify-center shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="user-group" class="w-6 h-6 text-white" />
                    </div>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400">
                    {{ __("Suppliers you've engaged with via quotes.") }}
                </p>
            </div>
        </div>
    </div>

    {{-- Procurement Overview - 2026 Modern Design --}}
    <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-cyan-500/5 dark:from-blue-500/10 dark:to-cyan-500/10"></div>

        <div class="relative p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <x-icon name="chart-bar" class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-gray-50">{{ __('Procurement Overview') }}</h2>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">
                            {{ __('Snapshot of your purchasing activity and pipeline.') }}
                        </p>
                    </div>
                </div>
                <div class="hidden md:flex items-center gap-2">
                    <div class="px-3 py-1.5 rounded-full bg-gradient-to-r from-blue-500/10 to-cyan-500/10 border border-blue-500/30 dark:border-blue-500/20">
                        <span class="text-[10px] font-bold uppercase tracking-wider bg-gradient-to-r from-blue-600 to-cyan-600 dark:from-blue-400 dark:to-cyan-400 bg-clip-text text-transparent">Live Data</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                {{-- Total Spend Card --}}
                <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-indigo-500/5 to-indigo-600/10 dark:from-indigo-500/10 dark:to-indigo-600/20 p-5 border border-indigo-200/50 dark:border-indigo-500/30 hover:border-indigo-400/60 dark:hover:border-indigo-400/50 transition-all duration-300 hover:shadow-lg hover:shadow-indigo-500/20">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-indigo-500/20 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>

                    <div class="relative">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">
                                {{ __('Total Spend') }}
                            </p>
                            <span class="px-2 py-1 rounded-lg bg-indigo-500/15 text-[9px] font-bold text-indigo-600 dark:text-indigo-300 border border-indigo-500/30">
                                {{ __('Orders') }}
                            </span>
                        </div>

                        <p class="text-3xl font-bold bg-gradient-to-br from-indigo-600 to-purple-600 dark:from-indigo-300 dark:to-purple-300 bg-clip-text text-transparent mb-4">
                            ${{ number_format($totalSpend, 2) }}
                        </p>

                        <div class="mb-3">
                            <div class="flex items-center justify-between text-[10px] mb-1.5">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">{{ __('Budget Usage') }}</span>
                                <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ min(100, ($totalSpend / 10000) * 100) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200/50 dark:bg-gray-700/50 rounded-full h-2.5 overflow-hidden backdrop-blur-sm">
                                <div class="h-2.5 rounded-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 transition-all duration-500 shadow-lg shadow-indigo-500/50" style="width: {{ min(100, ($totalSpend / 10000) * 100) }}%"></div>
                            </div>
                        </div>

                        <p class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400">
                            {{ __('Total amount spent on completed procurement orders.') }}
                        </p>
                    </div>
                </div>

                {{-- Completed Orders Card --}}
                <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-emerald-500/5 to-emerald-600/10 dark:from-emerald-500/10 dark:to-emerald-600/20 p-5 border border-emerald-200/50 dark:border-emerald-500/30 hover:border-emerald-400/60 dark:hover:border-emerald-400/50 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/20">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-emerald-500/20 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>

                    <div class="relative">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                                {{ __('Completed Orders') }}
                            </p>
                            <span class="px-2 py-1 rounded-lg bg-emerald-500/15 text-[9px] font-bold text-emerald-600 dark:text-emerald-300 border border-emerald-500/30">
                                {{ __('Success') }}
                            </span>
                        </div>

                        <p class="text-3xl font-bold bg-gradient-to-br from-emerald-600 to-lime-600 dark:from-emerald-300 dark:to-lime-300 bg-clip-text text-transparent mb-4">
                            {{ $completedOrders }}
                        </p>

                        <div class="mb-3">
                            <div class="flex items-center justify-between text-[10px] mb-1.5">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">{{ __('Success Rate') }}</span>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $completedOrders > 0 ? '100' : '0' }}%</span>
                            </div>
                            <div class="w-full bg-gray-200/50 dark:bg-gray-700/50 rounded-full h-2.5 overflow-hidden backdrop-blur-sm">
                                <div class="h-2.5 rounded-full bg-gradient-to-r from-emerald-500 via-teal-500 to-lime-500 transition-all duration-500 shadow-lg shadow-emerald-500/50" style="width: {{ $completedOrders > 0 ? '100' : '0' }}%"></div>
                            </div>
                        </div>

                        <p class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400">
                            {{ __('Successfully fulfilled procurement orders.') }}
                        </p>
                    </div>
                </div>

                {{-- RFQ Activity Card --}}
                <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-cyan-500/5 to-cyan-600/10 dark:from-cyan-500/10 dark:to-cyan-600/20 p-5 border border-cyan-200/50 dark:border-cyan-500/30 hover:border-cyan-400/60 dark:hover:border-cyan-400/50 transition-all duration-300 hover:shadow-lg hover:shadow-cyan-500/20">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-cyan-500/20 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>

                    <div class="relative">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">
                                {{ __('RFQ Activity') }}
                            </p>
                            <span class="px-2 py-1 rounded-lg bg-cyan-500/15 text-[9px] font-bold text-cyan-600 dark:text-cyan-300 border border-cyan-500/30 flex items-center gap-1">
                                @if($pendingQuotes > 0)
                                    <span class="w-1.5 h-1.5 bg-cyan-500 rounded-full animate-pulse"></span>
                                @endif
                                {{ __('Pipeline') }}
                            </span>
                        </div>

                        <p class="text-3xl font-bold bg-gradient-to-br from-cyan-600 to-blue-600 dark:from-cyan-300 dark:to-blue-300 bg-clip-text text-transparent mb-4">
                            {{ $openRfqs + $pendingQuotes }}
                        </p>

                        <div class="mb-3">
                            <div class="flex items-center justify-between text-[10px] mb-1.5">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">{{ __('Pipeline Health') }}</span>
                                <span class="font-bold text-cyan-600 dark:text-cyan-400">{{ min(100, (($openRfqs + $pendingQuotes) / 20) * 100) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200/50 dark:bg-gray-700/50 rounded-full h-2.5 overflow-hidden backdrop-blur-sm">
                                <div class="h-2.5 rounded-full bg-gradient-to-r from-cyan-500 via-blue-500 to-indigo-500 transition-all duration-500 shadow-lg shadow-cyan-500/50" style="width: {{ min(100, (($openRfqs + $pendingQuotes) / 20) * 100) }}%"></div>
                            </div>
                        </div>

                        <p class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400">
                            {{ __('Combined open RFQs and quotes awaiting review.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions as cards --}}
    <x-card class="bg-slate-950/60 border-slate-800">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <x-icon name="bolt" class="w-5 h-5 text-yellow-400" />
                <h2 class="text-sm font-semibold text-slate-50">{{ __('Quick Actions') }}</h2>
            </div>
            <p class="text-[11px] text-slate-400 hidden md:block">
                {{ __('Jump to your most common procurement workflows.') }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <a href="{{ route('buyer.rfq.index') }}" class="group rounded-lg bg-slate-900/80 hover:bg-slate-800 transition border border-slate-800 hover:border-blue-500/60 p-4 flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-blue-500/15 flex items-center justify-center">
                        <x-icon name="document-text" class="w-4 h-4 text-blue-400" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-50">{{ __('Manage RFQs') }}</p>
                        <p class="text-[11px] text-slate-400">{{ __('Create and manage requests for quotation.') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400">
                    <span>{{ __('Go to RFQs') }}</span>
                    <x-icon name="arrow-right" class="w-4 h-4 text-slate-400 group-hover:text-blue-400" />
                </div>
            </a>

            <a href="{{ route('buyer.products.index') }}" class="group rounded-lg bg-slate-900/80 hover:bg-slate-800 transition border border-slate-800 hover:border-purple-500/60 p-4 flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-purple-500/15 flex items-center justify-center">
                        <x-icon name="cube" class="w-4 h-4 text-purple-400" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-50">{{ __('Browse Products') }}</p>
                        <p class="text-[11px] text-slate-400">{{ __('Explore available products from suppliers.') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400">
                    <span>{{ __('Go to products') }}</span>
                    <x-icon name="arrow-right" class="w-4 h-4 text-slate-400 group-hover:text-purple-400" />
                </div>
            </a>

            <a href="{{ route('buyer.markets.index') }}" class="group rounded-lg bg-slate-900/80 hover:bg-slate-800 transition border border-slate-800 hover:border-indigo-500/60 p-4 flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-500/15 flex items-center justify-center">
                        <x-icon name="building-storefront" class="w-4 h-4 text-indigo-400" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-50">{{ __('Browse Markets') }}</p>
                        <p class="text-[11px] text-slate-400">{{ __('Discover supplier marketplaces.') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400">
                    <span>{{ __('Go to markets') }}</span>
                    <x-icon name="arrow-right" class="w-4 h-4 text-slate-400 group-hover:text-indigo-400" />
                </div>
            </a>

            <a href="{{ route('buyer.logs.index') }}" class="group rounded-lg bg-slate-900/80 hover:bg-slate-800 transition border border-slate-800 hover:border-gray-500/60 p-4 flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-gray-500/15 flex items-center justify-center">
                        <x-icon name="clipboard-document-list" class="w-4 h-4 text-gray-200" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-50">{{ __('View Activity Log') }}</p>
                        <p class="text-[11px] text-slate-400">{{ __('Track changes and events in your account.') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400">
                    <span>{{ __('Go to activity') }}</span>
                    <x-icon name="arrow-right" class="w-4 h-4 text-slate-400 group-hover:text-gray-200" />
                </div>
            </a>

            <a href="{{ route('settings.index') }}" class="group rounded-lg bg-slate-900/80 hover:bg-slate-800 transition border border-slate-800 hover:border-slate-500/60 p-4 flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-slate-500/15 flex items-center justify-center">
                        <x-icon name="cog-6-tooth" class="w-4 h-4 text-slate-200" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-50">{{ __('Settings') }}</p>
                        <p class="text-[11px] text-slate-400">{{ __('Update profile and preferences.') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400">
                    <span>{{ __('Open settings') }}</span>
                    <x-icon name="arrow-right" class="w-4 h-4 text-slate-400 group-hover:text-slate-200" />
                </div>
            </a>
        </div>
    </x-card>

    {{-- Bottom: guidance --}}
    @if($openRfqs === 0 && $pendingQuotes === 0)
        <x-card class="bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800">
            <div class="flex items-start gap-4">
                <x-icon name="light-bulb" class="w-8 h-8 text-blue-500" />
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-50 mb-1">
                        {{ __('Start Your Procurement Journey') }}
                    </h3>
                    <p class="text-xs text-gray-600 dark:text-gray-300 mb-3">
                        {{ __('Create your first RFQ to start receiving competitive quotes from qualified suppliers.') }}
                    </p>
                    <a href="{{ route('buyer.rfq.index') }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition">
                        <x-icon name="plus" class="w-4 h-4 mr-1.5" />
                        {{ __('Create RFQ') }}
                    </a>
                </div>
            </div>
        </x-card>
    @endif
</div>
