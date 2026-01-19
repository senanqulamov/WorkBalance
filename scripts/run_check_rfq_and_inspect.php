<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Active RFQs total: " . \App\Models\Request::whereIn('status', ['draft','open'])->where('deadline', '>', now())->count() . "\n";
$thresholds = [7,3,1];
foreach ($thresholds as $t) {
    $count = \App\Models\Request::whereIn('status', ['draft','open'])->whereDate('deadline', now()->addDays($t)->toDateString())->count();
    echo "RFQs due in {$t} days: {$count}\n";
}

// Run job synchronously
$job = new \App\Jobs\CheckRfqDeadlines();
$job->handle();

echo "Ran job handle. Now searching jobs table for SlaReminder payloads...\n";
$rows = DB::table('jobs')->get();
$found = 0;
foreach ($rows as $r) {
    if (strpos($r->payload, 'SlaReminder') !== false || strpos($r->payload, 'SlaReminderDue') !== false || strpos($r->payload, 'SlaReminderDue') !== false) {
        echo "Found job id {$r->id}: contains SlaReminder or SlaReminderDue\n";
        $found++;
    }
}
if ($found === 0) echo "No SlaReminder jobs in queue after running job.\n";
else echo "Total SlaReminder-related jobs: {$found}\n";
