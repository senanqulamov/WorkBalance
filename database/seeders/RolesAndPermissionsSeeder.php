<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔐 Setting up roles and permissions...');

        // Create Permissions
        $permissions = $this->createPermissions();
        $this->command->info("✅ Created " . count($permissions) . " permissions");

        // Create Roles
        $roles = $this->createRoles();
        $this->command->info("✅ Created " . count($roles) . " roles");

        // Assign Permissions to Roles
        $this->assignPermissionsToRoles($roles, $permissions);
        $this->command->info('✅ Assigned permissions to roles');

        // Create Admin User
            $this->createAdminUser($roles);
    }

    private function createPermissions(): array
    {
        $permissionsList = [
            // Dashboard
            ['name' => 'view_dashboard', 'display_name' => 'View Dashboard', 'group' => 'Dashboard'],

            // Users
            ['name' => 'view_users', 'display_name' => 'View Users', 'group' => 'Users'],
            ['name' => 'create_users', 'display_name' => 'Create Users', 'group' => 'Users'],
            ['name' => 'edit_users', 'display_name' => 'Edit Users', 'group' => 'Users'],
            ['name' => 'delete_users', 'display_name' => 'Delete Users', 'group' => 'Users'],

            // Products
            ['name' => 'view_products', 'display_name' => 'View Products', 'group' => 'Products'],
            ['name' => 'create_products', 'display_name' => 'Create Products', 'group' => 'Products'],
            ['name' => 'edit_products', 'display_name' => 'Edit Products', 'group' => 'Products'],
            ['name' => 'delete_products', 'display_name' => 'Delete Products', 'group' => 'Products'],

            // Orders
            ['name' => 'view_orders', 'display_name' => 'View Orders', 'group' => 'Orders'],
            ['name' => 'create_orders', 'display_name' => 'Create Orders', 'group' => 'Orders'],
            ['name' => 'edit_orders', 'display_name' => 'Edit Orders', 'group' => 'Orders'],
            ['name' => 'delete_orders', 'display_name' => 'Delete Orders', 'group' => 'Orders'],

            // RFQs
            ['name' => 'view_rfqs', 'display_name' => 'View RFQs', 'group' => 'RFQ'],
            ['name' => 'create_rfqs', 'display_name' => 'Create RFQs', 'group' => 'RFQ'],
            ['name' => 'edit_rfqs', 'display_name' => 'Edit RFQs', 'group' => 'RFQ'],
            ['name' => 'delete_rfqs', 'display_name' => 'Delete RFQs', 'group' => 'RFQ'],
            ['name' => 'submit_quotes', 'display_name' => 'Submit Quotes', 'group' => 'RFQ'],
            ['name' => 'view_quotes', 'display_name' => 'View Quotes', 'group' => 'RFQ'],
            ['name' => 'edit_quotes', 'display_name' => 'Edit Quotes', 'group' => 'RFQ'],

            // Markets
            ['name' => 'view_markets', 'display_name' => 'View Markets', 'group' => 'Markets'],
            ['name' => 'create_markets', 'display_name' => 'Create Markets', 'group' => 'Markets'],
            ['name' => 'edit_markets', 'display_name' => 'Edit Markets', 'group' => 'Markets'],
            ['name' => 'delete_markets', 'display_name' => 'Delete Markets', 'group' => 'Markets'],

            // Supplier Portal
            ['name' => 'access_supplier_portal', 'display_name' => 'Access Supplier Portal', 'group' => 'Supplier'],
            ['name' => 'manage_supplier_invitations', 'display_name' => 'Manage Invitations', 'group' => 'Supplier'],

            // Settings
            ['name' => 'view_settings', 'display_name' => 'View Settings', 'group' => 'Settings'],
            ['name' => 'edit_settings', 'display_name' => 'Edit Settings', 'group' => 'Settings'],

            // Logs
            ['name' => 'view_logs', 'display_name' => 'View Logs', 'group' => 'Logs'],

            // Privacy/Roles
            ['name' => 'manage_roles', 'display_name' => 'Manage Roles', 'group' => 'Privacy'],
            ['name' => 'manage_permissions', 'display_name' => 'Manage Permissions', 'group' => 'Privacy'],

            // Admin Panel Enhancements
            ['name' => 'view_health', 'display_name' => 'View System Health', 'group' => 'Admin'],
            ['name' => 'view_notifications', 'display_name' => 'View Notifications', 'group' => 'Admin'],
            ['name' => 'use_search', 'display_name' => 'Use Global Search', 'group' => 'Admin'],
            ['name' => 'view_monitoring', 'display_name' => 'View RFQ Monitoring', 'group' => 'Admin'],
            ['name' => 'manage_sla', 'display_name' => 'Manage SLA', 'group' => 'Admin'],
            ['name' => 'view_feature_flags', 'display_name' => 'View Feature Flags', 'group' => 'Admin'],
            ['name' => 'manage_feature_flags', 'display_name' => 'Manage Feature Flags', 'group' => 'Admin'],
        ];

        $permissions = [];
        foreach ($permissionsList as $perm) {
            $permissions[$perm['name']] = Permission::firstOrCreate(
                ['name' => $perm['name']],
                $perm
            );
        }

        return $permissions;
    }

    private function createRoles(): array
    {
        $rolesList = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system access - can do everything',
                'is_system' => true,
            ],
            [
                'name' => 'buyer',
                'display_name' => 'Buyer',
                'description' => 'Can create RFQs, view quotes, and place orders',
                'is_system' => true,
            ],
            [
                'name' => 'seller',
                'display_name' => 'Seller',
                'description' => 'Can manage markets and sell products',
                'is_system' => true,
            ],
            [
                'name' => 'supplier',
                'display_name' => 'Supplier',
                'description' => 'Can respond to RFQs and submit quotes',
                'is_system' => true,
            ],
            [
                'name' => 'market_worker',
                'display_name' => 'Market Worker',
                'description' => 'Worker account managed by a seller; can be assigned to one or more markets',
                'is_system' => true,
            ],
        ];

        $roles = [];
        foreach ($rolesList as $role) {
            $roles[$role['name']] = Role::firstOrCreate(
                ['name' => $role['name']],
                $role
            );
        }

        return $roles;
    }

    private function assignPermissionsToRoles(array $roles, array $permissions): void
    {
        // Admin - all permissions
        $roles['admin']->permissions()->sync(array_map(fn ($perm) => $perm->id, $permissions));

        // Buyer permissions
        $roles['buyer']->permissions()->sync([
            $permissions['view_dashboard']->id,
            $permissions['view_products']->id,
            $permissions['view_orders']->id,
            $permissions['create_orders']->id,
            $permissions['view_rfqs']->id,
            $permissions['create_rfqs']->id,
            $permissions['edit_rfqs']->id,
            $permissions['view_quotes']->id,
            $permissions['view_markets']->id,
            $permissions['view_settings']->id,
            $permissions['view_logs']->id,
        ]);

        // Seller permissions
        $roles['seller']->permissions()->sync([
            $permissions['view_dashboard']->id,
            $permissions['view_products']->id,
            $permissions['create_products']->id,
            $permissions['edit_products']->id,
            $permissions['view_orders']->id,
            $permissions['view_markets']->id,
            $permissions['create_markets']->id,
            $permissions['edit_markets']->id,
            $permissions['view_settings']->id,
            $permissions['view_logs']->id,
        ]);

        // Supplier permissions
        $roles['supplier']->permissions()->sync([
            $permissions['view_dashboard']->id,
            $permissions['view_products']->id,
            $permissions['view_markets']->id,
            $permissions['view_rfqs']->id,
            $permissions['submit_quotes']->id,
            $permissions['view_quotes']->id,
            $permissions['edit_quotes']->id,
            $permissions['view_orders']->id,
            $permissions['create_orders']->id,
            $permissions['access_supplier_portal']->id,
            $permissions['manage_supplier_invitations']->id,
            $permissions['view_settings']->id,
        ]);
    }

    private function createAdminUser(array $roles): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@dpanel.test'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin' => true,
                'is_buyer' => true,
                'is_seller' => true,
                'is_supplier' => true,
                'role' => 'admin',
                'is_active' => true,
                // Complete profile for admin
                'company_name' => 'DPanel Administration',
                'tax_id' => 'TAX-ADMIN001',
                'business_type' => 'Corporation',
                'business_description' => 'System administration and management',
                'phone' => '+1-555-0100',
                'mobile' => '+1-555-0101',
                'website' => 'https://dpanel.test',
                'address_line1' => '123 Admin Street',
                'address_line2' => 'Suite 100',
                'city' => 'Tech City',
                'state' => 'California',
                'postal_code' => '90210',
                'country' => 'United States',
                'rating' => 5.0,
                'total_orders' => 0,
                'completed_orders' => 0,
                'cancelled_orders' => 0,
            ]
        );

        // Ensure admin role is attached
        if (!$admin->roles()->where('name', 'admin')->exists()) {
            $admin->roles()->attach($roles['admin']);
        }

        $this->command->info('✅ Admin user: admin@dpanel.test / password');
    }
}
