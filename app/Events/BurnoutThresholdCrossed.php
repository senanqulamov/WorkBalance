<?php

namespace App\Events;

use App\Models\TeamMetric;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a team's burnout risk crosses into elevated or high.
 * Triggers immediate notification to managers and owners.
 */
class BurnoutThresholdCrossed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public TeamMetric $metric,
        public string $riskLevel
    ) {}
}
