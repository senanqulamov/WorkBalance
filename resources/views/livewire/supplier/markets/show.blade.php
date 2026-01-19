<div class="space-y-6">

    {{-- Modern Header Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-600 via-cyan-500 to-blue-500 text-white shadow-2xl shadow-cyan-500/30">
        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative p-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('supplier.markets.index') }}" class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30 hover:bg-white/30 transition">
                    <x-icon name="arrow-left" class="w-5 h-5 text-white" />
                </a>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight">
                        {{ $market->name }}
                    </h1>
                    <p class="text-sm text-cyan-100 mt-0.5">
                        {{ __('Market Details') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="space-y-6">
        {{-- Top three cards in a single row with equal width --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Market Information --}}
            <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl p-6">
                <h2 class="text-xl font-bold mb-4">{{ __('Market Information') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-400">{{ __('Location') }}</label>
                        <p class="font-medium">{{ $market->location ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-400">{{ __('Seller') }}</label>
                        <p class="font-medium">{{ $market->seller?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-400">{{ __('Total Products') }}</label>
                        <p class="font-bold text-xl text-cyan-600">{{ $market->products->count() }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-400">{{ __('Created') }}</label>
                        <p class="font-medium">{{ $market->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                @if($market->description)
                    <div class="mt-4">
                        <label class="text-sm text-gray-600 dark:text-gray-400">{{ __('Description') }}</label>
                        <p class="mt-1">{{ $market->description }}</p>
                    </div>
                @endif
            </div>

            {{-- Market Stats --}}
            <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl p-6">
                <h3 class="text-lg font-bold mb-4">{{ __('Market Stats') }}</h3>
                <div class="space-y-4">
                    <div class="p-3 rounded-lg bg-cyan-50 dark:bg-cyan-900/20">
                        <div class="text-xs text-cyan-600 dark:text-cyan-400 mb-1">{{ __('Products') }}</div>
                        <div class="text-2xl font-bold">{{ $market->products->count() }}</div>
                    </div>
                    <div class="p-3 rounded-lg bg-green-50 dark:bg-green-900/20">
                        <div class="text-xs text-green-600 dark:text-green-400 mb-1">{{ __('In Stock') }}</div>
                        <div class="text-2xl font-bold">{{ $market->products->where('stock', '>', 0)->count() }}</div>
                    </div>
                </div>
            </div>

            {{-- Market Owner Information --}}
            @if($market->seller)
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-50 to-blue-50 dark:from-cyan-900/20 dark:to-blue-900/20 backdrop-blur-xl border border-cyan-200/50 dark:border-cyan-700/50 shadow-xl p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-600 to-blue-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                            {{ substr($market->seller->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <h2 class="text-xl font-bold mb-1 text-gray-900 dark:text-white">{{ __('Market Owner') }}</h2>
                            <p class="text-cyan-600 dark:text-cyan-400 font-semibold text-lg mb-3">{{ $market->seller->name }}</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @if($market->seller->email)
                                    <div class="flex items-center gap-2 text-sm">
                                        <x-icon name="envelope" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                                        <span class="text-gray-700 dark:text-gray-300">{{ $market->seller->email }}</span>
                                    </div>
                                @endif

                                @if($market->seller->phone)
                                    <div class="flex items-center gap-2 text-sm">
                                        <x-icon name="phone" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                                        <span class="text-gray-700 dark:text-gray-300">{{ $market->seller->phone }}</span>
                                    </div>
                                @endif

                                @if($market->seller->company_name)
                                    <div class="flex items-center gap-2 text-sm">
                                        <x-icon name="building-office" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                                        <span class="text-gray-700 dark:text-gray-300">{{ $market->seller->company_name }}</span>
                                    </div>
                                @endif

                                @if($market->seller->city || $market->seller->country)
                                    <div class="flex items-center gap-2 text-sm">
                                        <x-icon name="map-pin" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                                        <span class="text-gray-700 dark:text-gray-300">
                                            {{ implode(', ', array_filter([$market->seller->city, $market->seller->country])) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            @if($market->seller->rating && $market->seller->rating > 0)
                                <div class="mt-3 flex items-center gap-2">
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <x-icon
                                                name="star"
                                                class="w-4 h-4 {{ $i <= $market->seller->rating ? 'text-yellow-500 fill-yellow-500' : 'text-gray-300 dark:text-gray-600' }}"
                                            />
                                        @endfor
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ number_format($market->seller->rating, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Products in this Market --}}
        <div class="space-y-6">
            @if($market->products->count() > 0)
                <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl p-6">
                    <h2 class="text-xl font-bold mb-4">{{ __('Products in this Market') }} ({{ $market->products->count() }})</h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        @foreach($market->products as $product)
                            <div class="p-4 rounded-xl border border-gray-200 dark:border-slate-700 hover:border-cyan-500 dark:hover:border-cyan-500 transition-all hover:shadow-lg bg-white dark:bg-slate-800">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $product->name }}</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('SKU') }}: {{ $product->sku }}</p>
                                    </div>
                                    <span class="font-bold text-lg text-cyan-600 dark:text-cyan-400">${{ number_format($product->price, 2) }}</span>
                                </div>

                                <div class="space-y-2 mb-3">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">{{ __('Category') }}:</span>
                                        <x-badge :text="$product->category->name" color="gray" sm />
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">{{ __('Stock') }}:</span>
                                        <x-badge
                                            :text="$product->stock . ' ' . __('units')"
                                            :color="$product->stock > 10 ? 'green' : ($product->stock > 0 ? 'yellow' : 'red')"
                                            sm
                                        />
                                    </div>
                                </div>

                                @if($product->description)
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">{{ $product->description }}</p>
                                @endif

                                <x-button
                                    color="cyan"
                                    icon="eye"
                                    href="{{ route('supplier.products.show', $product) }}"
                                    class="w-full justify-center"
                                    sm
                                >
                                    {{ __('View Product') }}
                                </x-button>
                            </div>
                        @endforeach
                    </div>

                    @if($market->products->count() > 6)
                        <div class="mt-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Showing all :count products', ['count' => $market->products->count()]) }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
