/**
 * Search Modal Module - AffiliateCMS
 * Simple search overlay
 */

import { $, $$, on } from './utils.js';

/**
 * Open search modal
 */
function open(modal) {
    // Close mobile nav first if open
    const mobileNav = $('#mobile-navigation');
    const overlay = $('[data-mobile-nav-overlay]');
    const menuToggle = $('[data-mobile-menu-toggle]');

    if (mobileNav && mobileNav.classList.contains('is-open')) {
        mobileNav.classList.remove('is-open');
        overlay?.classList.remove('is-visible');
        document.body.classList.remove('mobile-nav-open');
        if (menuToggle) {
            menuToggle.setAttribute('aria-expanded', 'false');
        }
    }

    modal.classList.add('is-open');
    document.body.style.overflow = 'hidden';

    // Focus input
    const input = $('.search-modal__input', modal);
    if (input) {
        setTimeout(() => {
            input.focus();
            input.select();
        }, 100);
    }
}

/**
 * Close search modal
 */
function close(modal) {
    modal.classList.remove('is-open');
    document.body.style.overflow = '';

    // Clear input
    const input = $('.search-modal__input', modal);
    if (input) {
        input.value = '';
    }
}

/**
 * Toggle search modal
 */
function toggle(modal) {
    if (modal.classList.contains('is-open')) {
        close(modal);
    } else {
        open(modal);
    }
}

/**
 * Initialize search modal
 */
export function init() {
    const modal = $('#search-modal');
    if (!modal) return;

    // Open triggers
    $$('[data-search-toggle]').forEach(btn => {
        on(btn, 'click', () => open(modal));
    });

    // Close triggers (backdrop)
    $$('[data-search-close]', modal).forEach(el => {
        on(el, 'click', () => close(modal));
    });

    // ESC key to close
    on(document, 'keydown', e => {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) {
            close(modal);
        }
    });

    // Ctrl+K / Cmd+K shortcut to toggle
    on(document, 'keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            toggle(modal);
        }
    });
}

export default { init };
