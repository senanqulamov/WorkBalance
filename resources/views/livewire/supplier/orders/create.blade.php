<div class="space-y-6">

    {{-- Modern Header Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-green-500 text-white shadow-2xl shadow-emerald-500/30">
        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative p-6">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('supplier.orders.index') }}" class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30 hover:bg-white/30 transition">
                        <x-icon name="arrow-left" class="w-5 h-5 text-white" />
                    </a>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight">
                            {{ __('Create New Order') }}
                        </h1>
                        <p class="text-sm text-emerald-100 mt-0.5">
                            {{ __('Select products from markets to place an order') }}
                        </p>
                    </div>
                </div>

                @if(count($items) > 0 && $this->calculateTotal() > 0)
                    <div class="px-4 py-2 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30">
                        <div class="text-xs text-emerald-100">{{ __('Order Total') }}</div>
                        <div class="text-2xl font-bold">${{ number_format($this->calculateTotal(), 2) }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 via-transparent to-green-500/5 dark:from-emerald-500/10 dark:to-green-500/10"></div>

        <div class="relative p-6">
            <div class="space-y-6">
                {{-- Product Search & Comparison Section --}}
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border-2 border-blue-200 dark:border-blue-800 shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-600 dark:bg-blue-500 flex items-center justify-center">
                                <x-icon name="magnifying-glass" class="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ __('Find & Compare Products') }}
                                </h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ __('Compare prices across all markets') }}
                                </p>
                            </div>
                        </div>
                        @if($showSearchResults)
                            <x-badge
                                :text="count($searchResults) . ' ' . __('results')"
                                color="blue"
                                icon="check-circle"
                                position="left"
                            />
                        @endif
                    </div>

                    <div class="flex gap-2 mb-2">
                        <div class="flex-1">
                            <x-input
                                wire:model.live.debounce.500ms="searchQuery"
                                placeholder="{{ __('Type product name, SKU, or category (min 2 chars)...') }}"
                                icon="magnifying-glass"
                            />
                        </div>
                        @if($searchQuery)
                            <x-button
                                wire:click="clearSearch"
                                color="red"
                                icon="x-mark"
                            >
                                {{ __('Clear') }}
                            </x-button>
                        @endif
                    </div>

                    @if($searchQuery && strlen($searchQuery) < 2)
                        <div class="mt-2">
                            <x-badge text="{{ __('Type at least 2 characters to search') }}" color="yellow" icon="exclamation-triangle" position="left" sm />
                        </div>
                    @endif

                    {{-- Loading State in Results Area --}}
                    <div wire:loading wire:target="searchQuery" class="mt-4 animate-fade-in w-full">
                        <div class="flex items-center justify-center py-12 bg-white/80 dark:bg-slate-800/80 rounded-xl border-2 border-dashed border-blue-300 dark:border-blue-700 backdrop-blur-sm">
                            <div class="text-center">
                                <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-base font-semibold text-gray-900 dark:text-white mb-1">{{ __('Searching products...') }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Comparing prices and stock across markets') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($showSearchResults && count($searchResults) > 0)
                        <div class="mt-4 space-y-4 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($searchResults as $index => $group)
                                <div class="bg-white dark:bg-slate-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 hover:border-blue-400 dark:hover:border-blue-600">
                                    {{-- Product Group Header --}}
                                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-slate-700 dark:to-slate-600 px-5 py-4 border-b-2 border-gray-200 dark:border-gray-600">
                                        <div class="flex items-center justify-between flex-wrap gap-3">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">
                                                        {{ $index + 1 }}
                                                    </span>
                                                    <h4 class="font-bold text-gray-900 dark:text-white text-base">
                                                        {{ $group['name'] }}
                                                    </h4>
                                                </div>
                                                <div class="flex flex-wrap gap-3 text-xs">
                                                    <x-badge
                                                        :text="$group['markets_count'] . ' ' . __('markets')"
                                                        color="blue"
                                                        icon="building-storefront"
                                                        position="left"
                                                        sm
                                                    />
                                                    <x-badge
                                                        :text="$group['total_stock'] . ' ' . __('in stock')"
                                                        color="green"
                                                        icon="cube"
                                                        position="left"
                                                        sm
                                                    />
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('Price Range') }}</div>
                                                <div class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                                    ${{ number_format($group['min_price'], 2) }} - ${{ number_format($group['max_price'], 2) }}
                                                </div>
                                                @if($group['min_price'] != $group['max_price'])
                                                    <div class="text-xs text-green-600 dark:text-green-400 font-semibold">
                                                        {{ __('Save up to') }} ${{ number_format($group['max_price'] - $group['min_price'], 2) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Product Comparison Table --}}
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-slate-700/50">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                        <x-icon name="building-storefront" class="w-4 h-4 inline mr-1" />
                                                        {{ __('Market') }}
                                                    </th>
                                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                        <x-icon name="user" class="w-4 h-4 inline mr-1" />
                                                        {{ __('Seller') }}
                                                    </th>
                                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                        <x-icon name="tag" class="w-4 h-4 inline mr-1" />
                                                        {{ __('Category') }}
                                                    </th>
                                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                        <x-icon name="cube" class="w-4 h-4 inline mr-1" />
                                                        {{ __('Stock') }}
                                                    </th>
                                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                        <x-icon name="currency-dollar" class="w-4 h-4 inline mr-1" />
                                                        {{ __('Price') }}
                                                    </th>
                                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                        {{ __('Action') }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach($group['products'] as $product)
                                                    <tr class="hover:bg-blue-50 dark:hover:bg-slate-700/50 transition-colors duration-150 {{ $product['price'] == $group['min_price'] ? 'bg-green-50/50 dark:bg-green-900/10' : '' }}">
                                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $product['market_name'] }}
                                                        </td>
                                                        <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                                                            {{ $product['seller_name'] }}
                                                        </td>
                                                        <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                                                            {{ $product['category'] }}
                                                        </td>
                                                        <td class="px-4 py-4 text-center">
                                                            <x-badge
                                                                :text="(string)$product['stock']"
                                                                :color="$product['stock'] > 10 ? 'green' : ($product['stock'] > 5 ? 'yellow' : 'red')"
                                                                sm
                                                            />
                                                        </td>
                                                        <td class="px-4 py-4 text-right">
                                                            <div class="flex items-center justify-end gap-2">
                                                                <span class="text-lg font-bold text-gray-900 dark:text-white">
                                                                    ${{ number_format($product['price'], 2) }}
                                                                </span>
                                                                @if($product['price'] == $group['min_price'])
                                                                    <x-badge text="{{ __('Best Price') }}" color="green" icon="check-badge" position="left" sm />
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-4 text-center">
                                                            <x-button
                                                                wire:click="addProductFromSearch({{ $product['id'] }})"
                                                                color="blue"
                                                                icon="plus"
                                                                xs
                                                            >
                                                                {{ __('Add to Order') }}
                                                            </x-button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif($showSearchResults && count($searchResults) === 0)
                        <div class="mt-4 text-center py-12 bg-white dark:bg-slate-800 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700">
                            <x-icon name="magnifying-glass-minus" class="w-16 h-16 mx-auto mb-3 text-gray-400" />
                            <p class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('No products found') }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Try searching with different keywords') }}</p>
                        </div>
                    @endif
                </div>

                {{-- Order Items Section --}}
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <label class="block text-lg font-semibold text-gray-800 dark:text-gray-200">
                            {{ __('Order Items') }} *
                        </label>
                        <x-button wire:click="addItem" icon="plus" color="emerald" sm>
                            {{ __('Add Product') }}
                        </x-button>
                    </div>

                    <div class="space-y-4">
                        @foreach($items as $index => $item)
                            <div class="border-2 border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 hover:border-emerald-300 dark:hover:border-emerald-700 transition-all" wire:key="item-{{ $index }}-{{ $item['market_id'] ?? 'new' }}">
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
                                                    <x-badge color="primary" text="{{ __('Available') }}: {{ $item['max_stock'] }}" icon="shopping-cart" position="left" />
                                                </span>
                                            @endif
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('Subtotal') }}:
                                            </span>
                                            <span class="text-lg font-semibold text-emerald-600 dark:text-emerald-400">
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
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm focus:border-emerald-500 focus:ring-emerald-500"
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
                            <div class="bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 rounded-xl p-6 border-2 border-emerald-200 dark:border-emerald-800">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    {{ __('Order Summary') }}
                                </h3>

                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                        <span>{{ __('Total Items') }}:</span>
                                        <span class="font-medium">{{ count($items) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                        <span>{{ __('Total Count') }}:</span>
                                        <span class="font-medium">{{ array_sum(array_column($items, 'quantity')) }}</span>
                                    </div>
                                    <div class="border-t border-emerald-200 dark:border-emerald-800 pt-2 mt-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Total Amount') }}:</span>
                                            <span class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
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
                        href="{{ route('supplier.orders.index') }}"
                    >
                        {{ __('Cancel') }}
                    </x-button>

                    <x-button
                        wire:click="confirmSave"
                        color="emerald"
                        icon="check"
                    >
                        {{ __('Place Order') }}
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgb(241 245 249 / 0.5);
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgb(59 130 246 / 0.5);
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgb(59 130 246 / 0.8);
        }
        .dark .custom-scrollbar::-webkit-scrollbar-track {
            background: rgb(30 41 59 / 0.5);
        }
    </style>
</div>
