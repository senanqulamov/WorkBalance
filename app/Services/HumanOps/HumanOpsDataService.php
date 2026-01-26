<?php

namespace App\Services\HumanOps;

use App\Models\AggregatedWellbeingSignal;
use App\Models\Department;
use App\Models\HumanopsViewsLog;
use App\Models\TrendSnapshot;
use Illuminate\Support\Collection;

class HumanOpsDataService
{
    /**
     * Keep privacy rules centralized for HumanOps.
     *
     * NOTE: AggregationService currently enforces MIN_GROUP_SIZE=7 + 48h delay.
     * About.md mentions 10; we can bump later, but for now we mirror the backend so UI isn't misleading.
     */
    public const MIN_GROUP_SIZE = 7;

    public function logView(string $section, ?int $departmentId = null): void
    {
        if (!auth()->check()) {
            return;
        }

        HumanopsViewsLog::create([
            'user_id' => auth()->id(),
            'section' => $section,
            'department_id' => $departmentId,
            'viewed_at' => now(),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Latest aggregated signal for a department or whole org.
     */
    public function latestSignal(?int $departmentId = null): ?AggregatedWellbeingSignal
    {
        return AggregatedWellbeingSignal::query()
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId), fn($q) => $q->whereNull('department_id'))
            ->latest('period_end')
            ->first();
    }

    /**
     * Aggregated signals for a date range.
     *
     * @return Collection<int, AggregatedWellbeingSignal>
     */
    public function signalsSince(int $daysBack, ?int $departmentId = null): Collection
    {
        return AggregatedWellbeingSignal::query()
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId), fn($q) => $q->whereNull('department_id'))
            ->where('period_start', '>=', now()->subDays($daysBack)->startOfDay())
            ->where('data_confidence', '>', 0)
            ->orderBy('period_start')
            ->get();
    }

    /**
     * Trend snapshots for a metric.
     *
     * @return Collection<int, TrendSnapshot>
     */
    public function trendSnapshots(string $metric, int $daysBack, ?int $departmentId = null): Collection
    {
        return TrendSnapshot::query()
            ->where('metric', $metric)
            ->when($departmentId, fn($q) => $q->where('scope', 'department')->where('department_id', $departmentId), fn($q) => $q->where('scope', 'organization'))
            ->where('period_start', '>=', now()->subDays($daysBack)->startOfDay())
            ->orderBy('period_start')
            ->get();
    }

    /**
     * Departments list (active only).
     */
    public function activeDepartments(): Collection
    {
        return Department::query()->where('is_active', true)->orderBy('name')->get();
    }
}
