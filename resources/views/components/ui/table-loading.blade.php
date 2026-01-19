@props([
    // Number of fake rows for skeleton
    'rows' => 8,
    // Number of columns in the skeleton
    'cols' => 6,
    // Whether to show the skeleton automatically during Livewire requests
    'livewire' => true,
    // Optional: scope loading to a specific Livewire target (method/property)
    'target' => null,
])

<div class="fx-table" style="--fx-table-cols: {{ (int) $cols }};" {{ $attributes }}>
    @if($livewire)
        <div @if($target) wire:loading.delay wire:target="{{ $target }}" @else wire:loading.delay @endif class="hidden fx-table__loading" wire:loading.delay.class.remove="hidden">
            <div class="fx-table__skeleton" aria-hidden="true">
                @for ($r = 0; $r < $rows; $r++)
                    <div class="fx-table__skeleton-row">
                        @for ($c = 0; $c < $cols; $c++)
                            <div class="fx-table__skeleton-cell"></div>
                        @endfor
                    </div>
                @endfor
            </div>
        </div>

        <div @if($target) wire:loading.delay.class="opacity-50 pointer-events-none" wire:target="{{ $target }}" @else wire:loading.delay.class="opacity-50 pointer-events-none" @endif class="fx-table__content">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</div>
