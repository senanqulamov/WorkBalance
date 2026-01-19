<div class="space-y-6">

    {{-- Modern Header Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-700 via-slate-600 to-gray-600 text-white shadow-2xl shadow-slate-500/30">
        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30">
                        <x-icon name="clipboard-document-list" class="w-7 h-7 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight">
                            @lang('Activity Log')
                        </h1>
                        <p class="text-sm text-slate-100 mt-0.5">
                            {{ __('Track your account activity and changes') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30">
                        <div class="text-xs text-slate-100">{{ __('Total Logs') }}</div>
                        <div class="text-2xl font-bold">{{ $this->rows->total() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-500/5 via-transparent to-gray-500/5 dark:from-slate-500/10 dark:to-gray-500/10"></div>

        <div class="relative p-6">
            <div class="mb-6 flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <x-select.styled
                        label="{{ __('Filter by Type') }}"
                        wire:model.live="typeFilter"
                        :options="collect($this->logTypes)->map(fn($type) => ['label' => ucfirst($type), 'value' => $type])->toArray()"
                        select="label:label|value:value"
                    />
                </div>
                @if($typeFilter)
                    <x-button color="red" text="{{ __('Clear Filter') }}" wire:click="clearTypeFilter" sm />
                @endif
            </div>

            <x-table :$headers :$sort :rows="$this->rows" paginate :paginator="null" filter loading :quantity="[5, 10, 20, 'all']">
                @interact('column_id', $row)
                    {{ $row->id }}
                @endinteract

                @interact('column_type', $row)
                    <x-badge
                        :text="ucfirst($row->type)"
                        :color="match($row->type) {
                            'info' => 'blue',
                            'success' => 'green',
                            'warning' => 'yellow',
                            'error' => 'red',
                            default => 'gray'
                        }"
                    />
                @endinteract

                @interact('column_action', $row)
                    <span class="text-sm font-medium">{{ $row->action }}</span>
                @endinteract

                @interact('column_message', $row)
                    <div class="text-sm text-gray-600 dark:text-gray-400 max-w-md truncate">
                        {{ $row->message ?? '-' }}
                    </div>
                @endinteract

                @interact('column_created_at', $row)
                    {{ $row->created_at->diffForHumans() }}
                @endinteract
            </x-table>
        </div>
    </div>
</div>
