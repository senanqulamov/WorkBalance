<div>
    <x-card>
        {{-- Header Section --}}
        <div class="mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ __('RFQ Monitoring') }}</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Real-time overview and workflow tracking') }}</p>
                </div>

                <div class="flex items-center gap-3">
                    <x-badge color="primary" dark>
                        <svg class="w-3 h-3 mr-1.5 animate-pulse" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3" />
                        </svg>
                        {{ __('Live Data') }}
                    </x-badge>

                    @can('create_rfqs')
                        <livewire:monitoring.rfq.create @created="$refresh"/>
                    @endcan
                </div>
            </div>
        </div>

        {{-- KPI Dashboard --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide">{{ __('Open') }}</p>
                        <p class="text-2xl font-bold text-blue-900 dark:text-blue-100 mt-1">{{ $metrics['open'] }}</p>
                    </div>
                    <div class="bg-blue-200 dark:bg-blue-800 rounded-lg p-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900/20 dark:to-slate-800/20 rounded-xl p-4 border border-slate-200 dark:border-slate-800 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-600 dark:text-slate-400 uppercase tracking-wide">{{ __('Draft') }}</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-slate-100 mt-1">{{ $metrics['draft'] }}</p>
                    </div>
                    <div class="bg-slate-200 dark:bg-slate-800 rounded-lg p-3">
                        <svg class="w-6 h-6 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 rounded-xl p-4 border border-emerald-200 dark:border-emerald-800 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">{{ __('Closed') }}</p>
                        <p class="text-2xl font-bold text-emerald-900 dark:text-emerald-100 mt-1">{{ $metrics['closed'] }}</p>
                    </div>
                    <div class="bg-emerald-200 dark:bg-emerald-800 rounded-lg p-3">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 rounded-xl p-4 border border-amber-200 dark:border-amber-800 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wide">{{ __('Due in 3 days') }}</p>
                        <p class="text-2xl font-bold text-amber-900 dark:text-amber-100 mt-1">{{ $metrics['due_3_days'] }}</p>
                    </div>
                    <div class="bg-amber-200 dark:bg-amber-800 rounded-lg p-3">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-xl p-4 border border-red-200 dark:border-red-800 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-red-600 dark:text-red-400 uppercase tracking-wide">{{ __('Overdue') }}</p>
                        <p class="text-2xl font-bold text-red-900 dark:text-red-100 mt-1">{{ $metrics['overdue'] }}</p>
                    </div>
                    <div class="bg-red-200 dark:bg-red-800 rounded-lg p-3">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Workflow Events Quick Stats --}}
        <div class="mb-6">
            <div class="bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/10 dark:to-indigo-900/10 rounded-xl p-5 border border-purple-200 dark:border-purple-800">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="bg-purple-600 dark:bg-purple-500 rounded-lg p-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            {{ __('Workflow Activity') }}
                        </h3>
                    </div>
                    <x-badge color="purple" text="{{ __('Real-time Tracking') }}" icon="bolt" position="left" />
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    {{ __('Track all RFQ activities, status changes, supplier interactions, and system events in real-time. Click the timeline icon in any RFQ row to view detailed workflow events.') }}
                </p>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-purple-100 dark:border-purple-900">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('Status Changes') }}</span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-purple-100 dark:border-purple-900">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('Supplier Invites') }}</span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-purple-100 dark:border-purple-900">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('Quote Submissions') }}</span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-purple-100 dark:border-purple-900">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('SLA Reminders') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions / RFQ controls: reuse existing rfq components for create/edit/delete --}}
        <div class="mb-6">
            <div class="flex items-center justify-end gap-3">
                @can('create_rfqs')
                    <livewire:monitoring.rfq.create @created="$refresh"/>
                @endcan
            </div>
        </div>

        <x-table :$headers :$sort :rows="$this->rows" paginate :paginator="null" filter loading :quantity="[5, 10, 20, 'all']">
            @interact('column_id', $row)
            {{ $row->id }}
            @endinteract

            @interact('column_title', $row)
            <a href="{{ route('monitoring.rfq.show', $row) }}" class="text-blue-600 hover:underline">
                <x-badge text="{{ $row->title }}" icon="eye" position="left"/>
            </a>
            @if($row->description)
                <div class="text-xs text-gray-500 truncate max-w-xs mt-1">
                    {{ $row->description }}
                </div>
            @endif
            @endinteract

            @interact('column_deadline', $row)
            {{ optional($row->deadline)->format('Y-m-d') ?? '—' }}
            @endinteract

            @interact('column_status', $row)
            <x-badge
                :color="$row->status === 'open' ? 'green' : ($row->status === 'closed' ? 'red' : 'gray')"
                :text="ucfirst($row->status)"
            />
            @endinteract

            @interact('column_items_count', $row)
            <button type="button" wire:click="$dispatch('monitoring::load::rfq_items', { rfq: '{{ $row->id }}' })" class="text-blue-600 transition hover:opacity-75 cursor-pointer">
                <x-badge :text="$row->items_count" icon="list-bullet"/>
            </button>
            @endinteract

            @interact('column_quotes_count', $row)
            <x-badge :text="$row->quotes_count" icon="document-duplicate"/>
            @endinteract

            @interact('column_created_at', $row)
            {{ $row->created_at->diffForHumans() }}
            @endinteract

            @interact('column_action', $row)
            <div class="flex gap-1">
                <x-button.circle
                    icon="clock"
                    color="purple"
                    wire:click="$dispatch('monitoring::load::workflow_events', { rfq: '{{ $row->id }}' })"
                    title="{{ __('View Workflow Events') }}"
                />
                @can('edit_rfqs')
                    <x-button.circle icon="pencil" wire:click="$dispatch('monitoring::load::rfq', { rfq: '{{ $row->id }}' })"/>
                @endcan
                @can('delete_rfqs')
                    <livewire:monitoring.rfq.delete :rfq="$row" :key="uniqid('', true)" @deleted="$refresh"/>
                @endcan
            </div>
            @endinteract
        </x-table>
    </x-card>

    <livewire:monitoring.rfq.create @created="$refresh"/>
    <livewire:monitoring.rfq.update @updated="$refresh"/>
    <livewire:monitoring.rfq.items/>
    <livewire:monitoring.rfq.workflow-events/>
</div>
