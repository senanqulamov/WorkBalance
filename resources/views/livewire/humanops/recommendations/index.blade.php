<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-light text-slate-100 mb-2">Recommended Actions</h1>
        <p class="text-slate-400 text-lg">Evidence-based suggestions generated from aggregated, anonymized patterns</p>
    </div>

    <div class="bg-blue-500/10 border border-blue-500/30 rounded-2xl p-4 mb-8">
        <p class="text-blue-300 text-sm">
            <x-icon name="information-circle" class="w-5 h-5 inline mr-2"/>
            These are recommendations, not commands. Focus on systemic improvements, not individual correction.
        </p>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-500/10 border border-green-500/30 rounded-2xl p-4 mb-6">
            <p class="text-green-300">{{ session('message') }}</p>
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-slate-800/50 rounded-2xl p-6 mb-6 border border-slate-700/50">
        <div class="flex flex-col md:flex-row md:items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm text-slate-400 mb-2">Priority</label>
                <select wire:model.live="filterPriority" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-slate-200">
                    <option value="all">All Priorities</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>
            <div>
                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                    <input type="checkbox" wire:model.live="showAcknowledged" class="rounded bg-slate-900 border-slate-700">
                    <span class="text-sm">Show acknowledged</span>
                </label>
            </div>
        </div>
    </div>

    {{-- Recommendations List --}}
    @if($recommendations->isEmpty())
        <div class="bg-slate-800/50 rounded-2xl p-12 text-center border border-slate-700/50">
            <x-icon name="check-circle" class="w-16 h-16 text-slate-600 mx-auto mb-4"/>
            <p class="text-slate-400">No recommendations to show right now.</p>
        </div>
    @else
        <div class="space-y-6 mb-8">
            @foreach($recommendations as $rec)
                <div class="bg-slate-800/50 rounded-2xl p-6 border
                    {{ $rec->priority === 'high' ? 'border-orange-500/50' : ($rec->priority === 'medium' ? 'border-yellow-500/50' : 'border-blue-500/50') }}">

                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $rec->priority === 'high' ? 'bg-orange-500/20 text-orange-400' : ($rec->priority === 'medium' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-blue-500/20 text-blue-400') }}">
                                    {{ ucfirst($rec->priority) }} Priority
                                </span>
                                <span class="text-slate-500 text-sm">{{ $rec->category }}</span>
                                <span class="text-slate-400 text-sm">{{ $rec->department?->name ?? 'Organization-wide' }}</span>
                                @if($rec->acknowledged_at)
                                    <span class="px-2 py-1 rounded-full text-xs bg-blue-500/20 text-blue-400">Acknowledged</span>
                                @endif
                            </div>

                            <h3 class="text-xl font-medium text-slate-100 mb-2">{{ $rec->title }}</h3>
                            <p class="text-slate-300">{{ $rec->text }}</p>

                            <p class="text-xs text-slate-500 mt-3">
                                Generated {{ optional($rec->generated_at)->diffForHumans() ?? 'recently' }}
                            </p>
                        </div>

                        <div class="flex gap-2">
                            @if(!$rec->acknowledged_at)
                                <button wire:click="acknowledgeRecommendation({{ $rec->id }})"
                                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm transition">
                                    Acknowledge
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $recommendations->links() }}
        </div>
    @endif

    <div class="mt-8 bg-slate-800/50 rounded-2xl p-6 border border-slate-700/50">
        <h3 class="text-lg font-medium text-slate-100 mb-3">
            <x-icon name="information-circle" class="w-5 h-5 inline mr-2"/>
            About These Recommendations
        </h3>
        <div class="space-y-2 text-sm text-slate-400">
            <p>• <strong class="text-slate-300">Aggregated-only:</strong> Recommendations are generated from anonymized, aggregated data.</p>
            <p>• <strong class="text-slate-300">Trust-first:</strong> Never use these to single out individuals.</p>
            <p>• <strong class="text-slate-300">Act + learn:</strong> Track changes and watch trends over time.</p>
        </div>
    </div>
</div>
