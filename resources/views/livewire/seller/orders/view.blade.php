<x-modal wire="showDetailModal" size="4xl" blur="xl">
    @if($selectedOrder)
        <x-slot name="title">
            @lang('Order Details') - {{ $selectedOrder->order_number }}
        </x-slot>

        <div class="space-y-4">
            {{-- Order Number and Status --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        @lang('Order Number')
                    </label>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $selectedOrder->order_number }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        @lang('Status')
                    </label>
                    <x-badge
                        :text="ucfirst($selectedOrder->status)"
                        :color="match($selectedOrder->status) {
                            'processing' => 'blue',
                            'completed' => 'green',
                            'cancelled' => 'red',
                            default => 'gray'
                        }"
                    />
                </div>
            </div>

            {{-- User and Markets --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        @lang('User (Supplier)')
                    </label>
                    <x-badge text="{{ $selectedOrder->user ? $selectedOrder->user->name : '-' }}" icon="users" position="left"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        @lang('Markets')
                    </label>
                    @php
                        $markets = $selectedOrder->items->pluck('market')->unique('id')->filter();
                    @endphp
                    @if($markets->count() > 0)
                        <div class="flex flex-wrap gap-1">
                            @foreach($markets as $market)
                                <x-badge text="{{ $market->name }}" icon="building-storefront" position="left" xs/>
                            @endforeach
                        </div>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </div>
            </div>

            {{-- Order Items by Market --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    @lang('Order Items by Market')
                </label>
                @php
                    $itemsByMarket = $selectedOrder->items->groupBy('market_id');
                @endphp

                <div class="space-y-4">
                    @forelse($itemsByMarket as $marketId => $items)
                        @php
                            $market = $items->first()->market;
                            $marketTotal = $items->sum('subtotal');
                        @endphp

                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            <div class="bg-primary-50 dark:bg-primary-900/20 px-3 py-2 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <x-icon name="building-storefront" class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $market ? $market->name : 'Unknown Market' }}
                                        </span>
                                    </div>
                                    <span class="text-xs font-medium text-primary-700 dark:text-primary-300">
                                        ${{ number_format($marketTotal, 2) }}
                                    </span>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800">
                                <table class="min-w-full">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 dark:text-gray-300">
                                            @lang('Product')
                                        </th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-700 dark:text-gray-300">
                                            @lang('Qty')
                                        </th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-700 dark:text-gray-300">
                                            @lang('Price')
                                        </th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-700 dark:text-gray-300">
                                            @lang('Subtotal')
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach($items as $item)
                                        <tr>
                                            <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $item->product->name ?? 'Unknown Product' }}
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100 text-right">
                                                {{ $item->quantity }}
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100 text-right">
                                                ${{ number_format($item->unit_price, 2) }}
                                            </td>
                                            <td class="px-3 py-2 text-sm font-medium text-gray-900 dark:text-gray-100 text-right">
                                                ${{ number_format($item->subtotal, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @lang('No items found')
                            </p>
                        </div>
                    @endforelse
                </div>

                {{-- Grand Total --}}
                <div class="mt-3 bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/30 dark:to-primary-800/30 rounded-lg p-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            @lang('Grand Total')
                        </span>
                        <span class="text-lg font-bold text-primary-700 dark:text-primary-300">
                            ${{ number_format($selectedOrder->total, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Timestamps --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        @lang('Created')
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $selectedOrder->created_at->format('Y-m-d H:i:s') }}
                        <span class="text-gray-500 text-xs">({{ $selectedOrder->created_at->diffForHumans() }})</span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        @lang('Last Updated')
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $selectedOrder->updated_at->format('Y-m-d H:i:s') }}
                        <span class="text-gray-500 text-xs">({{ $selectedOrder->updated_at->diffForHumans() }})</span>
                    </p>
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <x-button text="Close" wire:click="closeDetailModal"/>
        </x-slot>
    @endif
</x-modal>
