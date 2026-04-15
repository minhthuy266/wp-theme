/**
 * Dropdown Hover Module - AffiliateCMS
 * Desktop dropdown with hover intent
 */

import { $$, on } from './utils.js';

/**
 * Initialize dropdown hover behavior
 */
export function init() {
    if (window.matchMedia('(hover: none)').matches) return;

    const menuItems = $$('.main-nav .menu-item-has-children');

    menuItems.forEach(item => {
        let timeout;

        on(item, 'mouseenter', () => {
            clearTimeout(timeout);
            item.classList.add('is-hovered');
        });

        on(item, 'mouseleave', () => {
            timeout = setTimeout(() => {
                item.classList.remove('is-hovered');
            }, 150);
        });
    });
}

export default { init };
