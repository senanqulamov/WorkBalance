<div>
    <x-modal wire="showModal" size="6xl" title="{{ __('Workflow Events Timeline') }}" blur>
        @if($request)
            {{-- Header with RFQ Info --}}
            <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            {{ $request->title }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ __('RFQ') }} #{{ $request->id }} • {{ __('Created by') }}: {{ $request->buyer->name }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-badge
                            :color="$request->status === 'open' ? 'green' : ($request->status === 'closed' ? 'red' : 'gray')"
                            :text="ucfirst($request->status)"
                        />
                    </div>
                </div>
            </div>

            {{-- Filters Section - Redesigned --}}
            <div class="mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    {{-- Filter Header --}}
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-blue-500/10 dark:bg-blue-500/20">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                        {{ __('Filter Events') }}
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ __('Refine your workflow timeline view') }}
                                    </p>
                                </div>
                            </div>
                            @if(!empty($filterEventTypes) || $filterUser || $filterDateFrom || $filterDateTo)
                                <button
                                    wire:click="resetFilters"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    {{ __('Reset All Filters') }}
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Filter Controls --}}
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            {{-- Event Type Filter --}}
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    {{ __('Event Type') }}
                                </label>

                                {{-- Event type select --}}
                                <x-select.styled
                                    wire:model.live="filterEventTypes"
                                    :options="collect($availableEventTypes)->map(fn($label, $value) => ['label' => __($label), 'value' => $value])->toArray()"
                                    select="label:label|value:value"
                                    multiple
                                    :placeholders="[
                                        'default' => __('Select event types...')
                                     ]"
                                />
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Hold Ctrl/Cmd to select multiple') }}</p>
                            </div>

                            {{-- User Filter --}}
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ __('User') }}
                                </label>
                                <div>
                                    <x-input
                                        wire:model.live.debounce.300ms="filterUser"
                                        placeholder="{{ __('Search by user name...') }}"
                                        icon="magnifying-glass"
                                        clearable
                                    />
                                </div>
                            </div>

                            {{-- Date From Filter --}}
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ __('From Date') }}
                                </label>
                                <x-date
                                    wire:model.live="filterDateFrom"
                                />
                            </div>

                            {{-- Date To Filter --}}
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ __('To Date') }}
                                </label>
                                <x-date
                                    wire:model.live="filterDateTo"
                                />
                            </div>
                        </div>

                        {{-- Active Filters Display --}}
                        @if(!empty($filterEventTypes) || $filterUser || $filterDateFrom || $filterDateTo)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Active Filters:') }}</span>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($filterEventTypes as $eventType)
                                        <span
                                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                            {{ $availableEventTypes[$eventType] ?? $eventType }}
                                            <button
                                                wire:click="$set('filterEventTypes', {{ json_encode(array_values(array_diff($filterEventTypes, [$eventType]))) }})"
                                                class="hover:text-blue-900 dark:hover:text-blue-100 transition-colors"
                                            >
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </span>
                                    @endforeach
                                    @if($filterUser)
                                        <span
                                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            {{ __('User:') }} {{ $filterUser }}
                                            <button wire:click="$set('filterUser', null)" class="hover:text-purple-900 dark:hover:text-purple-100 transition-colors">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </span>
                                    @endif
                                    @if($filterDateFrom || $filterDateTo)
                                        <span
                                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300 border border-green-200 dark:border-green-800">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            @if($filterDateFrom && $filterDateTo)
                                                {{ $filterDateFrom }} - {{ $filterDateTo }}
                                            @elseif($filterDateFrom)
                                                {{ __('From:') }} {{ $filterDateFrom }}
                                            @else
                                                {{ __('To:') }} {{ $filterDateTo }}
                                            @endif
                                            <button wire:click="$set('filterDateFrom', null); $set('filterDateTo', null)" class="hover:text-green-900 dark:hover:text-green-100 transition-colors">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Timeline Section --}}
            <div class="relative">
                @if($this->workflowEvents->count() > 0)
                    {{-- Timeline Container --}}
                    <div class="space-y-6 overflow-y-auto h-80">
                        @foreach($this->workflowEvents as $event)
                            <div class="relative flex gap-4" wire:key="event-{{ $event->id }}">
                                {{-- Timeline Line --}}
                                @if(!$loop->last)
                                    <div class="absolute left-6 top-12 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                                @endif

                                {{-- Event Icon --}}
                                <div class="relative flex-shrink-0">
                                    <div
                                        class="flex items-center justify-center w-12 h-12 rounded-full bg-{{ $this->getEventColor($event->event_type) }}-100 dark:bg-{{ $this->getEventColor($event->event_type) }}-900/30 border-2 border-{{ $this->getEventColor($event->event_type) }}-500 dark:border-{{ $this->getEventColor($event->event_type) }}-400">
                                        <svg class="w-6 h-6 text-{{ $this->getEventColor($event->event_type) }}-600 dark:text-{{ $this->getEventColor($event->event_type) }}-400" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            @if($event->event_type === 'status_changed')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            @elseif($event->event_type === 'supplier_invited')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                            @elseif($event->event_type === 'quote_submitted')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            @elseif($event->event_type === 'sla_reminder')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                            @elseif($event->event_type === 'quote_accepted')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            @elseif($event->event_type === 'quote_rejected')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                            @endif
                                        </svg>
                                    </div>
                                </div>

                                {{-- Event Content --}}
                                <div class="flex-1 min-w-0 pb-6">
                                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
                                        {{-- Event Header --}}
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex items-center gap-2">
                                                <x-badge
                                                    :color="$this->getEventColor($event->event_type)"
                                                    :text="$availableEventTypes[$event->event_type] ?? ucfirst(str_replace('_', ' ', $event->event_type))"
                                                />
                                                @if($event->user)
                                                    <span class="text-xs text-gray-600 dark:text-gray-400">
                                                        {!! __('by :user', [
                                                            'user' => '<span class="font-medium text-gray-900 dark:text-gray-100">'.$event->user->name.'</span>'
                                                        ]) !!}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                <time datetime="{{ $event->occurred_at->toIso8601String() }}" title="{{ $event->occurred_at->format('Y-m-d H:i:s') }}">
                                                    {{ $event->occurred_at->diffForHumans() }}
                                                </time>
                                            </div>
                                        </div>

                                        {{-- Event Description --}}
                                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                            {{ $event->description }}
                                        </p>

                                        {{-- State Transition --}}
                                        @if($event->from_state || $event->to_state)
                                            <div class="flex items-center gap-2 text-xs">
                                                @if($event->from_state)
                                                    <span class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium">
                                                        {{ ucfirst($event->from_state) }}
                                                    </span>
                                                @endif
                                                @if($event->from_state && $event->to_state)
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                                    </svg>
                                                @endif
                                                @if($event->to_state)
                                                    <span class="px-2 py-1 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium">
                                                        {{ ucfirst($event->to_state) }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Metadata --}}
                                        @if($event->metadata && count($event->metadata) > 0)
                                            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                                <details class="group">
                                                    <summary
                                                        class="text-xs font-medium text-gray-600 dark:text-gray-400 cursor-pointer hover:text-gray-900 dark:hover:text-gray-200 flex items-center gap-1">
                                                        <svg class="w-3 h-3 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                        </svg>
                                                        {{ __('Additional Details') }}
                                                    </summary>
                                                    <div class="mt-2 pl-4 space-y-1">
                                                        @foreach($event->metadata as $key => $value)
                                                            <div class="text-xs">
                                                                <span class="font-medium text-gray-600 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                                <span class="text-gray-900 dark:text-gray-100">
                                                                    @if(is_array($value))
                                                                        {{ json_encode($value) }}
                                                                    @else
                                                                        {{ $value }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </details>
                                            </div>
                                        @endif

                                        {{-- Timestamp (absolute) --}}
                                        <div class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                                            <svg class="w-3 h-3 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $event->occurred_at->format('F j, Y \a\t g:i A') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $this->workflowEvents->links() }}
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('No workflow events') }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @if(!empty($filterEventTypes) || $filterUser || $filterDateFrom || $filterDateTo)
                                {{ __('No events match your current filters.') }}
                            @else
                                {{ __('No workflow events have been recorded for this RFQ yet.') }}
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        @endif

        <x-slot:footer>
            <div class="flex items-center justify-between w-full">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Total Events') }}: <span class="font-bold">{{ $this->workflowEvents->total() ?? 0 }}</span>
                </div>
                <x-button text="{{ __('Close') }}" wire:click="closeModal" flat/>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
