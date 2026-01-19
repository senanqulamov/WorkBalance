<div>
    <x-card>
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-200 mr-3">{{ __('System Health') }}</h3>
                <div class="flex gap-2">
                    <x-button wire:click="runChecks" sm>
                        <x-icon name="arrow-path" class="w-4 h-4 animate-spin-slow"/>
                        {{ __('Refresh') }}
                    </x-button>
                    <x-badge color="primary" dark title="{{ __('Auto-refreshes every 30 seconds') }}" tabindex="0">
                        <svg class="w-3 h-3 mr-1.5 animate-pulse" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3"/>
                        </svg>
                        {{ __('Live') }}
                    </x-badge>
                    <x-tooltip text="{{ __('Auto-refreshes every 30 seconds') }}" />
                </div>
            </div>
        </x-slot>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <x-card>
                <x-slot name="header">
                    <div class="text-slate-200">{{ __('Application') }}</div>
                </x-slot>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between items-center"><span>Env</span><span class="font-semibold">{{ $checks['app']['env'] }}</span></div>
                    <div class="flex justify-between items-center"><span>Debug</span><span class="font-semibold">{{ $checks['app']['debug'] ? 'on' : 'off' }}</span></div>
                    <div class="flex justify-between items-center"><span>Timezone</span><span class="font-semibold">{{ $checks['app']['timezone'] }}</span></div>
                </div>
            </x-card>

            <x-card>
                <x-slot name="header" class="text-slate-200">
                    <div class="text-slate-200">{{ __('Database') }}
                    </div>
                </x-slot>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span>{{ __('Connected') }}</span>
                        <x-badge :color="$checks['database']['connected'] ? 'green' : 'red'" dark title="{{ $checks['database']['connected'] ? __('Driver: :driver; Host: :host', ['driver' => ($checks['database']['driver'] ?? config('database.default')), 'host' => ($checks['database']['host'] ?? 'unknown')]) : __('Unable to connect. Check DB config or logs.') }}" tabindex="0">
                            <x-icon :name="$checks['database']['connected'] ? 'check-circle' : 'x-circle'" class="w-4 h-4 mr-1"/>
                            {{ $checks['database']['connected'] ? __('Yes') : __('No') }}
                        </x-badge>
                    </div>
                </div>
            </x-card>

            <x-card>
                <x-slot name="header">
                    <div class="text-slate-200">{{ __('Cache') }}</div>
                </x-slot>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span>{{ __('Working') }}</span>
                        <x-badge :color="$checks['cache']['connected'] ? 'green' : 'red'" dark title="{{ $checks['cache']['connected'] ? __('Driver: :driver', ['driver' => ($checks['cache']['driver'] ?? config('cache.default'))]) : __('Cache connection failed. Check cache config or logs.') }}" tabindex="0">
                            <x-icon :name="$checks['cache']['connected'] ? 'check-circle' : 'x-circle'" class="w-4 h-4 mr-1"/>
                            {{ $checks['cache']['connected'] ? __('Yes') : __('No') }}
                        </x-badge>
                    </div>
                </div>
            </x-card>

            <x-card class="md:col-span-2">
                <x-slot name="header">
                    <div class="text-slate-200">{{ __('Queue') }}</div>
                </x-slot>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between items-center"><span>{{ __('Connection') }}</span><span class="font-semibold">{{ $checks['queue']['connection'] }}</span></div>
                    <div class="flex justify-between items-center"><span>{{ __('Pending Jobs') }}</span><span class="font-semibold">{{ $checks['queue']['size'] }}</span></div>
                    <div class="flex justify-between items-center"><span>{{ __('Failed Jobs') }}</span><span class="font-semibold text-red-400">{{ $checks['queue']['failed'] }}</span></div>
                </div>
            </x-card>

            <x-card>
                <x-slot name="header">
                    <div class="text-slate-200">{{ __('Scheduler') }}</div>
                </x-slot>
                <div class="text-sm">
                    <div class="flex justify-between items-center"><span>{{ __('Last Run') }}</span><span class="font-semibold">{{ $checks['scheduler']['last_run'] }}</span></div>
                </div>
            </x-card>
        </div>
        <div class="mt-6">
            <x-alert color="info" icon="information-circle">
                {{ __('Tip: Hover over each badge for more details. Health checks auto-refresh every 30 seconds.') }}
            </x-alert>
        </div>
    </x-card>
</div>
