<?php

/**
 * Error Page Testing Routes
 *
 * Add these routes to your routes/web.php file to test error pages
 * ONLY USE IN DEVELOPMENT ENVIRONMENT
 *
 * Usage: Visit /test-errors to see all error pages
 */

// Only enable in local/development environment
if (app()->environment('local')) {

    // Error page testing dashboard
    Route::get('/test-errors', function () {
        $errors = [
            401 => 'Unauthorized - Authentication Required',
            403 => 'Forbidden - Access Denied',
            404 => 'Not Found - Page Does Not Exist',
            419 => 'Session Expired - CSRF Token Invalid',
            429 => 'Too Many Requests - Rate Limited',
            500 => 'Internal Server Error - Server Exception',
            503 => 'Service Unavailable - Maintenance Mode',
        ];

        return view('test-errors', compact('errors'));
    })->name('test.errors');

    // Individual error page routes
    Route::get('/test-errors/{code}', function ($code) {
        $messages = [
            401 => 'You need to be logged in to access this resource.',
            403 => 'This RFQ is not open for quotes.',
            404 => 'The page you are looking for could not be found.',
            419 => 'Your session has expired. Please refresh and try again.',
            429 => 'You are making too many requests. Please slow down.',
            500 => 'An unexpected error occurred on the server.',
            503 => 'The application is currently in maintenance mode.',
        ];

        abort($code, $messages[$code] ?? 'Test error message');
    })->where('code', '[0-9]+')->name('test.error.show');
}
