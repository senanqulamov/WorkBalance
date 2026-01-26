<div class="max-w-4xl mx-auto">
    {{-- Livewire Table Component --}}
    <div>
        <x-card>
            <x-slot name="header">
                <div>
                    <h3 class="text-lg font-semibold text-slate-200">{{ __('Insights') }}</h3>
                    <p class="text-sm text-slate-400">{{ __('Private, supportive observations visible only to you.') }}</p>
                </div>
            </x-slot>

            @if(empty($this->rows))
                <x-alert icon="information-circle" color="info">
                    {{ __('No personal insights yet. Check in for a few days and we’ll start spotting gentle patterns.') }}
                </x-alert>
            @else
                <x-table :headers="$headers" :rows="$this->rows" paginate="false" :paginator="null">
                    @interact('column_action', $row)
                        @if($row['status'] === 'New')
                            <x-button sm wire:click="acknowledgeInsight({{ $row['id'] }})">{{ __('Acknowledge') }}</x-button>
                        @else
                            <x-badge color="green" text="{{ __('Seen') }}" sm />
                        @endif
                    @endinteract
                </x-table>

                <div class="mt-4">
                    <x-alert icon="shield-check" color="info">
                        {{ __('Reminder: your employer never sees your individual insights or check-ins. HumanOps only receives aggregated signals.') }}
                    </x-alert>
                </div>
            @endif
        </x-card>
    </div>
</div>
