<?php

namespace App\Livewire\Health;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Livewire\Component;

class Index extends Component
{
    public array $checks = [];

    public function mount(): void
    {
        $this->runChecks();
    }

    public function runChecks(): void
    {
        $this->checks = [
            'app' => [
                'env' => config('app.env'),
                'debug' => config('app.debug'),
                'timezone' => config('app.timezone'),
            ],
            'database' => [
                'connected' => $this->dbConnected(),
            ],
            'cache' => [
                'connected' => $this->cacheWorking(),
            ],
            'queue' => [
                'connection' => config('queue.default'),
                'size' => $this->queueSize(),
                'failed' => $this->failedJobsCount(),
            ],
            'scheduler' => [
                'last_run' => Cache::get('scheduler:last_run') ?? 'unknown',
            ],
        ];
    }

    private function dbConnected(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function cacheWorking(): bool
    {
        try {
            Cache::put('health_check', 'ok', 10);
            return Cache::get('health_check') === 'ok';
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function queueSize(): int
    {
        try {
            return DB::table('jobs')->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function failedJobsCount(): int
    {
        try {
            return DB::table('failed_jobs')->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function render(): View
    {
        return view('livewire.health.index');
    }
}
