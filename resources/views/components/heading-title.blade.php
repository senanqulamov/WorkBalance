@props(['title'=>null, 'text'=>null, 'icon'=>null, 'padding'=>null, 'hover'=>null])
<div class="{{ $padding }} rounded-2xl border-0 bg-slate-800 dark:bg-slate-800 shadow-sm transition-all {{ $hover == '-' ? 'hover:shadow-md' : 'hover:scale-103' }}">
    <div class="flex items-center gap-2 mb-1">
        <div class="uppercase tracking-wide text-slate-300 flex items-center gap-2">
            @if($icon)
                <x-icon name="{{$icon}}" class="w-5 h-5 text-slate-300" />
            @endif
            <span class="font-bold text-md">{{ $title }}</span>
            <span class="text-sm">{{ $text }}</span>
        </div>
    </div>
</div>
