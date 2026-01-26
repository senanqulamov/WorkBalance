<x-modal wire="showDetailModal" size="2xl" blur="xl">
    @if($selectedLog)
    <x-slot name="title">
        @lang('Log Details') - #{{ $selectedLog->id }}
    </x-slot>

    <div class="space-y-4">
        {{-- Type and Action --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    @lang('Type')
                </label>
                <x-badge
                        :text="ucfirst($selectedLog->type)"
                        :color="match($selectedLog->type) {
                            'error' => 'red',
                            'warning' => 'yellow',
                            'info' => 'blue',
                            'success' => 'green',
                            'create' => 'green',
                            'update' => 'blue',
                            'delete' => 'red',
                            'login' => 'green',
                            'logout' => 'gray',
                            default => 'gray'
                        }"
                />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    @lang('Action')
                </label>
                <p class="text-sm text-gray-900 dark:text-gray-100">{{ $selectedLog->action ?? '-' }}</p>
            </div>
        </div>

        {{-- Message --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                @lang('Message')
            </label>
            <p class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800 p-3 rounded">
                {{ $selectedLog->message }}
            </p>
        </div>

        {{-- Model Info --}}
        @if($selectedLog->model)
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Model
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $selectedLog->model }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Model ID
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $selectedLog->model_id }}</p>
                </div>
            </div>
        @endif

        {{-- User Info --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    @lang('User')
                </label>
                <p class="text-sm text-gray-900 dark:text-gray-100">
                    {{ $selectedLog->user ? $selectedLog->user->name : 'System' }}
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    @lang('IP Address')
                </label>
                <p class="text-sm font-mono text-gray-900 dark:text-gray-100">{{ $selectedLog->ip_address ?? '-' }}</p>
            </div>
        </div>

        {{-- User Agent --}}
        @if($selectedLog->user_agent)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    User Agent
                </label>
                <p class="text-xs font-mono text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800 p-3 rounded break-all">
                    {{ $selectedLog->user_agent }}
                </p>
            </div>
        @endif

        {{-- Metadata --}}
        @if($selectedLog->metadata)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Metadata
                </label>
                <pre class="text-xs font-mono text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800 p-3 rounded overflow-x-auto">{{ json_encode($selectedLog->metadata, JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif

        {{-- Timestamp --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                @lang('Created')
            </label>
            <p class="text-sm text-gray-900 dark:text-gray-100">
                {{ $selectedLog->created_at->format('Y-m-d H:i:s') }}
                <span class="text-gray-500">({{ $selectedLog->created_at->diffForHumans() }})</span>
            </p>
        </div>
    </div>

    <x-slot name="footer">
        <x-button text="Close" wire:click="closeDetailModal"/>
    </x-slot>
    @endif
</x-modal>
