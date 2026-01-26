<div class="max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-light text-slate-100 mb-2">Well-being Tools</h1>
        <p class="text-slate-400 text-lg">Short, helpful exercises for difficult moments.</p>
    </div>

    {{-- Tools Grid --}}
    <div class="grid md:grid-cols-2 gap-6">
        {{-- Breathing Pause --}}
        <div class="bg-gradient-to-br from-blue-50/10 to-blue-100/10 rounded-3xl p-8 border border-slate-700/50 hover:border-blue-500/30 transition">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-blue-500/20 rounded-2xl flex items-center justify-center">
                    <x-icon name="sparkles" class="w-8 h-8 text-blue-400"/>
                </div>
                <div>
                    <h3 class="text-2xl font-light text-slate-100">Breathing Pause</h3>
                    <p class="text-slate-400 text-sm">2-3 minutes</p>
                </div>
            </div>
            <p class="text-slate-300 mb-6">A simple breathing exercise to help you find calm and clarity in stressful moments.</p>
            <button class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-2xl font-medium transition shadow-lg">
                Start Exercise
            </button>
        </div>

        {{-- Positive Refocus --}}
        <div class="bg-gradient-to-br from-purple-50/10 to-purple-100/10 rounded-3xl p-8 border border-slate-700/50 hover:border-purple-500/30 transition">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-purple-500/20 rounded-2xl flex items-center justify-center">
                    <x-icon name="light-bulb" class="w-8 h-8 text-purple-400"/>
                </div>
                <div>
                    <h3 class="text-2xl font-light text-slate-100">Positive Refocus</h3>
                    <p class="text-slate-400 text-sm">3-5 minutes</p>
                </div>
            </div>
            <p class="text-slate-300 mb-6">A gentle cognitive exercise to help shift your perspective when you're feeling stuck.</p>
            <button class="w-full py-3 bg-purple-500 hover:bg-purple-600 text-white rounded-2xl font-medium transition shadow-lg">
                Start Exercise
            </button>
        </div>

        {{-- Grounding Exercise --}}
        <div class="bg-gradient-to-br from-green-50/10 to-green-100/10 rounded-3xl p-8 border border-slate-700/50 hover:border-green-500/30 transition">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-green-500/20 rounded-2xl flex items-center justify-center">
                    <x-icon name="hand-raised" class="w-8 h-8 text-green-400"/>
                </div>
                <div>
                    <h3 class="text-2xl font-light text-slate-100">Grounding</h3>
                    <p class="text-slate-400 text-sm">2 minutes</p>
                </div>
            </div>
            <p class="text-slate-300 mb-6">Use your senses to reconnect with the present moment when anxiety rises.</p>
            <button class="w-full py-3 bg-green-500 hover:bg-green-600 text-white rounded-2xl font-medium transition shadow-lg">
                Start Exercise
            </button>
        </div>

        {{-- Quick Reset --}}
        <div class="bg-gradient-to-br from-orange-50/10 to-orange-100/10 rounded-3xl p-8 border border-slate-700/50 hover:border-orange-500/30 transition">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-orange-500/20 rounded-2xl flex items-center justify-center">
                    <x-icon name="arrow-path" class="w-8 h-8 text-orange-400"/>
                </div>
                <div>
                    <h3 class="text-2xl font-light text-slate-100">Quick Reset</h3>
                    <p class="text-slate-400 text-sm">1 minute</p>
                </div>
            </div>
            <p class="text-slate-300 mb-6">A 60-second pause to reset your nervous system between tasks or meetings.</p>
            <button class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-2xl font-medium transition shadow-lg">
                Start Exercise
            </button>
        </div>
    </div>

    {{-- Important Note --}}
    <div class="mt-8 bg-slate-800/50 rounded-3xl p-6 border border-slate-700/50">
        <p class="text-slate-400 text-sm text-center">
            <x-icon name="information-circle" class="w-5 h-5 inline mr-2 text-blue-400"/>
            These are self-help tools, not therapy. If you're experiencing a mental health crisis, please reach out to a professional.
        </p>
    </div>
</div>
