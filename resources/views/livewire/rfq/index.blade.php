<div>
    <x-card>
        <div class="flex items-center justify-between gap-4 mb-4">
            <div>
                <h2 class="text-lg font-semibold text-[var(--color-text-high)]">
                    @lang('My RFQs')
                </h2>
                <p class="text-sm text-[var(--color-text-muted)]">
                    @lang('Manage your requests for quotation.')
                </p>
            </div>

            <div class="mb-2 mt-4">
                @can('create_rfqs')
                    <livewire:rfq.create @created="$refresh"/>
                @endcan
            </div>
        </div>

        <x-table
            :$headers
            :$sort
            :rows="$this->rows"
            paginate
            :paginator="null"
            filter
            loading
            :quantity="[5, 10, 20, 'all']"
        >
            @interact('column_id', $row)
            #{{ $row->id }}
            @endinteract

            @interact('column_title', $row)
            <a href="{{ route('rfq.show', $row) }}" class="text-blue-600 hover:underline">
                <x-badge text="{{ $row->title }}" icon="eye" position="left"/>
            </a>
            <div class="text-xs text-[var(--color-text-muted)] truncate max-w-xs">
                {{ $row->description }}
            </div>
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
            {{ $row->items_count }}
            @endinteract

            @interact('column_quotes_count', $row)
            {{ $row->quotes_count }}
            @endinteract

            @interact('column_created_at', $row)
            {{ $row->created_at->diffForHumans() }}
            @endinteract

            @interact('column_action', $row)
            <div class="flex gap-1">
                @can('edit_rfqs')
                    <x-button.circle icon="pencil" wire:click="$dispatch('load::rfq', { rfq: '{{ $row->id }}' })"/>
                @endcan
                @can('delete_rfqs')
                    <livewire:rfq.delete :rfq="$row" :key="uniqid('', true)" @deleted="$refresh"/>
                @endcan
            </div>
            @endinteract
        </x-table>
    </x-card>

    <livewire:rfq.update @updated="$refresh"/>
</div>
