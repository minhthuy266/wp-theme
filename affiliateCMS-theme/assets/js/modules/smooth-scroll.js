/**
 * Smooth Scroll Module - AffiliateCMS
 * Smooth scrolling for anchor links
 */

import { $, on } from './utils.js';

/**
 * Initialize smooth scroll
 */
export function init() {
    on(document, 'click', 'a[href^="#"]', function(e) {
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;

        const target = $(targetId);
        if (target) {
            e.preventDefault();

            const headerHeight = $('#masthead')?.offsetHeight || 0;
            const targetPosition = target.getBoundingClientRect().top + window.scrollY - headerHeight - 20;

            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    });
}

export default { init };
