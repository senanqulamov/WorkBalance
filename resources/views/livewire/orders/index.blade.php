<div>
    <x-card>
        <x-heading-title title="{{ __('Orders') }}" text="" icon="shopping-cart" padding="p-5" hover="-"/>

        <div class="mb-2 mt-4">
            <livewire:orders.create @created="$refresh"/>
        </div>

        <x-table :$headers :$sort :rows="$this->rows" paginate :paginator="null" filter loading :quantity="[5, 10, 20, 'all']">
            @interact('column_id', $row)
            {{ $row->id }}
            @endinteract

            @interact('column_order_number', $row)
            <a href="{{ route('orders.show', $row) }}" class="text-blue-600 hover:underline">
                <x-badge text="{{ $row->order_number }}" icon="document-text" position="left"/>
            </a>
            @endinteract

            @interact('column_user', $row)
            @if($row->user)
                <x-badge text="{{ $row->user->name }}" icon="users" position="left"/>
            @else
                <span class="text-gray-400">-</span>
            @endif
            @endinteract

            @interact('column_markets', $row)
            @php
                $markets = $row->items->pluck('market')->unique('id')->filter();
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
                <x-button.circle icon="eye" color="primary" wire:click="$dispatch('view::order', { 'order' : '{{ $row->id }}'})"/>
                @can('edit_orders')
                    <x-button.circle icon="pencil" wire:click="$dispatch('load::order', { 'order' : '{{ $row->id }}'})"/>
                @endcan
                @can('delete_orders')
                    <livewire:orders.delete :order="$row" :key="uniqid('', true)" @deleted="$refresh"/>
                @endcan
            </div>
            @endinteract
        </x-table>
    </x-card>

    <livewire:orders.update @updated="$refresh"/>
    <livewire:orders.view-order />
</div>
