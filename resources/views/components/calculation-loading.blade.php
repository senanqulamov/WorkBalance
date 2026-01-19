{{--
    Calculation Loading Overlay Component

    Usage:
        <x-calculation-loading />                  <!-- Default target: 'items' -->
        <x-calculation-loading target="totals" />  <!-- Custom target -->

    This component displays a loading overlay when Livewire detects updates
    to the specified target property. Works with wire:loading.delay to prevent
    flashing on fast operations.
--}}
@props(['target' => 'items'])

<!-- Loading Overlay for Calculations -->
<div wire:loading.delay wire:target="{{ $target }}" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl">
        <div class="flex items-center text-primary-500 dark:text-white">
            <x-icon name="arrow-path" class="mr-2 h-10 w-10 animate-spin" />
            <span class="text-lg font-medium">{{ __('Calculating...') }}</span>
        </div>
    </div>
</div>
