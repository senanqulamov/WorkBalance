<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Order::class => \App\Policies\OrderPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register permissions as gates
        try {
            foreach (Permission::all() as $permission) {
                Gate::define($permission->name, function ($user) use ($permission) {
                    return $user->hasPermission($permission->name);
                });
            }
        } catch (\Exception $e) {
            // Permissions table might not exist yet during migration
        }

        // Admin has all permissions
        Gate::before(function ($user, $ability) {
            return $user->isAdmin() ? true : null;
        });
    }
}
