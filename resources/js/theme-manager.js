/**
 * MASSAR Theme Manager
 * Handles theme switching with proper classList management
 */

(function() {
    'use strict';

    const STORAGE_KEY = 'masar_theme';
    const VALID_THEMES = ['classic', 'mint-green', 'dark', 'monokai'];
    const DEFAULT_THEME = 'classic';
    
    /**
     * Set theme using classList (preserves other classes)
     * @param {string} theme - Theme name
     */
    function setTheme(theme) {
        if (!VALID_THEMES.includes(theme)) {
            console.warn(`Invalid theme: ${theme}. Using default.`);
            theme = DEFAULT_THEME;
        }
        
        // Remove all theme classes from html element
        VALID_THEMES.forEach(t => {
            document.documentElement.classList.remove(`theme-${t}`);
        });
        
        // Add new theme class
        document.documentElement.classList.add(`theme-${theme}`);
        
        // Store preference
        try {
            localStorage.setItem(STORAGE_KEY, theme);
        } catch (e) {
            console.warn('Failed to save theme preference:', e);
        }
        
        // Dispatch custom event for other components
        document.dispatchEvent(new CustomEvent('theme-changed', {
            detail: { theme }
        }));
        
        return theme;
    }
    
    /**
     * Get current theme
     * @returns {string} Current theme name
     */
    function getTheme() {
        // Check classList first
        for (const theme of VALID_THEMES) {
            if (document.documentElement.classList.contains(`theme-${theme}`)) {
                return theme;
            }
        }
        
        // Fallback to localStorage
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            if (stored && VALID_THEMES.includes(stored)) {
                return stored;
            }
        } catch (e) {
            console.warn('Failed to read theme preference:', e);
        }
        
        return DEFAULT_THEME;
    }
    
    /**
     * Toggle between light and dark themes
     */
    function toggleDarkMode() {
        const current = getTheme();
        const isDark = current === 'dark' || current === 'monokai';
        const newTheme = isDark ? 'classic' : 'dark';
        return setTheme(newTheme);
    }
    
    /**
     * Bind theme dropdown to theme switcher
     * @param {string} selector - CSS selector for dropdown
     */
    function bindDropdown(selector) {
        const dropdown = document.querySelector(selector);
        if (!dropdown) {
            console.warn(`Theme dropdown not found: ${selector}`);
            return;
        }
        
        // Set initial active state
        const currentTheme = getTheme();
        const items = dropdown.querySelectorAll('[data-theme]');
        items.forEach(item => {
            const theme = item.getAttribute('data-theme');
            if (theme === currentTheme) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
        
        // Bind click events
        items.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const theme = this.getAttribute('data-theme');
                if (theme && VALID_THEMES.includes(theme)) {
                    setTheme(theme);
                    
                    // Update active state
                    items.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });
    }
    
    /**
     * Initialize theme on page load
     */
    function init() {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            const theme = (stored && VALID_THEMES.includes(stored)) ? stored : DEFAULT_THEME;
            setTheme(theme);
        } catch (e) {
            console.warn('Failed to initialize theme:', e);
            setTheme(DEFAULT_THEME);
        }
    }
    
    // Initialize theme immediately (before DOM ready to prevent flash)
    init();
    
    // Expose API globally
    window.MasarTheme = {
        setTheme,
        getTheme,
        toggleDarkMode,
        bindDropdown,
        VALID_THEMES,
        DEFAULT_THEME
    };
    
    // Legacy compatibility (for old code using MasarThemeSwitcher)
    window.MasarThemeSwitcher = {
        bindDropdown
    };
    
    console.log('✅ MASSAR Theme Manager initialized');
})();
