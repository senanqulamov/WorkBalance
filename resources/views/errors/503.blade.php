<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>503 - Service Unavailable | {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css'])
    <style>
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-25px); }
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
        .animate-bounce-slow {
            animation: bounce-slow 2s ease-in-out infinite;
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
                    <div class="w-64 h-64 bg-orange-500/10 rounded-full blur-3xl animate-pulse-glow"></div>
                </div>
                <div class="relative animate-bounce-slow">
                    <svg class="w-48 h-48 mx-auto text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Error Code -->
            <div class="mb-6">
                <h1 class="text-8xl font-bold bg-gradient-to-r from-orange-500 to-red-500 bg-clip-text text-transparent mb-2">
                    503
                </h1>
                <h2 class="text-3xl font-semibold text-dark-text mb-4">
                    Service Unavailable
                </h2>
            </div>

            <!-- Error Message -->
            <div class="mb-8 space-y-4">
                <p class="text-lg text-dark-muted">
                    We're currently performing maintenance.
                </p>
                <p class="text-sm text-dark-muted">
                    We'll be back up and running shortly. Thank you for your patience!
                </p>
                @if(isset($exception) && method_exists($exception, 'wantsJson'))
                    <div class="mt-4 p-4 bg-dark-surface border border-dark-border rounded-lg">
                        <p class="text-sm text-dark-muted">
                            Expected to be back at: <span class="text-dark-text font-medium">{{ $exception->retryAfter ?? 'Soon' }}</span>
                        </p>
                    </div>
                @endif
            </div>

            <!-- Progress Indicator -->
            <div class="mb-8">
                <div class="max-w-md mx-auto">
                    <div class="h-2 bg-dark-surface rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-orange-500 to-red-500 rounded-full animate-pulse" style="width: 60%"></div>
                    </div>
                    <p class="text-xs text-dark-muted mt-2">Maintenance in progress...</p>
                </div>
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
            </div>

            <!-- Status Updates -->
            <div class="mt-12 pt-8 border-t border-dark-border">
                <p class="text-sm text-dark-muted mb-6">What's happening?</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="p-4 bg-dark-surface rounded-lg border border-dark-border">
                        <div class="text-2xl mb-2">🔧</div>
                        <div class="text-xs text-dark-muted">System Upgrade</div>
                    </div>
                    <div class="p-4 bg-dark-surface rounded-lg border border-dark-border">
                        <div class="text-2xl mb-2">⚡</div>
                        <div class="text-xs text-dark-muted">Performance Boost</div>
                    </div>
                    <div class="p-4 bg-dark-surface rounded-lg border border-dark-border">
                        <div class="text-2xl mb-2">🛡️</div>
                        <div class="text-xs text-dark-muted">Security Update</div>
                    </div>
                </div>
                <div class="flex justify-center gap-6">
                    <a href="mailto:support@{{ config('app.domain', 'example.com') }}" class="text-sm text-dark-muted hover:text-dark-text transition-colors">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
