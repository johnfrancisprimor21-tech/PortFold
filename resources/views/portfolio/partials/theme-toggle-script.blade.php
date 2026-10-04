<script>
    (() => {
        const root = document.documentElement;
        const button = document.querySelector('[data-theme-toggle]');
        const label = button?.querySelector('[data-theme-label]');

        if (!button || !label) {
            return;
        }

        const applyTheme = (theme, persist = false) => {
            root.dataset.theme = theme;
            root.style.colorScheme = theme;
            button.setAttribute('aria-pressed', String(theme === 'dark'));
            button.title = theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode';
            label.textContent = theme === 'dark' ? 'Dark mode' : 'Light mode';

            if (persist) {
                try {
                    window.localStorage.setItem('portfold-theme-mode', theme);
                } catch {
                    // The visual theme still applies when browser storage is unavailable.
                }
            }
        };

        applyTheme(root.dataset.theme === 'light' ? 'light' : 'dark');
        button.addEventListener('click', () => {
            applyTheme(root.dataset.theme === 'dark' ? 'light' : 'dark', true);
        });
    })();
</script>
