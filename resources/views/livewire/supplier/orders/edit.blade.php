<div class="space-y-6">

    {{-- Modern Header Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-600 via-amber-500 to-orange-500 text-white shadow-2xl shadow-amber-500/30">
        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative p-6">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('supplier.orders.show', $order) }}" class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30 hover:bg-white/30 transition">
                        <x-icon name="arrow-left" class="w-5 h-5 text-white" />
                    </a>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight">
                            {{ __('Edit Order') }}
                        </h1>
                        <p class="text-sm text-amber-100 mt-0.5">
                            {{ $order->order_number }}
                        </p>
                    </div>
                </div>

                @if(count($items) > 0 && $this->calculateTotal() > 0)
                    <div class="px-4 py-2 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30">
                        <div class="text-xs text-amber-100">{{ __('Order Total') }}</div>
                        <div class="text-2xl font-bold">${{ number_format($this->calculateTotal(), 2) }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 via-transparent to-orange-500/5 dark:from-amber-500/10 dark:to-orange-500/10"></div>

        <div class="relative p-6">
            <form wire:submit="update" class="space-y-6">
                {{-- Order Items Section --}}
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <label class="block text-lg font-semibold text-gray-800 dark:text-gray-200">
                            {{ __('Order Items') }} *
                        </label>
                        <x-button wire:click="addItem" icon="plus" color="amber" sm>
                            {{ __('Add Product') }}
                        </x-button>
                    </div>

                    <div class="space-y-4">
                        @foreach($items as $index => $item)
                            <div class="border-2 border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 hover:border-amber-300 dark:hover:border-amber-700 transition-all" wire:key="item-{{ $index }}-{{ $item['market_id'] ?? 'new' }}">
                                <div class="flex flex-col space-y-3">
                                    <div class="grid grid-cols-12 gap-3 items-end">
                                        <div class="col-span-12 md:col-span-4">
                                            <x-select.styled
                                                label="{{ __('Market') }} *"
                                                wire:model.live="items.{{ $index }}.market_id"
                                                :options="$markets"
                                                select="label:name|value:id"
                                                searchable
                                                required
                                            />
                                        </div>

                                        <div class="col-span-12 md:col-span-4">
                                            <x-select.styled
                                                wire:key="product-select-{{ $index }}-{{ $item['market_id'] ?? 'none' }}"
                                                label="{{ __('Product') }} *"
                                                wire:model.live="items.{{ $index }}.product_id"
                                                :options="$this->getProductsForMarket($item['market_id'] ?? null)"
                                                select="label:name|value:id"
                                                searchable
                                                required
                                            />
                                        </div>

                                        <div class="col-span-6 md:col-span-2">
                                            <x-input
                                                label="{{ __('Quantity') }}"
                                                wire:model.live="items.{{ $index }}.quantity"
                                                type="number"
                                                min="1"
                                                max="{{ $item['max_stock'] ?? '' }}"
                                            />
                                        </div>

                                        <div class="col-span-6 md:col-span-2">
                                            <x-input
                                                label="{{ __('Price') }}"
                                                wire:model.live="items.{{ $index }}.unit_price"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                readonly
                                            />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-12 gap-3 items-center pt-2 border-t border-gray-200 dark:border-gray-700">
                                        <div class="col-span-10 flex justify-end items-center gap-2">
                                            @if(isset($item['max_stock']) && $item['max_stock'] > 0)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ __('Available') }}: {{ $item['max_stock'] }}
                                                </span>
                                            @endif
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('Subtotal') }}:
                                            </span>
                                            <span class="text-lg font-semibold text-amber-600 dark:text-amber-400">
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

                    @error('items')
                        <div class="mt-2 text-sm text-red-600 dark:text-red-400">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Order Summary --}}
                <div class="border-t-2 border-gray-200 dark:border-gray-700 pt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Notes Section --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Order Notes') }} ({{ __('Optional') }})
                            </label>
                            <textarea
                                wire:model="notes"
                                rows="4"
                                maxlength="1000"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm focus:border-amber-500 focus:ring-amber-500"
                                placeholder="{{ __('Add any special instructions or notes for the seller...') }}"
                            ></textarea>
                            @error('notes')
                                <div class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Total Summary --}}
                        <div>
                            <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl p-6 border-2 border-amber-200 dark:border-amber-800">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    {{ __('Order Summary') }}
                                </h3>

                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                        <span>{{ __('Total Items') }}:</span>
                                        <span class="font-medium">{{ count($items) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                        <span>{{ __('Total Products') }}:</span>
                                        <span class="font-medium">{{ array_sum(array_column($items, 'quantity')) }}</span>
                                    </div>
                                    <div class="border-t border-amber-200 dark:border-amber-800 pt-2 mt-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Total Amount') }}:</span>
                                            <span class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                                                ${{ number_format($this->calculateTotal(), 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <x-button
                        color="slate"
                        icon="x-mark"
                        href="{{ route('supplier.orders.show', $order) }}"
                    >
                        {{ __('Cancel') }}
                    </x-button>

                    <x-button
                        type="submit"
                        color="amber"
                        icon="check"
                        wire:confirm="{{ __('Are you sure you want to update this order?') }}"
                    >
                        {{ __('Update Order') }}
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
