<div class="space-y-6">

    {{-- Modern Header Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-600 via-sky-500 to-blue-500 text-white shadow-2xl shadow-sky-500/30">
        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative p-6">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('supplier.orders.index') }}" class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30 hover:bg-white/30 transition">
                        <x-icon name="arrow-left" class="w-5 h-5 text-white" />
                    </a>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight">
                            {{ __('Order Details') }}
                        </h1>
                        <p class="text-sm text-sky-100 mt-0.5">
                            {{ $order->order_number }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if($order->status === App\Models\Order::STATUS_PENDING)
                        <x-button
                            color="white"
                            icon="pencil"
                            href="{{ route('supplier.orders.edit', $order) }}"
                        >
                            {{ __('Edit') }}
                        </x-button>

                        <x-button
                            color="red"
                            icon="trash"
                            wire:click="confirmDelete"
                        >
                            {{ __('Delete') }}
                        </x-button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-sky-500/5 via-transparent to-blue-500/5 dark:from-sky-500/10 dark:to-blue-500/10"></div>

        <div class="relative p-6 space-y-6">
            {{-- Order Information --}}
            <div class="bg-gradient-to-br from-sky-50 to-blue-50 dark:from-sky-900/20 dark:to-blue-900/20 rounded-xl p-6 border-2 border-sky-200 dark:border-sky-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ __('Order Information') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Order Number') }}
                        </label>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $order->order_number }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Status') }}
                        </label>
                        <x-badge
                            :text="__(ucfirst($order->status))"
                            :color="match($order->status) {
                                'pending' => 'yellow',
                                'accepted' => 'blue',
                                'processing' => 'indigo',
                                'completed' => 'green',
                                'cancelled' => 'red',
                                'rejected' => 'red',
                                default => 'gray'
                            }"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Total Amount') }}
                        </label>
                        <p class="text-xl font-bold text-sky-600 dark:text-sky-400">
                            ${{ number_format($order->total, 2) }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Order Date') }}
                        </label>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $order->created_at->format('M d, Y') }}
                        </p>
                    </div>
                </div>

                @if($order->notes)
                    <div class="mt-4 pt-4 border-t border-sky-200 dark:border-sky-800">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Order Notes') }}
                        </label>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $order->notes }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Order Items by Market --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ __('Order Items by Market') }}
                </h3>

                @php
                    $itemsByMarket = $order->items->groupBy('market_id');
                @endphp

                <div class="space-y-4">
                    @forelse($itemsByMarket as $marketId => $items)
                        @php
                            $market = $items->first()->market;
                            $marketTotal = $items->sum('subtotal');
                        @endphp

                        <div class="border-2 border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                            <div class="bg-sky-50 dark:bg-sky-900/20 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <x-icon name="building-storefront" class="w-5 h-5 text-sky-600" />
                                        <span class="font-semibold text-gray-900 dark:text-white">
                                            {{ $market ? $market->name : __('Unknown Market') }}
                                        </span>
                                        @if($market && $market->location)
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                ({{ $market->location }})
                                            </span>
                                        @endif
                                    </div>
                                    <span class="font-semibold text-sky-600 dark:text-sky-400">
                                        ${{ number_format($marketTotal, 2) }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-4">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr class="bg-gray-50 dark:bg-gray-800">
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('Product') }}
                                            </th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('Unit Price') }}
                                            </th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('Quantity') }}
                                            </th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('Subtotal') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($items as $item)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                                <td class="px-4 py-3">
                                                    <div class="flex flex-col">
                                                        <span class="font-medium text-gray-900 dark:text-white">
                                                            {{ $item->product ? $item->product->name : __('Unknown Product') }}
                                                        </span>
                                                        @if($item->product && $item->product->sku)
                                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                                {{ __('SKU') }}: {{ $item->product->sku }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white">
                                                    ${{ number_format($item->unit_price, 2) }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <x-badge :text="$item->quantity" color="sky" />
                                                </td>
                                                <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                                                    ${{ number_format($item->subtotal, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            {{ __('No items in this order') }}
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Order Total --}}
            <div class="border-t-2 border-gray-200 dark:border-gray-700 pt-4">
                <div class="flex justify-end">
                    <div class="text-right">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Total Order Amount') }}: </span>
                        <span class="text-2xl font-bold text-sky-600 dark:text-sky-400">
                            ${{ number_format($order->total, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
