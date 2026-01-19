<?php

namespace App\Console\Commands;

use App\Jobs\CheckRfqDeadlines;
use Illuminate\Console\Command;

class CheckRfqDeadlinesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rfq:check-deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check RFQ deadlines and send SLA reminders';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking RFQ deadlines...');

        // Dispatch the job
        CheckRfqDeadlines::dispatch();

        $this->info('RFQ deadline check job dispatched successfully.');

        return Command::SUCCESS;
    }
}
