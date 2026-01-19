<?php

namespace App\Livewire\Settings;

use App\Models\FeatureFlag;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class FeatureFlags extends Component
{
    public array $flags = [];

    public function mount(): void
    {
        $this->flags = FeatureFlag::orderBy('key')->get()->map(fn($f) => [
            'id' => $f->id,
            'key' => $f->key,
            'description' => $f->description,
            'enabled' => (bool)$f->enabled,
        ])->toArray();
    }

    public function toggle(int $id): void
    {
        $flag = FeatureFlag::findOrFail($id);
        $flag->enabled = !$flag->enabled;
        $flag->save();
        foreach ($this->flags as &$row) {
            if ($row['id'] === $id) {
                $row['enabled'] = $flag->enabled;
                break;
            }
        }
        cache()->forget("feature_flag_{$flag->key}");
        $this->dispatchBrowserEvent('toast', ['type' => 'success', 'message' => "Flag '{$flag->key}' updated"]);
    }

    public function render(): View
    {
        return view('livewire.settings.feature-flags');
    }
}
