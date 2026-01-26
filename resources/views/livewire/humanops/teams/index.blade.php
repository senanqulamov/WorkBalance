<div class="space-y-4">
    <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-6">
        <p class="text-xs uppercase tracking-wide text-slate-400">Teams</p>
        <h1 class="text-2xl font-bold text-slate-100">Cohort wellbeing</h1>
        <p class="text-sm text-slate-400">Aggregated signals only. Minimum cohort size enforced.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        @foreach (range(1, 6) as $team)
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4 shadow">
                <p class="text-sm font-semibold text-slate-100">Team {{ $team }}</p>
                <p class="text-xs text-slate-400">Stress trend: steady</p>
                <p class="text-xs text-slate-400">Engagement: healthy</p>
                <a href="{{ route('humanops.teams.show', ['team' => $team]) }}" class="mt-2 inline-flex text-xs text-indigo-300 hover:text-indigo-200">View cohort</a>
            </div>
        @endforeach
    </div>
</div>
