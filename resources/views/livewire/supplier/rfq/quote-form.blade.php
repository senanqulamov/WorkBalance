<div>

    <x-card>
        <x-slot:header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-[var(--color-text-high)]">
                        @lang('Submit Quote for :title', ['title' => $request->title])
                    </h2>
                    <p class="text-sm text-[var(--color-text-muted)]">
                        @lang('Buyer: :name', ['name' => $request->buyer?->name ?? __('Unknown')])
                    </p>
                </div>

                <div class="text-xs text-[var(--color-text-muted)] text-right">
                    <div>
                        @lang('RFQ #:id', ['id' => $request->id])
                    </div>
                    <div>
                        @lang('Deadline'): {{ optional($request->deadline)->format('Y-m-d') ?? '—' }}
                    </div>
                </div>
            </div>
        </x-slot:header>

        <form wire:submit.prevent="save" class="space-y-6">
            <!-- RFQ Information -->
            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">
                    @lang('RFQ Details')
                </h3>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $request->description }}
                </div>
            </div>

            <!-- Quote Items -->
            <div class="space-y-4">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">
                    @lang('Quote Items')
                </h3>

                @foreach($request->items as $item)
                    <div class="border border-[var(--color-border)] rounded-lg p-4 bg-[var(--color-surface-raised)]" wire:key="quote-item-{{ $item->id }}">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                            <div class="md:col-span-4">
                                <x-input
                                    label="{{ __('Description') }}"
                                    wire:model="items.{{ $item->id }}.description"
                                    required
                                    readonly
                                />
                                <div class="mt-1 text-xs text-[var(--color-text-muted)]">
                                    @lang('Original'): {{ $item->product_name ?? __('Unknown product') }}
                                </div>
                                @if($item->specifications)
                                    <div class="mt-1 text-xs text-[var(--color-text-muted)]">
                                        {{ $item->specifications }}
                                    </div>
                                @endif
                            </div>

                            <div class="md:col-span-2">
                                <x-number
                                    label="{{ __('Quantity') }}"
                                    wire:model.live="items.{{ $item->id }}.quantity"
                                    min="1"
                                    step="1"
                                    required
                                />
                            </div>

                            <div class="md:col-span-2">
                                <x-number
                                    label="{{ __('Unit Price') }}"
                                    wire:model.live="items.{{ $item->id }}.unit_price"
                                    min="0"
                                    step="0.1"
                                    required
                                />
                            </div>

                            <div class="md:col-span-2">
                                <x-number
                                    label="{{ __('Tax (%)') }}"
                                    wire:model.live="items.{{ $item->id }}.tax_rate"
                                    min="0"
                                    max="100"
                                    step="0.1"
                                />
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Item Total') }}
                                </label>
                                <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    @if(isset($items[$item->id]['unit_price']) && isset($items[$item->id]['quantity']))
                                        @php
                                            $subtotal = ($items[$item->id]['unit_price'] ?? 0) * ($items[$item->id]['quantity'] ?? 0);
                                            $tax = $subtotal * (($items[$item->id]['tax_rate'] ?? 0) / 100);
                                            $total = $subtotal + $tax;
                                        @endphp
                                        ${{ number_format($total, 2) }}
                                    @else
                                        $0.00
                                    @endif
                                </div>
                            </div>

                            <div class="md:col-span-12">
                                <x-textarea
                                    label="{{ __('Item Notes (optional)') }}"
                                    wire:model="items.{{ $item->id }}.notes"
                                    rows="2"
                                />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Quote Total -->
            <div class="flex justify-end p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="text-right">
                    <div class="text-sm text-gray-600 dark:text-gray-400">@lang('Total Quote Amount')</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $currency }} ${{ number_format($this->calculateTotal(), 2) }}
                    </div>
                </div>
            </div>

            <!-- Quote Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-date
                    label="{{ __('Valid Until') }}"
                    wire:model="valid_until"
                    hint="{{ __('Date until which this quote is valid') }}"
                    required
                />

                <x-select.styled
                    label="{{ __('Currency') }}"
                    wire:model="currency"
                    :options="[
                        ['label' => 'USD - US Dollar', 'value' => 'USD'],
                        ['label' => 'EUR - Euro', 'value' => 'EUR'],
                        ['label' => 'GBP - British Pound', 'value' => 'GBP'],
                        ['label' => 'JPY - Japanese Yen', 'value' => 'JPY'],
                        ['label' => 'CNY - Chinese Yuan', 'value' => 'CNY']
                    ]"
                    select="label:label|value:value"
                />
            </div>

            <x-textarea
                label="{{ __('Notes (optional)') }}"
                wire:model="notes"
                hint="{{ __('Additional information or clarifications') }}"
                rows="3"
            />

            <x-textarea
                label="{{ __('Terms & Conditions (optional)') }}"
                wire:model="terms_conditions"
                hint="{{ __('Payment terms, delivery conditions, etc.') }}"
                rows="4"
            />

            <div class="flex justify-between items-center pt-4 border-t mt-4">
                <x-button
                    flat
                    color="gray"
                    href="{{ route('supplier.rfq.show', $request) }}"
                >
                    @lang('Cancel')
                </x-button>

                <x-button type="submit" color="primary">
                    @lang('Submit Quote')
                </x-button>
            </div>
        </form>
    </x-card>
</div>
