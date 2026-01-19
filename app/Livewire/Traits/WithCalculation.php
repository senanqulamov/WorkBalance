<?php

namespace App\Livewire\Traits;

trait WithCalculation
{
    /**
     * Show calculating toast notification
     *
     * Fast, simple toast that shows for 1 second during calculations.
     * Components should pre-filter to only call this for calculation fields.
     */
    public function triggerCalculationToast(?string $propertyName = null): void
    {
        $this->toast()
            ->timeout(1)
            ->info(__('Calculating...'), __('Updating totals'))
            ->send();
    }
}
