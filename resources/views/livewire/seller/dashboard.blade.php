<div class="space-y-6">
    {{-- Hero / Summary --}}
    <x-card class="bg-gradient-to-r from-emerald-600 via-emerald-500 to-cyan-500 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <x-icon name="shopping-bag" class="w-7 h-7" />
                    <h1 class="text-2xl md:text-3xl font-semibold tracking-tight">
                        {{ __('Seller Dashboard') }}
                    </h1>
                </div>
                <p class="text-sm md:text-base text-emerald-50/90 max-w-xl">
                    {{ __('Monitor your marketplace performance, manage products, and track orders in one unified view.') }}
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 w-full md:w-auto">
                <div class="bg-emerald-900/30 rounded-lg px-3 py-2 border border-emerald-400/40">
                    <p class="text-[10px] uppercase tracking-wide text-emerald-100/70 mb-1">{{ __('Total Revenue') }}</p>
                    <p class="text-lg font-semibold leading-tight">
                        {{ '$' . number_format($totalRevenue, 2) }}
                    </p>
                </div>
                <div class="bg-emerald-900/30 rounded-lg px-3 py-2 border border-emerald-400/40">
                    <p class="text-[10px] uppercase tracking-wide text-emerald-100/70 mb-1">{{ __('Total Sales') }}</p>
                    <p class="text-lg font-semibold leading-tight">{{ $totalSales }}</p>
                </div>
                <div class="bg-emerald-900/30 rounded-lg px-3 py-2 border border-emerald-400/40">
                    <p class="text-[10px] uppercase tracking-wide text-emerald-100/70 mb-1">{{ __('Active Markets') }}</p>
                    <p class="text-lg font-semibold leading-tight">{{ $activeMarkets }}</p>
                </div>
                <div class="bg-emerald-900/30 rounded-lg px-3 py-2 border border-emerald-400/40">
                    <p class="text-[10px] uppercase tracking-wide text-emerald-100/70 mb-1">{{ __('Products Listed') }}</p>
                    <p class="text-lg font-semibold leading-tight">{{ $productsListed }}</p>
                </div>
            </div>
        </div>
    </x-card>

    {{-- Seller nav --}}


    {{-- KPI grid - 2026 Modern Design --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
        {{-- Commission Earned Card --}}
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-transparent dark:from-emerald-500/20 dark:via-emerald-500/10 backdrop-blur-sm border border-emerald-200/50 dark:border-emerald-500/30 hover:border-emerald-400/60 dark:hover:border-emerald-400/50 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/20 dark:hover:shadow-emerald-500/30">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-400/0 to-emerald-600/0 group-hover:from-emerald-400/5 group-hover:to-emerald-600/5 transition-all duration-500"></div>
            <div class="relative p-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1.5">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">{{ __('Commission Earned') }}</p>
                        </div>
                        <p class="text-2xl font-bold bg-gradient-to-br from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                            {{ '$' . number_format($commissionEarned, 2) }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 dark:from-emerald-400 dark:to-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="banknotes" class="w-6 h-6 text-white" />
                    </div>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400">
                    {{ __('Based on your current commission rate and completed orders.') }}
                </p>
            </div>
        </div>

        {{-- Pending Orders Card --}}
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent dark:from-amber-500/20 dark:via-amber-500/10 backdrop-blur-sm border border-amber-200/50 dark:border-amber-500/30 hover:border-amber-400/60 dark:hover:border-amber-400/50 transition-all duration-300 hover:shadow-lg hover:shadow-amber-500/20 dark:hover:shadow-amber-500/30">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-400/0 to-amber-600/0 group-hover:from-amber-400/5 group-hover:to-amber-600/5 transition-all duration-500"></div>
            <div class="relative p-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1.5">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">{{ __('Pending Orders') }}</p>
                            @if($pendingOrders > 0)
                                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded-full bg-amber-500 text-white animate-pulse">{{ $pendingOrders }}</span>
                            @endif
                        </div>
                        <p class="text-2xl font-bold bg-gradient-to-br from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                            {{ $pendingOrders }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 dark:from-amber-400 dark:to-amber-500 flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="clock" class="w-6 h-6 text-white" />
                    </div>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400">
                    {{ __('Orders awaiting fulfillment or confirmation.') }}
                </p>
            </div>
        </div>

        {{-- Seller Rating Card --}}
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-yellow-500/10 via-yellow-500/5 to-transparent dark:from-yellow-500/20 dark:via-yellow-500/10 backdrop-blur-sm border border-yellow-200/50 dark:border-yellow-500/30 hover:border-yellow-400/60 dark:hover:border-yellow-400/50 transition-all duration-300 hover:shadow-lg hover:shadow-yellow-500/20 dark:hover:shadow-yellow-500/30">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-400/0 to-yellow-600/0 group-hover:from-yellow-400/5 group-hover:to-yellow-600/5 transition-all duration-500"></div>
            <div class="relative p-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1.5">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-yellow-600 dark:text-yellow-400">{{ __('Seller Rating') }}</p>
                        </div>
                        <p class="text-2xl font-bold bg-gradient-to-br from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                            {{ number_format($averageRating, 1) }}<span class="text-lg text-gray-500 dark:text-gray-400">/5.0</span>
                        </p>
                    </div>
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-yellow-400 to-yellow-500 dark:from-yellow-400 dark:to-yellow-500 flex items-center justify-center shadow-lg shadow-yellow-500/30 group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="star" class="w-6 h-6 text-white" />
                    </div>
                </div>
                <div class="mb-3">
                    <div class="w-full bg-gray-200/50 dark:bg-gray-700/50 rounded-full h-2 overflow-hidden backdrop-blur-sm">
                        <div class="h-2 rounded-full bg-gradient-to-r from-yellow-400 via-yellow-500 to-amber-500 transition-all duration-500" style="width: {{ min(100, ($averageRating / 5) * 100) }}%"></div>
                    </div>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400">
                    {{ __('Build trust by keeping a high fulfillment and response rate.') }}
                </p>
            </div>
        </div>

        {{-- Verification Card --}}
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br {{ auth()->user()->verified_seller ? 'from-emerald-500/10 via-emerald-500/5' : 'from-gray-500/10 via-gray-500/5' }} to-transparent dark:{{ auth()->user()->verified_seller ? 'from-emerald-500/20 dark:via-emerald-500/10' : 'from-gray-500/20 dark:via-gray-500/10' }} backdrop-blur-sm border {{ auth()->user()->verified_seller ? 'border-emerald-200/50 dark:border-emerald-500/30 hover:border-emerald-400/60 dark:hover:border-emerald-400/50' : 'border-gray-200/50 dark:border-gray-500/30 hover:border-gray-400/60 dark:hover:border-gray-400/50' }} transition-all duration-300 hover:shadow-lg {{ auth()->user()->verified_seller ? 'hover:shadow-emerald-500/20 dark:hover:shadow-emerald-500/30' : 'hover:shadow-gray-500/20 dark:hover:shadow-gray-500/30' }}">
            <div class="absolute inset-0 bg-gradient-to-br {{ auth()->user()->verified_seller ? 'from-emerald-400/0 to-emerald-600/0 group-hover:from-emerald-400/5 group-hover:to-emerald-600/5' : 'from-gray-400/0 to-gray-600/0 group-hover:from-gray-400/5 group-hover:to-gray-600/5' }} transition-all duration-500"></div>
            <div class="relative p-5">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1.5">
                            <p class="text-[10px] font-bold uppercase tracking-wider {{ auth()->user()->verified_seller ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-600 dark:text-gray-400' }}">{{ __('Verification') }}</p>
                            @if(auth()->user()->verified_seller)
                                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded-full bg-emerald-500 text-white">✓</span>
                            @endif
                        </div>
                        <p class="text-2xl font-bold bg-gradient-to-br from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                            {{ auth()->user()->verified_seller ? __('Verified') : __('Not Verified') }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br {{ auth()->user()->verified_seller ? 'from-emerald-500 to-emerald-600 dark:from-emerald-400 dark:to-emerald-500 shadow-emerald-500/30' : 'from-gray-400 to-gray-500 dark:from-gray-500 dark:to-gray-600 shadow-gray-500/30' }} flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="shield-check" class="w-6 h-6 text-white" />
                    </div>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400">
                    {{ __('Verified sellers gain higher visibility and customer confidence.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Performance Overview - 2026 Modern Design --}}
    <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 via-transparent to-blue-500/5 dark:from-purple-500/10 dark:to-blue-500/10"></div>

        <div class="relative p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <x-icon name="chart-bar" class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-gray-50">{{ __('Performance Overview') }}</h2>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">
                            {{ __('Snapshot of your catalog and order flow.') }}
                        </p>
                    </div>
                </div>
                <div class="hidden md:flex items-center gap-2">
                    <div class="px-3 py-1.5 rounded-full bg-gradient-to-r from-emerald-500/10 to-cyan-500/10 border border-emerald-500/30 dark:border-emerald-500/20">
                        <span class="text-[10px] font-bold uppercase tracking-wider bg-gradient-to-r from-emerald-600 to-cyan-600 dark:from-emerald-400 dark:to-cyan-400 bg-clip-text text-transparent">Live Data</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                {{-- Products Listed Card --}}
                <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-purple-500/5 to-purple-600/10 dark:from-purple-500/10 dark:to-purple-600/20 p-5 border border-purple-200/50 dark:border-purple-500/30 hover:border-purple-400/60 dark:hover:border-purple-400/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/20">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-purple-500/20 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>

                    <div class="relative">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">
                                {{ __('Products Listed') }}
                            </p>
                            <span class="px-2 py-1 rounded-lg bg-purple-500/15 text-[9px] font-bold text-purple-600 dark:text-purple-300 border border-purple-500/30">
                                {{ __('Catalog') }}
                            </span>
                        </div>

                        <p class="text-3xl font-bold bg-gradient-to-br from-purple-600 to-fuchsia-600 dark:from-purple-300 dark:to-fuchsia-300 bg-clip-text text-transparent mb-4">
                            {{ $productsListed }}
                        </p>

                        <div class="mb-3">
                            <div class="flex items-center justify-between text-[10px] mb-1.5">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">{{ __('Visibility Score') }}</span>
                                <span class="font-bold text-purple-600 dark:text-purple-400">{{ min(100, ($productsListed / 50) * 100) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200/50 dark:bg-gray-700/50 rounded-full h-2.5 overflow-hidden backdrop-blur-sm">
                                <div class="h-2.5 rounded-full bg-gradient-to-r from-purple-500 via-fuchsia-500 to-pink-500 transition-all duration-500 shadow-lg shadow-purple-500/50" style="width: {{ min(100, ($productsListed / 50) * 100) }}%"></div>
                            </div>
                        </div>

                        <p class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400">
                            {{ __('More quality products improve your visibility across markets.') }}
                        </p>
                    </div>
                </div>

                {{-- Total Sales Card --}}
                <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-emerald-500/5 to-emerald-600/10 dark:from-emerald-500/10 dark:to-emerald-600/20 p-5 border border-emerald-200/50 dark:border-emerald-500/30 hover:border-emerald-400/60 dark:hover:border-emerald-400/50 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/20">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-emerald-500/20 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>

                    <div class="relative">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                                {{ __('Total Sales') }}
                            </p>
                            <span class="px-2 py-1 rounded-lg bg-emerald-500/15 text-[9px] font-bold text-emerald-600 dark:text-emerald-300 border border-emerald-500/30">
                                {{ __('Orders') }}
                            </span>
                        </div>

                        <p class="text-3xl font-bold bg-gradient-to-br from-emerald-600 to-lime-600 dark:from-emerald-300 dark:to-lime-300 bg-clip-text text-transparent mb-4">
                            {{ $totalSales }}
                        </p>

                        <div class="mb-3">
                            <div class="flex items-center justify-between text-[10px] mb-1.5">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">{{ __('Target Progress') }}</span>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ min(100, ($totalSales / 100) * 100) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200/50 dark:bg-gray-700/50 rounded-full h-2.5 overflow-hidden backdrop-blur-sm">
                                <div class="h-2.5 rounded-full bg-gradient-to-r from-emerald-500 via-teal-500 to-lime-500 transition-all duration-500 shadow-lg shadow-emerald-500/50" style="width: {{ min(100, ($totalSales / 100) * 100) }}%"></div>
                            </div>
                        </div>

                        <p class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400">
                            {{ __('Represents completed orders containing your products.') }}
                        </p>
                    </div>
                </div>

                {{-- Pending Orders Card --}}
                <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-amber-500/5 to-amber-600/10 dark:from-amber-500/10 dark:to-amber-600/20 p-5 border border-amber-200/50 dark:border-amber-500/30 hover:border-amber-400/60 dark:hover:border-amber-400/50 transition-all duration-300 hover:shadow-lg hover:shadow-amber-500/20">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-amber-500/20 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>

                    <div class="relative">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                                {{ __('Pending Orders') }}
                            </p>
                            <span class="px-2 py-1 rounded-lg bg-amber-500/15 text-[9px] font-bold text-amber-600 dark:text-amber-300 border border-amber-500/30 flex items-center gap-1">
                                @if($pendingOrders > 0)
                                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                                @endif
                                {{ __('Queue') }}
                            </span>
                        </div>

                        <p class="text-3xl font-bold bg-gradient-to-br from-amber-600 to-orange-600 dark:from-amber-300 dark:to-orange-300 bg-clip-text text-transparent mb-4">
                            {{ $pendingOrders }}
                        </p>

                        <div class="mb-3">
                            <div class="flex items-center justify-between text-[10px] mb-1.5">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">{{ __('Queue Status') }}</span>
                                <span class="font-bold text-amber-600 dark:text-amber-400">{{ min(100, ($pendingOrders / 50) * 100) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200/50 dark:bg-gray-700/50 rounded-full h-2.5 overflow-hidden backdrop-blur-sm">
                                <div class="h-2.5 rounded-full bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 transition-all duration-500 shadow-lg shadow-amber-500/50" style="width: {{ min(100, ($pendingOrders / 50) * 100) }}%"></div>
                            </div>
                        </div>

                        <p class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400">
                            {{ __('Lower is better – keep this small with fast fulfillment.') }}
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
                {{ __('Jump to your most common seller workflows.') }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <a href="{{ route('seller.products.index') }}" class="group rounded-lg bg-slate-900/80 hover:bg-slate-800 transition border border-slate-800 hover:border-emerald-500/60 p-4 flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-500/15 flex items-center justify-center">
                        <x-icon name="cube" class="w-4 h-4 text-emerald-400" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-50">{{ __('Manage Products') }}</p>
                        <p class="text-[11px] text-slate-400">{{ __('Create, update, and organize your catalog.') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400">
                    <span>{{ __('Go to products') }}</span>
                    <x-icon name="arrow-right" class="w-4 h-4 text-slate-400 group-hover:text-emerald-400" />
                </div>
            </a>

            <a href="{{ route('seller.orders.index') }}" class="group rounded-lg bg-slate-900/80 hover:bg-slate-800 transition border border-slate-800 hover:border-sky-500/60 p-4 flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-sky-500/15 flex items-center justify-center">
                        <x-icon name="receipt-percent" class="w-4 h-4 text-sky-400" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-50">{{ __('View Orders') }}</p>
                        <p class="text-[11px] text-slate-400">{{ __('Track incoming orders and fulfillment status.') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400">
                    <span>{{ __('Go to orders') }}</span>
                    <x-icon name="arrow-right" class="w-4 h-4 text-slate-400 group-hover:text-sky-400" />
                </div>
            </a>

            <a href="{{ route('seller.markets.index') }}" class="group rounded-lg bg-slate-900/80 hover:bg-slate-800 transition border border-slate-800 hover:border-purple-500/60 p-4 flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-purple-500/15 flex items-center justify-center">
                        <x-icon name="building-storefront" class="w-4 h-4 text-purple-400" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-50">{{ __('Browse Markets') }}</p>
                        <p class="text-[11px] text-slate-400">{{ __('Configure and optimize your sales channels.') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400">
                    <span>{{ __('Go to markets') }}</span>
                    <x-icon name="arrow-right" class="w-4 h-4 text-slate-400 group-hover:text-purple-400" />
                </div>
            </a>

            <a href="{{ route('seller.logs.index') }}" class="group rounded-lg bg-slate-900/80 hover:bg-slate-800 transition border border-slate-800 hover:border-indigo-500/60 p-4 flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-500/15 flex items-center justify-center">
                        <x-icon name="clipboard-document-list" class="w-4 h-4 text-indigo-400" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-50">{{ __('View Activity Log') }}</p>
                        <p class="text-[11px] text-slate-400">{{ __('See changes and events related to your account.') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400">
                    <span>{{ __('Go to activity') }}</span>
                    <x-icon name="arrow-right" class="w-4 h-4 text-slate-400 group-hover:text-indigo-400" />
                </div>
            </a>

            <a href="{{ route('settings.index') }}" class="group rounded-lg bg-slate-900/80 hover:bg-slate-800 transition border border-slate-800 hover:border-gray-500/60 p-4 flex flex-col justify-between">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full bg-slate-500/15 flex items-center justify-center">
                        <x-icon name="cog-6-tooth" class="w-4 h-4 text-gray-200" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-50">{{ __('Settings') }}</p>
                        <p class="text-[11px] text-slate-400">{{ __('Update profile, preferences, and verification.') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400">
                    <span>{{ __('Open settings') }}</span>
                    <x-icon name="arrow-right" class="w-4 h-4 text-slate-400 group-hover:text-gray-200" />
                </div>
            </a>
        </div>
    </x-card>

    {{-- Bottom: guidance + verification --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        @if($productsListed === 0)
            <x-card class="bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800">
                <div class="flex items-start gap-4">
                    <x-icon name="light-bulb" class="w-8 h-8 text-emerald-500" />
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-50 mb-1">
                            {{ __('Start Selling Today') }}
                        </h3>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mb-3">
                            {{ __('List your first product to start reaching buyers across all your markets.') }}
                        </p>
                        <a href="{{ route('seller.products.index') }}" class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-md transition">
                            <x-icon name="plus" class="w-4 h-4 mr-1.5" />
                            {{ __('Add Product') }}
                        </a>
                    </div>
                </div>
            </x-card>
        @endif

        @if(!auth()->user()->verified_seller)
            <x-card class="bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800">
                <div class="flex items-start gap-4">
                    <x-icon name="exclamation-triangle" class="w-7 h-7 text-amber-500" />
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-50 mb-1">
                            {{ __('Verify Your Seller Account') }}
                        </h3>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mb-3">
                            {{ __('Complete verification to unlock higher limits, priority placement, and trust badges.') }}
                        </p>
                        <a href="{{ route('settings.index') }}" class="inline-flex items-center text-xs font-medium text-amber-700 dark:text-amber-300 hover:text-amber-900 dark:hover:text-amber-100">
                            {{ __('Start Verification') }}
                            <x-icon name="arrow-right" class="w-4 h-4 ml-1" />
                        </a>
                    </div>
                </div>
            </x-card>
        @endif
    </div>
</div>
