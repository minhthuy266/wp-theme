/**
 * Theme Toggle Module - AffiliateCMS
 * Dark/Light Mode Switching
 *
 * Respects admin setting (acmsThemeConfig):
 *  - "light" / "dark": forced, toggle button hidden
 *  - "system": follow OS preference, user can toggle & save to localStorage
 *  - "time": follow server schedule, user can toggle & save to localStorage
 */

import { $$, on, storage } from './utils.js';

const STORAGE_KEY = 'acms-theme';
const DARK = 'dark';
const LIGHT = 'light';

/**
 * Get admin config
 */
function getConfig() {
    return window.acmsThemeConfig || { mode: 'system' };
}

/**
 * Check if mode is forced (no user toggle allowed)
 */
function isForced() {
    const mode = getConfig().mode;
    return mode === 'light' || mode === 'dark';
}

/**
 * Set theme on document
 */
function setTheme(theme, save = true) {
    document.documentElement.setAttribute('data-theme', theme);
    if (save && !isForced()) {
        storage.set(STORAGE_KEY, theme);
    }
}

/**
 * Toggle between dark and light
 */
function toggle() {
    if (isForced()) return;

    const current = document.documentElement.getAttribute('data-theme');
    const next = current === DARK ? LIGHT : DARK;
    setTheme(next, true);
}

/**
 * Initialize theme toggle
 */
export function init() {
    const cfg = getConfig();
    const toggleBtns = $$('[data-theme-toggle]');

    // Forced mode: hide toggle buttons, no interactivity
    if (isForced()) {
        toggleBtns.forEach(btn => { btn.style.display = 'none'; });
        return;
    }

    // System or Time mode: check saved preference first
    const savedTheme = storage.get(STORAGE_KEY);

    if (!savedTheme) {
        // No user override - apply default based on mode
        if (cfg.mode === 'system') {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            setTheme(prefersDark ? DARK : LIGHT, false);
        }
        // For "time" mode, the header inline script already set the correct theme
    } else {
        setTheme(savedTheme, false);
    }

    // Bind toggle buttons
    toggleBtns.forEach(btn => {
        on(btn, 'click', () => toggle());
    });

    // System mode: listen for OS preference changes (only if no saved preference)
    if (cfg.mode === 'system') {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (!storage.get(STORAGE_KEY)) {
                setTheme(e.matches ? DARK : LIGHT, false);
            }
        });
    }
}

export default { init };
