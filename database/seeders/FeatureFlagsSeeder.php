<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagsSeeder extends Seeder
{
    public function run(): void
    {
        $flags = [
            ['key' => 'admin_notifications', 'description' => 'Enable Admin Notification Center', 'enabled' => true],
            ['key' => 'admin_health', 'description' => 'Enable System Health Page', 'enabled' => true],
            ['key' => 'admin_rfq_monitoring', 'description' => 'Enable RFQ Monitoring Page', 'enabled' => true],
            ['key' => 'admin_sla', 'description' => 'Enable SLA Tracker Page', 'enabled' => true],
            ['key' => 'admin_command_palette', 'description' => 'Enable Command Palette', 'enabled' => true],
        ];

        foreach ($flags as $flag) {
            FeatureFlag::firstOrCreate(['key' => $flag['key']], $flag);
        }
    }
}
