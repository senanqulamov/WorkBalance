<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagsSeeder extends Seeder
{
    public function run(): void
    {
        $flags = [
            ['key' => 'humanops_notifications', 'description' => 'Enable HumanOps Notification Center', 'enabled' => true],
            ['key' => 'humanops_health', 'description' => 'Enable System Health Page', 'enabled' => true],
            ['key' => 'humanops_burnout_monitoring', 'description' => 'Enable Burnout Risk Monitoring', 'enabled' => true],
            ['key' => 'humanops_stress_trends', 'description' => 'Enable Stress Trend Tracking', 'enabled' => true],
            ['key' => 'workbalance_therapeutic_paths', 'description' => 'Enable Therapeutic Paths for Employees', 'enabled' => true],
            ['key' => 'workbalance_reflections', 'description' => 'Enable Personal Reflections', 'enabled' => true],
            ['key' => 'humanops_command_palette', 'description' => 'Enable Command Palette', 'enabled' => true],
            ['key' => 'privacy_anonymization', 'description' => 'Enable Privacy Anonymization (minimum cohort enforcement)', 'enabled' => true],
        ];

        foreach ($flags as $flag) {
            FeatureFlag::firstOrCreate(['key' => $flag['key']], $flag);
        }
    }
}
