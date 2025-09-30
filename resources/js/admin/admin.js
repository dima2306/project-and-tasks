import Alpine from 'alpinejs';

function themeSwitcher() {
    return {
        isDark: false,
        init() {
            const savedTheme = localStorage.getItem('theme');
            // Check localStorage or system preference
            this.isDark = savedTheme
                ? savedTheme === 'dark'
                : window.matchMedia('(prefers-color-scheme: dark)').matches;
        },
        toggleTheme() {
            this.isDark = !this.isDark;
            localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
        },
    };
}

window.Alpine = Alpine;
window.themeSwitcher = themeSwitcher;
Alpine.start();
