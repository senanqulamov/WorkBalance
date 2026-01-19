<div class="space-y-6">

    {{-- Modern Header Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 via-blue-500 to-cyan-500 text-white shadow-2xl shadow-blue-500/30">
        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30">
                        <x-icon name="envelope" class="w-7 h-7 text-white"/>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight">
                            {{__('RFQ Invitations')}}
                        </h1>
                        <p class="text-sm text-blue-100 mt-0.5">
                            {{ __('Review and respond to RFQ invitations from buyers') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="px-4 py-2 rounded-xl bg-white/20 backdrop-blur-sm border border-white/30">
                        <div class="text-xs text-blue-100">{{ __('Total Invitations') }}</div>
                        <div class="text-2xl font-bold">{{ $this->rows->total() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-cyan-500/5 dark:from-blue-500/10 dark:to-cyan-500/10"></div>

        <div class="relative p-6">
            <div class="mb-6 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Accept invitations and submit competitive quotes.') }}
                </div>

                <div class="w-full sm:w-64">
                    <x-select.styled
                        :label="__('Filter by Status')"
                        wire:model.live="statusFilter"
                        :options="[
                            ['label' => __('All Statuses'), 'value' => null],
                            ['label' => __('Pending'), 'value' => 'pending'],
                            ['label' => __('Accepted'), 'value' => 'accepted'],
                            ['label' => __('Declined'), 'value' => 'declined'],
                            ['label' => __('Quoted'), 'value' => 'quoted']
                        ]"
                        select="label:label|value:value"
                    />
                </div>
            </div>

            <x-table :$headers :$sort :rows="$this->rows" paginate :paginator="null" filter loading :quantity="[5, 10, 20, 'all']">
                @interact('column_id', $row)
                <x-badge text="#{{ $row->id }}" color="gray"/>
                @endinteract

                @interact('column_request_id', $row)
                <x-badge text="RFQ-{{ $row->request_id }}" color="blue"/>
                @endinteract

                @interact('column_title', $row)
                <div>
                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $row->request->title }}</div>
                    @if($row->request->description)
                        <div class="text-xs text-gray-500 mt-1">{{ Str::limit($row->request->description, 50) }}</div>
                    @endif
                </div>
                @endinteract

                @interact('column_status', $row)
                <x-badge
                    :text="ucfirst($row->status)"
                    :color="match($row->status) {
                            'pending' => 'yellow',
                            'accepted' => 'green',
                            'declined' => 'red',
                            'quoted' => 'blue',
                            default => 'gray'
                        }"
                />
                @endinteract

                @interact('column_deadline', $row)
                @if($row->request->deadline)
                    <div class="text-sm">
                        <div class="text-gray-900 dark:text-gray-100">{{ $row->request->deadline->format('M d, Y') }}</div>
                        @if($row->request->deadline->isPast())
                            <div class="text-xs text-red-500">{{ __('Expired') }}</div>
                        @else
                            <div class="text-xs text-gray-500">{{ $row->request->deadline->diffForHumans() }}</div>
                        @endif
                    </div>
                @else
                    <span class="text-gray-400">—</span>
                @endif
                @endinteract

                @interact('column_invited_at', $row)
                <div class="text-sm text-gray-900 dark:text-gray-100">
                    {{ $row->created_at->diffForHumans() }}
                </div>
                @endinteract

                @interact('column_action', $row)
                <div class="flex gap-1">
                    @if($row->status === 'pending')
                        <x-button.circle
                            icon="check"
                            color="green"
                            wire:click="acceptInvitation({{ $row->id }})"
                            title="{{ __('Accept Invitation') }}"
                        />
                        <x-button.circle
                            icon="x-mark"
                            color="red"
                            wire:click="declineInvitation({{ $row->id }})"
                            title="{{ __('Decline Invitation') }}"
                        />
                    @endif
                    @if($row->status === 'accepted' || $row->status === 'pending')
                        <x-button.circle
                            icon="document-plus"
                            color="purple"
                            href="{{ route('supplier.rfq.quote', $row->request) }}"
                            title="{{ __('Submit Quote') }}"
                        />
                    @endif
                    <x-button.circle
                        icon="eye"
                        color="blue"
                        href="{{ route('supplier.rfq.show', $row->request) }}"
                        title="{{ __('View Details') }}"
                    />
                </div>
                @endinteract
            </x-table>
        </div>
    </div>
</div>
