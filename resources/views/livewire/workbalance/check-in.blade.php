<div class="space-y-6">
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
        <p class="text-xs uppercase tracking-wide text-slate-400">Daily check-in</p>
        <h1 class="text-2xl font-bold text-slate-100">How are you feeling?</h1>
        <p class="text-sm text-slate-300">Take a gentle pause. Your notes are private and never shared with employers.</p>
    </div>

    <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4 space-y-4">
        <label class="block text-sm text-slate-200">Mood</label>
        <input type="range" min="1" max="5" value="3" class="w-full accent-indigo-500" aria-label="Mood scale" />

        <label class="block text-sm text-slate-200">Energy</label>
        <input type="range" min="1" max="5" value="3" class="w-full accent-indigo-500" aria-label="Energy scale" />

        <label class="block text-sm text-slate-200">Optional note</label>
        <textarea class="w-full rounded-lg border border-slate-800 bg-slate-900 text-sm text-slate-200" rows="3" placeholder="Write anything you want to release. This stays with you."></textarea>

        <div class="flex justify-end">
            <button class="rounded-lg bg-indigo-500 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-400">Save check-in</button>
        </div>
    </div>
</div>
