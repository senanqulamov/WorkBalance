<div>
    <x-card>
        <x-heading-title title="{{ __('Products') }}" text="" icon="shopping-cart" padding="p-5" hover="-"/>

        <div class="mb-2 mt-4 flex flex-col sm:flex-row gap-2 items-start sm:items-center">
            <livewire:products.create @created="$refresh"/>
            <x-select.styled
                :label="__('Filter by Category')"
                wire:model.live="categoryFilter"
                :options="$this->categories"
                select="label:name|value:id"
                searchable
                :placeholder="__('All Categories')"
                class="w-full"
            />
        </div>

        <x-table :$headers :$sort :rows="$this->rows" paginate :paginator="null" filter loading :quantity="[5, 10, 20, 'all']">
            @interact('column_name', $row)
            <a href="{{ route('products.show', $row) }}" class="text-blue-600 hover:underline">
                <x-badge text="{{ $row->name }}" icon="eye" position="left"/>
            </a>
            @endinteract

            @interact('column_price', $row)
            ${{ number_format($row->price, 2) }}
            @endinteract

            @interact('column_stock', $row)
            <x-badge :text="$row->stock" :color="$row->stock > 0 ? 'green' : 'red'"/>
            @endinteract

            @interact('column_category', $row)
            {{ $row->category?->name ?? '-' }}
            @endinteract

            @interact('column_market', $row)
            @if($row->market)
                <a href="{{ route('markets.show', $row->market) }}" class="text-blue-600 hover:underline">
                    <x-badge text="{{ $row->market->name }}" icon="building-storefront" position="left"/>
                </a>
            @else
                -
            @endif
            @endinteract

            @interact('column_created_at', $row)
            {{ $row->created_at->diffForHumans() }}
            @endinteract

            @interact('column_action', $row)
            <div class="flex gap-1">
                @can('edit_products')
                    <x-button.circle icon="pencil" wire:click="$dispatch('load::product', { 'product' : '{{ $row->id }}'})"/>
                @endcan
                @can('delete_products')
                    <livewire:products.delete :product="$row" :key="uniqid('', true)" @deleted="$refresh"/>
                @endcan
            </div>
            @endinteract
        </x-table>
    </x-card>

    <livewire:products.update @updated="$refresh"/>
</div>
