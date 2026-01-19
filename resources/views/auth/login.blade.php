<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo Section -->
            <div class="text-center mb-10">

                <!-- Welcome Message -->
                <div class="mb-6">
                    <div class="inline-flex items-center gap-2 px-4 py-2 mb-4 bg-white/10 backdrop-blur-sm rounded-full shadow-lg shadow-blue-500/10 border border-blue-500/20">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                        </span>
                        <span class="text-sm font-semibold text-white">{{ __('Welcome Back') }}</span>
                    </div>

                    <h2 class="text-3xl md:text-4xl font-black mb-3 flex flex-row gap-2 justify-center">
                        <span class="block bg-gradient-to-r from-white via-blue-200 to-indigo-200 bg-clip-text text-transparent">
                            {{ __('Sign In to') }}
                        </span>
                        <span class="block bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 bg-clip-text text-transparent">
                            {{ __('dPanel') }}
                        </span>
                    </h2>
                    <p class="text-slate-300 text-lg">
                        {{ __('Next-Gen SAP Procurement Platform') }}
                    </p>
                </div>
            </div>

            <!-- Login Form -->
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-cyan-500/20 via-blue-500/20 to-purple-500/20 rounded-2xl blur-xl opacity-50 group-hover:opacity-70 transition-opacity duration-500"></div>
                <div class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl shadow-black/50 overflow-hidden">
                    <!-- Form Header -->
                    <div class="px-8 pt-8 pb-6 border-b border-white/10">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-cyan-500/30">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">{{ __('Account Login') }}</h3>
                                <p class="text-sm text-slate-400">{{ __('Enter your credentials to continue') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <div class="px-8 py-6">
                        <form method="POST" action="{{ route('login') }}" class="space-y-6">
                            @csrf

                            <!-- Email Input -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-300 mb-1">
                                    {{ __('Email Address') }}
                                    <span class="text-red-400">*</span>
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/0 via-blue-500/0 to-purple-500/0 group-hover:from-cyan-500/5 group-hover:via-blue-500/5 group-hover:to-purple-500/5 rounded-xl transition-all duration-300"></div>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <input
                                            type="email"
                                            name="email"
                                            id="email"
                                            value="{{ old('email', 'test@example.com') }}"
                                            required
                                            autofocus
                                            autocomplete="username"
                                            class="block w-full pl-10 pr-4 py-3 bg-black/30 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                            placeholder="Enter your email"
                                        />
                                    </div>
                                </div>
                                @error('email')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password Input -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-300 mb-1">
                                    {{ __('Password') }}
                                    <span class="text-red-400">*</span>
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/0 via-blue-500/0 to-purple-500/0 group-hover:from-cyan-500/5 group-hover:via-blue-500/5 group-hover:to-purple-500/5 rounded-xl transition-all duration-300"></div>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                        <input
                                            type="password"
                                            name="password"
                                            id="password"
                                            required
                                            autocomplete="current-password"
                                            class="block w-full pl-10 pr-4 py-3 bg-black/30 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                                            placeholder="Enter your password"
                                        />
                                    </div>
                                </div>
                                @error('password')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="flex items-center justify-between">
                                <label class="flex items-center cursor-pointer group">
                                    <div class="relative">
                                        <input
                                            type="checkbox"
                                            id="remember_me"
                                            name="remember"
                                            class="sr-only peer"
                                        />
                                        <div class="w-5 h-5 bg-black/30 border border-white/20 rounded-md peer-checked:bg-blue-500/20 peer-checked:border-blue-500 flex items-center justify-center transition-all duration-200">
                                            <svg class="w-3 h-3 text-blue-400 opacity-0 peer-checked:opacity-100 transition-opacity duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <span class="ml-3 text-sm text-slate-300 group-hover:text-white transition-colors duration-200">
                                        {{ __('Remember me') }}
                                    </span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-sm text-blue-400 hover:text-blue-300 transition-colors duration-200 font-medium">
                                        {{ __('Forgot password?') }}
                                    </a>
                                @endif
                            </div>

                            <!-- Submit Button -->
                            <div>
                                <button
                                    type="submit"
                                    class="group relative w-full flex justify-center items-center gap-2 px-6 py-4 text-base font-bold text-white bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 hover:from-blue-700 hover:via-indigo-700 hover:to-purple-700 rounded-xl shadow-2xl shadow-blue-500/40 hover:shadow-blue-500/60 transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50"
                                >
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                    {{ __('Sign In') }}
                                </button>
                            </div>

                            <!-- Divider -->
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-white/10"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-2 bg-transparent text-slate-500">{{ __('Or') }}</span>
                                </div>
                            </div>

                            <!-- Register Link -->
                            @if (Route::has('register'))
                                <div class="text-center">
                                    <p class="text-slate-400">
                                        {{ __("Don't have an account?") }}
                                        <a href="{{ route('register') }}" class="font-semibold text-blue-400 hover:text-blue-300 transition-colors duration-200 group inline-flex items-center gap-1">
                                            {{ __('Sign up') }}
                                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                            </svg>
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <!-- Stats Footer -->
            <div class="text-center">
                <div class="grid grid-cols-3 gap-4 max-w-xs mx-auto">
                    <div class="text-center">
                        <div class="text-xl font-black bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">500+</div>
                        <div class="text-xs text-slate-500">{{ __('Active Users') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">98%</div>
                        <div class="text-xs text-slate-500">{{ __('Satisfaction') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">24/7</div>
                        <div class="text-xs text-slate-500">{{ __('Support') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
