<?php

namespace Database\Seeders;

use App\Models\ActionRecommendation;
use App\Models\CheckIn;
use App\Models\Department;
use App\Models\OrganizationHealthIndex;
use App\Models\RiskSignal;
use App\Models\User;
use App\Models\WellBeingToolUsage;
use App\Services\HumanOpsIntelligenceService;
use Illuminate\Database\Seeder;

class HumanOpsIntelligenceSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🧠 Seeding HumanOps Intelligence data...');

        // Generate historical health indices
        $this->command->info('Generating organization health indices...');
        OrganizationHealthIndex::factory()->count(30)->create();

        // Generate risk signals
        $this->command->info('Generating risk signals...');
        RiskSignal::factory()->count(8)->create();
        RiskSignal::factory()->elevated()->unresolved()->count(3)->create();

        // Generate action recommendations
        $this->command->info('Generating action recommendations...');
        ActionRecommendation::factory()->count(10)->create();
        ActionRecommendation::factory()->highPriority()->pending()->count(3)->create();
        ActionRecommendation::factory()->positive()->count(2)->create();

        // Run intelligence service to generate real data from check-ins
        $this->command->info('Running intelligence analysis on existing check-ins...');

        $service = new HumanOpsIntelligenceService();

        try {
            $healthIndex = $service->calculateDailyHealthIndex();
            $this->command->info(sprintf(
                '  ✓ Health index: %.1f/10 (confidence: %.0f%%)',
                $healthIndex->overall_wellbeing_score,
                $healthIndex->confidence_level * 100
            ));
        } catch (\Exception $e) {
            $this->command->warn('  ⚠ Could not calculate health index: ' . $e->getMessage());
        }

        try {
            $service->detectRiskSignals();
            $this->command->info('  ✓ Risk signals detected');
        } catch (\Exception $e) {
            $this->command->warn('  ⚠ Could not detect risk signals: ' . $e->getMessage());
        }

        try {
            $service->generateRecommendations();
            $this->command->info('  ✓ Recommendations generated');
        } catch (\Exception $e) {
            $this->command->warn('  ⚠ Could not generate recommendations: ' . $e->getMessage());
        }

        $this->command->info('');
        $this->command->info('📊 HumanOps Intelligence Summary:');
        $this->command->info('  ' . OrganizationHealthIndex::count() . ' health indices');
        $this->command->info('  ' . RiskSignal::whereNull('resolved_at')->count() . ' active risk signals');
        $this->command->info('  ' . ActionRecommendation::whereNull('implemented_at')->count() . ' pending recommendations');
    }
}
