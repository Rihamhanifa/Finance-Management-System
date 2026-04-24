document.addEventListener('DOMContentLoaded', () => {
    // Mobile Menu Toggle
    const menuBtn = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            if (overlay) overlay.classList.toggle('show');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });
    }

    // Confirm Delete
    const deleteLinks = document.querySelectorAll('.delete-btn');
    deleteLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            if (!confirm('Are you sure you want to delete this record? This cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // Simple Table Search Filter
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('.table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});

// Theme Toggle Logic - Placed outside to ensure it runs independently of other UI errors
(function() {
    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const themeText = document.getElementById('theme-text');

    function updateThemeUI(theme) {
        if (!themeToggleBtn) return;
        if (theme === 'dark') {
            themeIcon.textContent = '☀️';
            if(themeText) themeText.textContent = 'Light Mode';
        } else {
            themeIcon.textContent = '🌙';
            if(themeText) themeText.textContent = 'Dark Mode';
        }
    }

    if (themeToggleBtn) {
        // Initial load UI sync
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        updateThemeUI(currentTheme);

        themeToggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            let targetTheme = 'dark';
            
            if (document.documentElement.getAttribute('data-theme') === 'dark') {
                targetTheme = 'light';
            }
            
            document.documentElement.setAttribute('data-theme', targetTheme);
            localStorage.setItem('theme', targetTheme);
            updateThemeUI(targetTheme);
        });
    }
})();
