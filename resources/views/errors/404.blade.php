<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Page Not Found | {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css'])
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
        .animate-float {
            animation: float 4s ease-in-out infinite;
        }
        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
    </style>
</head>
<body class="font-sans antialiased bg-dark-bg text-dark-text">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-2xl w-full text-center">
            <!-- Animated Icon -->
            <div class="mb-8 relative">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-64 h-64 bg-blue-500/10 rounded-full blur-3xl animate-pulse-glow"></div>
                </div>
                <div class="relative animate-float">
                    <svg class="w-48 h-48 mx-auto text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Error Code -->
            <div class="mb-6">
                <h1 class="text-8xl font-bold bg-gradient-to-r from-blue-500 to-cyan-500 bg-clip-text text-transparent mb-2">
                    404
                </h1>
                <h2 class="text-3xl font-semibold text-dark-text mb-4">
                    Page Not Found
                </h2>
            </div>

            <!-- Error Message -->
            <div class="mb-8 space-y-4">
                <p class="text-lg text-dark-muted">
                    Oops! The page you're looking for seems to have wandered off.
                </p>
                <p class="text-sm text-dark-muted">
                    The page might have been moved, deleted, or perhaps it never existed. Let's get you back on track!
                </p>
            </div>

            <!-- Search Box (Optional) -->
            <div class="mb-8">
                <form action="{{ route('dashboard') }}" method="GET" class="max-w-md mx-auto">
                    <div class="relative">
                        <input type="text"
                               name="search"
                               placeholder="Search for something..."
                               class="w-full px-6 py-3 bg-dark-surface border border-dark-border rounded-lg text-dark-text placeholder-dark-muted focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-dark-muted hover:text-dark-text transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="javascript:history.back()"
                   class="inline-flex items-center px-6 py-3 bg-dark-surface border border-dark-border rounded-lg text-dark-text hover:bg-dark-border transition-all duration-200 group">
                    <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Go Back
                </a>
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg text-white font-semibold hover:shadow-lg hover:shadow-blue-500/50 transition-all duration-200 group">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Go to Dashboard
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>

            <!-- Quick Links -->
            <div class="mt-12 pt-8 border-t border-dark-border">
                <p class="text-sm text-dark-muted mb-4">Quick Links</p>
                <div class="flex flex-wrap justify-center gap-6">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm text-dark-muted hover:text-dark-text transition-colors">
                            Dashboard
                        </a>
                        @if(Route::has('rfq.index'))
                            <span class="text-dark-border">•</span>
                            <a href="{{ route('rfq.index') }}" class="text-sm text-dark-muted hover:text-dark-text transition-colors">
                                RFQs
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-dark-muted hover:text-dark-text transition-colors">
                            Login
                        </a>
                        @if(Route::has('register'))
                            <span class="text-dark-border">•</span>
                            <a href="{{ route('register') }}" class="text-sm text-dark-muted hover:text-dark-text transition-colors">
                                Register
                            </a>
                        @endif
                    @endauth
                    <span class="text-dark-border">•</span>
                    <a href="mailto:support@{{ config('app.domain', 'example.com') }}" class="text-sm text-dark-muted hover:text-dark-text transition-colors">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
