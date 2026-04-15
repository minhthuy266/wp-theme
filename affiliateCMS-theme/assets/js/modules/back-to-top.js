/**
 * Back to Top Module - AffiliateCMS
 * Scroll to top button
 */

import { $, on, debounce } from './utils.js';

/**
 * Initialize back to top button
 */
export function init() {
    const btn = $('#backToTopBtn');
    if (!btn) return;

    const onScroll = debounce(() => {
        const show = window.scrollY > 300;
        btn.classList.toggle('show', show);
    }, 100);

    on(window, 'scroll', onScroll);
    on(btn, 'click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

export default { init };
