/**
 * Mobile Navigation Module - AffiliateCMS
 * Sidebar Navigation for Mobile
 */

import { $, $$, on } from './utils.js';

/**
 * Open mobile navigation
 */
function open(nav, overlay) {
    nav.classList.add('is-open');
    overlay?.classList.add('is-visible');
    document.body.classList.add('mobile-nav-open');
}

/**
 * Close mobile navigation
 */
function close(nav, overlay) {
    nav.classList.remove('is-open');
    overlay?.classList.remove('is-visible');
    document.body.classList.remove('mobile-nav-open');

    $$('[data-mobile-menu-toggle]').forEach(b => {
        b.setAttribute('aria-expanded', false);
    });
}

/**
 * Toggle mobile navigation
 */
function toggle(nav, overlay, btn) {
    const isOpen = nav.classList.contains('is-open');

    if (isOpen) {
        close(nav, overlay);
    } else {
        open(nav, overlay);
    }

    // Update aria-expanded
    $$('[data-mobile-menu-toggle]').forEach(b => {
        b.setAttribute('aria-expanded', !isOpen);
    });
}

/**
 * Toggle submenu (accordion behavior)
 */
function toggleSubmenu(btn) {
    const parent = btn.closest('.mobile-nav__item--has-children');
    if (!parent) return;

    const isOpening = !parent.classList.contains('is-open');

    // Accordion behavior: close all other open submenus first
    if (isOpening) {
        const allOpenItems = $$('.mobile-nav__item--has-children.is-open');
        allOpenItems.forEach(item => {
            if (item !== parent) {
                item.classList.remove('is-open');
            }
        });
    }

    parent.classList.toggle('is-open');
}

/**
 * Initialize mobile navigation
 */
export function init() {
    const mobileNav = $('#mobile-navigation');
    const overlay = $('[data-mobile-nav-overlay]');

    if (!mobileNav) return;

    // Toggle button
    $$('[data-mobile-menu-toggle]').forEach(btn => {
        on(btn, 'click', () => toggle(mobileNav, overlay, btn));
    });

    // Close button
    const closeBtn = $('[data-mobile-menu-close]', mobileNav);
    if (closeBtn) {
        on(closeBtn, 'click', () => close(mobileNav, overlay));
    }

    // Overlay click
    if (overlay) {
        on(overlay, 'click', () => close(mobileNav, overlay));
    }

    // ESC key
    on(document, 'keydown', e => {
        if (e.key === 'Escape' && mobileNav.classList.contains('is-open')) {
            close(mobileNav, overlay);
        }
    });

    // Submenu toggles
    $$('[data-mobile-submenu-toggle]', mobileNav).forEach(btn => {
        on(btn, 'click', () => toggleSubmenu(btn));
    });
}

// Export close function for use by other modules
export { close };

export default { init, close };
