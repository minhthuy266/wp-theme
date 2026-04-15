/**
 * Utility Functions - AffiliateCMS
 * Core helpers used across all modules
 */

/**
 * Get element(s) by selector
 */
export const $ = (selector, context = document) => context.querySelector(selector);
export const $$ = (selector, context = document) => [...context.querySelectorAll(selector)];

/**
 * Add event listener with delegation support
 */
export const on = (element, event, selector, handler) => {
    if (typeof selector === 'function') {
        handler = selector;
        element.addEventListener(event, handler);
    } else {
        element.addEventListener(event, e => {
            const target = e.target.closest(selector);
            if (target) handler.call(target, e);
        });
    }
};

/**
 * Debounce function
 */
export const debounce = (fn, delay = 100) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn.apply(null, args), delay);
    };
};

/**
 * Local Storage helper
 */
export const storage = {
    get: (key, defaultValue = null) => {
        try {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : defaultValue;
        } catch {
            return defaultValue;
        }
    },
    set: (key, value) => {
        try {
            localStorage.setItem(key, JSON.stringify(value));
        } catch (e) {
        }
    }
};
