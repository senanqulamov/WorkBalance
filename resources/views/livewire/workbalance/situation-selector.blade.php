<div class="space-y-6">
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
        <p class="text-xs uppercase tracking-wide text-slate-400">Choose a focus</p>
        <h1 class="text-2xl font-bold text-slate-100">What feels most present?</h1>
        <p class="text-sm text-slate-300">Pick a situation to receive a gentle, therapist-inspired path.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @foreach ([
            'Deadline pressure',
            'Feeling undervalued',
            'Conflict with colleagues',
            'Low motivation',
            'Emotional exhaustion',
            'Anxiety before meetings',
        ] as $situation)
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4 hover:border-indigo-500/60">
                <p class="text-sm font-semibold text-slate-100">{{ $situation }}</p>
                <p class="text-xs text-slate-400">Guided support, never diagnostic.</p>
                <button class="mt-3 inline-flex rounded-lg bg-slate-800 px-3 py-2 text-xs font-semibold text-slate-100 hover:bg-slate-700">Begin path</button>
            </div>
        @endforeach
    </div>
</div>
