<div>
    <x-card>
        <x-alert color="blue" icon="document-plus">
            @lang('Submit Quote for RFQ #:id - :title', ['id' => $invitation->request_id, 'title' => $invitation->request->title])
        </x-alert>

        <form wire:submit="save" class="mt-6 space-y-6">
            <!-- RFQ Information -->
            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">
                    <x-icon name="information-circle" class="w-5 h-5 inline" /> {{ __('RFQ Information') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">{{ __('Buyer') }}:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100 ml-2">{{ $invitation->request->buyer->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">{{ __('Deadline') }}:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100 ml-2">
                            @if($invitation->request->deadline)
                                {{ $invitation->request->deadline->format('M d, Y H:i') }}
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('Description') }}:</span>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $invitation->request->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Quote Items -->
            <div class="p-4 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    <x-icon name="shopping-cart" class="w-5 h-5 inline" /> {{ __('Quote Items') }}
                </h3>

                <div class="space-y-4">
                    @foreach($items as $index => $item)
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                <div class="md:col-span-4">
                                    <x-input
                                        label="{{ __('Description') }}"
                                        wire:model="items.{{ $index }}.description"
                                        required
                                    />
                                </div>
                                <div class="md:col-span-2">
                                    <x-number
                                        label="{{ __('Quantity') }}"
                                        wire:model.live="items.{{ $index }}.quantity"
                                        min="0.01"
                                        step="0.01"
                                        required
                                    />
                                </div>
                                <div class="md:col-span-2">
                                    <x-number
                                        label="{{ __('Unit Price') }}"
                                        wire:model.live="items.{{ $index }}.unit_price"
                                        min="0"
                                        step="0.01"
                                        required
                                    />
                                </div>
                                <div class="md:col-span-2">
                                    <x-number
                                        label="{{ __('Tax (%)') }}"
                                        wire:model.live="items.{{ $index }}.tax_rate"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                    />
                                </div>
                                <div class="md:col-span-2 flex items-end">
                                    @if(count($items) > 1)
                                        <x-button
                                            color="red"
                                            icon="trash"
                                            wire:click.prevent="removeItem({{ $index }})"
                                            class="w-full"
                                        >
                                            {{ __('Remove') }}
                                        </x-button>
                                    @endif
                                </div>
                            </div>

                            @if(isset($item['quantity']) && isset($item['unit_price']))
                                <div class="mt-2 text-right text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Subtotal') }}:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100 ml-2">
                                        ${{ number_format($item['quantity'] * $item['unit_price'] * (1 + ($item['tax_rate'] ?? 0) / 100), 2) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 flex justify-between items-center">
                    <x-button
                        color="blue"
                        icon="plus"
                        wire:click.prevent="addItem"
                        outline
                    >
                        {{ __('Add Item') }}
                    </x-button>

                    <div class="text-right">
                        <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Total') }}: ${{ number_format($this->calculateTotal(), 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quote Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    type="date"
                    label="{{ __('Valid Until') }}"
                    wire:model="valid_until"
                    hint="{{ __('Date until which this quote is valid') }}"
                    required
                />
            </div>

            <x-textarea
                label="{{ __('Notes') }}"
                wire:model="notes"
                hint="{{ __('Additional information or clarifications') }}"
                rows="3"
            />

            <x-textarea
                label="{{ __('Terms & Conditions') }}"
                wire:model="terms_conditions"
                hint="{{ __('Payment terms, delivery conditions, etc.') }}"
                rows="4"
            />

            <!-- Actions -->
            <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                <x-button
                    flat
                    color="gray"
                    icon="arrow-left"
                    onclick="window.location.href='{{ route('supplier.invitations.index') }}'"
                >
                    {{ __('Cancel') }}
                </x-button>

                <x-button
                    type="submit"
                    color="primary"
                    icon="paper-airplane"
                    loading="save"
                >
                    {{ __('Submit Quote') }}
                </x-button>
            </div>
        </form>
    </x-card>
</div>
