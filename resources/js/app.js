import './bootstrap';
import './theme';

// -----------------------------------------------------------------------------
// Global Loader — ONLY for full-page + non-table Livewire actions
// -----------------------------------------------------------------------------
(function initGlobalLoader() {
    const el = document.getElementById('global-loader');
    if (!el) return;

    let showTimer = null;
    let inflight = 0;

    const show = () => {
        if (showTimer) return;
        showTimer = setTimeout(() => {
            el.style.display = 'block';
            el.setAttribute('aria-hidden', 'false');
        }, 180);
    };

    const hide = () => {
        inflight = Math.max(0, inflight - 1);
        if (inflight > 0) return;

        clearTimeout(showTimer);
        showTimer = null;
        el.style.display = 'none';
        el.setAttribute('aria-hidden', 'true');
    };

    const start = () => {
        inflight++;
        show();
    };

    // Hard navigation
    window.addEventListener('beforeunload', () => {
        clearTimeout(showTimer);
        el.style.display = 'block';
        el.setAttribute('aria-hidden', 'false');
    });

    document.addEventListener('livewire:init', () => {
        // wire:navigate (SPA page changes)
        document.addEventListener('livewire:navigate', start);
        document.addEventListener('livewire:navigated', () => {
            inflight = 1;
            hide();
        });
    });
})();
