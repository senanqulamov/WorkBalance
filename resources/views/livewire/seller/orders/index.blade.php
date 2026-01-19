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
                            @lang('My Orders')
                        </h1>
                        <p class="text-sm text-blue-100 mt-0.5">
                            {{ __('Track and manage your order fulfillment') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30">
                        <div class="text-xs text-blue-100">{{ __('Total Orders') }}</div>
                        <div class="text-2xl font-bold">{{ $this->rows->total() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-cyan-500/5 dark:from-blue-500/10 dark:to-cyan-500/10"></div>

        <div class="relative p-6">
            <x-table :$headers :$sort :rows="$this->rows" paginate :paginator="null" filter loading :quantity="[5, 10, 20, 'all']">
            @interact('column_id', $row)
                {{ $row->id }}
            @endinteract

            @interact('column_order_number', $row)
                <a href="{{ route('seller.orders.show', $row) }}" class="text-blue-600 hover:underline">
                    <x-badge text="{{ $row->order_number }}" icon="document-text" position="left" />
                </a>
            @endinteract

            @interact('column_markets', $row)
                @php
                    $markets = $row->items->pluck('market')->unique('id')->filter();
                @endphp
                @if($markets->count() > 0)
                    <div class="flex flex-wrap gap-1">
                        @foreach($markets as $market)
                            <x-badge text="{{ $market->name }}" icon="building-storefront" position="left" xs />
                        @endforeach
                    </div>
                @else
                    <span class="text-gray-400">-</span>
                @endif
            @endinteract

            @interact('column_total', $row)
                <span class="font-semibold">${{ number_format($row->total, 2) }}</span>
            @endinteract

            @interact('column_status', $row)
                <x-badge
                    :text="ucfirst($row->status)"
                    :color="match($row->status) {
                        'processing' => 'blue',
                        'completed' => 'green',
                        'cancelled' => 'red',
                        default => 'gray'
                    }"
                />
            @endinteract

            @interact('column_created_at', $row)
                {{ $row->created_at->diffForHumans() }}
            @endinteract

            @interact('column_action', $row)
                <div class="flex gap-1">
                    <x-button.circle icon="eye" color="primary" wire:click="$dispatch('view::order', { 'order' : '{{ $row->id }}'})" />
                </div>
            @endinteract
        </x-table>
        </div>
    </div>

    <livewire:seller.orders.view-order />
</div>
