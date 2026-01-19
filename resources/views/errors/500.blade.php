<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Server Error | {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css'])
    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0) rotate(0deg); }
            25% { transform: translateX(-10px) rotate(-5deg); }
            75% { transform: translateX(10px) rotate(5deg); }
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
        .animate-shake {
            animation: shake 0.5s ease-in-out infinite;
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
                    <div class="w-64 h-64 bg-yellow-500/10 rounded-full blur-3xl animate-pulse-glow"></div>
                </div>
                <div class="relative animate-shake">
                    <svg class="w-48 h-48 mx-auto text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>

            <!-- Error Code -->
            <div class="mb-6">
                <h1 class="text-8xl font-bold bg-gradient-to-r from-yellow-500 to-orange-500 bg-clip-text text-transparent mb-2">
                    500
                </h1>
                <h2 class="text-3xl font-semibold text-dark-text mb-4">
                    Server Error
                </h2>
            </div>

            <!-- Error Message -->
            <div class="mb-8 space-y-4">
                <p class="text-lg text-dark-muted">
                    Whoops! Something went wrong on our end.
                </p>
                <p class="text-sm text-dark-muted">
                    Our team has been notified and we're working to fix the issue. Please try again in a few moments.
                </p>
                @if(config('app.debug') && isset($exception))
                    <div class="mt-6 p-4 bg-dark-surface border border-dark-border rounded-lg text-left">
                        <p class="text-xs font-mono text-red-400 break-all">
                            {{ $exception->getMessage() }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <button onclick="location.reload()"
                        class="inline-flex items-center px-6 py-3 bg-dark-surface border border-dark-border rounded-lg text-dark-text hover:bg-dark-border transition-all duration-200 group">
                    <svg class="w-5 h-5 mr-2 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Try Again
                </button>
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg text-white font-semibold hover:shadow-lg hover:shadow-yellow-500/50 transition-all duration-200 group">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Go to Dashboard
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>

            <!-- Status Information -->
            <div class="mt-12 pt-8 border-t border-dark-border">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="p-4 bg-dark-surface rounded-lg border border-dark-border">
                        <div class="text-2xl font-bold text-green-500 mb-1">✓</div>
                        <div class="text-xs text-dark-muted">Your data is safe</div>
                    </div>
                    <div class="p-4 bg-dark-surface rounded-lg border border-dark-border">
                        <div class="text-2xl font-bold text-blue-500 mb-1">⚡</div>
                        <div class="text-xs text-dark-muted">Team notified</div>
                    </div>
                    <div class="p-4 bg-dark-surface rounded-lg border border-dark-border">
                        <div class="text-2xl font-bold text-purple-500 mb-1">🔧</div>
                        <div class="text-xs text-dark-muted">Working on fix</div>
                    </div>
                </div>
                <p class="text-sm text-dark-muted mb-4">Need immediate help?</p>
                <div class="flex justify-center gap-6">
                    <a href="{{ route('dashboard') }}" class="text-sm text-dark-muted hover:text-dark-text transition-colors">
                        Dashboard
                    </a>
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
