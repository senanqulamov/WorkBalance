<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class SettingsPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'view_settings',
                'display_name' => 'View Settings',
                'description' => 'Can view system settings',
                'group' => 'settings',
            ],
            [
                'name' => 'edit_settings',
                'display_name' => 'Edit Settings',
                'description' => 'Can modify system settings',
                'group' => 'settings',
            ],
            [
                'name' => 'manage_feature_flags',
                'display_name' => 'Manage Feature Flags',
                'description' => 'Can enable/disable feature flags',
                'group' => 'settings',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        $this->command->info('Settings permissions created successfully!');
    }
}
