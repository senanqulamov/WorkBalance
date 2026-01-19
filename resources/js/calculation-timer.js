/**
 * Calculation Toast Timer Handler
 *
 * This script manages the calculation toast, showing it during calculations
 * and auto-dismissing it when calculations complete.
 */

document.addEventListener('livewire:init', () => {
    let calculationTimer = null;
    let isCalculating = false;

    // Listen for calculation start
    Livewire.on('calculation-started', () => {
        isCalculating = true;
        console.log('Calculation started - toast will remain visible');

        // Clear any existing timer
        if (calculationTimer) {
            clearTimeout(calculationTimer);
        }
    });

    // Listen for calculation finish
    Livewire.on('calculation-finished', () => {
        console.log('Calculation finished - dismissing toast');
        dismissCalculationToast();
        isCalculating = false;
    });

    // Listen for Livewire update finish to auto-dismiss toast
    document.addEventListener('livewire:update', () => {
        if (isCalculating) {
            // Clear previous timer
            if (calculationTimer) {
                clearTimeout(calculationTimer);
            }

            // Set a short delay to dismiss toast after Livewire finishes updating
            // This ensures the calculation is actually complete
            calculationTimer = setTimeout(() => {
                console.log('Calculation complete (auto-detected) - dismissing toast');
                dismissCalculationToast();
                isCalculating = false;
            }, 500); // Wait 500ms after update to ensure calculation is done
        }
    });

    function dismissCalculationToast() {
        // Find and close the calculation toast
        const toastCloseButtons = document.querySelectorAll('[x-data*="tallstackui_toastLoop"] button[aria-label*="close"], [x-data*="tallstackui_toastLoop"] button[x-on\\:click*="close"]');

        if (toastCloseButtons.length > 0) {
            // Click the close button of the most recent toast
            const lastToast = toastCloseButtons[toastCloseButtons.length - 1];
            if (lastToast) {
                lastToast.click();
                console.log('Toast dismissed');
            }
        } else {
            console.warn('Toast close button not found');
        }
    }
});
