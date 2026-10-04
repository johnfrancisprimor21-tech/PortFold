<script>
    (() => {
        const root = document.documentElement;
        const defaultTheme = root.dataset.defaultTheme === 'light' ? 'light' : 'dark';
        let savedTheme = null;

        try {
            savedTheme = window.localStorage.getItem('portfold-theme-mode');
        } catch {
            savedTheme = null;
        }

        root.dataset.theme = savedTheme === 'light' || savedTheme === 'dark' ? savedTheme : defaultTheme;
        root.style.colorScheme = root.dataset.theme;
    })();
</script>
