@props([
    'label' => '',
    'value' => null,
    'model' => null,
    'disabled' => false,
    'id' => null,
])

@php
    $wireModel = $model ?: $attributes->get('wire:model');

    $inputId = $id ?: ('cst_checkbox_' . md5((string) $wireModel . '|' . (string) $value . '|' . spl_object_id($attributes)));
@endphp

<div class="inline-flex" {{ $attributes->only(['wire:key']) }}>
    <input
        type="checkbox"
        id="{{ $inputId }}"
        value="{{ $value }}"
        wire:model="{{ $wireModel }}"
        {{ $disabled ? 'disabled' : '' }}
        class="peer sr-only"
    />

    <label
        for="{{ $inputId }}"
        class="group relative inline-flex items-center gap-2.5 rounded-full px-4 py-2.5
        text-sm font-medium whitespace-nowrap cursor-pointer select-none
        border-2 transition-colors duration-200 ease-out
        border-zinc-700/50 bg-slate-800 text-slate-200
        hover:border-zinc-600 hover:bg-zinc-800 dark:hover:text-slate-200
        hover:text-slate-800 active:scale-[0.98]
        peer-checked:border-emerald-500/50
        peer-checked:bg-emerald-500/10
        peer-checked:text-emerald-400
        {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}"
    >
        <!-- Icon Circle -->
        <span class="relative flex-shrink-0 w-5 h-5 rounded-full
            flex items-center justify-center
            border-2 border-zinc-600
            transition-colors duration-200
            peer-checked:border-emerald-500
            peer-checked:bg-emerald-500">

            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                 fill="currentColor"
                 class="w-3 h-3 text-white
                scale-0 opacity-0
                transition-all duration-200
                peer-checked:scale-100 peer-checked:opacity-100">
                <path fill-rule="evenodd"
                      d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                      clip-rule="evenodd" />
            </svg>
        </span>

        <span>{{ __($label) }}</span>
    </label>
</div>
