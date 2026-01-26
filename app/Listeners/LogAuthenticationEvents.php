<?php

namespace App\Listeners;

use App\Models\ActivitySignal;
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
        ActivitySignal::create([
            'team_id' => optional($event->user->teams()->first())->id,
            'action_type' => 'user_login',
            'description' => "User logged in",
            'context' => 'authentication',
            'occurred_at' => now(),
            'metadata' => [
                'email' => $event->user->email,
                'guard' => $event->guard,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
        ]);
    }

    /**
     * Handle user logout events.
     */
    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            ActivitySignal::create([
                'team_id' => optional($event->user->teams()->first())->id,
                'action_type' => 'user_logout',
                'description' => "User logged out",
                'context' => 'authentication',
                'occurred_at' => now(),
                'metadata' => [
                    'email' => $event->user->email,
                    'guard' => $event->guard,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ],
            ]);
        }
    }

    /**
     * Handle user registration events.
     */
    public function handleRegistered(Registered $event): void
    {
        ActivitySignal::create([
            'team_id' => null, // New user may not have team yet
            'action_type' => 'user_registered',
            'description' => "New user registered",
            'context' => 'authentication',
            'occurred_at' => now(),
            'metadata' => [
                'email' => $event->user->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
        ]);
    }

    /**
     * Handle failed login attempts.
     */
    public function handleFailed(Failed $event): void
    {
        ActivitySignal::create([
            'team_id' => null,
            'action_type' => 'login_failed',
            'description' => "Failed login attempt",
            'context' => 'authentication',
            'occurred_at' => now(),
            'metadata' => [
                'email' => $event->credentials['email'] ?? 'unknown',
                'guard' => $event->guard,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
        ]);
    }
}
