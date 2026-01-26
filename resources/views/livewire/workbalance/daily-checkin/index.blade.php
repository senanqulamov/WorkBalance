<div class="max-w-3xl mx-auto">
    {{-- Welcoming Header --}}
    <div class="mb-6 text-center">
        <h1 class="text-3xl font-light text-slate-100 mb-3">How are you doing today?</h1>
        <p class="text-slate-400 text-lg">Take a moment to notice how you're feeling. There are no right or wrong answers.</p>
    </div>

    {{-- Success Message --}}
    @if($showSuccess)
        <div class="bg-gradient-to-r from-green-500/10 to-blue-500/10 border border-green-500/30 rounded-3xl p-6 mb-6 text-center">
            <div class="flex items-center justify-center gap-3 mb-2">
                <x-icon name="check-circle" class="w-8 h-8 text-green-400"/>
                <p class="text-xl font-light text-green-300">Thank you for checking in</p>
            </div>
            <p class="text-slate-400">Your well-being matters. We're here to support you.</p>
        </div>
    @endif

    {{-- Check-in Form --}}
    <div class="bg-gradient-to-br from-blue-50/5 to-purple-50/5 rounded-3xl p-8 shadow-xl border border-slate-700/30">

        @if($alreadyCheckedInToday && !$showSuccess)
            <div class="bg-blue-500/10 border border-blue-500/30 rounded-2xl p-4 mb-6 text-center">
                <p class="text-blue-300 text-sm">You've already checked in today. You can update your check-in below if things have changed.</p>
            </div>
        @endif

        <div class="space-y-8">
            {{-- Stress Level --}}
            <div>
                <label class="block text-slate-200 text-lg mb-3 font-light">How is your stress level?</label>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-400 w-20">Calm</span>
                    <input type="range"
                           wire:model.live="stressLevel"
                           min="1"
                           max="5"
                           class="flex-1 h-2 bg-slate-700/50 rounded-lg appearance-none cursor-pointer accent-blue-400
                                  [&::-webkit-slider-thumb]:w-5 [&::-webkit-slider-thumb]:h-5 [&::-webkit-slider-thumb]:rounded-full
                                  [&::-webkit-slider-thumb]:bg-blue-400 [&::-webkit-slider-thumb]:cursor-pointer">
                    <span class="text-sm text-slate-400 w-20 text-right">Very High</span>
                </div>
                <div class="text-center mt-2">
                    <span class="text-2xl font-light text-slate-200">{{ $stressLevel }}/5</span>
                </div>
            </div>

            {{-- Energy Level --}}
            <div>
                <label class="block text-slate-200 text-lg mb-3 font-light">How is your energy level?</label>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-400 w-20">Drained</span>
                    <input type="range"
                           wire:model.live="energyLevel"
                           min="1"
                           max="5"
                           class="flex-1 h-2 bg-slate-700/50 rounded-lg appearance-none cursor-pointer accent-green-400
                                  [&::-webkit-slider-thumb]:w-5 [&::-webkit-slider-thumb]:h-5 [&::-webkit-slider-thumb]:rounded-full
                                  [&::-webkit-slider-thumb]:bg-green-400 [&::-webkit-slider-thumb]:cursor-pointer">
                    <span class="text-sm text-slate-400 w-20 text-right">Energized</span>
                </div>
                <div class="text-center mt-2">
                    <span class="text-2xl font-light text-slate-200">{{ $energyLevel }}/5</span>
                </div>
            </div>

            {{-- Mood --}}
            <div>
                <label class="block text-slate-200 text-lg mb-3 font-light">How is your mood?</label>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                    @foreach($availableMoods as $mood)
                        <button
                            type="button"
                            wire:click="$set('moodState', '{{ $mood }}')"
                            class="px-4 py-2 rounded-full text-sm transition
                                {{ $moodState === $mood
                                    ? 'bg-purple-500/30 text-purple-200 border border-purple-500/50'
                                    : 'bg-slate-800/50 text-slate-400 border border-slate-700/50 hover:border-slate-600' }}">
                            {{ ucfirst($mood) }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Optional Note --}}
            <div>
                <label class="block text-slate-200 text-lg mb-3 font-light">
                    Anything you'd like to note? (optional)
                    <span class="text-sm text-slate-500 font-normal ml-2">Private—only you can see this</span>
                </label>
                <textarea
                    wire:model="optionalNote"
                    rows="3"
                    maxlength="500"
                    placeholder="Anything important about today?..."
                    class="w-full bg-slate-900/50 border border-slate-700/50 rounded-2xl px-4 py-3 text-slate-200 placeholder-slate-500 focus:outline-none focus:border-blue-500/50 transition resize-none"></textarea>
                <div class="text-right text-xs text-slate-500 mt-1">
                    {{ strlen($optionalNote) }}/500 characters
                </div>
            </div>

            {{-- Optional Reflection --}}
            <div>
                <label class="block text-slate-200 text-lg mb-3 font-light">
                    Reflection (optional)
                    <span class="text-sm text-slate-500 font-normal ml-2">Private—only you can see this</span>
                </label>
                <textarea
                    wire:model="reflectionText"
                    rows="4"
                    maxlength="1000"
                    placeholder="What helped? What drained you? Anything you want to remember..."
                    class="w-full bg-slate-900/50 border border-slate-700/50 rounded-2xl px-4 py-3 text-slate-200 placeholder-slate-500 focus:outline-none focus:border-blue-500/50 transition resize-none"></textarea>
                <div class="text-right text-xs text-slate-500 mt-1">
                    {{ strlen($reflectionText) }}/1000 characters
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="mt-8 flex justify-center">
            <button
                wire:click="submit"
                type="button"
                class="px-10 py-4 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700
                       text-white rounded-3xl font-medium text-lg transition-all shadow-lg hover:shadow-xl
                       transform hover:scale-105 active:scale-95">
                {{ $alreadyCheckedInToday ? 'Update Check-In' : 'Complete Check-In' }}
            </button>
        </div>

        {{-- Privacy Reminder --}}
        <div class="mt-6 text-center text-sm text-slate-500">
            <x-icon name="shield-check" class="w-4 h-4 inline mr-1"/>
            Your check-in is private. Only you can see your individual responses.
        </div>
    </div>

    {{-- Supportive Message --}}
    <div class="mt-6 text-center">
        <p class="text-slate-400 text-sm">
            Checking in regularly helps you stay aware of your patterns.
            <br class="hidden sm:block">
            You can skip any day—this is for you, not anyone else.
        </p>
    </div>
</div>

@script
<script>
    $wire.on('show-success-redirect', () => {
        setTimeout(() => {
            window.location.href = '{{ route("workbalance.insights") }}';
        }, 2000);
    });
</script>
@endscript
