<?php

namespace App\Listeners;

use App\Models\Log;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Failed;

class LogAuthenticationEvents
{
    /**
     * Handle user login events.
     */
    public function handleLogin(Login $event): void
    {
        Log::create([
            'user_id' => $event->user->id,
            'type' => 'auth',
            'action' => 'auth.login',
            'message' => "User {$event->user->name} logged in",
            'metadata' => [
                'email' => $event->user->email,
                'guard' => $event->guard,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Handle user logout events.
     */
    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            Log::create([
                'user_id' => $event->user->id,
                'type' => 'auth',
                'action' => 'auth.logout',
                'message' => "User {$event->user->name} logged out",
                'metadata' => [
                    'email' => $event->user->email,
                    'guard' => $event->guard,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        }
    }

    /**
     * Handle user registration events.
     */
    public function handleRegistered(Registered $event): void
    {
        Log::create([
            'user_id' => $event->user->id,
            'type' => 'auth',
            'action' => 'auth.register',
            'message' => "New user {$event->user->name} registered",
            'metadata' => [
                'email' => $event->user->email,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Handle failed login attempts.
     */
    public function handleFailed(Failed $event): void
    {
        Log::create([
            'user_id' => null,
            'type' => 'auth',
            'action' => 'auth.failed',
            'message' => "Failed login attempt for {$event->credentials['email']}",
            'metadata' => [
                'email' => $event->credentials['email'],
                'guard' => $event->guard,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
