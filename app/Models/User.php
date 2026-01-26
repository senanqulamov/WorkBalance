<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'is_active',
        'phone',
        'timezone',
        'language',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'verified_seller' => 'boolean',
            'is_active' => 'boolean',
            'payment_terms' => 'json',
            'credit_limit' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'rating' => 'decimal:2',
            'supplier_approved_at' => 'datetime',
            'verified_at' => 'datetime',
            'seller_id' => 'integer',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // REMOVED: Old procurement relationships (orders, products, markets, quotes, rfqs, etc.)
    // These have been replaced by WorkBalance/HumanOps relationships below

    // Role and Permission Relationships

    /**
     * Get the roles for this user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withTimestamps();
    }

    /**
     * Get cached permission names for this user.
     *
     * Important: permissions() currently hits the database (roles()->with('permissions')->get()).
     * Calling it repeatedly (e.g., in layouts/components) can easily cause very slow page loads.
     */
    public function cachedPermissionNames(int $ttlSeconds = 60): array
    {
        // Admin path stays fast and avoids any DB work.
        if ($this->is_admin) {
            return ['*'];
        }

        return cache()->remember(
            'auth:permission_names:user:'.$this->id,
            now()->addSeconds($ttlSeconds),
            function () {
                // If the user is admin via pivot role, short-circuit.
                if ($this->roles()->where('name', 'admin')->exists()) {
                    return ['*'];
                }

                return $this->roles()
                    ->with('permissions:id,name')
                    ->get()
                    ->pluck('permissions')
                    ->flatten()
                    ->pluck('name')
                    ->unique()
                    ->values()
                    ->all();
            }
        );
    }

    /**
     * Get all permissions for this user through roles.
     */
    public function permissions()
    {
        return $this->roles()->with('permissions')->get()
            ->pluck('permissions')
            ->flatten()
            ->unique('id');
    }

    // Role Check Methods

    public function isAdmin(): bool
    {
        return $this->roles()->where('name', 'admin')->exists() || $this->is_admin;
    }

    public function isBuyer(): bool
    {
        // Pivot roles are source of truth; keep legacy fallback during transition.
        return $this->roles()->where('name', 'buyer')->exists() || $this->is_buyer || $this->role === 'buyer';
    }

    public function isSeller(): bool
    {
        return $this->roles()->where('name', 'seller')->exists() || $this->is_seller;
    }

    public function isSupplier(): bool
    {
        return $this->roles()->where('name', 'supplier')->exists() || $this->is_supplier;
    }

    /**
     * Check if user has a permission.
     */
    public function hasPermission(string $permissionName): bool
    {
        // Fast-path admin checks.
        if ($this->isAdmin()) {
            return true; // Admin has all permissions
        }

        $names = $this->cachedPermissionNames();

        return in_array('*', $names, true) || in_array($permissionName, $names, true);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(...$permissions): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has specific role.
     *
     * Users can have multiple roles; this must use pivot roles.
     */
    public function hasRole(string $role): bool
    {
        $role = strtolower($role);

        // Pivot roles first
        if ($this->roles()->where('name', $role)->exists()) {
            return true;
        }

        // Legacy fallback during transition
        return match ($role) {
            'buyer' => (bool) $this->is_buyer,
            'seller' => (bool) $this->is_seller,
            'supplier' => (bool) $this->is_supplier,
            'admin' => (bool) $this->is_admin,
            default => false,
        };
    }

    /**
     * Get role names for this user.
     */
    public function getRoles(): array
    {
        $roleNames = $this->roles()->pluck('name')->all();

        // Legacy fallback (kept only for safety while migrating)
        if (empty($roleNames)) {
            $roles = [];
            if ($this->is_buyer) {
                $roles[] = 'buyer';
            }
            if ($this->is_seller) {
                $roles[] = 'seller';
            }
            if ($this->is_supplier) {
                $roles[] = 'supplier';
            }
            if ($this->is_admin) {
                $roles[] = 'admin';
            }
            return $roles;
        }

        return $roleNames;
    }

    // Address & Contact Methods

    public function getFullAddress(): string
    {
        $parts = array_filter([
            $this->address_line1,
            $this->address_line2,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    public function hasCompleteAddress(): bool
    {
        return ! empty($this->address_line1)
            && ! empty($this->city)
            && ! empty($this->country);
    }

    public function getDisplayName(): string
    {
        return $this->company_name ?? $this->name;
    }

    // Supplier-Specific Methods

    public function canSupply(): bool
    {
        return $this->is_supplier
            && $this->supplier_status === 'active'
            && $this->is_active;
    }

    public function approveAsSupplier(): void
    {
        $this->update([
            'supplier_status' => 'active',
            'supplier_approved_at' => now(),
        ]);
    }

    public function blockSupplier(): void
    {
        $this->update([
            'supplier_status' => 'blocked',
        ]);
    }

    public function hasAribaIntegration(): bool
    {
        return ! empty($this->ariba_network_id);
    }

    // Performance Methods

    public function updateRating(float $newRating): void
    {
        // Calculate new average rating
        $totalRatings = $this->completed_orders;
        $currentTotal = $this->rating * max($totalRatings - 1, 0);
        $newAverage = ($currentTotal + $newRating) / max($totalRatings, 1);

        $this->update(['rating' => round($newAverage, 2)]);
    }

    public function incrementOrderCount(string $status = 'total'): void
    {
        match ($status) {
            'completed' => $this->increment('completed_orders'),
            'cancelled' => $this->increment('cancelled_orders'),
            default => $this->increment('total_orders'),
        };
    }

    public function getSuccessRate(): float
    {
        if (!$this->total_orders || $this->total_orders === 0) {
            return 0.0;
        }

        $completed = $this->completed_orders ?? 0;

        return round(($completed / $this->total_orders) * 100, 2);
    }

    // Scopes - Updated for WorkBalance

    public function scopeEmployees($query)
    {
        return $query->whereHas('roles', fn ($q) => $q->where('name', 'employee'));
    }

    public function scopeManagers($query)
    {
        return $query->whereHas('roles', fn ($q) => $q->where('name', 'manager'));
    }

    public function scopeOwners($query)
    {
        return $query->whereHas('roles', fn ($q) => $q->where('name', 'owner'));
    }

    public function scopeWithRole($query, string $role)
    {
        return $query->whereHas('roles', fn ($q) => $q->where('name', strtolower($role)));
    }

    public function getDashboardRouteName(): string
    {
        // WorkBalance routing: admin/manager → HumanOps, employee → WorkBalance
        if ($this->isAdmin() || $this->canAccessHumanOps()) {
            return 'dashboard'; // HumanOps Intelligence
        }

        if ($this->isEmployee() || $this->canAccessWorkBalance()) {
            return 'workbalance.dashboard'; // Employee WorkBalance
        }

        // Fallback
        return 'dashboard';
    }

    // REMOVED: seller(), workers(), workerMarkets() - old procurement relationships

    /**
     * ========================================
     * WORKBALANCE / HUMANOPS RELATIONSHIPS
     * ========================================
     */

    /**
     * Get the teams this user belongs to.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user');
    }

    /**
     * Get the wellbeing cycles for this employee.
     * PRIVACY: Only accessible by the employee themselves.
     */
    public function wellbeingCycles(): HasMany
    {
        return $this->hasMany(WellbeingCycle::class, 'employee_id');
    }

    /**
     * Get the emotional check-ins for this employee.
     * PRIVACY: Never accessible to employers.
     */
    public function checkIns(): HasMany
    {
        return $this->hasMany(EmotionalCheckIn::class, 'employee_id');
    }

    /**
     * Get the therapeutic sessions for this employee.
     * PRIVACY: Personal content never exposed to employers.
     */
    public function therapeuticSessions(): HasMany
    {
        return $this->hasMany(TherapeuticSession::class, 'employee_id');
    }

    /**
     * Get the reflections for this employee.
     * PRIVACY: Completely private to the employee.
     */
    public function reflections(): HasMany
    {
        return $this->hasMany(Reflection::class, 'employee_id');
    }

    /**
     * Get teams managed by this user.
     */
    public function managedTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'manager_id');
    }

    /**
     * ========================================
     * ROLE HELPERS FOR WORKBALANCE
     * ========================================
     */

    /**
     * Check if user is an employee (has WorkBalance access).
     */
    public function isEmployee(): bool
    {
        return $this->hasRole('employee') || $this->teams()->exists();
    }

    /**
     * Check if user is a manager (has HumanOps access).
     */
    public function isManager(): bool
    {
        return $this->hasRole('manager') || $this->managedTeams()->exists();
    }

    /**
     * Check if user is an owner (full HumanOps access).
     */
    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    /**
     * Check if user has access to HumanOps Intelligence.
     */
    public function canAccessHumanOps(): bool
    {
        return $this->isAdmin() || $this->isManager() || $this->isOwner();
    }

    /**
     * Check if user has access to WorkBalance employee area.
     */
    public function canAccessWorkBalance(): bool
    {
        return $this->isEmployee() || $this->isAdmin();
    }
}
