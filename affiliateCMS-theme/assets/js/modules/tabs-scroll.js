/**
 * Tabs Scroll Navigation - AffiliateCMS
 * Handles horizontal scroll navigation for cat2-tabs
 */

const TabsScroll = {
    config: {
        wrapperSelector: '.cat2-tabs__wrapper',
        listSelector: '.cat2-tabs__list',
        prevSelector: '.cat2-tabs__nav--prev',
        nextSelector: '.cat2-tabs__nav--next',
        hiddenClass: 'is-hidden',
        hasScrollLeftClass: 'has-scroll-left',
        scrollAmount: 200
    },

    init() {
        const wrappers = document.querySelectorAll(this.config.wrapperSelector);

        if (!wrappers.length) return;

        wrappers.forEach(wrapper => this.setupWrapper(wrapper));
    },

    setupWrapper(wrapper) {
        const list = wrapper.querySelector(this.config.listSelector);
        const prevBtn = wrapper.querySelector(this.config.prevSelector);
        const nextBtn = wrapper.querySelector(this.config.nextSelector);

        if (!list) return;

        // Initial state check
        this.updateNavVisibility(list, prevBtn, nextBtn);

        // Scroll event to update nav visibility
        list.addEventListener('scroll', () => {
            this.updateNavVisibility(list, prevBtn, nextBtn);
        });

        // Window resize
        window.addEventListener('resize', () => {
            this.updateNavVisibility(list, prevBtn, nextBtn);
        });

        // Nav button clicks
        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.scrollList(list, -this.config.scrollAmount);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.scrollList(list, this.config.scrollAmount);
            });
        }
    },

    scrollList(list, amount) {
        list.scrollBy({
            left: amount,
            behavior: 'smooth'
        });
    },

    updateNavVisibility(list, prevBtn, nextBtn) {
        const scrollLeft = list.scrollLeft;
        const scrollWidth = list.scrollWidth;
        const clientWidth = list.clientWidth;
        const maxScroll = scrollWidth - clientWidth;

        // Check if scrollable
        const isScrollable = scrollWidth > clientWidth;

        // Update prev button
        if (prevBtn) {
            if (!isScrollable || scrollLeft <= 1) {
                prevBtn.classList.add(this.config.hiddenClass);
                list.classList.remove(this.config.hasScrollLeftClass);
            } else {
                prevBtn.classList.remove(this.config.hiddenClass);
                list.classList.add(this.config.hasScrollLeftClass);
            }
        }

        // Update next button
        if (nextBtn) {
            if (!isScrollable || scrollLeft >= maxScroll - 1) {
                nextBtn.classList.add(this.config.hiddenClass);
            } else {
                nextBtn.classList.remove(this.config.hiddenClass);
            }
        }
    }
};

export default TabsScroll;
