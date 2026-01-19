@props(['title' => '', 'value' => 0, 'color' => 'blue'])

@php
    $map = [
        'blue' => 'from-blue-500 to-blue-700',
        'emerald' => 'from-emerald-500 to-emerald-700',
        'slate' => 'from-slate-500 to-slate-700',
        'amber' => 'from-amber-500 to-amber-700',
        'red' => 'from-red-500 to-red-700',
        'indigo' => 'from-indigo-500 to-indigo-700',
        'rose' => 'from-rose-500 to-rose-700',
        'purple' => 'from-purple-500 to-purple-700',
        'lime' => 'from-lime-500 to-lime-700',
    ];
    $gradient = $map[$color] ?? $map['blue'];
@endphp

<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $gradient }} p-4 shadow">
    <div class="text-sm text-white/80">{{ $title }}</div>
    <div class="mt-1 text-2xl font-bold text-white">{{ is_numeric($value) ? number_format($value) : $value }}</div>
</div>
