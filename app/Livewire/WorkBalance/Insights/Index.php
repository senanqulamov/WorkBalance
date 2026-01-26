<?php

namespace App\Livewire\WorkBalance\Insights;

use App\Models\PersonalInsight;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    public array $newInsights = [];
    public array $acknowledgedInsights = [];

    public array $headers = [
        ['index' => 'title', 'label' => 'Title'],
        ['index' => 'type', 'label' => 'Type'],
        ['index' => 'generated', 'label' => 'Generated'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'action', 'label' => 'Action', 'sortable' => false],
    ];

    public function mount(): void
    {
        $this->loadInsights();
    }

    protected function loadInsights(): void
    {
        $allInsights = PersonalInsight::where('user_id', auth()->id())
            ->where('generated_at', '>=', now()->subDays(30))
            ->orderByDesc('generated_at')
            ->get();

        $this->newInsights = $allInsights->whereNull('acknowledged_at')->values()->all();
        $this->acknowledgedInsights = $allInsights->whereNotNull('acknowledged_at')->values()->all();
    }

    #[Computed]
    public function rows(): array
    {
        $insights = array_merge($this->newInsights, $this->acknowledgedInsights);

        return collect($insights)->map(function ($i) {
            $isNew = empty($i['acknowledged_at']);

            return [
                'id' => $i['id'],
                'title' => $i['title'],
                'type' => $i['insight_type'],
                'generated' => (string)($i['generated_at'] ?? ''),
                'status' => $isNew ? 'New' : 'Acknowledged',
            ];
        })->toArray();
    }

    public function acknowledgeInsight(int $insightId): void
    {
        $insight = PersonalInsight::where('id', $insightId)
            ->where('user_id', auth()->id())
            ->first();

        if ($insight) {
            $insight->acknowledge();
            $this->loadInsights();
        }
    }

    public function render(): View
    {
        return view('livewire.workbalance.insights.index');
    }
}
