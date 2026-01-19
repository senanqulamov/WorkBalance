<div class="space-y-6">

    {{-- Modern Header Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-600 via-cyan-500 to-blue-500 text-white shadow-2xl shadow-cyan-500/30">
        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30">
                        <x-icon name="building-storefront" class="w-7 h-7 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight">
                            @lang('Markets')
                        </h1>
                        <p class="text-sm text-cyan-100 mt-0.5">
                            {{ __('Browse available marketplaces') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30">
                        <div class="text-xs text-cyan-100">{{ __('Total Markets') }}</div>
                        <div class="text-2xl font-bold">{{ $this->rows->total() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/5 via-transparent to-blue-500/5 dark:from-cyan-500/10 dark:to-blue-500/10"></div>

        <div class="relative p-6">
            <div class="mb-6">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('View-only access. Browse markets and their offerings.') }}
                </div>
            </div>

            <x-table :$headers :$sort :rows="$this->rows" paginate :paginator="null" filter loading :quantity="[5, 10, 20, 'all']">
                @interact('column_id', $row)
                    {{ $row->id }}
                @endinteract

                @interact('column_name', $row)
                    <x-badge text="{{ $row->name }}" icon="building-storefront" position="left" />
                @endinteract

                @interact('column_location', $row)
                    {{ $row->location ?? '-' }}
                @endinteract

                @interact('column_seller', $row)
                    @if($row->seller)
                        <x-badge text="{{ $row->seller->name }}" icon="user" position="left" />
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                @endinteract

                @interact('column_products_count', $row)
                    <x-badge :text="$row->products_count" icon="archive-box" />
                @endinteract

                @interact('column_created_at', $row)
                    {{ $row->created_at->diffForHumans() }}
                @endinteract

                @interact('column_action', $row)
                    <x-button.circle
                        icon="eye"
                        color="cyan"
                        href="{{ route('supplier.markets.show', $row) }}"
                        title="{{ __('View Details') }}"
                    />
                @endinteract
            </x-table>
        </div>
    </div>
</div>
