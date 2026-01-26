<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-light text-slate-100 mb-2">Your Trends</h1>
        <p class="text-slate-400 text-lg">See how you've been feeling over time.</p>
    </div>

    {{-- Time Range Selector --}}
    <div class="flex gap-2 mb-6">
        <button wire:click="setTimeRange('7days')"
                class="px-4 py-2 rounded-xl {{ $timeRange === '7days' ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400 hover:text-slate-200' }} transition">
            Last 7 Days
        </button>
        <button wire:click="setTimeRange('30days')"
                class="px-4 py-2 rounded-xl {{ $timeRange === '30days' ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400 hover:text-slate-200' }} transition">
            Last 30 Days
        </button>
        <button wire:click="setTimeRange('90days')"
                class="px-4 py-2 rounded-xl {{ $timeRange === '90days' ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400 hover:text-slate-200' }} transition">
            Last 3 Months
        </button>
    </div>

    {{-- Trend Cards --}}
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-50/10 to-blue-100/10 rounded-3xl p-6 border border-slate-700/50">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center">
                    <x-icon name="heart" class="w-5 h-5 text-blue-400"/>
                </div>
                <h3 class="text-lg font-light text-slate-100">Stress</h3>
            </div>
            <p class="text-slate-400 text-sm mb-3">Your stress levels have been stable.</p>
            <div class="h-32 bg-slate-800/50 rounded-xl flex items-center justify-center text-slate-500">
                <span class="text-sm">Chart placeholder</span>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50/10 to-green-100/10 rounded-3xl p-6 border border-slate-700/50">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-green-500/20 rounded-xl flex items-center justify-center">
                    <x-icon name="bolt" class="w-5 h-5 text-green-400"/>
                </div>
                <h3 class="text-lg font-light text-slate-100">Energy</h3>
            </div>
            <p class="text-slate-400 text-sm mb-3">Your energy is trending upward.</p>
            <div class="h-32 bg-slate-800/50 rounded-xl flex items-center justify-center text-slate-500">
                <span class="text-sm">Chart placeholder</span>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50/10 to-purple-100/10 rounded-3xl p-6 border border-slate-700/50">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-purple-500/20 rounded-xl flex items-center justify-center">
                    <x-icon name="face-smile" class="w-5 h-5 text-purple-400"/>
                </div>
                <h3 class="text-lg font-light text-slate-100">Mood</h3>
            </div>
            <p class="text-slate-400 text-sm mb-3">Your mood has been mostly positive.</p>
            <div class="h-32 bg-slate-800/50 rounded-xl flex items-center justify-center text-slate-500">
                <span class="text-sm">Chart placeholder</span>
            </div>
        </div>
    </div>

    {{-- Insights Card --}}
    <div class="bg-slate-800/50 rounded-3xl p-8 border border-slate-700/50">
        <h2 class="text-2xl font-light text-slate-100 mb-4 flex items-center gap-2">
            <x-icon name="light-bulb" class="w-6 h-6 text-yellow-400"/>
            Gentle Insights
        </h2>
        <div class="space-y-4">
            <p class="text-slate-300">You've checked in consistently this week. That's wonderful self-awareness.</p>
            <p class="text-slate-300">Your stress levels seem lower on days when you start with a check-in.</p>
            <p class="text-slate-400 text-sm italic mt-4">Remember: These are observations, not judgments. You're doing great by showing up.</p>
        </div>
    </div>
</div>
