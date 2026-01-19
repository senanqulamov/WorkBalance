<div class="space-y-6">

    {{-- Modern Header Card - 2026 Style --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 via-blue-500 to-cyan-500 text-white shadow-2xl shadow-blue-500/30">
        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30">
                        <x-icon name="document-text" class="w-7 h-7 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight">
                            @lang('My RFQs')
                        </h1>
                        <p class="text-sm text-blue-100 mt-0.5">
                            {{ __('Manage your requests for quotation') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30">
                        <div class="text-xs text-blue-100">{{ __('Total RFQs') }}</div>
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
            <div class="mb-6 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                @can('create_rfqs')
                    <x-button :text="__('Create New RFQ')" wire:click="$dispatch('buyer::rfq::create::open')" sm />
                @endcan

                <div class="w-full sm:w-64">
                    <x-select.styled
                        :label="__('Filter by Status')"
                        wire:model.live="statusFilter"
                        :options="collect($this->statuses)->map(fn($status) => ['label' => ucfirst(__($status)), 'value' => $status])->toArray()"
                        select="label:label|value:value"
                        :placeholder="__('All Statuses')"
                    />
                </div>
            </div>

            <x-table :$headers :$sort :rows="$this->rows" paginate :paginator="null" filter loading :quantity="[5, 10, 20, 'all']">
                @interact('column_id', $row)
                    {{ $row->id }}
                @endinteract

                @interact('column_title', $row)
                    <a href="{{ route('buyer.rfq.show', $row) }}" class="text-blue-600 hover:underline">
                        <x-badge text="{{ $row->title }}" icon="eye" position="left" />
                    </a>
                    @if($row->description)
                        <div class="text-xs text-gray-500 truncate max-w-xs mt-1">
                            {{ $row->description }}
                        </div>
                    @endif
                @endinteract

                @interact('column_deadline', $row)
                    {{ optional($row->deadline)->format('Y-m-d') ?? '—' }}
                @endinteract

                @interact('column_status', $row)
                    <x-badge
                        :color="$row->status === 'open' ? 'green' : ($row->status === 'closed' ? 'red' : 'gray')"
                        :text="ucfirst($row->status)"
                    />
                @endinteract

                @interact('column_items_count', $row)
                    <x-badge :text="$row->items_count" icon="list-bullet" />
                @endinteract

                @interact('column_quotes_count', $row)
                    <x-badge :text="$row->quotes_count" icon="document-duplicate" />
                @endinteract

                @interact('column_created_at', $row)
                    {{ $row->created_at->diffForHumans() }}
                @endinteract

                @interact('column_action', $row)
                    <div class="flex gap-1">
                        @can('edit_rfqs')
                            <x-button.circle icon="pencil" wire:click="$dispatch('buyer::load::rfq', { 'rfq' : '{{ $row->id }}'})" />
                        @endcan
                        @can('delete_rfqs')
                            <livewire:buyer.rfq.delete :rfq="$row" :key="uniqid('', true)" @deleted="$refresh" />
                        @endcan
                    </div>
                @endinteract
            </x-table>
        </div>
    </div>

    <livewire:buyer.rfq.create @created="$refresh" />
    <livewire:buyer.rfq.update @updated="$refresh" />
</div>
