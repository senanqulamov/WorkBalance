<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚩 Seeding Feature Flags...');

        $flags = [
            [
                'key' => 'enable_humanops',
                'name' => 'Enable HumanOps Intelligence',
                'description' => 'Toggle access to HumanOps organizational well-being intelligence',
                'is_enabled' => true,
            ],
            [
                'key' => 'enable_workbalance',
                'name' => 'Enable WorkBalance',
                'description' => 'Toggle access to WorkBalance employee wellness application',
                'is_enabled' => true,
            ],
            [
                'key' => 'enable_aggregation',
                'name' => 'Enable Automatic Aggregation',
                'description' => 'Toggle automatic weekly aggregation from WorkBalance to HumanOps',
                'is_enabled' => true,
            ],
            [
                'key' => 'enable_email_notifications',
                'name' => 'Enable Email Notifications',
                'description' => 'Send email notifications for important events',
                'is_enabled' => false,
            ],
            [
                'key' => 'enable_privacy_mode',
                'name' => 'Enable Extra Privacy Mode',
                'description' => 'Apply additional privacy restrictions (higher minimums, longer delays)',
                'is_enabled' => false,
            ],
            [
                'key' => 'maintenance_mode',
                'name' => 'Maintenance Mode',
                'description' => 'Put system in maintenance mode (only admins can access)',
                'is_enabled' => false,
            ],
        ];

        foreach ($flags as $flag) {
            FeatureFlag::firstOrCreate(
                ['key' => $flag['key']],
                $flag
            );
            $this->command->info("  ✓ {$flag['name']}");
        }

        $this->command->info("✅ Feature flags seeded successfully!");
    }
}
