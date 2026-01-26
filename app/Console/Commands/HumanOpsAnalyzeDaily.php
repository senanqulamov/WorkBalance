<?php

namespace App\Console\Commands;

use App\Services\HumanOpsIntelligenceService;
use Illuminate\Console\Command;

class HumanOpsAnalyzeDaily extends Command
{
    protected $signature = 'humanops:analyze-daily';
    protected $description = 'Generate daily HumanOps intelligence insights (health index, risk signals, recommendations)';

    public function handle(HumanOpsIntelligenceService $service): int
    {
        $this->info('Starting HumanOps daily intelligence analysis...');

        try {
            // Calculate daily health index
            $this->info('Calculating organization health index...');
            $healthIndex = $service->calculateDailyHealthIndex();
            $this->info(sprintf(
                'Health index calculated: %.1f/10 (confidence: %.0f%%)',
                $healthIndex->overall_wellbeing_score,
                $healthIndex->confidence_level * 100
            ));

            // Detect risk signals
            $this->info('Detecting risk signals...');
            $service->detectRiskSignals();
            $this->info('Risk signal detection complete.');

            // Generate recommendations
            $this->info('Generating action recommendations...');
            $service->generateRecommendations();
            $this->info('Recommendations generated.');

            $this->info('✓ HumanOps daily analysis complete!');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to complete HumanOps analysis: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
