<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-light text-slate-100 mb-2">Well-Being Trends</h1>
        <p class="text-slate-400 text-lg">Time-based patterns in organizational health (aggregated + delayed)</p>
    </div>

    {{-- Time Range Selector --}}
    <div class="flex gap-2 mb-6">
        <button wire:click="$set('timeRange', '7days')"
                class="px-4 py-2 rounded-xl {{ $timeRange === '7days' ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400 hover:text-slate-200' }} transition">
            Last 7 Days
        </button>
        <button wire:click="$set('timeRange', '30days')"
                class="px-4 py-2 rounded-xl {{ $timeRange === '30days' ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400 hover:text-slate-200' }} transition">
            Last 30 Days
        </button>
        <button wire:click="$set('timeRange', '90days')"
                class="px-4 py-2 rounded-xl {{ $timeRange === '90days' ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400 hover:text-slate-200' }} transition">
            Last 90 Days
        </button>
    </div>

    {{-- Insights --}}
    @if(!empty($insights))
        <div class="mb-8 space-y-4">
            @foreach($insights as $insight)
                <div class="bg-slate-800/50 rounded-2xl p-4 border
                    {{ $insight['type'] === 'positive' ? 'border-green-500/50' :
                       ($insight['type'] === 'concern' ? 'border-orange-500/50' : 'border-blue-500/50') }}">
                    <div class="flex items-start gap-3">
                        <x-icon name="{{ $insight['type'] === 'positive' ? 'arrow-trending-up' : ($insight['type'] === 'concern' ? 'exclamation-triangle' : 'information-circle') }}"
                                class="w-5 h-5 {{ $insight['type'] === 'positive' ? 'text-green-400' : ($insight['type'] === 'concern' ? 'text-orange-400' : 'text-blue-400') }}"/>
                        <div>
                            <h3 class="font-medium text-slate-100">{{ $insight['title'] }}</h3>
                            <p class="text-slate-400 text-sm">{{ $insight['description'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Trend Data Table --}}
    <div class="bg-slate-800/50 rounded-2xl border border-slate-700/50 overflow-hidden mb-8">
        <div class="p-6 border-b border-slate-700/50">
            <h2 class="text-xl font-medium text-slate-100">Aggregated Metrics Over Time</h2>
            <p class="text-slate-400 text-sm mt-1">All values are aggregated across the organization</p>
        </div>

        @if(empty($trendData))
            <div class="p-12 text-center">
                <x-icon name="chart-bar" class="w-16 h-16 text-slate-600 mx-auto mb-4"/>
                <p class="text-slate-400">No trend data available for the selected period.</p>
                <p class="text-slate-500 text-sm mt-2">Data appears after the privacy delay and only when participation is sufficient.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Stress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Energy</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Mood</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Participants</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Confidence</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                    @foreach($trendData as $data)
                        <tr class="hover:bg-slate-900/30 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">{{ $data['date'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">{{ number_format($data['stress_score'], 1) }}/10</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">{{ number_format($data['energy_score'], 1) }}/10</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">{{ number_format($data['mood_score'], 1) }}/10</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">{{ $data['participants'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">{{ round($data['confidence'] * 100) }}%</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="bg-slate-800/50 rounded-2xl p-6 border border-slate-700/50">
        <h3 class="text-lg font-medium text-slate-100 mb-4">
            <x-icon name="information-circle" class="w-5 h-5 inline mr-2"/>
            Understanding Trend Data
        </h3>
        <div class="space-y-2 text-sm text-slate-400">
            <p><strong class="text-slate-300">Delay:</strong> Aggregation is delayed to prevent real-time emotional tracking.</p>
            <p><strong class="text-slate-300">Confidence:</strong> Low participation reduces confidence and interpretability.</p>
            <p><strong class="text-slate-300">Use responsibly:</strong> Investigate trends via conversation and systemic improvement.</p>
        </div>
    </div>
</div>
