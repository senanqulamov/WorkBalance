<div>
    <x-card>
        <x-slot name="header">
            <div>
                <h3 class="text-lg font-semibold text-slate-200">{{ __('HumanOps Overview') }}</h3>
                <p class="text-sm text-slate-400">{{ __('Organization-level, aggregated insights only (privacy-first).') }}</p>
            </div>
        </x-slot>

        @if(($overviewData['status'] ?? null) === 'insufficient_data')
            <x-alert color="info" icon="information-circle">
                {{ $overviewData['message'] ?? __('Collecting baseline data...') }}
            </x-alert>
        @else
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-kpi :title="__('Wellness')" :value="($overviewData['wellness_score'] ?? 0) . '/10'" color="indigo" />
                <x-kpi :title="__('Avg Stress')" :value="$overviewData['avg_stress'] ?? '—'" color="rose" />
                <x-kpi :title="__('Avg Energy')" :value="$overviewData['avg_energy'] ?? '—'" color="lime" />
                <x-kpi :title="__('Mood Index')" :value="$overviewData['mood_index'] ?? '—'" color="blue" />
            </div>

            <div class="mt-4 text-xs text-slate-500">
                {{ __('Period ending:') }} {{ $overviewData['period_end'] ?? '—' }} · {{ __('Participants:') }} {{ $overviewData['participant_count'] ?? 0 }} · {{ __('Confidence:') }} {{ round(($overviewData['confidence'] ?? 0) * 100) }}%
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <x-card>
                    <x-slot name="header">
                        <div class="text-slate-200">{{ __('Recent risk signals') }}</div>
                    </x-slot>

                    @if(empty($riskSignals))
                        <x-alert color="info" icon="check-circle">
                            {{ __('No recent risk signals found.') }}
                        </x-alert>
                    @else
                        <div class="space-y-2">
                            @foreach($riskSignals as $s)
                                <div class="p-3 rounded-xl bg-slate-800/50 border border-slate-700/50">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-sm font-semibold text-slate-200">{{ $s['type'] }}</div>
                                            <div class="text-xs text-slate-400">{{ $s['department'] }} · {{ $s['detected'] }}</div>
                                        </div>
                                        <x-badge :text="ucfirst($s['level'])" color="slate" />
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            <x-button sm href="{{ route('humanops.risk-signals') }}">{{ __('Open Risk Signals') }}</x-button>
                        </div>
                    @endif
                </x-card>

                <x-card>
                    <x-slot name="header">
                        <div class="text-slate-200">{{ __('Recommendations') }}</div>
                    </x-slot>

                    @if(empty($recommendations))
                        <x-alert color="info" icon="information-circle">
                            {{ __('No pending recommendations.') }}
                        </x-alert>
                    @else
                        <div class="space-y-2">
                            @foreach($recommendations as $r)
                                <div class="p-3 rounded-xl bg-slate-800/50 border border-slate-700/50">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-slate-200 truncate">{{ $r['title'] }}</div>
                                            <div class="text-xs text-slate-400 truncate">{{ $r['department'] }}</div>
                                        </div>
                                        <x-badge :text="ucfirst($r['priority'])" color="indigo" />
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            <x-button sm href="{{ route('humanops.recommendations') }}">{{ __('Open Recommendations') }}</x-button>
                        </div>
                    @endif
                </x-card>
            </div>
        @endif

        <div class="mt-6">
            <x-alert color="info" icon="shield-check">
                {{ __('This dashboard is aggregated and anonymized. HumanOps never displays individual employee check-ins.') }}
            </x-alert>
        </div>
    </x-card>
</div>
