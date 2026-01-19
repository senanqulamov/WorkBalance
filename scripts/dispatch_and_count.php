<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo 'Jobs before: ' . DB::table('jobs')->count() . PHP_EOL;
App\Jobs\CheckRfqDeadlines::dispatch();
echo 'dispatched' . PHP_EOL;
echo 'Jobs after: ' . DB::table('jobs')->count() . PHP_EOL;
