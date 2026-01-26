<?php

namespace App\Console\Commands;

use App\Services\AggregationService;
use Illuminate\Console\Command;

class RunWeeklyAggregation extends Command
{
    protected $signature = 'aggregation:run-weekly';
    protected $description = 'Run weekly aggregation from WorkBalance to HumanOps (respects privacy rules)';

    public function handle(AggregationService $service): int
    {
        $this->info('🔒 Starting privacy-protected aggregation...');
        $this->info('Rules: Min 7 employees, 48-hour delay, consent required');
        $this->newLine();

        try {
            $service->runWeeklyAggregation();

            $this->info('✓ Aggregation completed successfully');
            $this->info('Check aggregation_exports and privacy_audit_log tables for details');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('✗ Aggregation failed: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
