/**
 * Topbar Module - AffiliateCMS
 * Dismissible Affiliate Disclosure Bar
 */

import { $, on, storage } from './utils.js';

const STORAGE_KEY = 'acms-topbar-dismissed';

/**
 * Dismiss the topbar
 */
function dismiss(topbar) {
    topbar.classList.add('is-hidden');
    storage.set(STORAGE_KEY, true);
}

/**
 * Initialize topbar
 */
export function init() {
    const topbar = $('#topbar');
    if (!topbar) return;

    // Check if already dismissed
    if (storage.get(STORAGE_KEY)) {
        topbar.classList.add('is-hidden');
        return;
    }

    // Bind close button
    const closeBtn = $('[data-topbar-close]', topbar);
    if (closeBtn) {
        on(closeBtn, 'click', () => dismiss(topbar));
    }
}

export default { init };
