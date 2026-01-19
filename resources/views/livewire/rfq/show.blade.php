<div>
    <x-card>
        {{-- Header / Title + Actions --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ $request->title }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    @lang('RFQ #:id', ['id' => $request->id])
                </p>
            </div>

            <div class="flex flex-col md:flex-row items-end md:items-center gap-3">
                <div class="flex flex-col items-end">
                    <x-badge
                        :text="ucfirst($request->status)"
                        :color="match($request->status) {
                            'open' => 'green',
                            'closed' => 'red',
                            default => 'gray'
                        }"
                    />
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        @lang('Deadline'):
                        {{ optional($request->deadline)->format('Y-m-d') ?? '—' }}
                    </div>
                </div>

                <div class="flex gap-2">
                    <x-button
                        icon="pencil"
                        color="lime"
                        wire:click="$dispatch('load::rfq', { rfq: '{{ $request->id }}' })"
                    >
                        @lang('Edit Request')
                    </x-button>

                    <x-button icon="arrow-left" href="{{ route('rfq.index') }}">
                        @lang('Back to RFQs')
                    </x-button>

                    {{--                    <x-button color="purple" icon="arrow-right" href="{{ route('rfq.quote', $request->id) }}">--}}
                    {{--                        @lang('Quote (Only Supplier)')--}}
                    {{--                    </x-button>--}}

                    @if($canQuote)
                        <x-button icon="currency-dollar" :href="route('rfq.quote', $request)">
                            @lang('Submit Quote')
                        </x-button>
                    @endif
                </div>
            </div>
        </div>

        <div class="w-70 mb-6">
            <x-select.styled
                label="{{ __('Change Status') }}"
                wire:model.live="statusValue"
                :options="collect($availableStatuses)->map(fn($label, $value) => ['label' => __($label), 'value' => $value])->values()->toArray()"
                select="label:label|value:value"
            />
        </div>

        <div class="space-y-6">
            {{-- RFQ Details --}}
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    @lang('RFQ Details')
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-xs uppercase mb-1">@lang('Buyer')</p>
                        <p class="text-gray-900 dark:text-gray-100 font-medium">
                            {{ $request->buyer?->name ?? __('Unknown') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-xs uppercase mb-1">@lang('Created at')</p>
                        <p class="text-gray-900 dark:text-gray-100 font-medium">
                            {{ $request->created_at->format('Y-m-d H:i') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-xs uppercase mb-1">@lang('Deadline')</p>
                        <p class="text-gray-900 dark:text-gray-100 font-medium">
                            {{ optional($request->deadline)->format('Y-m-d') ?? '—' }}
                        </p>
                    </div>
                </div>

                @if($request->description)
                    <div class="mt-4">
                        <p class="text-gray-500 dark:text-gray-400 text-xs uppercase mb-1">@lang('Description')</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ $request->description }}</p>
                    </div>
                @endif
            </div>

            {{-- RFQ Items --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                    @lang('Items') ({{ $request->items->count() }})
                </h3>

                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                @lang('Product')
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                @lang('Quantity')
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                @lang('Specifications')
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($request->items as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $item->product_name ?? __('Unknown product') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 text-right">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $item->specifications ?: '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                    @lang('No items found.')
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Quotes --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        @lang('Quotes') ({{ $request->quotes->count() }})
                    </h3>

                    @if($canQuote)
                        <x-button size="sm" icon="currency-dollar" :href="route('rfq.quote', $request)">
                            @lang('Submit Quote')
                        </x-button>
                    @endif
                </div>

                @if($request->quotes->isEmpty())
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            @lang('No quotes submitted yet.')
                        </p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($request->quotes as $quote)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                {{-- Quote Header --}}
                                <div class="bg-gray-100 dark:bg-gray-800 px-4 py-3 flex justify-between items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $quote->supplier?->name ?? __('Unknown Supplier') }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            @lang('Submitted'): {{ $quote->submitted_at ? $quote->submitted_at->format('M d, Y H:i') : $quote->created_at->format('M d, Y H:i') }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="text-right">
                                            <div class="text-xs text-gray-500 dark:text-gray-400">@lang('Total Amount')</div>
                                            <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $quote->currency ?? 'USD' }} ${{ number_format($quote->total_amount ?? $quote->total_price, 2) }}
                                            </div>
                                        </div>
                                        <x-badge
                                            :text="ucfirst(str_replace('_', ' ', $quote->status ?? 'submitted'))"
                                            :color="match($quote->status) {
                                                'draft' => 'gray',
                                                'submitted' => 'blue',
                                                'under_review' => 'yellow',
                                                'accepted', 'won' => 'green',
                                                'rejected', 'lost' => 'red',
                                                default => 'gray'
                                            }"
                                        />
                                    </div>
                                </div>

                                {{-- Quote Items --}}
                                @if($quote->items && $quote->items->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                                    @lang('Item')
                                                </th>
                                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                                    @lang('Qty')
                                                </th>
                                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                                    @lang('Unit Price')
                                                </th>
                                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                                    @lang('Tax %')
                                                </th>
                                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                                    @lang('Total')
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($quote->items as $item)
                                                <tr>
                                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                                        {{ $item->description }}
                                                        @if($item->notes)
                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $item->notes }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100 text-right">
                                                        {{ $item->quantity }}
                                                    </td>
                                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100 text-right">
                                                        ${{ number_format($item->unit_price, 2) }}
                                                    </td>
                                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100 text-right">
                                                        {{ number_format($item->tax_rate, 1) }}%
                                                    </td>
                                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100 text-right font-medium">
                                                        ${{ number_format($item->total, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                {{-- Quote Details --}}
                                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                                        @if($quote->valid_until)
                                            <div>
                                                <span class="text-gray-500 dark:text-gray-400">@lang('Valid Until'):</span>
                                                <span class="ml-1 text-gray-900 dark:text-gray-100">{{ $quote->valid_until->format('M d, Y') }}</span>
                                            </div>
                                        @endif
                                        @if($quote->notes)
                                            <div class="md:col-span-2">
                                                <span class="text-gray-500 dark:text-gray-400">@lang('Notes'):</span>
                                                <span class="ml-1 text-gray-900 dark:text-gray-100">{{ $quote->notes }}</span>
                                            </div>
                                        @endif
                                        @if($quote->terms_conditions)
                                            <div class="md:col-span-3">
                                                <span class="text-gray-500 dark:text-gray-400">@lang('Terms & Conditions'):</span>
                                                <div class="ml-1 text-gray-900 dark:text-gray-100 mt-1 text-sm whitespace-pre-line">{{ $quote->terms_conditions }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </x-card>

    <livewire:rfq.update @updated="$refresh"/>
</div>
