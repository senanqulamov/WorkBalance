<div class="space-y-6">
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg">
        <p class="text-xs uppercase tracking-wide text-slate-400">WorkBalance</p>
        <h1 class="text-2xl font-bold text-slate-100">Welcome back</h1>
        <p class="text-sm text-slate-300">This is a private space. Your reflections stay with you.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
            <p class="text-xs uppercase tracking-wide text-slate-400">Today</p>
            <p class="mt-2 text-lg font-semibold text-slate-100">Check in with how you feel</p>
            <a href="{{ route('workbalance.check-in') }}" class="mt-3 inline-flex rounded-lg bg-indigo-500 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-400">Start check-in</a>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
            <p class="text-xs uppercase tracking-wide text-slate-400">Progress</p>
            <p class="mt-2 text-lg font-semibold text-slate-100">See gentle trends, not scores</p>
            <a href="{{ route('workbalance.progress') }}" class="mt-3 inline-flex rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-100 hover:bg-slate-700">View progress</a>
        </div>
    </div>
</div>
