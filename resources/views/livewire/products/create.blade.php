<div>
    <x-modal :title="__('Create New Product')" wire x-on:open="setTimeout(() => $refs.name.focus(), 250)" size="md" blur="xl" x-on:products::create::open.window="show = true">
        <form id="product-create" wire:submit="save" class="space-y-4">
            <div>
                <x-input label="{{ __('Name') }} *" x-ref="name" wire:model="product.name" required />
            </div>

            <div>
                <x-input label="{{ __('SKU') }} *" wire:model="product.sku" required />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <x-number label="{{ __('Price') }} *" wire:model="product.price" min="0" step="0.01" required />
                <x-number label="{{ __('Stock') }} *" wire:model="product.stock" min="0" required />
            </div>

            <div>
                <x-select.styled label="{{ __('Category') }} *" wire:model="product.category_id" :options="$categories" select="label:name|value:id" searchable required />
            </div>

            <div>
                <x-select.styled label="{{ __('Market') }} *" wire:model="product.market_id" :options="$markets" select="label:name|value:id" searchable required />
            </div>
        </form>
        <x-slot:footer>
            <x-button type="submit" form="product-create">
                @lang('Save')
            </x-button>
        </x-slot:footer>
    </x-modal>
</div>
