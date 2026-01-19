<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error Pages Testing | {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-dark-bg text-dark-text">
    <div class="min-h-screen py-12 px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-dark-text mb-4">
                    Error Pages Testing
                </h1>
                <p class="text-dark-muted">
                    Click on any error code below to view the corresponding error page
                </p>
            </div>

            <!-- Error Pages Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                @foreach($errors as $code => $description)
                    <a href="{{ route('test.error.show', $code) }}"
                       class="block p-6 bg-dark-surface border border-dark-border rounded-lg hover:border-blue-500 transition-all duration-200 group">
                        <div class="flex items-start justify-between mb-4">
                            <span class="text-3xl font-bold bg-gradient-to-r from-blue-500 to-cyan-500 bg-clip-text text-transparent">
                                {{ $code }}
                            </span>
                            <svg class="w-5 h-5 text-dark-muted group-hover:text-blue-500 group-hover:translate-x-1 transition-all"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                        <p class="text-sm text-dark-muted">{{ $description }}</p>
                    </a>
                @endforeach
            </div>

            <!-- Info Box -->
            <div class="bg-dark-surface border border-dark-border rounded-lg p-6">
                <div class="flex items-start gap-4">
                    <svg class="w-6 h-6 text-blue-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-dark-text mb-2">Testing Information</h3>
                        <ul class="space-y-2 text-sm text-dark-muted">
                            <li>• These routes are only available in <strong class="text-dark-text">local/development</strong> environment</li>
                            <li>• Each error page features modern 2025 design with animations</li>
                            <li>• All pages are fully responsive and dark theme compatible</li>
                            <li>• Error pages will automatically be used by Laravel when errors occur</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8 flex justify-center gap-4">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-6 py-3 bg-dark-surface border border-dark-border rounded-lg text-dark-text hover:bg-dark-border transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Dashboard
                </a>
            </div>

            <!-- Footer -->
            <div class="mt-12 pt-8 border-t border-dark-border text-center">
                <p class="text-sm text-dark-muted">
                    Error pages are located in <code class="px-2 py-1 bg-dark-surface rounded">resources/views/errors/</code>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
