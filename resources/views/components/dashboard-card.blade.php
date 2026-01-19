@props([
    'title',
    'description',
    'icon' => 'squares-2x2',
    'color' => 'blue',
    'href' => '#',
    'stats' => null,
])

@php
$colorClasses = match($color) {
    'blue' => 'from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800',
    'green' => 'from-green-500 to-green-700 hover:from-green-600 hover:to-green-800',
    'purple' => 'from-purple-500 to-purple-700 hover:from-purple-600 hover:to-purple-800',
    'yellow' => 'from-yellow-500 to-yellow-700 hover:from-yellow-600 hover:to-yellow-800',
    'red' => 'from-red-500 to-red-700 hover:from-red-600 hover:to-red-800',
    'indigo' => 'from-indigo-500 to-indigo-700 hover:from-indigo-600 hover:to-indigo-800',
    'pink' => 'from-pink-500 to-pink-700 hover:from-pink-600 hover:to-pink-800',
    default => 'from-gray-500 to-gray-700 hover:from-gray-600 hover:to-gray-800',
};
@endphp

<a href="{{ $href }}" class="group block">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $colorClasses }} p-6 shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <div class="rounded-full bg-white/20 p-2 backdrop-blur-sm">
                        <x-icon name="{{ $icon }}" class="h-6 w-6 text-white" />
                    </div>
                    <h3 class="text-xl font-bold text-white">{{ $title }}</h3>
                </div>
                <p class="text-sm text-white/80 leading-relaxed">{{ $description }}</p>

                @if($stats)
                <div class="mt-4 pt-4 border-t border-white/20">
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        @foreach($stats as $statLabel => $statValue)
                        <div>
                            <div class="text-white/70 uppercase tracking-wide">{{ $statLabel }}</div>
                            <div class="text-white font-bold text-lg mt-0.5">{{ $statValue }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            <div class="ml-4 transition-transform duration-300 group-hover:translate-x-1">
                <x-icon name="arrow-right" class="h-5 w-5 text-white/80" />
            </div>
        </div>

        <!-- Decorative elements -->
        <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -bottom-4 -left-4 h-24 w-24 rounded-full bg-white/10 blur-2xl"></div>
    </div>
</a>
