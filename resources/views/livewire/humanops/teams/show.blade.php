<div class="space-y-4">
    <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-6">
        <p class="text-xs uppercase tracking-wide text-slate-400">Team</p>
        <h1 class="text-2xl font-bold text-slate-100">Cohort {{ $team }}</h1>
        <p class="text-sm text-slate-400">Anonymized wellbeing indicators. Individual data is never shown.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
            <p class="text-xs uppercase tracking-wide text-slate-400">Stress trend</p>
            <p class="mt-2 text-3xl font-semibold text-slate-100">Cooling</p>
            <p class="text-xs text-slate-400">Last 2 weeks</p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
            <p class="text-xs uppercase tracking-wide text-slate-400">Burnout risk</p>
            <p class="mt-2 text-3xl font-semibold text-slate-100">Low</p>
            <p class="text-xs text-slate-400">Signals stay below threshold</p>
        </div>
    </div>
</div>
