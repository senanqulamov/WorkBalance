<div>
    <x-slide wire="modal" right size="lg" blur="md">
        @if($request)

            {{-- Header --}}
            <x-slot:title>
                <div class="space-y-1">
                    <h3 class="text-xl font-semibold tracking-tight">
                        {{ __('RFQ Items') }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $request->title }}
                        · <span class="font-mono text-xs">#{{ $request->id }}</span>
                    </p>
                </div>
            </x-slot:title>


            <div class="space-y-5">

                @if($request->items->isEmpty())
                    <div class="py-24 text-center text-gray-500">
                        {{ __('No items found for this RFQ.') }}
                    </div>
                @else
                    @foreach($request->items as $item)
                        <div
                            class="group relative rounded-2xl
                                       bg-white dark:bg-slate-900
                                       border border-gray-200/60 dark:border-slate-700/60
                                       transition-all hover:shadow-xl hover:shadow-black/10">

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 p-6">

                                {{-- Left: Product & Specs --}}
                                <div class="md:col-span-9 space-y-3">

                                    <div class="space-y-1">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $item->product_name }}
                                        </div>

                                        @if($item->product_id)
                                            <div class="text-xs text-gray-500">
                                                {{ __('Product ID') }}:
                                                <span class="font-mono">{{ $item->product_id }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    @if($item->specifications)
                                        <div
                                            class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed
                                                       max-w-3xl line-clamp-3">
                                            {{ $item->specifications }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Right: Quantity --}}
                                <div class="md:col-span-3 flex md:justify-end">
                                    <div
                                        class="flex flex-col justify-center items-center
                                                   w-full md:w-28 h-20
                                                   rounded-xl
                                                   bg-gray-50 dark:bg-slate-800
                                                   border border-gray-200 dark:border-slate-700">

                                            <span class="text-xs uppercase tracking-wide text-gray-500">
                                                {{ __('Quantity') }}
                                            </span>
                                        <span class="text-3xl font-bold text-gray-900 dark:text-white">
                                                {{ $item->quantity }}
                                            </span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                @endif

            </div>

            {{-- Footer --}}
            <x-slot:footer>
                <div class="flex justify-end pt-4">
                    <x-button wire:click="close">
                        {{ __('Close') }}
                    </x-button>
                </div>
            </x-slot:footer>
        @endif
    </x-slide>
</div>
