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
        // Role system (admin, employer, employee)
        'role',
        'is_admin',
        // WorkBalance fields
        'department_id',
        'job_title',
        'hire_date',
        // Business info
        'company_name',
        'tax_id',
        'business_type',
        'business_description',
        // Contact info
        'phone',
        'mobile',
        'website',
        // Address
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        // Supplier fields
        'supplier_code',
        'duns_number',
        'ariba_network_id',
        'payment_terms',
        'currency',
        'credit_limit',
        'supplier_status',
        'supplier_approved_at',
        // Seller fields
        'commission_rate',
        'verified_seller',
        'verified_at',
        // Performance
        'rating',
        'total_orders',
        'completed_orders',
        'cancelled_orders',
        // Status
        'is_active',
        'notes',
        // Seller-owned worker
        'seller_id',
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
            'hire_date' => 'date',
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

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Orders where this user is the buyer
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    /**
     * Products supplied by this user (if supplier)
     */
    public function suppliedProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }

    /**
     * Markets owned by this user (if seller)
     */
    public function markets(): HasMany
    {
        return $this->hasMany(Market::class, 'user_id');
    }

    /**
     * Supplier invitations received by this supplier
     */
    public function supplierInvitations(): HasMany
    {
        return $this->hasMany(SupplierInvitation::class, 'supplier_id');
    }

    /**
     * Quotes submitted by this supplier
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'supplier_id');
    }

    /**
     * RFQs created by this buyer
     */
    public function rfqs(): HasMany
    {
        return $this->hasMany(Request::class, 'buyer_id');
    }

    /**
     * Check-ins for WorkBalance
     */
    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    /**
     * Well-being tool usage records
     */
    public function wellBeingToolUsages(): HasMany
    {
        return $this->hasMany(WellBeingToolUsage::class);
    }

    /**
     * Department this user belongs to
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // New Role System (3 roles: admin, employer, employee)

    /**
     * Check if user is an employer (can access HumanOps)
     */
    public function isEmployer(): bool
    {
        return $this->role === 'employer' || $this->is_admin;
    }

    /**
     * Check if user is an employee (can access WorkBalance)
     */
    public function isEmployee(): bool
    {
        return $this->role === 'employee' || $this->is_admin;
    }

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

    // Scopes

    public function scopeSuppliers($query)
    {
        return $query->whereHas('roles', fn ($q) => $q->where('name', 'supplier'));
    }

    public function scopeActiveSuppliers($query)
    {
        return $query->whereHas('roles', fn ($q) => $q->where('name', 'supplier'))
            ->where('supplier_status', 'active')
            ->where('is_active', true);
    }

    public function scopeSellers($query)
    {
        return $query->whereHas('roles', fn ($q) => $q->where('name', 'seller'));
    }

    public function scopeVerifiedSellers($query)
    {
        return $query->whereHas('roles', fn ($q) => $q->where('name', 'seller'))
            ->where('verified_seller', true);
    }

    public function scopeBuyers($query)
    {
        return $query->whereHas('roles', fn ($q) => $q->where('name', 'buyer'));
    }

    public function scopeWithRole($query, string $role)
    {
        return match (strtolower($role)) {
            'supplier' => $query->suppliers(),
            'seller' => $query->sellers(),
            'buyer' => $query->buyers(),
            default => $query,
        };
    }

    public function getDashboardRouteName(): string
    {
        // Priority: admin > supplier > seller > buyer > generic
        if ($this->isAdmin()) {
            return 'dashboard';
        }

        if ($this->isSupplier()) {
            return 'supplier.dashboard';
        }

        if ($this->isSeller()) {
            return 'seller.dashboard';
        }

        if ($this->isBuyer()) {
            return 'buyer.dashboard';
        }

        // Fallback to global dashboard
        return 'dashboard';
    }

    /**
     * If this user is a worker, this references the seller owner account.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Seller-owned worker accounts (Amazon-style).
     */
    public function workers(): HasMany
    {
        return $this->hasMany(User::class, 'seller_id');
    }

    /**
     * Markets this worker is assigned to (market_users pivot).
     */
    public function workerMarkets(): BelongsToMany
    {
        return $this->belongsToMany(Market::class, 'market_users')
            ->withTimestamps();
    }
}
