<div>
    <x-card>
        <x-heading-title title="{{ __('Markets') }}" text="" icon="map-pin" padding="p-5" hover="-"/>

        <div class="mb-2 mt-4">
            @can('create_markets')
                <livewire:markets.create @created="$refresh"/>
            @endcan
        </div>

        <x-table :$headers :$sort :rows="$this->rows" paginate :paginator="null" filter loading :quantity="[5, 10, 20, 'all']">
            @interact('column_id', $row)
            {{ $row->id }}
            @endinteract

            @interact('column_name', $row)
            <a href="{{ route('markets.show', $row) }}" class="text-blue-600 hover:underline">
                <x-badge text="{{ $row->name }}" icon="building-storefront" position="left"/>
            </a>
            @endinteract

            @interact('column_location', $row)
            {{ $row->location ?? '-' }}
            @endinteract

            @interact('column_owner', $row)
            @if($row->seller)
                <x-badge color="green" :text="$row->seller->name" icon="user" position="left" sm />
            @else
                <span class="text-gray-400">-</span>
            @endif
            @endinteract

            @interact('column_created_at', $row)
            {{ $row->created_at->diffForHumans() }}
            @endinteract

            @interact('column_products_count', $row)
            <x-badge :text="$row->products_count" icon="archive-box" />
            @endinteract

            @interact('column_action', $row)
            <div class="flex gap-1">
                @can('edit_markets')
                    <x-button.circle icon="pencil" wire:click="$dispatch('load::market', { 'market' : '{{ $row->id }}'})"/>
                @endcan
                @can('delete_markets')
                    <livewire:markets.delete :market="$row" :key="uniqid('', true)" @deleted="$refresh"/>
                @endcan
            </div>
            @endinteract
        </x-table>
    </x-card>

    <livewire:markets.update @updated="$refresh"/>
</div>
