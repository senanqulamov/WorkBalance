<div class="space-y-6">
    {{-- Modern Header Card - 2026 Style --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-500 to-purple-500 text-white shadow-2xl shadow-indigo-500/30">
        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative p-6">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30">
                    <x-icon name="map-pin" class="w-7 h-7 text-white" />
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight">
                        {{ $market->name }}
                    </h1>
                    <p class="text-sm text-indigo-100 mt-0.5">
                        {{ __('Market Performance & Analytics') }}
                    </p>
                </div>
            </div>

            {{-- Stats Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20">
                    <div class="flex items-center gap-2 mb-1">
                        <x-icon name="shopping-bag" class="w-4 h-4 text-white/80" />
                        <p class="text-[10px] uppercase tracking-wide text-white/70">{{ __('Orders') }}</p>
                    </div>
                    <p class="text-2xl font-bold">{{ $this->metrics['orders_count'] }}</p>
                </div>
                <div class="p-4 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20">
                    <div class="flex items-center gap-2 mb-1">
                        <x-icon name="banknotes" class="w-4 h-4 text-white/80" />
                        <p class="text-[10px] uppercase tracking-wide text-white/70">{{ __('Revenue') }}</p>
                    </div>
                    <p class="text-2xl font-bold">${{ number_format($this->metrics['revenue'], 2) }}</p>
                </div>
                <div class="p-4 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20">
                    <div class="flex items-center gap-2 mb-1">
                        <x-icon name="chart-bar" class="w-4 h-4 text-white/80" />
                        <p class="text-[10px] uppercase tracking-wide text-white/70">{{ __('Avg Order') }}</p>
                    </div>
                    <p class="text-2xl font-bold">${{ number_format($this->metrics['avg_order_value'], 2) }}</p>
                </div>
                <div class="p-4 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20">
                    <div class="flex items-center gap-2 mb-1">
                        <x-icon name="archive-box" class="w-4 h-4 text-white/80" />
                        <p class="text-[10px] uppercase tracking-wide text-white/70">{{ __('Products') }}</p>
                    </div>
                    <p class="text-2xl font-bold">{{ $this->metrics['products_count'] }}</p>
                </div>
            </div>

            <div class="mt-6 flex gap-2">
                <x-button icon="arrow-left" href="{{ route('seller.markets.index') }}" color="white">@lang('Markets')</x-button>
                <x-button icon="pencil" wire:click="$dispatch('seller::load::market', { market: '{{ $market->id }}'})" color="white">{{ __('Update Market: #:id', ['id' => $market->id]) }}</x-button>
            </div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-cyan-500/5 dark:from-blue-500/10 dark:to-cyan-500/10"></div>

        <div class="relative p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center">
                    <x-icon name="shopping-cart" class="w-5 h-5 text-white" />
                </div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">@lang('Recent Orders')</h2>
            </div>
            <x-table
                :headers="[['index'=>'order_number','label'=>__('Order Number')],['index'=>'total','label'=>__('Total')],['index'=>'status','label'=>__('Status')],['index'=>'created_at','label'=>__('Created')]]"
                :rows="$this->orders" :sort="['column'=>'created_at','direction'=>'desc']" paginate :paginator="null" loading>
                @interact('column_order_number', $row)
                <a href="{{ route('seller.orders.show', $row) }}">
                    <x-badge text="{{ $row->order_number }}" icon="eye" position="left"/>
                </a>
                @endinteract

                @interact('column_total', $row)
                ${{ number_format($row->total,2) }}
                @endinteract

                @interact('column_status', $row)
                <x-badge :text="ucfirst($row->status)"/>
                @endinteract

                @interact('column_created_at', $row)
                {{ $row->created_at->diffForHumans() }}
                @endinteract
            </x-table>
        </div>
    </div>

    {{-- Products Grid --}}
    <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 via-transparent to-fuchsia-500/5 dark:from-purple-500/10 dark:to-fuchsia-500/10"></div>

        <div class="relative p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-fuchsia-500 flex items-center justify-center">
                    <x-icon name="cube" class="w-5 h-5 text-white" />
                </div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">@lang('Products')</h2>
            </div>

            @if($this->products->count())
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($this->products as $product)
                        <div class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-gray-50/80 to-white dark:from-slate-800/80 dark:to-slate-900 border border-gray-200/50 dark:border-slate-700/50 hover:border-purple-400/50 dark:hover:border-purple-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/10">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-500/0 to-fuchsia-500/0 group-hover:from-purple-500/5 group-hover:to-fuchsia-500/5 transition-all duration-500"></div>

                            <div class="relative p-5 flex flex-col gap-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-base font-bold text-gray-900 dark:text-gray-100 mb-1">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $product->sku ?? __('N/A') }}</p>
                                    </div>
                                    <x-badge :text="$product->stock > 0 ? __('In Stock') : __('Out of Stock')" :color="$product->stock > 0 ? 'green' : 'red'" sm/>
                                </div>

                                <dl class="text-sm space-y-2">
                                    <div class="flex justify-between items-center p-2 rounded-lg bg-gray-100/50 dark:bg-slate-800/50">
                                        <dt class="text-gray-600 dark:text-gray-400 font-medium">@lang('Category')</dt>
                                        <dd class="font-bold text-gray-900 dark:text-gray-100">{{ $product->category?->name ?? __('N/A') }}</dd>
                                    </div>
                                    <div class="flex justify-between items-center p-2 rounded-lg bg-gray-100/50 dark:bg-slate-800/50">
                                        <dt class="text-gray-600 dark:text-gray-400 font-medium">@lang('Price')</dt>
                                        <dd class="font-bold text-gray-900 dark:text-gray-100">${{ number_format($product->price, 2) }}</dd>
                                    </div>
                                    <div class="flex justify-between items-center p-2 rounded-lg bg-gray-100/50 dark:bg-slate-800/50">
                                        <dt class="text-gray-600 dark:text-gray-400 font-medium">@lang('Stock')</dt>
                                        <dd class="font-bold text-gray-900 dark:text-gray-100">{{ $product->stock }}</dd>
                                    </div>
                                    <div class="flex justify-between items-center p-2 rounded-lg bg-gray-100/50 dark:bg-slate-800/50">
                                        <dt class="text-gray-600 dark:text-gray-400 font-medium">@lang('Created')</dt>
                                        <dd class="text-gray-700 dark:text-gray-300 text-xs">{{ $product->created_at->diffForHumans() }}</dd>
                                    </div>
                                </dl>

                                <x-button size="sm" icon="arrow-top-right-on-square" href="{{ route('seller.products.show', $product) }}" class="w-full group-hover:scale-105 transition-transform">
                                    @lang('View Product')
                                </x-button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $this->products->links() }}
                </div>
            @else
                <div class="p-8 text-center rounded-xl bg-gray-50/50 dark:bg-slate-800/50">
                    <x-icon name="inbox" class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                    <p class="text-gray-500 dark:text-gray-400">@lang('No products available for this market yet.')</p>
                </div>
            @endif
        </div>
    </div>

    <livewire:seller.markets.update @updated="$refresh"/>
</div>
