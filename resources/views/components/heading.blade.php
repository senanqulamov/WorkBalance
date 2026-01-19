@props(['size' => 'base'])
@php($classes = [
    'xs' => 'text-xs font-semibold',
    'sm' => 'text-sm font-semibold',
    'base' => 'text-base font-semibold',
    'lg' => 'text-lg font-semibold',
    'xl' => 'text-xl font-semibold',
][$size] ?? 'text-base font-semibold')
<h2 {{ $attributes->class($classes) }}>{{ $slot }}</h2>
