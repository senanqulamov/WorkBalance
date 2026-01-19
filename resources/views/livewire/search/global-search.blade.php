<div x-data="{
    open: @entangle('isOpen'),
    selectedIndex: @entangle('selectedIndex'),
    init() {
        // Listen for openSearch event from Alpine
        window.addEventListener('openSearch', () => {
            this.open = true;
            this.$nextTick(() => this.$refs.searchInput?.focus());
        });

        document.addEventListener('keydown', (e) => {
            // Ctrl+K or Cmd+K to open
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                this.open = true;
                this.$nextTick(() => this.$refs.searchInput?.focus());
            }
            // Escape to close
            if (e.key === 'Escape' && this.open) {
                this.open = false;
            }
            // Arrow keys for navigation
            if (this.open) {
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    $wire.selectNext();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    $wire.selectPrevious();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    $wire.selectCurrent();
                }
            }
        });
    }
}">
    <!-- Search Modal -->
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" x-on:click="open = false"></div>

        <!-- Modal Content -->
        <div class="flex min-h-screen items-start justify-center p-4 pt-[10vh]">
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-2xl overflow-hidden rounded-2xl bg-slate-900 shadow-2xl border border-slate-700"
                 style="display: none;">

                <!-- Search Input -->
                <div class="relative border-b border-slate-700">
                    <x-icon name="magnifying-glass" class="absolute left-5 top-5 w-6 h-6 text-slate-400"/>
                    <input
                            x-ref="searchInput"
                            wire:model.live.debounce.300ms="query"
                            type="text"
                            placeholder="{{ __('Search for RFQs, products, orders, users...')}}"
                            class="w-full bg-transparent pl-14 pr-5 py-5 text-lg text-slate-100 placeholder-slate-400 focus:outline-none border-0"
                            autofocus
                    />
                    <div class="absolute right-5 top-5">
                        <button x-on:click="open = false" class="flex items-center gap-1.5 px-2 py-1 rounded-md bg-slate-800 text-xs text-slate-400 hover:text-slate-300">
                            <span>ESC</span>
                        </button>
                    </div>
                </div>

                <!-- Results -->
                <div class="max-h-[60vh] overflow-y-auto">
                    @if(strlen($query) < 2)
                        <div class="px-6 py-12 text-center">
                            <x-icon name="magnifying-glass" class="w-12 h-12 text-slate-600 mx-auto mb-3"/>
                            <p class="text-slate-400 text-sm">{{__('Type at least 2 characters to search')}}</p>
                            <div class="mt-6 text-xs text-slate-500">
                                <p class="mb-2">Quick tips:</p>
                                <ul class="space-y-1">
                                    <li>• {{__('Search by reference number, name, or description')}}</li>
                                    <li>• {{__('Use ↑↓ arrows to navigate results')}}</li>
                                    <li>• {{__('Press Enter to open selected item')}}</li>
                                </ul>
                            </div>
                        </div>
                    @elseif(empty($results))
                        <div class="px-6 py-12 text-center">
                            <x-icon name="magnifying-glass-circle" class="w-12 h-12 text-slate-600 mx-auto mb-3"/>
                            <p class="text-slate-400 text-sm">{{ __('No results: ')}} "{{ $query }}"</p>
                            <p class="text-slate-500 text-xs mt-2">{{__('Try a different search term')}}</p>
                        </div>
                    @else
                        @php $itemIndex = 0; @endphp
                        @foreach($results as $category => $items)
                            <div class="px-2 py-3">
                                <div class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                    {{ $category }}
                                </div>
                                <div class="space-y-1">
                                    @foreach($items as $item)
                                        @php
                                            $isSelected = $itemIndex === $selectedIndex;
                                            $colorClasses = [
                                                'blue' => 'text-blue-400',
                                                'green' => 'text-green-400',
                                                'purple' => 'text-purple-400',
                                                'indigo' => 'text-indigo-400',
                                                'red' => 'text-red-400',
                                                'gray' => 'text-slate-400',
                                            ];
                                            $color = $colorClasses[$item['color']] ?? 'text-slate-400';
                                        @endphp
                                        <a href="{{ $item['url'] }}"
                                           class="flex items-center gap-4 px-3 py-3 rounded-lg transition-colors {{ $isSelected ? 'bg-slate-800 ring-2 ring-blue-500/50' : 'hover:bg-slate-800/50' }}"
                                           wire:click="close">
                                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center {{ $color }}">
                                                <x-icon name="{{ $item['icon'] }}" class="w-5 h-5"/>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-slate-200 truncate">
                                                    {{ $item['title'] }}
                                                </div>
                                                <div class="text-xs text-slate-400 truncate">
                                                    {{ $item['subtitle'] }}
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <span class="text-xs px-2 py-1 rounded-md bg-slate-800/50 text-slate-400">
                                                    {{ $item['type'] }}
                                                </span>
                                            </div>
                                        </a>
                                        @php $itemIndex++; @endphp
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Footer -->
                <div class="border-t border-slate-700 px-4 py-3 bg-slate-900/50">
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <div class="flex items-center gap-4">
                            <span class="flex items-center gap-1.5">
                                <kbd class="px-2 py-1 rounded bg-slate-800 text-slate-400">↑↓</kbd> {{ __('Navigate')}}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <kbd class="px-2 py-1 rounded bg-slate-800 text-slate-400">Enter</kbd> {{__('Select')}}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <kbd class="px-2 py-1 rounded bg-slate-800 text-slate-400">ESC</kbd> {{__('Close')}}
                            </span>
                        </div>
                        <span>
                            @if(!empty($results))
                                {{ collect($results)->flatten(1)->count() }} {{__('results')}}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
