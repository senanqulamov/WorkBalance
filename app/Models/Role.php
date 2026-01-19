<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    /**
     * Get the permissions for this role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role')
            ->withTimestamps();
    }

    /**
     * Get the users with this role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user')
            ->withTimestamps();
    }

    /**
     * Check if a role has a specific permission.
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions()->where('name', $permissionName)->exists();
    }

    /**
     * Attach permissions to role.
     */
    public function givePermissionTo(...$permissions): void
    {
        $permissions = $this->getAllPermissions($permissions);

        if ($permissions->isEmpty()) {
            return;
        }

        $this->permissions()->syncWithoutDetaching($permissions);
    }

    /**
     * Remove permissions from role.
     */
    public function revokePermissionTo(...$permissions): void
    {
        $permissions = $this->getAllPermissions($permissions);
        $this->permissions()->detach($permissions);
    }

    /**
     * Get all permissions from names or IDs.
     */
    protected function getAllPermissions(array $permissions)
    {
        return Permission::query()
            ->whereIn('name', $permissions)
            ->orWhereIn('id', $permissions)
            ->get();
    }
}
