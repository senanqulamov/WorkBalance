<div class="space-y-6">
    {{-- Modern Header Card - 2026 Style --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 via-blue-500 to-cyan-500 text-white shadow-2xl shadow-blue-500/30">
        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30">
                        <x-icon name="shopping-cart" class="w-7 h-7 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight">
                            @lang('Order Details')
                        </h1>
                        <p class="text-sm text-blue-100 mt-0.5">
                            {{ $order->order_number }}
                        </p>
                    </div>
                </div>

                <div class="flex gap-2">
                    @can('edit_orders')
                        @if(Auth::user()->isAdmin() || $order->user_id === Auth::id())
                            <x-button
                                text="{{ __('Edit') }}"
                                icon="pencil"
                                color="white"
                                wire:click="$dispatch('load::order', { 'order' : '{{ $order->id }}'})"
                            />
                        @endif
                    @endcan
                    <x-button
                        text="{{ __('Back to List') }}"
                        icon="arrow-left"
                        href="{{ route('seller.orders.index') }}"
                        color="white"
                    />
                </div>
            </div>
        </div>
    </div>

    {{-- Order Information Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-cyan-500/5 dark:from-blue-500/10 dark:to-cyan-500/10"></div>

        <div class="relative p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center">
                    <x-icon name="information-circle" class="w-5 h-5 text-white" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    @lang('Order Information')
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl bg-gradient-to-br from-gray-50/80 to-white dark:from-slate-800/80 dark:to-slate-900 border border-gray-200/50 dark:border-slate-700/50">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
                        @lang('Order Number')
                    </label>
                    <p class="text-base font-bold text-gray-900 dark:text-gray-100">
                        {{ $order->order_number }}
                    </p>
                </div>
                <div class="p-4 rounded-xl bg-gradient-to-br from-gray-50/80 to-white dark:from-slate-800/80 dark:to-slate-900 border border-gray-200/50 dark:border-slate-700/50">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
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
                <div class="p-4 rounded-xl bg-gradient-to-br from-gray-50/80 to-white dark:from-slate-800/80 dark:to-slate-900 border border-gray-200/50 dark:border-slate-700/50">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
                        @lang('User (Supplier)')
                    </label>
                    <x-badge text="{{ $order->user ? $order->user->name : '-' }}" icon="users" position="left"/>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Items by Market --}}
    <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 via-transparent to-fuchsia-500/5 dark:from-purple-500/10 dark:to-fuchsia-500/10"></div>

        <div class="relative p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-fuchsia-500 flex items-center justify-center">
                    <x-icon name="cube" class="w-5 h-5 text-white" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    @lang('Order Items by Market')
                </h3>
            </div>

            @php
                $itemsByMarket = $order->items->groupBy('market_id');
            @endphp

            <div class="space-y-5">
                @forelse($itemsByMarket as $marketId => $items)
                    @php
                        $market = $items->first()->market;
                        $marketTotal = $items->sum('subtotal');
                    @endphp

                    <div class="group relative overflow-hidden rounded-xl border-2 border-gray-200/50 dark:border-slate-700/50 hover:border-purple-400/50 dark:hover:border-purple-500/50 transition-all duration-300">
                        {{-- Market Header --}}
                        <div class="bg-gradient-to-r from-purple-50/80 to-fuchsia-50/80 dark:from-purple-900/20 dark:to-fuchsia-900/20 px-5 py-4 border-b border-gray-200/50 dark:border-slate-700/50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('seller.markets.show', $market) }}" class="flex items-center gap-2 group-hover:scale-105 transition-transform">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-fuchsia-500 flex items-center justify-center">
                                            <x-icon name="building-storefront" class="w-4 h-4 text-white" />
                                        </div>
                                        <span class="font-bold text-gray-900 dark:text-gray-100 text-base">
                                            {{ $market ? $market->name : 'Unknown Market' }}
                                        </span>
                                    </a>
                                    @if($market && $market->location)
                                        <span class="text-xs text-gray-500 dark:text-gray-400 px-2 py-1 rounded-full bg-gray-100 dark:bg-slate-800">
                                            {{ $market->location }}
                                        </span>
                                    @endif
                                </div>
                                <div class="px-3 py-1.5 rounded-lg bg-purple-500/10 border border-purple-500/30">
                                    <span class="text-xs font-bold text-purple-600 dark:text-purple-400">
                                        @lang('Subtotal'): ${{ number_format($marketTotal, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Items Table --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                                <thead class="bg-gray-50/80 dark:bg-slate-800/80">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                        @lang('Product')
                                    </th>
                                    <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                        @lang('Quantity')
                                    </th>
                                    <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                        @lang('Unit Price')
                                    </th>
                                    <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                        @lang('Subtotal')
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-slate-700 bg-white dark:bg-slate-900">
                                @foreach($items as $item)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="px-5 py-4 text-sm">
                                            <a href="{{ route('seller.products.show', $item->product) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                                {{ $item->product->name ?? 'Unknown Product' }}
                                            </a>
                                            @if($item->product && $item->product->sku)
                                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">({{ $item->product->sku }})</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-sm text-gray-900 dark:text-gray-100 text-right font-medium">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-5 py-4 text-sm text-gray-900 dark:text-gray-100 text-right">
                                            ${{ number_format($item->unit_price, 2) }}
                                        </td>
                                        <td class="px-5 py-4 text-sm font-bold text-gray-900 dark:text-gray-100 text-right">
                                            ${{ number_format($item->subtotal, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center rounded-xl bg-gray-50/50 dark:bg-slate-800/50">
                        <x-icon name="inbox" class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            @lang('No items found')
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Grand Total --}}
            <div class="mt-6 p-5 rounded-xl bg-gradient-to-r from-emerald-50 to-cyan-50 dark:from-emerald-900/20 dark:to-cyan-900/20 border-2 border-emerald-200/50 dark:border-emerald-700/50">
                <div class="flex justify-between items-center">
                    <span class="text-base font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <x-icon name="banknotes" class="w-5 h-5 text-emerald-600" />
                        @lang('Grand Total')
                    </span>
                    <span class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-cyan-600 dark:from-emerald-400 dark:to-cyan-400 bg-clip-text text-transparent">
                        ${{ number_format($order->total, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Actions for Sellers (Accept/Reject) --}}
    @if($order->isPending())
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 backdrop-blur-xl border-2 border-amber-200/50 dark:border-amber-700/50 shadow-xl">
            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>

            <div class="relative p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center">
                        <x-icon name="exclamation-triangle" class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            {{ __('Order Awaiting Your Response') }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('This order is pending. Please review and accept or reject it.') }}
                        </p>
                    </div>
                </div>

                @if($order->notes)
                    <div class="mb-4 p-4 rounded-xl bg-white/50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
                            {{ __('Supplier Notes') }}:
                        </label>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $order->notes }}</p>
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Response Notes') }} <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        wire:model="sellerNotes"
                        rows="3"
                        class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 text-sm"
                        placeholder="{{ __('Add notes for your response (required for rejection)...') }}"
                    ></textarea>
                </div>

                <div class="flex gap-3">
                    <x-button
                        color="green"
                        icon="check-circle"
                        wire:click="confirmAccept"
                        class="flex-1 justify-center"
                    >
                        {{ __('Accept Order') }}
                    </x-button>
                    <x-button
                        color="red"
                        icon="x-circle"
                        wire:click="confirmReject"
                        class="flex-1 justify-center"
                    >
                        {{ __('Reject Order') }}
                    </x-button>
                </div>
            </div>
        </div>
    @elseif($order->seller_notes)
        <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
            <div class="relative p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center">
                        <x-icon name="chat-bubble-left-right" class="w-5 h-5 text-white" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        {{ __('Seller Response') }}
                    </h3>
                </div>
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $order->seller_notes }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Timestamps Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-500/5 via-transparent to-gray-500/5 dark:from-slate-500/10 dark:to-gray-500/10"></div>

        <div class="relative p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-600 to-gray-600 flex items-center justify-center">
                    <x-icon name="clock" class="w-5 h-5 text-white" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    @lang('Timestamps')
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-xl bg-gradient-to-br from-gray-50/80 to-white dark:from-slate-800/80 dark:to-slate-900 border border-gray-200/50 dark:border-slate-700/50">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
                        @lang('Created')
                    </label>
                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100">
                        {{ $order->created_at->format('Y-m-d H:i:s') }}
                    </p>
                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ $order->created_at->diffForHumans() }})</span>
                </div>
                <div class="p-4 rounded-xl bg-gradient-to-br from-gray-50/80 to-white dark:from-slate-800/80 dark:to-slate-900 border border-gray-200/50 dark:border-slate-700/50">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
                        @lang('Last Updated')
                    </label>
                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100">
                        {{ $order->updated_at->format('Y-m-d H:i:s') }}
                    </p>
                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ $order->updated_at->diffForHumans() }})</span>
                </div>
            </div>
        </div>
    </div>

    <livewire:orders.update @updated="$refresh"/>
</div>
