/**
 * Scroll Animations Module - AffiliateCMS
 * Intersection Observer based animations
 */

import { $$ } from './utils.js';

/**
 * Initialize scroll animations
 */
export function init() {
    if (!('IntersectionObserver' in window)) return;

    const animatedElements = $$('[data-animate]');
    if (!animatedElements.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    animatedElements.forEach(el => observer.observe(el));
}

export default { init };
