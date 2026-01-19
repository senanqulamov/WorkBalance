<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $statusCode ?? 'Error' }} | {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css'])
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
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
                    <div class="w-64 h-64 bg-gray-500/10 rounded-full blur-3xl animate-pulse-glow"></div>
                </div>
                <div class="relative animate-float">
                    <svg class="w-48 h-48 mx-auto text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Error Code -->
            <div class="mb-6">
                <h1 class="text-8xl font-bold bg-gradient-to-r from-gray-400 to-gray-600 bg-clip-text text-transparent mb-2">
                    {{ $statusCode ?? '???' }}
                </h1>
                <h2 class="text-3xl font-semibold text-dark-text mb-4">
                    {{ $title ?? 'Something Went Wrong' }}
                </h2>
            </div>

            <!-- Error Message -->
            <div class="mb-8 space-y-4">
                <p class="text-lg text-dark-muted">
                    {{ $message ?? 'An unexpected error occurred.' }}
                </p>
                @if(isset($exception) && $exception->getMessage() && config('app.debug'))
                    <div class="mt-6 p-4 bg-dark-surface border border-dark-border rounded-lg text-left">
                        <p class="text-xs font-mono text-gray-400 break-all">
                            {{ $exception->getMessage() }}
                        </p>
                    </div>
                @endif
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
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 rounded-lg text-white font-semibold hover:shadow-lg hover:shadow-gray-500/50 transition-all duration-200 group">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Go to Dashboard
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>

            <!-- Additional Help -->
            <div class="mt-12 pt-8 border-t border-dark-border">
                <p class="text-sm text-dark-muted mb-4">Need assistance?</p>
                <div class="flex justify-center gap-6">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm text-dark-muted hover:text-dark-text transition-colors">
                            Dashboard
                        </a>
                        <span class="text-dark-border">•</span>
                    @endauth
                    <a href="mailto:support@{{ config('app.domain', 'example.com') }}" class="text-sm text-dark-muted hover:text-dark-text transition-colors">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
