<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$rows = DB::table('jobs')->limit(5)->get();
foreach ($rows as $r) {
    echo "--- JOB ---\n";
    echo $r->id . "\n";
    echo substr($r->payload, 0, 500) . "\n\n";
}
