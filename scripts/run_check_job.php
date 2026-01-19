<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $job = new App\Jobs\CheckRfqDeadlines();
    $job->handle();
    echo "Job handled OK\n";
} catch (\Throwable $e) {
    echo "Exception: " . get_class($e) . " - " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
