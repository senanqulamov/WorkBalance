<div class="space-y-6">

    {{-- Modern Header Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-600 via-green-500 to-emerald-500 text-white shadow-2xl shadow-green-500/30">
        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30">
                        <x-icon name="document-text" class="w-7 h-7 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight">
                            @lang('My Quotes')
                        </h1>
                        <p class="text-sm text-green-100 mt-0.5">
                            {{ __('Manage your submitted quotes and track their status') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30">
                        <div class="text-xs text-green-100">{{ __('Total Quotes') }}</div>
                        <div class="text-2xl font-bold">{{ $this->rows->total() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-green-500/5 via-transparent to-emerald-500/5 dark:from-green-500/10 dark:to-emerald-500/10"></div>

        <div class="relative p-6">
            <div class="mb-6 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Track and manage all your quote submissions.') }}
                </div>

                <div class="w-full sm:w-64">
                    <x-select.styled
                        :label="__('Filter by Status')"
                        wire:model.live="statusFilter"
                        :options="[
                            ['label' => __('All Statuses'), 'value' => null],
                            ['label' => __('Draft'), 'value' => 'draft'],
                            ['label' => __('Submitted'), 'value' => 'submitted'],
                            ['label' => __('Under Review'), 'value' => 'under_review'],
                            ['label' => __('Won'), 'value' => 'won'],
                            ['label' => __('Lost'), 'value' => 'lost'],
                            ['label' => __('Withdrawn'), 'value' => 'withdrawn']
                        ]"
                        select="label:label|value:value"
                    />
                </div>
            </div>

            <x-table :$headers :$sort :rows="$this->rows" paginate :paginator="null" filter loading :quantity="[5, 10, 20, __('all')]">
                @interact('column_id', $row)
                    <x-badge text="#{{ $row->id }}" color="gray"/>
                @endinteract

                @interact('column_request', $row)
                    <div>
                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $row->request->title }}</div>
                        <div class="text-xs text-gray-500">RFQ #{{ $row->request_id }}</div>
                    </div>
                @endinteract

                @interact('column_total_amount', $row)
                    <div class="font-semibold text-gray-900 dark:text-gray-100">
                        {{ $row->currency ?? 'USD' }} ${{ number_format($row->total_amount, 2) }}
                    </div>
                @endinteract

                @interact('column_status', $row)
                    <x-badge
                        :text="ucfirst(__(str_replace('_', ' ', $row->status)))"
                        :color="match($row->status) {
                            'draft' => 'gray',
                            'submitted' => 'blue',
                            'under_review' => 'yellow',
                            'won' => 'green',
                            'lost' => 'red',
                            'withdrawn' => 'orange',
                            default => 'gray'
                        }"
                    />
                @endinteract

                @interact('column_valid_until', $row)
                    @if($row->valid_until)
                        <div class="text-sm">
                            <div class="text-gray-900 dark:text-gray-100">{{ $row->valid_until->format('M d, Y') }}</div>
                            @if($row->valid_until->isPast())
                                <div class="text-xs text-red-500">{{ __('Expired') }}</div>
                            @else
                                <div class="text-xs text-gray-500">{{ $row->valid_until->diffForHumans() }}</div>
                            @endif
                        </div>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                @endinteract

                @interact('column_submitted_at', $row)
                    @if($row->submitted_at)
                        <div class="text-sm text-gray-900 dark:text-gray-100">
                            {{ $row->submitted_at->diffForHumans() }}
                        </div>
                    @else
                        <x-badge text="{{__('Draft')}}" color="gray"/>
                    @endif
                @endinteract

                @interact('column_action', $row)
                    <div class="flex gap-1">
                        <x-button.circle
                            icon="eye"
                            color="blue"
                            href="{{ route('supplier.rfq.show', $row->request) }}"
                            title="{{ __('View RFQ') }}"
                        />
                        @if(in_array($row->status, ['draft', 'submitted']))
                            <x-button.circle
                                icon="pencil"
                                color="purple"
                                href="{{ route('supplier.quotes.edit', $row) }}"
                                title="{{ __('Edit Quote') }}"
                            />
                        @endif
                    </div>
                @endinteract
            </x-table>
        </div>
    </div>
</div>
