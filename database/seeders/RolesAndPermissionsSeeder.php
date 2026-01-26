<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔐 Seeding Roles and Permissions...');
        $this->command->newLine();

        // Create Permissions
        $this->command->info('Creating permissions...');
        $permissions = $this->createPermissions();
        $this->command->info("  ✓ Created {$permissions->count()} permissions");
        $this->command->newLine();

        // Create Roles
        $this->command->info('Creating roles...');
        $roles = $this->createRoles($permissions);
        $this->command->info("  ✓ Created {$roles->count()} roles");
        $this->command->newLine();

        $this->command->info('✅ Roles and permissions seeded successfully!');
    }

    protected function createPermissions()
    {
        $permissionsData = [
            // Dashboard & Core
            ['name' => 'view_dashboard', 'display_name' => 'View Dashboard', 'group' => 'dashboard', 'description' => 'Access main dashboard'],
            ['name' => 'view_users', 'display_name' => 'View Users', 'group' => 'users', 'description' => 'View user list and details'],
            ['name' => 'create_users', 'display_name' => 'Create Users', 'group' => 'users', 'description' => 'Create new users'],
            ['name' => 'edit_users', 'display_name' => 'Edit Users', 'group' => 'users', 'description' => 'Edit existing users'],
            ['name' => 'delete_users', 'display_name' => 'Delete Users', 'group' => 'users', 'description' => 'Delete users'],

            // Settings
            ['name' => 'view_settings', 'display_name' => 'View Settings', 'group' => 'settings', 'description' => 'Access settings'],
            ['name' => 'manage_settings', 'display_name' => 'Manage Settings', 'group' => 'settings', 'description' => 'Modify system settings'],
            ['name' => 'manage_feature_flags', 'display_name' => 'Manage Feature Flags', 'group' => 'settings', 'description' => 'Toggle feature flags'],

            // Notifications
            ['name' => 'view_notifications', 'display_name' => 'View Notifications', 'group' => 'notifications', 'description' => 'View notifications'],

            // Logs (Admin Only)
            ['name' => 'view_logs', 'display_name' => 'View Logs', 'group' => 'admin', 'description' => 'View system activity logs'],
            ['name' => 'delete_logs', 'display_name' => 'Delete Logs', 'group' => 'admin', 'description' => 'Delete log entries'],

            // Roles & Permissions (Admin Only)
            ['name' => 'manage_roles', 'display_name' => 'Manage Roles', 'group' => 'admin', 'description' => 'Create, edit, and delete roles'],
            ['name' => 'manage_permissions', 'display_name' => 'Manage Permissions', 'group' => 'admin', 'description' => 'Assign permissions to roles'],
            ['name' => 'assign_roles', 'display_name' => 'Assign Roles', 'group' => 'admin', 'description' => 'Assign roles to users'],

            // HumanOps (Employer Only)
            ['name' => 'view_humanops', 'display_name' => 'View HumanOps', 'group' => 'humanops', 'description' => 'Access HumanOps Intelligence'],
            ['name' => 'view_humanops_overview', 'display_name' => 'View Organization Overview', 'group' => 'humanops', 'description' => 'View organization-level insights'],
            ['name' => 'view_humanops_departments', 'display_name' => 'View Department Insights', 'group' => 'humanops', 'description' => 'View department-level data'],
            ['name' => 'view_humanops_risk_signals', 'display_name' => 'View Risk Signals', 'group' => 'humanops', 'description' => 'View detected risks'],
            ['name' => 'view_humanops_recommendations', 'display_name' => 'View Recommendations', 'group' => 'humanops', 'description' => 'View suggested actions'],
            ['name' => 'view_humanops_trends', 'display_name' => 'View Trends', 'group' => 'humanops', 'description' => 'View historical trends'],
            ['name' => 'acknowledge_recommendations', 'display_name' => 'Acknowledge Recommendations', 'group' => 'humanops', 'description' => 'Mark recommendations as reviewed'],

            // WorkBalance (Employee Only)
            ['name' => 'use_workbalance', 'display_name' => 'Use WorkBalance', 'group' => 'workbalance', 'description' => 'Access WorkBalance wellness app'],
            ['name' => 'submit_checkins', 'display_name' => 'Submit Check-ins', 'group' => 'workbalance', 'description' => 'Submit daily check-ins'],
            ['name' => 'view_personal_insights', 'display_name' => 'View Personal Insights', 'group' => 'workbalance', 'description' => 'View personal well-being insights'],
            ['name' => 'use_wellbeing_tools', 'display_name' => 'Use Well-being Tools', 'group' => 'workbalance', 'description' => 'Access breathing, grounding tools'],
            ['name' => 'view_personal_trends', 'display_name' => 'View Personal Trends', 'group' => 'workbalance', 'description' => 'View personal trend charts'],
            ['name' => 'manage_privacy_settings', 'display_name' => 'Manage Privacy Settings', 'group' => 'workbalance', 'description' => 'Control data aggregation consent'],
        ];

        $permissions = collect();
        foreach ($permissionsData as $permission) {
            $permissions->push(Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            ));
        }

        return $permissions;
    }

    protected function createRoles($permissions)
    {
        $roles = collect();

        // Admin Role - Full Access
        $this->command->info('  • Creating Admin role...');
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Full system access - Can access logs, privacy settings, and all features',
                'is_system' => true,
            ]
        );

        // Admin gets ALL permissions
        $admin->permissions()->sync($permissions->pluck('id'));
        $roles->push($admin);

        // Employer Role - HumanOps Access
        $this->command->info('  • Creating Employer role...');
        $employer = Role::firstOrCreate(
            ['name' => 'employer'],
            [
                'display_name' => 'Employer',
                'description' => 'Access to HumanOps Intelligence and organizational insights',
                'is_system' => true,
            ]
        );

        $employerPermissions = $permissions->filter(fn($p) =>
            in_array($p->group, ['dashboard', 'humanops', 'settings', 'notifications']) ||
            $p->name === 'view_users'
        );
        $employer->permissions()->sync($employerPermissions->pluck('id'));
        $roles->push($employer);

        // Employee Role - WorkBalance Access
        $this->command->info('  • Creating Employee role...');
        $employee = Role::firstOrCreate(
            ['name' => 'employee'],
            [
                'display_name' => 'Employee',
                'description' => 'Access to WorkBalance wellness application',
                'is_system' => true,
            ]
        );

        $employeePermissions = $permissions->filter(fn($p) =>
            $p->group === 'workbalance' ||
            $p->name === 'view_notifications'
        );
        $employee->permissions()->sync($employeePermissions->pluck('id'));
        $roles->push($employee);

        // Manager Role - Department HumanOps + WorkBalance
        $this->command->info('  • Creating Manager role...');
        $manager = Role::firstOrCreate(
            ['name' => 'manager'],
            [
                'display_name' => 'Manager',
                'description' => 'Department-level HumanOps access plus WorkBalance',
                'is_system' => false,
            ]
        );

        $managerPermissions = $permissions->filter(fn($p) =>
            in_array($p->group, ['dashboard', 'humanops', 'workbalance', 'notifications'])
        );
        $manager->permissions()->sync($managerPermissions->pluck('id'));
        $roles->push($manager);

        return $roles;
    }
}
