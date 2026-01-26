<div>
    <x-card>
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-200">{{ __('Well-Being Overview') }}</h3>
                    <p class="text-sm text-slate-400">{{ __('Aggregated signals only. No individual data is shown.') }}</p>
                </div>
            </div>
        </x-slot>

        @if(!$org)
            <x-alert icon="information-circle" color="info">
                {{ __('Collecting baseline data...') }}
            </x-alert>
        @else
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <x-kpi :title="__('Wellness')" :value="($org['wellness_score'] ?? 0) . '/10'" color="indigo" />
                <x-kpi :title="__('Avg Stress')" :value="$org['avg_stress'] ?? '—'" color="rose" />
                <x-kpi :title="__('Avg Energy')" :value="$org['avg_energy'] ?? '—'" color="lime" />
                <x-kpi :title="__('Participants')" :value="$org['participants'] ?? 0" color="blue" />
            </div>

            <div class="text-xs text-slate-500 mb-6">
                {{ __('Period ending:') }} {{ $org['period_end'] ?? '—' }} · {{ __('Confidence:') }} {{ round(($org['confidence'] ?? 0) * 100) }}%
            </div>
        @endif

        <x-card class="mt-4">
            <x-slot name="header">
                <div class="text-slate-200">{{ __('Department Signals') }}</div>
            </x-slot>

            <x-table :headers="$headers" :rows="$this->rows" />
        </x-card>

        <div class="mt-6">
            <x-alert icon="shield-check" color="info">
                {{ __('HumanOps is privacy-first: metrics appear only after aggregation and anonymity thresholds are met.') }}
            </x-alert>
        </div>
    </x-card>
</div>
