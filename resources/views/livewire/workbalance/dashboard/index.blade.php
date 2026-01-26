<div class="max-w-4xl mx-auto">
    {{-- Welcome Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-light text-slate-100 mb-2">Welcome back, {{ auth()->user()->name }}</h1>
        <p class="text-slate-400 text-lg">Your private space for a calmer mind at work.</p>
    </div>

    {{-- Quick Stats --}}
    @if($recentTrends['check_in_count'] > 0)
    <div class="grid md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500/10 to-blue-600/10 rounded-2xl p-4 border border-slate-700/50">
            <div class="text-sm text-slate-400 mb-1">7-Day Avg Stress</div>
            <div class="text-2xl font-light text-slate-100">{{ $recentTrends['avg_stress'] }}/10</div>
        </div>
        <div class="bg-gradient-to-br from-green-500/10 to-green-600/10 rounded-2xl p-4 border border-slate-700/50">
            <div class="text-sm text-slate-400 mb-1">7-Day Avg Energy</div>
            <div class="text-2xl font-light text-slate-100">{{ $recentTrends['avg_energy'] }}/10</div>
        </div>
        <div class="bg-gradient-to-br from-purple-500/10 to-purple-600/10 rounded-2xl p-4 border border-slate-700/50">
            <div class="text-sm text-slate-400 mb-1">7-Day Avg Mood</div>
            <div class="text-2xl font-light text-slate-100">{{ $recentTrends['avg_mood'] }}/10</div>
        </div>
    </div>
    @endif

    {{-- Daily Check-In Card --}}
    <div class="bg-gradient-to-br from-blue-50/10 to-purple-50/10 rounded-3xl p-8 shadow-xl border border-slate-700/50 mb-6">
        <div class="mb-6">
            <h2 class="text-2xl font-light text-slate-100 mb-2 flex items-center gap-2">
                <x-icon name="heart" class="w-6 h-6 text-blue-400"/>
                Daily Check-In
            </h2>
            @if($alreadyCheckedInToday)
                <p class="text-green-400">✓ You've already checked in today. You can update your check-in below.</p>
            @else
                <p class="text-slate-400">Take a moment to notice how you're feeling today.</p>
            @endif
        </div>

        @if($showSuccess)
            <div class="bg-green-500/20 border border-green-500/30 rounded-2xl p-4 mb-6">
                <p class="text-green-300 text-center">✓ Thank you for checking in. Your well-being matters.</p>
            </div>
        @endif

        <div class="space-y-8">
            {{-- Stress Level --}}
            <div>
                <label class="block text-slate-200 text-lg mb-3 font-light">How is your stress level?</label>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-400 w-20">Low</span>
                    <input type="range"
                           wire:model.live="stressLevel"
                           min="1"
                           max="10"
                           class="flex-1 h-2 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-blue-400">
                    <span class="text-sm text-slate-400 w-20 text-right">High</span>
                </div>
            </div>

            {{-- Energy Level --}}
            <div>
                <label class="block text-slate-200 text-lg mb-3 font-light">How is your energy level?</label>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-400 w-20">Drained</span>
                    <input type="range"
                           wire:model.live="energyLevel"
                           min="1"
                           max="10"
                           class="flex-1 h-2 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-green-400">
                    <span class="text-sm text-slate-400 w-20 text-right">Energized</span>
                </div>
            </div>

            {{-- Mood Level --}}
            <div>
                <label class="block text-slate-200 text-lg mb-3 font-light">How is your mood?</label>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-400 w-20">Low</span>
                    <input type="range"
                           wire:model.live="moodLevel"
                           min="1"
                           max="10"
                           class="flex-1 h-2 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-purple-400">
                    <span class="text-sm text-slate-400 w-20 text-right">Good</span>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-center">
            <button wire:click="submit"
                    class="px-8 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white rounded-2xl font-medium transition-all shadow-lg hover:shadow-xl">
                {{ $alreadyCheckedInToday ? 'Update Check-In' : 'Submit Check-In' }}
            </button>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="grid md:grid-cols-3 gap-6">
        <a href="{{ route('workbalance.trends') }}" class="bg-slate-800/50 rounded-2xl p-6 border border-slate-700/50 hover:border-blue-500/30 transition group">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                    <x-icon name="chart-bar" class="w-6 h-6 text-blue-400"/>
                </div>
                <h3 class="text-xl font-light text-slate-100">My Trends</h3>
            </div>
            <p class="text-slate-400 text-sm">See your patterns over time</p>
        </a>

        <a href="{{ route('workbalance.tools') }}" class="bg-slate-800/50 rounded-2xl p-6 border border-slate-700/50 hover:border-purple-500/30 transition group">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                    <x-icon name="sparkles" class="w-6 h-6 text-purple-400"/>
                </div>
                <h3 class="text-xl font-light text-slate-100">Well-being Tools</h3>
            </div>
            <p class="text-slate-400 text-sm">Quick exercises for difficult moments</p>
        </a>

        <a href="{{ route('workbalance.insights') }}" class="bg-slate-800/50 rounded-2xl p-6 border border-slate-700/50 hover:border-green-500/30 transition group">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                    <x-icon name="light-bulb" class="w-6 h-6 text-green-400"/>
                </div>
                <h3 class="text-xl font-light text-slate-100">Insights</h3>
            </div>
            <p class="text-slate-400 text-sm">Gentle observations & support</p>
        </a>
    </div>

    {{-- Encouragement Message --}}
    @if($toolUsageStats['total_sessions'] > 0)
    <div class="mt-6 bg-gradient-to-r from-blue-500/10 to-purple-500/10 rounded-2xl p-6 border border-slate-700/50">
        <p class="text-slate-300 text-center">
            You've used well-being tools <span class="font-semibold text-blue-400">{{ $toolUsageStats['total_sessions'] }}</span> times this month.
            That shows real self-awareness and care. Keep going! 💙
        </p>
    </div>
    @endif
</div>
