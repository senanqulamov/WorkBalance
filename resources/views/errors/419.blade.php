<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 - Session Expired | {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css'])
    <style>
        @keyframes spin-slow {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
        .animate-spin-slow {
            animation: spin-slow 8s linear infinite;
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
                    <div class="w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl animate-pulse-glow"></div>
                </div>
                <div class="relative animate-spin-slow">
                    <svg class="w-48 h-48 mx-auto text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Error Code -->
            <div class="mb-6">
                <h1 class="text-8xl font-bold bg-gradient-to-r from-indigo-500 to-purple-500 bg-clip-text text-transparent mb-2">
                    419
                </h1>
                <h2 class="text-3xl font-semibold text-dark-text mb-4">
                    Session Expired
                </h2>
            </div>

            <!-- Error Message -->
            <div class="mb-8 space-y-4">
                <p class="text-lg text-dark-muted">
                    Your session has expired due to inactivity.
                </p>
                <p class="text-sm text-dark-muted">
                    For your security, please refresh the page or log in again to continue.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <button onclick="location.reload()"
                        class="inline-flex items-center px-6 py-3 bg-dark-surface border border-dark-border rounded-lg text-dark-text hover:bg-dark-border transition-all duration-200 group">
                    <svg class="w-5 h-5 mr-2 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Refresh Page
                </button>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg text-white font-semibold hover:shadow-lg hover:shadow-indigo-500/50 transition-all duration-200 group">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Login Again
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>

            <!-- Security Info -->
            <div class="mt-12 pt-8 border-t border-dark-border">
                <div class="max-w-md mx-auto p-4 bg-dark-surface rounded-lg border border-dark-border mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-indigo-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-left">
                            <p class="text-sm font-medium text-dark-text mb-1">Why did this happen?</p>
                            <p class="text-xs text-dark-muted">
                                Sessions expire after a period of inactivity to protect your account. This is a standard security measure.
                            </p>
                        </div>
                    </div>
                </div>
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
