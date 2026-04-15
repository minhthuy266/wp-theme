/**
 * Header Scroll Module - AffiliateCMS
 * Sticky Header with Scroll Effects
 *
 * Simple compact mode without hiding navigation
 */

import { $ } from './utils.js';

/**
 * Initialize header scroll behavior
 */
export function init() {
    const header = $('#masthead');
    if (!header) return;

    // State
    let isCompact = false;
    let ticking = false;

    // Config - Hysteresis thresholds to prevent jitter
    const SCROLL_THRESHOLD_ENTER = 80; // Enter compact mode when scrolled past this
    const SCROLL_THRESHOLD_EXIT = 40;  // Exit compact mode when scrolled above this

    const updateScroll = () => {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        // Header compact mode with hysteresis to prevent jitter
        // Enter compact: scroll > 80px
        // Exit compact: scroll < 40px
        // Between 40-80: maintain current state (dead zone)
        if (!isCompact && scrollTop > SCROLL_THRESHOLD_ENTER) {
            isCompact = true;
            header.classList.add('is-scrolled');
        } else if (isCompact && scrollTop < SCROLL_THRESHOLD_EXIT) {
            isCompact = false;
            header.classList.remove('is-scrolled');
        }

        ticking = false;
    };

    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(updateScroll);
            ticking = true;
        }
    }, { passive: true });
}

export default { init };
