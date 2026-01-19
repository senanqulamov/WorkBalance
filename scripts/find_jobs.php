<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$rows = DB::table('jobs')->get();
$found = 0;
foreach ($rows as $r) {
    if (strpos($r->payload, 'SlaReminder') !== false || strpos($r->payload, 'SlaReminderDue') !== false) {
        echo "Found job id {$r->id}: contains SlaReminder\n";
        $found++;
    }
}
if ($found === 0) echo "No SlaReminder jobs in queue.\n";
else echo "Total: $found\n";
