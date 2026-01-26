<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-light text-slate-100 mb-2">Department Well-Being Insights</h1>
        <p class="text-slate-400 text-lg">Aggregated patterns across departments (privacy-protected)</p>
    </div>

    @if(empty($departments))
        <div class="bg-slate-800/50 rounded-2xl p-12 text-center border border-slate-700/50">
            <x-icon name="building-office" class="w-16 h-16 text-slate-600 mx-auto mb-4"/>
            <p class="text-slate-400">No department data available yet.</p>
        </div>
    @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($departments as $dept)
                <div class="bg-slate-800/50 rounded-2xl p-6 border border-slate-700/50">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-medium text-slate-100">{{ $dept['name'] }}</h3>
                            <p class="text-sm text-slate-500">Code: {{ $dept['code'] }}</p>
                        </div>
                        @if(isset($dept['status']))
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                {{ $dept['status'] === 'healthy' ? 'bg-green-500/20 text-green-400' :
                                   ($dept['status'] === 'monitor' ? 'bg-yellow-500/20 text-yellow-400' :
                                   'bg-orange-500/20 text-orange-400') }}">
                                {{ ucfirst(str_replace('_', ' ', $dept['status'])) }}
                            </span>
                        @endif
                    </div>

                    @if(!empty($dept['insufficient_data']))
                        <div class="bg-slate-900/50 rounded-xl p-4 text-center">
                            <p class="text-slate-400 text-sm">
                                <x-icon name="shield-check" class="w-5 h-5 inline mb-1"/>
                                {{ $dept['message'] ?? 'Insufficient aggregated data yet.' }}
                            </p>
                        </div>
                    @else
                        <div class="space-y-4">
                            <div class="bg-slate-900/50 rounded-xl p-4">
                                <div class="text-center mb-3">
                                    <div class="text-3xl font-bold text-slate-100">{{ $dept['wellness_score'] }}/10</div>
                                    <p class="text-xs text-slate-500">Wellness Score (derived)</p>
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="text-center">
                                        <div class="text-sm font-semibold text-slate-300">{{ $dept['avg_stress'] }}</div>
                                        <p class="text-xs text-slate-500">Avg Stress</p>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm font-semibold text-slate-300">{{ $dept['avg_energy'] }}</div>
                                        <p class="text-xs text-slate-500">Avg Energy</p>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm font-semibold text-slate-300">{{ $dept['mood_index'] }}</div>
                                        <p class="text-xs text-slate-500">Mood Index</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-sm">
                                <div class="text-slate-400">
                                    <x-icon name="users" class="w-4 h-4 inline mr-1"/>
                                    {{ $dept['participant_count'] }} participants
                                </div>
                                <div class="text-slate-400">
                                    {{ round(($dept['confidence'] ?? 0) * 100) }}% confidence
                                </div>
                            </div>

                            <p class="text-xs text-slate-500">
                                Period ending: {{ $dept['period_end'] ?? '—' }}
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <div class="mt-8 bg-blue-500/10 border border-blue-500/30 rounded-2xl p-4">
        <p class="text-blue-300 text-sm">
            <x-icon name="information-circle" class="w-5 h-5 inline mr-2"/>
            HumanOps displays only aggregated, anonymized department-level data. No individual check-ins are shown.
        </p>
    </div>
</div>
