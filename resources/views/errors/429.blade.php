<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>429 - Too Many Requests | {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css'])
    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
        @keyframes countdown {
            from { stroke-dashoffset: 0; }
            to { stroke-dashoffset: 283; }
        }
        .animate-shake {
            animation: shake 0.5s ease-in-out;
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
                    <div class="w-64 h-64 bg-amber-500/10 rounded-full blur-3xl animate-pulse-glow"></div>
                </div>
                <div class="relative">
                    <svg class="w-48 h-48 mx-auto text-amber-500 animate-shake" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>

            <!-- Error Code -->
            <div class="mb-6">
                <h1 class="text-8xl font-bold bg-gradient-to-r from-amber-500 to-yellow-500 bg-clip-text text-transparent mb-2">
                    429
                </h1>
                <h2 class="text-3xl font-semibold text-dark-text mb-4">
                    Too Many Requests
                </h2>
            </div>

            <!-- Error Message -->
            <div class="mb-8 space-y-4">
                <p class="text-lg text-dark-muted">
                    Whoa there! You're making requests too quickly.
                </p>
                <p class="text-sm text-dark-muted">
                    Please slow down and try again in a moment. This helps us maintain quality service for everyone.
                </p>
                @if(isset($exception) && method_exists($exception, 'getHeaders'))
                    @php
                        $retryAfter = $exception->getHeaders()['Retry-After'] ?? null;
                    @endphp
                    @if($retryAfter)
                        <div class="mt-4 p-4 bg-dark-surface border border-dark-border rounded-lg">
                            <p class="text-sm text-dark-muted">
                                Please wait <span class="text-amber-500 font-bold">{{ $retryAfter }}</span> seconds before trying again.
                            </p>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Rate Limit Info -->
            <div class="mb-8">
                <div class="max-w-md mx-auto p-6 bg-dark-surface rounded-lg border border-dark-border">
                    <div class="flex items-center justify-center gap-4 mb-4">
                        <div class="relative w-16 h-16">
                            <svg class="w-16 h-16 transform -rotate-90">
                                <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="4" fill="none" class="text-dark-border"/>
                                <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="4" fill="none" class="text-amber-500"
                                        stroke-dasharray="175.93" stroke-dashoffset="88" stroke-linecap="round"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm text-dark-text font-medium mb-2">Rate Limit Exceeded</p>
                    <p class="text-xs text-dark-muted">
                        You've exceeded the maximum number of requests allowed in this time period.
                    </p>
                </div>
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
                <button onclick="setTimeout(() => location.reload(), 3000)"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-amber-500 to-yellow-500 rounded-lg text-white font-semibold hover:shadow-lg hover:shadow-amber-500/50 transition-all duration-200 group">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Wait & Retry
                    <svg class="w-5 h-5 ml-2 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>

            <!-- Tips -->
            <div class="mt-12 pt-8 border-t border-dark-border">
                <p class="text-sm text-dark-muted mb-6">Pro Tips</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="p-4 bg-dark-surface rounded-lg border border-dark-border text-left">
                        <div class="flex items-start gap-3">
                            <div class="text-xl">⏱️</div>
                            <div>
                                <p class="text-sm font-medium text-dark-text mb-1">Slow Down</p>
                                <p class="text-xs text-dark-muted">Space out your requests</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-dark-surface rounded-lg border border-dark-border text-left">
                        <div class="flex items-start gap-3">
                            <div class="text-xl">💾</div>
                            <div>
                                <p class="text-sm font-medium text-dark-text mb-1">Cache Results</p>
                                <p class="text-xs text-dark-muted">Reduce repeated calls</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-dark-surface rounded-lg border border-dark-border text-left">
                        <div class="flex items-start gap-3">
                            <div class="text-xl">📧</div>
                            <div>
                                <p class="text-sm font-medium text-dark-text mb-1">Need More?</p>
                                <p class="text-xs text-dark-muted">Contact us for limits</p>
                            </div>
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
