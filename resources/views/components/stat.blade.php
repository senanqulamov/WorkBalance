@props(['label','value','icon'=>null])
<div class="p-4 rounded-2xl border-0 bg-slate-800 dark:bg-slate-800 shadow-sm">
    <div class="flex items-center gap-2 mb-1">
        @if($icon)
            <x-icon name="{{$icon}}" class="w-4 h-4 text-slate-300" />
        @endif
        <span class="text-sm uppercase tracking-wide text-slate-300">{{ $label }}</span>
    </div>
    <div class="text-xl font-semibold text-brand-primary">{{ $value }}</div>
</div>
