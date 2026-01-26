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
        $this->command->info('🔐 Setting up WorkBalance roles and permissions...');

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
            // Dashboard & Core
            ['name' => 'view_dashboard', 'display_name' => 'View Dashboard', 'group' => 'Core'],
            ['name' => 'view_settings', 'display_name' => 'View Settings', 'group' => 'Core'],
            ['name' => 'edit_settings', 'display_name' => 'Edit Settings', 'group' => 'Core'],

            // Users & Team Management
            ['name' => 'view_users', 'display_name' => 'View Users', 'group' => 'Users'],
            ['name' => 'create_users', 'display_name' => 'Create Users', 'group' => 'Users'],
            ['name' => 'edit_users', 'display_name' => 'Edit Users', 'group' => 'Users'],
            ['name' => 'delete_users', 'display_name' => 'Delete Users', 'group' => 'Users'],

            // HumanOps Intelligence (Employer Side)
            ['name' => 'view_humanops', 'display_name' => 'View HumanOps Intelligence', 'group' => 'HumanOps'],
            ['name' => 'view_team_metrics', 'display_name' => 'View Team Metrics', 'group' => 'HumanOps'],
            ['name' => 'view_burnout_signals', 'display_name' => 'View Burnout Signals', 'group' => 'HumanOps'],
            ['name' => 'view_stress_trends', 'display_name' => 'View Stress Trends', 'group' => 'HumanOps'],
            ['name' => 'view_human_events', 'display_name' => 'View Human Events', 'group' => 'HumanOps'],
            ['name' => 'export_humanops_data', 'display_name' => 'Export HumanOps Data', 'group' => 'HumanOps'],

            // Teams Management
            ['name' => 'view_teams', 'display_name' => 'View Teams', 'group' => 'Teams'],
            ['name' => 'create_teams', 'display_name' => 'Create Teams', 'group' => 'Teams'],
            ['name' => 'edit_teams', 'display_name' => 'Edit Teams', 'group' => 'Teams'],
            ['name' => 'delete_teams', 'display_name' => 'Delete Teams', 'group' => 'Teams'],
            ['name' => 'manage_team_members', 'display_name' => 'Manage Team Members', 'group' => 'Teams'],

            // Therapeutic Paths Management
            ['name' => 'view_therapeutic_paths', 'display_name' => 'View Therapeutic Paths', 'group' => 'Paths'],
            ['name' => 'create_therapeutic_paths', 'display_name' => 'Create Therapeutic Paths', 'group' => 'Paths'],
            ['name' => 'edit_therapeutic_paths', 'display_name' => 'Edit Therapeutic Paths', 'group' => 'Paths'],
            ['name' => 'delete_therapeutic_paths', 'display_name' => 'Delete Therapeutic Paths', 'group' => 'Paths'],

            // WorkBalance (Employee Side)
            ['name' => 'access_workbalance', 'display_name' => 'Access WorkBalance', 'group' => 'WorkBalance'],
            ['name' => 'create_check_ins', 'display_name' => 'Create Check-ins', 'group' => 'WorkBalance'],
            ['name' => 'start_therapeutic_sessions', 'display_name' => 'Start Therapeutic Sessions', 'group' => 'WorkBalance'],
            ['name' => 'view_own_progress', 'display_name' => 'View Own Progress', 'group' => 'WorkBalance'],
            ['name' => 'create_reflections', 'display_name' => 'Create Reflections', 'group' => 'WorkBalance'],

            // Organizations (for multi-tenant future)
            ['name' => 'view_organizations', 'display_name' => 'View Organizations', 'group' => 'Organizations'],
            ['name' => 'edit_organizations', 'display_name' => 'Edit Organizations', 'group' => 'Organizations'],

            // Privacy & Compliance
            ['name' => 'manage_roles', 'display_name' => 'Manage Roles', 'group' => 'Privacy'],
            ['name' => 'view_activity_signals', 'display_name' => 'View Activity Signals', 'group' => 'Privacy'],

            // System & Monitoring
            ['name' => 'view_health', 'display_name' => 'View System Health', 'group' => 'System'],
            ['name' => 'view_notifications', 'display_name' => 'View Notifications', 'group' => 'System'],
            ['name' => 'view_monitoring', 'display_name' => 'View Monitoring', 'group' => 'System'],
            ['name' => 'manage_feature_flags', 'display_name' => 'Manage Feature Flags', 'group' => 'System'],
        ];

        $permissions = [];
        foreach ($permissionsList as $perm) {
            $permissions[$perm['name']] = Permission::firstOrCreate(
                ['name' => $perm['name']],
                [
                    'display_name' => $perm['display_name'],
                    'group' => $perm['group'],
                ]
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
                'description' => 'Full system access to both HumanOps and WorkBalance',
            ],
            [
                'name' => 'owner',
                'display_name' => 'Organization Owner',
                'description' => 'Full HumanOps access, organizational management',
            ],
            [
                'name' => 'manager',
                'display_name' => 'Team Manager',
                'description' => 'HumanOps access for assigned teams, aggregated insights only',
            ],
            [
                'name' => 'employee',
                'display_name' => 'Employee',
                'description' => 'WorkBalance access for personal wellbeing tracking',
            ],
        ];

        $roles = [];
        foreach ($rolesList as $roleData) {
            $roles[$roleData['name']] = Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                ]
            );
        }

        return $roles;
    }

    private function assignPermissionsToRoles(array $roles, array $permissions): void
    {
        // Admin - All permissions
        $roles['admin']->permissions()->sync(array_values(array_map(fn($p) => $p->id, $permissions)));

        // Owner - HumanOps + Management
        $roles['owner']->permissions()->sync([
            $permissions['view_dashboard']->id,
            $permissions['view_humanops']->id,
            $permissions['view_team_metrics']->id,
            $permissions['view_burnout_signals']->id,
            $permissions['view_stress_trends']->id,
            $permissions['view_human_events']->id,
            $permissions['export_humanops_data']->id,
            $permissions['view_teams']->id,
            $permissions['create_teams']->id,
            $permissions['edit_teams']->id,
            $permissions['delete_teams']->id,
            $permissions['manage_team_members']->id,
            $permissions['view_users']->id,
            $permissions['create_users']->id,
            $permissions['edit_users']->id,
            $permissions['view_organizations']->id,
            $permissions['edit_organizations']->id,
            $permissions['view_therapeutic_paths']->id,
            $permissions['create_therapeutic_paths']->id,
            $permissions['edit_therapeutic_paths']->id,
            $permissions['view_settings']->id,
            $permissions['view_notifications']->id,
        ]);

        // Manager - Limited HumanOps for their teams
        $roles['manager']->permissions()->sync([
            $permissions['view_dashboard']->id,
            $permissions['view_humanops']->id,
            $permissions['view_team_metrics']->id,
            $permissions['view_burnout_signals']->id,
            $permissions['view_stress_trends']->id,
            $permissions['view_human_events']->id,
            $permissions['view_teams']->id,
            $permissions['view_therapeutic_paths']->id,
            $permissions['view_notifications']->id,
        ]);

        // Employee - WorkBalance only
        $roles['employee']->permissions()->sync([
            $permissions['access_workbalance']->id,
            $permissions['create_check_ins']->id,
            $permissions['start_therapeutic_sessions']->id,
            $permissions['view_own_progress']->id,
            $permissions['create_reflections']->id,
            $permissions['view_therapeutic_paths']->id,
        ]);
    }

    private function createAdminUser(array $roles): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@workbalance.local'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin' => true,
                'is_active' => true,
            ]
        );

        $admin->roles()->syncWithoutDetaching([$roles['admin']->id]);

        $this->command->info("✅ Admin user created: admin@workbalance.local / password");
    }
}
