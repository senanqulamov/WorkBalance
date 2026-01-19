<div>
    <x-slide wire="modal" right size="2xl" blur="md">
        @if($request)
            <form id="buyer-rfq-update" wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input
                        label="{{ __('Title') }} *"
                        wire:model.defer="request.title"
                        required
                    />

                    <x-date
                        label="{{ __('Deadline') }} *"
                        wire:model.defer="request.deadline"
                        required
                    />
                </div>

                <div>
                    <x-textarea
                        label="{{ __('Description') }}"
                        wire:model.defer="request.description"
                        rows="4"
                    />
                </div>

                <div class="border-t pt-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100">
                            @lang('Items') *
                        </h3>
                        <x-button
                            type="button"
                            wire:click="addItem"
                            text="{{ __('Add Item') }}"
                            icon="plus"
                            sm
                        />
                    </div>

                    <div class="space-y-4">
                        @foreach($items as $index => $item)
                            <div
                                class="border border-gray-300 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-800"
                                wire:key="buyer-rfq-update-item-{{ $index }}-{{ $item['category_id'] ?? 'new' }}"
                            >
                                {{-- Category and Product Selection --}}
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end mb-4">
                                    <div class="md:col-span-6">
                                        <x-select.styled
                                            label="{{ __('Category') }} *"
                                            wire:model.live="items.{{ $index }}.category_id"
                                            :options="$categories"
                                            select="label:name|value:id"
                                            searchable
                                            required
                                            placeholder="{{ __('Select category...') }}"
                                        />
                                    </div>

                                    <div class="md:col-span-6">
                                        <x-select.styled
                                            wire:key="product-select-{{ $index }}-{{ $item['category_id'] ?? 'none' }}"
                                            label="{{ __('Select Product') }}"
                                            wire:model.live="items.{{ $index }}.product_id"
                                            :options="$this->getProductsForCategory($item['category_id'] ?? null)"
                                            select="label:name|value:id"
                                            searchable
                                            placeholder="{{ __('Select from category...') }}"
                                        />
                                    </div>
                                </div>

                                {{-- Manual Product Name Entry --}}
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end mb-4">
                                    <div class="md:col-span-12">
                                        <x-input
                                            label="{{ __('Product Name') }} *"
                                            wire:model.live="items.{{ $index }}.product_name"
                                            placeholder="{{ __('Enter product name or select from dropdown above') }}"
                                            required
                                        />
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            {{ __('Select from category dropdown or type custom product name') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Quantity and Specifications --}}
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end mb-4">
                                    <div class="md:col-span-6">
                                        <x-number
                                            label="{{ __('Quantity') }} *"
                                            wire:model="items.{{ $index }}.quantity"
                                            min="1"
                                            required
                                        />
                                    </div>

                                    <div class="md:col-span-6">
                                        <x-input
                                            label="{{ __('Specifications / Notes') }}"
                                            wire:model="items.{{ $index }}.specifications"
                                        />
                                    </div>
                                </div>

                                {{-- Remove Button --}}
                                <div class="flex justify-end pt-2 border-t border-gray-200 dark:border-gray-700">
                                    @if(count($items) > 1)
                                        <x-button.circle
                                            type="button"
                                            wire:click="removeItem({{ $index }})"
                                            icon="trash"
                                            color="red"
                                            xs
                                        />
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </form>
            <x-slot:footer>
                <x-button type="submit" form="buyer-rfq-update">
                    @lang('Update RFQ')
                </x-button>
            </x-slot:footer>
        @endif
    </x-slide>
</div>
