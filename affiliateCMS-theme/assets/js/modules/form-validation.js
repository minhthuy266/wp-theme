/**
 * Form Validation Module - AffiliateCMS
 * Basic form validation
 */

import { $, $$, on } from './utils.js';

/**
 * Initialize form validation
 */
export function init() {
    const searchForms = $$('.hero__search-form, .search-form');

    searchForms.forEach(form => {
        on(form, 'submit', e => {
            const input = $('input[type="search"]', form);
            if (input && !input.value.trim()) {
                e.preventDefault();
                input.focus();
            }
        });
    });
}

export default { init };
