<x-modal wire="showDetailModal" size="2xl" blur="xl">
    @if($selectedSignal)
    <x-slot name="title">
        @lang('Activity Signal Details') - #{{ $selectedSignal->id }}
    </x-slot>

    <div class="space-y-4">
        {{-- Action Type --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                @lang('Action Type')
            </label>
            <x-badge
                :text="Str::replace('_', ' ', Str::title($selectedSignal->action_type))"
                :color="match($selectedSignal->action_type) {
                    'check_in_completed' => 'green',
                    'path_started' => 'blue',
                    'path_completed' => 'purple',
                    'reflection_created' => 'indigo',
                    'session_started' => 'cyan',
                    'session_completed' => 'green',
                    'stress_trend_changed' => 'yellow',
                    'burnout_threshold_crossed' => 'orange',
                    'team_metric_aggregated' => 'slate',
                    default => 'gray'
                }"
            />
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                @lang('Description')
            </label>
            <p class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800 p-3 rounded">
                {{ $selectedSignal->description }}
            </p>
        </div>

        {{-- Context --}}
        @if($selectedSignal->context)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    @lang('Context')
                </label>
                <p class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800 p-3 rounded">
                    {{ $selectedSignal->context }}
                </p>
            </div>
        @endif

        {{-- Team Info (Privacy: No individual identification) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                @lang('Team')
            </label>
            <p class="text-sm text-gray-900 dark:text-gray-100">
                {{ $selectedSignal->team ? $selectedSignal->team->name : 'Anonymous' }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                <x-icon name="lock-closed" class="inline h-3 w-3" />
                Individual identity protected by design
            </p>
        </div>

        {{-- Metadata --}}
        @if($selectedSignal->metadata)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    @lang('Metadata')
                </label>
                <pre class="text-xs font-mono text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800 p-3 rounded overflow-x-auto">{{ json_encode($selectedSignal->metadata, JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif

        {{-- Timestamp --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                @lang('Occurred At')
            </label>
            <p class="text-sm text-gray-900 dark:text-gray-100">
                {{ $selectedSignal->occurred_at->format('Y-m-d H:i:s') }}
                <span class="text-gray-500">({{ $selectedSignal->occurred_at->diffForHumans() }})</span>
            </p>
        </div>
    </div>

    <x-slot name="footer">
        <x-button text="Close" wire:click="closeDetailModal"/>
    </x-slot>
    @endif
</x-modal>
