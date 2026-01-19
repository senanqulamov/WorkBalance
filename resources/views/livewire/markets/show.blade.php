<div>
    <x-card>
        <x-heading-title title="{{__('Market')}}: " text="{{ $market->name }}" icon="building-storefront" padding="p-5" hover="-"/>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
            <x-stat label="{{__('Orders')}}" :value="$this->metrics['orders_count']" icon="shopping-bag"/>
            <x-stat label="{{__('Revenue')}}" :value="'$'.number_format($this->metrics['revenue'], 2)" icon="banknotes"/>
            <x-stat label="{{__('Avg Order')}}" :value="'$'.number_format($this->metrics['avg_order_value'], 2)" icon="chart-bar"/>
            <x-stat label="{{__('Products')}}" :value="$this->metrics['products_count']" icon="archive-box"/>
        </div>

        <div class="mt-6 flex gap-2">
            <x-button icon="arrow-left" href="{{ route('markets.index') }}">@lang('Markets')</x-button>
            <x-button icon="pencil" wire:click="$dispatch('load::market', { market: '{{ $market->id }}'})">{{ __('Update Market: #:id', ['id' => $market->id]) }}</x-button>
        </div>

        <div class="mt-8">
            <h2 class="text-sm font-semibold mb-2">@lang('Recent Orders')</h2>
            <x-table
                :headers="[['index'=>'order_number','label'=>__('Order Number')],['index'=>'total','label'=>__('Total')],['index'=>'status','label'=>__('Status')],['index'=>'created_at','label'=>__('Created')]]"
                :rows="$this->orders" :sort="['column'=>'created_at','direction'=>'desc']" paginate :paginator="null" loading>
                @interact('column_order_number', $row)
                <a href="{{ route('orders.show', $row) }}">
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

        <div class="mt-8">
            <h2 class="text-sm font-semibold mb-2">@lang('Products')</h2>
            @if($this->products->count())
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($this->products as $product)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 flex flex-col gap-3 bg-white dark:bg-dark-700">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $product->sku ?? __('N/A') }}</p>
                                </div>
                                <x-badge :text="$product->stock > 0 ? __('In Stock') : __('Out of Stock')" :color="$product->stock > 0 ? 'green' : 'red'"/>
                            </div>
                            <dl class="text-sm space-y-1">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500 dark:text-gray-400">@lang('Price')</dt>
                                    <dd class="font-medium text-gray-900 dark:text-gray-100">${{ number_format($product->price, 2) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500 dark:text-gray-400">@lang('Stock')</dt>
                                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $product->stock }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500 dark:text-gray-400">@lang('Created')</dt>
                                    <dd class="text-gray-700 dark:text-gray-300">{{ $product->created_at->diffForHumans() }}</dd>
                                </div>
                            </dl>
                            <x-button size="sm" icon="arrow-top-right-on-square" href="{{ route('products.show', $product) }}" class="self-start">
                                @lang('View Product')
                            </x-button>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    {{ $this->products->links() }}
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">@lang('No products available for this market yet.')</p>
            @endif
        </div>
    </x-card>

    <livewire:markets.update @updated="$refresh"/>
</div>
