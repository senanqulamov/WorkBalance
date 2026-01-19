<div class="space-y-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 via-green-600 to-lime-500 text-white shadow-2xl shadow-emerald-500/30">
        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative p-6">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30">
                        <x-icon name="users" class="w-7 h-7 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight">@lang('Workers')</h1>
                        <p class="text-sm text-emerald-100 mt-0.5">{{ __('Manage your market worker accounts') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <x-button :text="__('Create Worker')" wire:click="$dispatch('seller::workers::create::open')" sm />
                </div>
            </div>
        </div>
    </div>

    <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 via-transparent to-lime-500/5 dark:from-emerald-500/10 dark:to-lime-500/10"></div>

        <div class="relative p-6">
            <x-table
                :headers="[
                    ['index' => 'name', 'label' => __('Name')],
                    ['index' => 'email', 'label' => __('Email')],
                    ['index' => 'markets', 'label' => __('Markets')],
                    ['index' => 'created_at', 'label' => __('Created')],
                    ['index' => 'action', 'label' => '', 'sortable' => false],
                ]"
                :rows="$workers"
                paginate
                :paginator="null"
            >
                @interact('column_name', $row)
                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $row->name }}</div>
                @endinteract

                @interact('column_email', $row)
                    <div class="text-gray-600 dark:text-gray-300">{{ $row->email }}</div>
                @endinteract

                @interact('column_markets', $row)
                    <div class="flex flex-wrap gap-1">
                        @foreach($row->workerMarkets()->get(['markets.id','markets.name']) as $m)
                            <x-badge :text="$m->name" color="slate" sm />
                        @endforeach
                    </div>
                @endinteract

                @interact('column_created_at', $row)
                    <span class="text-sm text-gray-500">{{ optional($row->created_at)->diffForHumans() }}</span>
                @endinteract

                @interact('column_action', $row)
                    <div class="flex gap-1">
                        <x-button.circle icon="pencil" wire:click="$dispatch('seller::workers::load', { 'worker' : '{{ $row->id }}'})" />
                    </div>
                @endinteract
            </x-table>
        </div>
    </div>

    <livewire:seller.workers.create @created="$refresh" />
    <livewire:seller.workers.update @updated="$refresh" @deleted="$refresh" />
</div>
