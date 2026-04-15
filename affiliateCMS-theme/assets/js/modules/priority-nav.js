/**
 * Priority Navigation Module
 *
 * @package AffiliateCMS
 * @since 1.0.0
 *
 * Automatically moves menu items to a "More" dropdown
 * when they overflow the available space.
 */

(function() {
    'use strict';

    const SELECTORS = {
        nav: '[data-priority-nav]',
        moreItem: '.menu-item--more',
        moreButton: '.menu-item--more > button',
        moreDropdown: '.more-menu__dropdown'
    };

    const CLASSES = {
        hidden: 'is-hidden',
        visible: 'is-visible',
        open: 'is-open'
    };

    class PriorityNav {
        constructor(element) {
            this.nav = element;
            this.moreItem = this.nav.querySelector(SELECTORS.moreItem);
            this.moreButton = this.nav.querySelector(SELECTORS.moreButton);
            this.moreDropdown = this.nav.querySelector(SELECTORS.moreDropdown);

            if (!this.moreItem || !this.moreDropdown) {
                return;
            }

            this.menuItems = [];
            this.itemWidths = [];
            this.resizeTimeout = null;

            this.init();
        }

        init() {
            // Collect all menu items (excluding "More")
            const items = this.nav.querySelectorAll(':scope > .menu-item:not(.menu-item--more)');
            this.menuItems = Array.from(items);

            if (this.menuItems.length === 0) {
                return;
            }

            // Store original widths
            this.measureItems();

            // Initial update
            requestAnimationFrame(() => {
                this.updateNav();
                // Mark as ready (removes CSS overflow fallback)
                this.nav.classList.add('js-priority-nav-ready');
                // Also mark parent nav container
                if (this.nav.parentElement) {
                    this.nav.parentElement.classList.add('js-priority-nav-ready');
                }
            });

            // Watch for resize with debounce
            window.addEventListener('resize', () => {
                clearTimeout(this.resizeTimeout);
                this.resizeTimeout = setTimeout(() => {
                    this.updateNav();
                }, 100);
            });

            // Toggle dropdown on button click
            if (this.moreButton) {
                this.moreButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.toggleDropdown();
                });
            }

            // Close on outside click
            document.addEventListener('click', (e) => {
                if (!this.moreItem.contains(e.target)) {
                    this.closeDropdown();
                }
            });

            // Close on escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeDropdown();
                }
            });
        }

        measureItems() {
            // Temporarily show all items to measure
            this.menuItems.forEach(item => {
                item.classList.remove(CLASSES.hidden);
            });
            this.moreItem.classList.remove(CLASSES.visible);

            // Measure each item
            this.itemWidths = this.menuItems.map(item => {
                const style = window.getComputedStyle(item);
                const marginLeft = parseFloat(style.marginLeft) || 0;
                const marginRight = parseFloat(style.marginRight) || 0;
                return item.offsetWidth + marginLeft + marginRight;
            });

            // Measure more button
            this.moreItem.style.display = 'block';
            this.moreWidth = this.moreItem.offsetWidth + 10; // Extra padding
            this.moreItem.style.display = '';

        }

        updateNav() {
            // Get container width (the nav wrapper, not the ul)
            const container = this.nav.parentElement;
            if (!container) {
                return;
            }

            const containerStyle = window.getComputedStyle(container);
            const containerPadding = parseFloat(containerStyle.paddingLeft) + parseFloat(containerStyle.paddingRight);
            const availableWidth = container.offsetWidth - containerPadding;

            // Reset all items first
            this.showAllItems();

            // Calculate total width needed
            let totalWidth = 0;
            this.itemWidths.forEach(width => {
                totalWidth += width;
            });

            // If everything fits, we're done
            if (totalWidth <= availableWidth) {
                this.moreItem.classList.remove(CLASSES.visible);
                return;
            }

            // Need to hide some items
            this.moreItem.classList.add(CLASSES.visible);

            // Calculate how much space we have (minus more button)
            const maxWidth = availableWidth - this.moreWidth;

            // Find which items to hide (from the end)
            let currentWidth = 0;
            let breakIndex = this.menuItems.length;

            for (let i = 0; i < this.menuItems.length; i++) {
                currentWidth += this.itemWidths[i];
                if (currentWidth > maxWidth) {
                    breakIndex = i;
                    break;
                }
            }

            // Hide items from breakIndex onwards
            const itemsToHide = this.menuItems.slice(breakIndex);
            this.hideItems(itemsToHide);
        }

        showAllItems() {
            this.menuItems.forEach(item => {
                item.classList.remove(CLASSES.hidden);
            });
            this.moreDropdown.innerHTML = '';
        }

        hideItems(items) {
            if (items.length === 0) {
                this.moreItem.classList.remove(CLASSES.visible);
                return;
            }

            // Clear dropdown
            this.moreDropdown.innerHTML = '';

            items.forEach(item => {
                // Hide original
                item.classList.add(CLASSES.hidden);

                // Clone to dropdown
                const link = item.querySelector('a');
                if (link) {
                    const li = document.createElement('li');
                    const clonedLink = link.cloneNode(true);
                    li.appendChild(clonedLink);
                    this.moreDropdown.appendChild(li);
                }
            });
        }

        toggleDropdown() {
            if (this.moreItem.classList.contains(CLASSES.open)) {
                this.closeDropdown();
            } else {
                this.openDropdown();
            }
        }

        openDropdown() {
            this.moreItem.classList.add(CLASSES.open);
            if (this.moreButton) {
                this.moreButton.setAttribute('aria-expanded', 'true');
            }
        }

        closeDropdown() {
            this.moreItem.classList.remove(CLASSES.open);
            if (this.moreButton) {
                this.moreButton.setAttribute('aria-expanded', 'false');
            }
        }
    }

    // Initialize
    function init() {
        const navs = document.querySelectorAll(SELECTORS.nav);
        if (navs.length === 0) {
            return;
        }
        navs.forEach((nav, index) => {
            new PriorityNav(nav);
        });
    }

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
