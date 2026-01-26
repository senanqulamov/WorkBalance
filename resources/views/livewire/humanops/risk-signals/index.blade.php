<div>
    <x-card>
        <x-slot name="header">
            <div>
                <h3 class="text-lg font-semibold text-slate-200">{{ __('Risk Signals') }}</h3>
                <p class="text-sm text-slate-400">{{ __('Early warning patterns from aggregated data (no individual tracking).') }}</p>
            </div>
        </x-slot>

        @if (session()->has('message'))
            <x-alert color="success" icon="check-circle" class="mb-4">
                {{ session('message') }}
            </x-alert>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <x-select.styled
                :label="__('Severity')"
                wire:model.live="filterSeverity"
                :options="$this->severityOptions"
                select="label:label|value:value"
            />

            <x-select.styled
                :label="__('Signal Type')"
                wire:model.live="filterType"
                :options="$this->signalTypes"
                select="label:label|value:value"
            />

            <div class="flex items-end">
                <x-checkbox wire:model.live="showMitigated" :label="__('Show mitigated')" />
            </div>
        </div>

        @if(empty($this->rows))
            <x-alert color="info" icon="check-circle">
                {{ __('No matching signals. Good news—nothing requires attention under current filters.') }}
            </x-alert>
        @else
            <x-table :headers="$headers" :rows="$this->rows" paginate="false" :paginator="null">
                @interact('column_action', $row)
                    <div class="flex gap-2">
                        @if($row['status'] === 'New')
                            <x-button sm wire:click="acknowledgeSignal('{{ $row['type'] }}', {{ $row['id'] }})">{{ __('Acknowledge') }}</x-button>
                        @endif
                        @if($row['status'] !== 'Mitigated')
                            <x-button sm color="green" wire:click="mitigateSignal('{{ $row['type'] }}', {{ $row['id'] }})">{{ __('Mitigate') }}</x-button>
                        @endif
                    </div>
                @endinteract
            </x-table>
        @endif

        <div class="mt-6">
            <x-alert color="info" icon="information-circle">
                {{ __('Signals guide attention, not blame. Always combine with context and systemic fixes; never use to target individuals.') }}
            </x-alert>
        </div>
    </x-card>
</div>
