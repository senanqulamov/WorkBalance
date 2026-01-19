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
                        :text="__(ucfirst($request->status))"
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
                    @if($canQuote)
                        <x-button icon="currency-dollar" color="purple" :href="route('supplier.rfq.quote', $request)">
                            @lang('Submit Quote')
                        </x-button>
                    @endif

                    <x-button icon="arrow-left" href="{{ route('supplier.rfq.index') }}">
                        @lang('Back to RFQs')
                    </x-button>
                </div>
            </div>
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

            {{-- Supplier's Quote (if exists) - Highlighted --}}
            @if($supplierQuote)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">
                        @lang('Your Quote')
                    </h3>

                    <div class="border-2 border-purple-500 dark:border-purple-400 rounded-lg overflow-hidden">
                        {{-- Quote Header --}}
                        <div class="bg-purple-50 dark:bg-purple-900/20 px-4 py-3 flex justify-between items-center">
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                    {{ __('Your Submitted Quote') }}
                                    <x-badge text="{{ __('You') }}" color="purple" />
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    @lang('Submitted'): {{ $supplierQuote->submitted_at ? $supplierQuote->submitted_at->format('M d, Y H:i') : $supplierQuote->created_at->format('M d, Y H:i') }}
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-right">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">@lang('Total Amount')</div>
                                    <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $supplierQuote->currency ?? 'USD' }} ${{ number_format($supplierQuote->total_amount ?? $supplierQuote->total_price, 2) }}
                                    </div>
                                </div>
                                <x-badge
                                    :text="ucfirst(__(str_replace('_', ' ', $supplierQuote->status ?? 'submitted')))"
                                    :color="match($supplierQuote->status) {
                                        'draft' => 'gray',
                                        'submitted' => 'blue',
                                        'under_review' => 'yellow',
                                        'accepted', 'won' => 'green',
                                        'rejected', 'lost' => 'red',
                                        default => 'gray'
                                    }"
                                />
                                @if(in_array($supplierQuote->status, ['draft', 'submitted']))
                                    <x-button.circle
                                        icon="pencil"
                                        color="purple"
                                        href="{{ route('supplier.quotes.edit', $supplierQuote) }}"
                                        title="{{ __('Edit Quote') }}"
                                    />
                                @endif
                            </div>
                        </div>

                        {{-- Quote Items --}}
                        @if($supplierQuote->items && $supplierQuote->items->count() > 0)
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
                                    @foreach($supplierQuote->items as $item)
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
                        <div class="bg-purple-50 dark:bg-purple-900/20 px-4 py-3 border-t border-purple-200 dark:border-purple-700">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                                @if($supplierQuote->valid_until)
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">@lang('Valid Until'):</span>
                                        <span class="ml-1 text-gray-900 dark:text-gray-100">{{ $supplierQuote->valid_until->format('M d, Y') }}</span>
                                    </div>
                                @endif
                                @if($supplierQuote->notes)
                                    <div class="md:col-span-2">
                                        <span class="text-gray-500 dark:text-gray-400">@lang('Notes'):</span>
                                        <span class="ml-1 text-gray-900 dark:text-gray-100">{{ $supplierQuote->notes }}</span>
                                    </div>
                                @endif
                                @if($supplierQuote->terms_conditions)
                                    <div class="md:col-span-3">
                                        <span class="text-gray-500 dark:text-gray-400">@lang('Terms & Conditions'):</span>
                                        <div class="ml-1 text-gray-900 dark:text-gray-100 mt-1 text-sm whitespace-pre-line">{{ $supplierQuote->terms_conditions }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                @if($canQuote)
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 text-center">
                        <x-icon name="light-bulb" class="w-12 h-12 text-blue-500 mx-auto mb-3" />
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                            {{ __('Submit Your Quote') }}
                        </h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            {{ __('This RFQ is open for quotes. Submit your competitive pricing now.') }}
                        </p>
                        <x-button icon="currency-dollar" color="purple" :href="route('supplier.rfq.quote', $request)">
                            @lang('Submit Quote')
                        </x-button>
                    </div>
                @endif
            @endif

            {{-- All Other Quotes (from other suppliers) --}}
            @php
                $otherQuotes = $request->quotes->where('supplier_id', '!=', auth()->id());
            @endphp

            @if($otherQuotes->count() > 0)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">
                        @lang('Other Quotes') ({{ $otherQuotes->count() }})
                    </h3>

                    <div class="space-y-4">
                        @foreach($otherQuotes as $quote)
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
                                            :text="ucfirst(__(str_replace('_', ' ', $quote->status ?? 'submitted')))"
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
{{--                                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">--}}
{{--                                                    @lang('Tax %')--}}
{{--                                                </th>--}}
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
{{--                                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100 text-right">--}}
{{--                                                        {{ number_format($item->tax_rate, 1) }}%--}}
{{--                                                    </td>--}}
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
                </div>
            @endif
        </div>
    </x-card>
</div>
