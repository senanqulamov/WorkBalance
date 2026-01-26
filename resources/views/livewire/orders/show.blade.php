<div>
    <x-card>
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        @lang('Order Details')
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $order->order_number }}
                    </p>
                </div>
                <div class="flex gap-2">
                    @can('edit_orders')
                        @if(Auth::user()->isAdmin() || $order->user_id === Auth::id())
                            <x-button
                                text="{{ __('Edit') }}"
                                icon="pencil"
                                color="lime"
                                wire:click="$dispatch('load::order', { 'order' : '{{ $order->id }}'})"
                            />
                        @endif
                    @endcan
                    <x-button
                        text="{{ __('Back to List') }}"
                        icon="arrow-left"
                        href="{{ route('orders.index') }}"
                    />
                </div>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Order Information --}}
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    @lang('Order Information')
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            @lang('Order Number')
                        </label>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $order->order_number }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            @lang('Status')
                        </label>
                        <x-badge
                            :text="ucfirst($order->status)"
                            :color="match($order->status) {
                                'processing' => 'blue',
                                'completed' => 'green',
                                'cancelled' => 'red',
                                default => 'gray'
                            }"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            @lang('User (Supplier)')
                        </label>
                        <x-badge text="{{ $order->user ? $order->user->name : '-' }}" icon="users" position="left"/>
                    </div>
                </div>
            </div>

            {{-- Order Items by Market --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    @lang('Order Items by Market')
                </h3>
                @php
                    $itemsByMarket = $order->items->groupBy('market_id');
                @endphp

                <div class="space-y-6">
                    @forelse($itemsByMarket as $marketId => $items)
                        @php
                            $market = $items->first()->market;
                            $marketTotal = $items->sum('subtotal');
                        @endphp

                        <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            <div class="bg-primary-50 dark:bg-primary-900/20 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('markets.show', $market) }}">
                                            <x-heading-title title="{{__('Market')}}: " text="{{ $market->name }}" icon="building-storefront" padding="pl-3 pr-3 pt-1 pb-1"/>
                                        </a>
                                        @if($market && $market->location)
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                ({{ $market->location }})
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-sm font-medium text-primary-700 dark:text-primary-300">
                                        @lang('Subtotal'): ${{ number_format($marketTotal, 2) }}
                                    </span>
                                </div>
                            </div>

                            <div class="">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                            @lang('Product')
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                            @lang('Quantity')
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                            @lang('Unit Price')
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                            @lang('Subtotal')
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($items as $item)
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $item->product->name ?? 'Unknown Product' }}
                                                @if($item->product && $item->product->sku)
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ $item->product->sku }})</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 text-right">
                                                {{ $item->quantity }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 text-right">
                                                ${{ number_format($item->unit_price, 2) }}
                                            </td>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 text-right">
                                                ${{ number_format($item->subtotal, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @lang('No items found')
                            </p>
                        </div>
                    @endforelse
                </div>

                {{-- Grand Total --}}
                <div class="mt-6 bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/30 dark:to-primary-800/30 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            @lang('Grand Total')
                        </span>
                        <span class="text-2xl font-bold text-primary-700 dark:text-primary-300">
                            ${{ number_format($order->total, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Timestamps --}}
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    @lang('Timestamps')
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            @lang('Created')
                        </label>
                        <p class="text-sm text-gray-900 dark:text-gray-100">
                            {{ $order->created_at->format('Y-m-d H:i:s') }}
                            <span class="text-gray-500 text-xs">({{ $order->created_at->diffForHumans() }})</span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            @lang('Last Updated')
                        </label>
                        <p class="text-sm text-gray-900 dark:text-gray-100">
                            {{ $order->updated_at->format('Y-m-d H:i:s') }}
                            <span class="text-gray-500 text-xs">({{ $order->updated_at->diffForHumans() }})</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </x-card>

    <livewire:orders.update @updated="$refresh"/>
</div>
