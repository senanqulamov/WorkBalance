<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-light text-slate-100 mb-2">Privacy & Trust</h1>
        <p class="text-slate-400 text-lg">How employee data is protected in HumanOps Intelligence</p>
    </div>

    {{-- Core Principle --}}
    <div class="bg-gradient-to-br from-blue-500/10 to-purple-500/10 rounded-2xl p-8 mb-8 border border-blue-500/30">
        <div class="flex items-start gap-4">
            <x-icon name="shield-check" class="w-12 h-12 text-blue-400 flex-shrink-0"/>
            <div>
                <h2 class="text-2xl font-medium text-slate-100 mb-3">Employee Trust First</h2>
                <p class="text-slate-300 text-lg mb-2">
                    WorkBalance helps employees first. HumanOps Intelligence benefits second.
                </p>
                <p class="text-slate-400">
                    If employees don't trust the system, they won't use it honestly. That's why privacy protection isn't optional—it's the foundation.
                </p>
            </div>
        </div>
    </div>

    {{-- Privacy Rules --}}
    <div class="mb-8">
        <h2 class="text-2xl font-medium text-slate-100 mb-4">Privacy Rules (Always Enforced)</h2>
        <div class="space-y-4">
            @foreach($privacyRules as $rule)
                <div class="bg-slate-800/50 rounded-2xl p-6 border border-slate-700/50">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-slate-100 mb-1">{{ $rule['rule'] }}</h3>
                            <p class="text-slate-300 mb-2">{{ $rule['description'] }}</p>
                            <p class="text-slate-400 text-sm italic">Why: {{ $rule['reason'] }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-500/20 text-green-400">
                            ✓ {{ ucfirst($rule['status']) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Data Flow --}}
    <div class="mb-8">
        <h2 class="text-2xl font-medium text-slate-100 mb-4">How Data Flows (Step by Step)</h2>
        <div class="relative">
            @foreach($dataFlowInfo as $key => $step)
                <div class="flex gap-4 mb-6">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold">
                            {{ substr($key, -1) }}
                        </div>
                        @if(!$loop->last)
                            <div class="w-0.5 h-12 bg-blue-500/30 my-2"></div>
                        @endif
                    </div>
                    <div class="flex-1 bg-slate-800/50 rounded-2xl p-6 border border-slate-700/50">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-lg font-medium text-slate-100">{{ $step['title'] }}</h3>
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400">
                                {{ $step['privacy_level'] }}
                            </span>
                        </div>
                        <p class="text-slate-300">{{ $step['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- What Employers See vs Don't See --}}
    <div class="grid md:grid-cols-2 gap-6 mb-8">
        <div class="bg-slate-800/50 rounded-2xl p-6 border border-green-500/50">
            <h3 class="text-lg font-medium text-green-400 mb-4 flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5"/>
                What Employers CAN See
            </h3>
            <ul class="space-y-2 text-slate-300">
                <li class="flex items-start gap-2">
                    <span class="text-green-400">✓</span>
                    <span>Department-level averages (10+ employees)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-green-400">✓</span>
                    <span>Aggregated trends over time</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-green-400">✓</span>
                    <span>Risk patterns (high stress departments)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-green-400">✓</span>
                    <span>Participation rates</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-green-400">✓</span>
                    <span>Suggested organizational actions</span>
                </li>
            </ul>
        </div>

        <div class="bg-slate-800/50 rounded-2xl p-6 border border-red-500/50">
            <h3 class="text-lg font-medium text-red-400 mb-4 flex items-center gap-2">
                <x-icon name="x-circle" class="w-5 h-5"/>
                What Employers CANNOT See
            </h3>
            <ul class="space-y-2 text-slate-300">
                <li class="flex items-start gap-2">
                    <span class="text-red-400">✗</span>
                    <span>Individual employee check-ins</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-red-400">✗</span>
                    <span>Who checked in or when</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-red-400">✗</span>
                    <span>Personal notes or reflections</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-red-400">✗</span>
                    <span>Real-time emotional states</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-red-400">✗</span>
                    <span>Small group data (< 10 people)</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- Trust Message --}}
    <div class="bg-gradient-to-r from-purple-500/10 to-blue-500/10 rounded-2xl p-6 border border-purple-500/30">
        <h3 class="text-lg font-medium text-slate-100 mb-3">Building and Maintaining Trust</h3>
        <div class="space-y-2 text-slate-300">
            <p>This system only works if employees trust it. That means:</p>
            <ul class="ml-6 space-y-1 list-disc">
                <li>Never trying to circumvent privacy protections</li>
                <li>Using insights to improve conditions, not to monitor individuals</li>
                <li>Communicating transparently about how data is used</li>
                <li>Respecting that participation is always voluntary</li>
                <li>Taking action on concerning patterns without blame</li>
            </ul>
        </div>
    </div>
</div>
