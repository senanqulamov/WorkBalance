<div class="space-y-6">

    {{-- Modern Header Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-600 via-purple-500 to-fuchsia-500 text-white shadow-2xl shadow-purple-500/30">
        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative p-6">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('supplier.products.index') }}" class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30 hover:bg-white/30 transition">
                        <x-icon name="arrow-left" class="w-5 h-5 text-white" />
                    </a>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight">
                            {{ $product->name }}
                        </h1>
                        <p class="text-sm text-purple-100 mt-0.5">
                            {{ __('Product Details') }}
                        </p>
                    </div>
                </div>
                <x-badge text="{{ $product->sku }}" color="white" />
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Product Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl p-6">
                <h2 class="text-xl font-bold mb-4">{{ __('Product Information') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-400">{{ __('Category') }}</label>
                        <p class="font-medium">{{ $product->category?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-400">{{ __('Price') }}</label>
                        <p class="font-bold text-xl text-purple-600">${{ number_format($product->price, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-400">{{ __('Stock') }}</label>
                        <p class="font-medium">{{ $product->stock }} {{ __('units') }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-400">{{ __('Market') }}</label>
                        <p class="font-medium">{{ $product->market?->name ?? '—' }}</p>
                    </div>
                </div>
                @if($product->description)
                    <div class="mt-4">
                        <label class="text-sm text-gray-600 dark:text-gray-400">{{ __('Description') }}</label>
                        <p class="mt-1">{{ $product->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl p-6">
                <h3 class="text-lg font-bold mb-4">{{ __('Quick Info') }}</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('Status') }}:</span>
                        <x-badge :text="$product->stock > 0 ? __('In Stock') : __('Out of Stock')" :color="$product->stock > 0 ? 'green' : 'red'" />
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('Added') }}:</span>
                        <span>{{ $product->created_at->format('M d, Y') }}</span>
                    </div>
                </div>

                @if($product->market)
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-slate-700">
                        <x-button
                            color="cyan"
                            icon="building-storefront"
                            href="{{ route('supplier.markets.show', $product->market) }}"
                            class="w-full justify-center"
                        >
                            {{ __('View Market') }}
                        </x-button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
