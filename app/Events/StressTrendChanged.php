<?php

namespace App\Events;

use App\Models\TeamMetric;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when team stress trends shift significantly.
 * Alerts managers and creates HumanEvent.
 */
class StressTrendChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public TeamMetric $metric,
        public string $previousTrend,
        public string $newTrend
    ) {}
}
