// Theme initializer and helper
// Runs early to prevent FOUC and exposes `setAppTheme`
(function () {
    const key = 'theme';
    const doc = document.documentElement;

    function applyTheme(theme) {
        doc.classList.toggle('light', theme === 'light');
        doc.classList.toggle('dark', theme === 'dark');
        // color-scheme helps browser render native controls correctly
        try { doc.style.colorScheme = theme; } catch (e) { /* ignore */ }
    }

    // Initial resolution: saved > prefers-color-scheme > default 'dark'
    const saved = localStorage.getItem(key);
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const initial = saved || (prefersDark ? 'dark' : 'light');

    applyTheme(initial);

    window.setAppTheme = function (t) {
        try { localStorage.setItem(key, t); } catch (e) { /* ignore */ }
        applyTheme(t);
        // Optional event for other scripts/components to react
        window.dispatchEvent(new CustomEvent('app:theme-changed', { detail: { theme: t } }));
    };

    window.getAppTheme = function () {
        return localStorage.getItem(key) || (prefersDark ? 'dark' : 'light');
    };
})();
