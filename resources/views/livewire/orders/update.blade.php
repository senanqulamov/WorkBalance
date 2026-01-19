<div>
    <x-slide wire="modal" right size="2xl" blur="md">
        <x-slot name="title">{{ __('Update Order: #:id', ['id' => $order?->order_number]) }}</x-slot>
        <form id="order-update-{{ $order?->id }}" wire:submit="save" class="space-y-6">

            <div class="grid grid-cols-2 gap-4">
                <x-input
                    label="{{ __('Order Number') }}"
                    wire:model.blur="order.order_number"
                    required
                    hint="{{ __('Unique order number') }}"
                />

                <x-select.styled
                    label="{{ __('Status') }}"
                    wire:model="order.status"
                    :options="[
                        ['label' => 'Processing', 'value' => 'processing'],
                        ['label' => 'Completed', 'value' => 'completed'],
                        ['label' => 'Cancelled', 'value' => 'cancelled'],
                    ]"
                    select="label:label|value:value"
                    required
                />
            </div>

            <div>
                <x-select.styled
                    label="{{ __('User (Supplier)') }}"
                    wire:model="order.user_id"
                    :options="$users"
                    select="label:name|value:id"
                    required
                    searchable
                />
            </div>

            <div class="border-t pt-4">
                <div class="flex justify-between items-center mb-4">
                    <label class="block text-lg font-semibold text-gray-800 dark:text-gray-200">
                        @lang('Markets & Products')
                    </label>
                    <x-button wire:click="addItem" text="{{ __('Add Product') }}" icon="plus" color="primary" sm />
                </div>

                <div class="space-y-6">
                    @foreach($items as $index => $item)
                        <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-800" wire:key="update-item-{{ $index }}-{{ $item['market_id'] ?? 'new' }}">
                            <div class="flex flex-col space-y-3">
                                <div class="grid grid-cols-12 gap-2 items-end">
                                    <div class="col-span-4">
                                        <x-select.styled
                                            label="{{ __('Market') }}"
                                            wire:model.live="items.{{ $index }}.market_id"
                                            :options="$markets"
                                            select="label:name|value:id"
                                            required
                                            searchable
                                        />
                                    </div>

                                    <div class="col-span-4">
                                        <x-select.styled
                                            wire:key="update-product-{{ $index }}-{{ $item['market_id'] ?? 'none' }}"
                                            label="{{ __('Product') }}"
                                            wire:model.live="items.{{ $index }}.product_id"
                                            :options="$this->getProductsForMarket($item['market_id'] ?? null)"
                                            select="label:name|value:id"
                                            searchable
                                            required
                                        />
                                    </div>

                                    <div class="col-span-2">
                                        <x-input
                                            label="{{ __('Qty') }}"
                                            wire:model.live="items.{{ $index }}.quantity"
                                            type="number"
                                            min="1"
                                        />
                                    </div>

                                    <div class="col-span-2">
                                        <x-input
                                            label="{{ __('Price') }}"
                                            wire:model.live="items.{{ $index }}.unit_price"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                        />
                                    </div>
                                </div>

                                <div class="grid grid-cols-12 gap-2 items-center mt-2">
                                    <div class="col-span-10 flex justify-end">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">
                                            @lang('Subtotal'):
                                        </span>
                                        <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            ${{ number_format(($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0), 2) }}
                                        </span>
                                    </div>
                                    <div class="col-span-2 flex justify-end">
                                        @if(count($items) > 1)
                                            <x-button.circle
                                                wire:click="removeItem({{ $index }})"
                                                icon="trash"
                                                color="red"
                                                xs
                                            />
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 pt-4 border-t">
                    <div class="flex justify-end">
                        <div class="text-right">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">@lang('Total Order Amount'): </span>
                            <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                ${{ number_format($this->calculateTotal(), 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <x-button
                type="submit"
                form="order-update-{{ $order?->id }}"
                color="primary"
                loading="save"
                icon="check"
            >
                {{ __('Save Changes') }}
            </x-button>
        </form>
    </x-slide>
</div>
