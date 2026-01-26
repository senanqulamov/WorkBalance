<div>
    <x-slide wire="modal" bottom size="2xl" blur="md">
        <x-slot name="title">{{ __('Update Product: #:id', ['id' => $product?->id]) }}</x-slot>
        <form id="product-update-{{ $product?->id }}" wire:submit="save" class="space-y-6">

            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <x-input
                    label="{{ __('Name') }}"
                    wire:model.blur="product.name"
                    required
                    hint="{{ __('Product name') }}"
                />

                <x-input
                    label="{{ __('SKU') }}"
                    wire:model.blur="product.sku"
                    required
                    hint="{{ __('Stock Keeping Unit') }}"
                />

                <x-number
                    label="{{ __('Price') }}"
                    wire:model.blur="product.price"
                    min="0"
                    step="0.01"
                    required
                    hint="{{ __('Product price') }}"
                />

                <x-number
                    label="{{ __('Stock') }}"
                    wire:model.blur="product.stock"
                    min="0"
                    required
                    hint="{{ __('Available quantity') }}"
                />

                <x-select.styled
                    label="{{ __('Market') }}"
                    wire:model="product.market_id"
                    :options="$markets"
                    select="label:name|value:id"
                    required
                    searchable
                />
            </div>

            <div>
                <x-select.styled label="{{ __('Category') }} *" wire:model="product.category_id" :options="$categories" select="label:name|value:id" searchable required />
            </div>

            <x-button
                type="submit"
                form="product-update-{{ $product?->id }}"
                color="primary"
                loading="save"
                icon="check"
            >
                {{ __('Save Changes') }}
            </x-button>
        </form>
    </x-slide>
</div>
