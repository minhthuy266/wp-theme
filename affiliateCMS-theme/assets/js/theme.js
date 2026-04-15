/**
 * AffiliateCMS - Theme JavaScript
 * Vanilla JS for all interactions
 */

(function() {
    'use strict';

    // ========================================
    // UTILITY FUNCTIONS
    // ========================================

    /**
     * Get element(s) by selector
     */
    const $ = (selector, context = document) => context.querySelector(selector);
    const $$ = (selector, context = document) => [...context.querySelectorAll(selector)];

    /**
     * Add event listener with delegation support
     */
    const on = (element, event, selector, handler) => {
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
    const debounce = (fn, delay = 100) => {
        let timeoutId;
        return (...args) => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => fn.apply(null, args), delay);
        };
    };

    /**
     * Local Storage helper
     */
    const storage = {
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
                return true;
            } catch (e) {
                return false;
            }
        },
        remove: (key) => {
            try {
                localStorage.removeItem(key);
                return true;
            } catch {
                return false;
            }
        }
    };

    // ========================================
    // THEME TOGGLE (Dark/Light Mode)
    // ========================================

    const ThemeToggle = {
        STORAGE_KEY: 'acms-theme',
        DARK: 'dark',
        LIGHT: 'light',

        getConfig() {
            return window.acmsThemeConfig || { mode: 'system' };
        },

        isForced() {
            const mode = this.getConfig().mode;
            return mode === 'light' || mode === 'dark';
        },

        init() {
            const cfg = this.getConfig();
            const toggleBtns = $$('[data-theme-toggle]');

            // Forced mode: hide toggle buttons, theme already set by inline script
            if (this.isForced()) {
                toggleBtns.forEach(btn => { btn.style.display = 'none'; });
                return;
            }

            // System or Time mode
            const savedTheme = storage.get(this.STORAGE_KEY);

            if (savedTheme) {
                this.setTheme(savedTheme, false);
            } else if (cfg.mode === 'system') {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                this.setTheme(prefersDark ? this.DARK : this.LIGHT, false);
            }
            // For "time" mode without saved preference, inline script already set correct theme

            // Bind toggle buttons
            toggleBtns.forEach(btn => {
                on(btn, 'click', () => this.toggle());
            });

            // System mode: listen for OS preference changes
            if (cfg.mode === 'system') {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                    if (!storage.get(this.STORAGE_KEY)) {
                        this.setTheme(e.matches ? this.DARK : this.LIGHT, false);
                    }
                });
            }
        },

        toggle() {
            if (this.isForced()) return;
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === this.DARK ? this.LIGHT : this.DARK;
            this.setTheme(next, true);
        },

        setTheme(theme, save = true) {
            document.documentElement.setAttribute('data-theme', theme);
            if (save && !this.isForced()) {
                storage.set(this.STORAGE_KEY, theme);
            }
        }
    };

    // ========================================
    // TOPBAR (Dismissible)
    // ========================================

    const Topbar = {
        STORAGE_KEY: 'acms-topbar-dismissed',

        init() {
            const topbar = $('#topbar');
            if (!topbar) return;

            // Check if already dismissed
            if (storage.get(this.STORAGE_KEY)) {
                topbar.classList.add('is-hidden');
                return;
            }

            // Bind close button
            const closeBtn = $('[data-topbar-close]', topbar);
            if (closeBtn) {
                on(closeBtn, 'click', () => this.dismiss(topbar));
            }
        },

        dismiss(topbar) {
            topbar.classList.add('is-hidden');
            storage.set(this.STORAGE_KEY, true);
        }
    };

    // ========================================
    // MOBILE NAVIGATION
    // ========================================

    const MobileNav = {
        init() {
            const mobileNav = $('#mobile-navigation');
            const overlay = $('[data-mobile-nav-overlay]');

            if (!mobileNav) return;

            // Toggle button
            $$('[data-mobile-menu-toggle]').forEach(btn => {
                on(btn, 'click', () => this.toggle(mobileNav, overlay, btn));
            });

            // Close button
            const closeBtn = $('[data-mobile-menu-close]', mobileNav);
            if (closeBtn) {
                on(closeBtn, 'click', () => this.close(mobileNav, overlay));
            }

            // Overlay click
            if (overlay) {
                on(overlay, 'click', () => this.close(mobileNav, overlay));
            }

            // ESC key
            on(document, 'keydown', e => {
                if (e.key === 'Escape' && mobileNav.classList.contains('is-open')) {
                    this.close(mobileNav, overlay);
                }
            });

            // Submenu toggles
            $$('[data-mobile-submenu-toggle]', mobileNav).forEach(btn => {
                on(btn, 'click', () => this.toggleSubmenu(btn));
            });
        },

        toggle(nav, overlay, btn) {
            const isOpen = nav.classList.contains('is-open');

            if (isOpen) {
                this.close(nav, overlay);
            } else {
                this.open(nav, overlay);
            }

            // Update aria-expanded
            $$('[data-mobile-menu-toggle]').forEach(b => {
                b.setAttribute('aria-expanded', !isOpen);
            });
        },

        open(nav, overlay) {
            nav.classList.add('is-open');
            overlay?.classList.add('is-visible');
            document.body.classList.add('mobile-nav-open');
        },

        close(nav, overlay) {
            nav.classList.remove('is-open');
            overlay?.classList.remove('is-visible');
            document.body.classList.remove('mobile-nav-open');

            $$('[data-mobile-menu-toggle]').forEach(b => {
                b.setAttribute('aria-expanded', false);
            });
        },

        toggleSubmenu(btn) {
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
    };

    // ========================================
    // SEARCH MODAL WITH LIVE SEARCH
    // ========================================

    const SearchModal = {
        modal: null,
        input: null,
        resultsContainer: null,
        resultsList: null,
        resultsCount: null,
        resultsAll: null,
        loadingEl: null,
        defaultEl: null,
        searchTimeout: null,
        currentQuery: '',
        MIN_QUERY_LENGTH: 2,
        DEBOUNCE_DELAY: 300,

        init() {
            this.modal = $('#search-modal');
            if (!this.modal) return;

            // Cache elements - New layout structure
            this.input = $('.search-modal__ai-field', this.modal);
            this.suggestionsArea = $('#search-suggestions'); // Try asking + Categories
            this.resultsArea = $('#search-results-area'); // Container for results/loading/no-results
            this.resultsContainer = $('#search-results');
            this.resultsList = $('.search-modal__results-list', this.modal);
            this.resultsCount = $('.search-modal__results-count', this.modal);
            this.resultsAll = $('#search-view-all'); // View all button at bottom
            this.loadingEl = $('#search-loading');
            this.noResultsEl = $('#search-no-results');

            // Open triggers
            $$('[data-search-toggle]').forEach(btn => {
                on(btn, 'click', () => this.open());
            });

            // Close triggers
            $$('[data-search-close]', this.modal).forEach(el => {
                on(el, 'click', () => this.close());
            });

            // Clear results
            const clearBtn = $('[data-search-clear]', this.modal);
            if (clearBtn) {
                on(clearBtn, 'click', () => this.clearSearch());
            }

            // ESC key
            on(document, 'keydown', e => {
                if (e.key === 'Escape' && this.modal.classList.contains('is-open')) {
                    this.close();
                }
            });

            // Ctrl+K shortcut
            on(document, 'keydown', e => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    this.toggle();
                }
            });

            // Live search on input
            if (this.input) {
                on(this.input, 'input', debounce(() => this.handleInput(), this.DEBOUNCE_DELAY));

                // Enter to submit form (go to search results page)
                on(this.input, 'keydown', e => {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        const query = this.input.value.trim();
                        if (query.length >= this.MIN_QUERY_LENGTH) {
                            window.location.href = `${window.acmsData?.homeUrl || '/'}?s=${encodeURIComponent(query)}`;
                        }
                    }
                });
            }

            // Question buttons - fill input and search
            $$('.search-modal__question[data-query]', this.modal).forEach(btn => {
                on(btn, 'click', () => {
                    const query = btn.dataset.query;
                    if (query && this.input) {
                        this.input.value = query;
                        this.handleInput();
                    }
                });
            });
        },

        toggle() {
            if (this.modal.classList.contains('is-open')) {
                this.close();
            } else {
                this.open();
            }
        },

        open() {
            // Close mobile nav first if open
            const mobileNav = $('#mobile-navigation');
            const overlay = $('[data-mobile-nav-overlay]');
            const menuToggle = $('[data-mobile-menu-toggle]');
            if (mobileNav && mobileNav.classList.contains('is-open')) {
                mobileNav.classList.remove('is-open');
                overlay?.classList.remove('is-visible');
                document.body.classList.remove('mobile-nav-open');
                if (menuToggle) {
                    menuToggle.setAttribute('aria-expanded', 'false');
                }
            }

            this.modal.classList.add('is-open');

            // Prevent layout shift when hiding scrollbar
            const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
            document.body.style.overflow = 'hidden';
            document.body.style.paddingRight = scrollbarWidth + 'px';

            // Focus AI input field
            if (this.input) {
                setTimeout(() => this.input.focus(), 100);
            }
        },

        close() {
            this.modal.classList.remove('is-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        },

        clearSearch() {
            if (this.input) {
                this.input.value = '';
                this.input.focus();
            }
            this.showDefault();
        },

        handleInput() {
            const query = this.input.value.trim();

            if (query.length < this.MIN_QUERY_LENGTH) {
                this.showDefault();
                return;
            }

            if (query === this.currentQuery) return;
            this.currentQuery = query;

            this.search(query);
        },

        async search(query) {
            const restUrl = window.acmsData?.restUrl;
            if (!restUrl) {
                return;
            }

            // Show loading
            this.showLoading();

            try {
                const response = await fetch(`${restUrl}search?q=${encodeURIComponent(query)}&limit=6`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                });

                const data = await response.json();

                if (data.success && data.results) {
                    this.showResults(data.results, data.total, query);
                } else {
                    this.showNoResults(query);
                }
            } catch (error) {
                this.showDefault();
            }
        },

        showLoading() {
            // Hide suggestions, show results area with loading
            if (this.suggestionsArea) this.suggestionsArea.hidden = true;
            if (this.resultsArea) this.resultsArea.hidden = false;
            if (this.resultsContainer) this.resultsContainer.hidden = true;
            if (this.noResultsEl) this.noResultsEl.hidden = true;
            if (this.resultsAll) this.resultsAll.hidden = true;
            if (this.loadingEl) this.loadingEl.hidden = false;
        },

        showDefault() {
            // Show suggestions, hide results area
            if (this.resultsArea) this.resultsArea.hidden = true;
            if (this.loadingEl) this.loadingEl.hidden = true;
            if (this.resultsContainer) this.resultsContainer.hidden = true;
            if (this.noResultsEl) this.noResultsEl.hidden = true;
            if (this.resultsAll) this.resultsAll.hidden = true;
            if (this.suggestionsArea) this.suggestionsArea.hidden = false;
            this.currentQuery = '';
        },

        showResults(results, total, query) {
            // Hide loading and suggestions
            if (this.loadingEl) this.loadingEl.hidden = true;
            if (this.suggestionsArea) this.suggestionsArea.hidden = true;
            if (this.resultsArea) this.resultsArea.hidden = false;

            if (results.length === 0) {
                // No results - show no-results state
                if (this.resultsContainer) this.resultsContainer.hidden = true;
                if (this.resultsAll) this.resultsAll.hidden = true;
                if (this.noResultsEl) this.noResultsEl.hidden = false;
            } else {
                // Has results - show results list
                if (this.noResultsEl) this.noResultsEl.hidden = true;

                // Update results count
                if (this.resultsCount) {
                    this.resultsCount.textContent = total === 1
                        ? '1 result found'
                        : `${total} results found`;
                }

                // Update "View all" link and show it
                if (this.resultsAll) {
                    this.resultsAll.href = `${window.acmsData?.homeUrl || '/'}?s=${encodeURIComponent(query)}`;
                    this.resultsAll.hidden = false;
                }

                // Render results
                if (this.resultsList) {
                    this.resultsList.innerHTML = results.map(post => this.renderResultItem(post)).join('');
                }

                if (this.resultsContainer) this.resultsContainer.hidden = false;
            }
        },

        showNoResults(query) {
            this.showResults([], 0, query);
        },

        renderResultItem(post) {
            const thumbnail = post.thumbnail
                ? `<img src="${this.escapeHtml(post.thumbnail)}" alt="" loading="lazy">`
                : `<div class="search-modal__result-placeholder"><i class="bi bi-image"></i></div>`;

            const category = post.category
                ? `<span class="search-modal__result-category">${this.escapeHtml(post.category.name)}</span>`
                : '';

            return `
                <a href="${this.escapeHtml(post.url)}" class="search-modal__result-item">
                    <div class="search-modal__result-image">
                        ${thumbnail}
                    </div>
                    <div class="search-modal__result-content">
                        <h4 class="search-modal__result-title">${this.escapeHtml(post.title)}</h4>
                        <p class="search-modal__result-excerpt">${this.escapeHtml(post.excerpt)}</p>
                        <div class="search-modal__result-meta">
                            ${category}
                        </div>
                    </div>
                </a>
            `;
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    // ========================================
    // HEADER SCROLL EFFECT (Compact mode only)
    // ========================================

    const HeaderScroll = {
        init() {
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
    };

    // ========================================
    // SMOOTH SCROLL
    // ========================================

    const SmoothScroll = {
        init() {
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
    };

    // ========================================
    // BACK TO TOP BUTTON
    // ========================================

    const BackToTop = {
        init() {
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
    };

    // ========================================
    // INTERSECTION OBSERVER ANIMATIONS
    // ========================================

    const ScrollAnimations = {
        init() {
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
    };

    // ========================================
    // DROPDOWN HOVER INTENT (Desktop)
    // ========================================

    const DropdownHover = {
        init() {
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
    };

    // ========================================
    // FORM VALIDATION (Basic)
    // ========================================

    const FormValidation = {
        init() {
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
    };

    // ========================================
    // TABS SCROLL NAVIGATION
    // ========================================

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
            const wrappers = $$(this.config.wrapperSelector);
            if (!wrappers.length) return;

            wrappers.forEach(wrapper => this.setupWrapper(wrapper));
        },

        setupWrapper(wrapper) {
            const list = $(this.config.listSelector, wrapper);
            const prevBtn = $(this.config.prevSelector, wrapper);
            const nextBtn = $(this.config.nextSelector, wrapper);

            if (!list) return;

            // Initial state check
            this.updateNavVisibility(list, prevBtn, nextBtn);

            // Scroll event to update nav visibility
            on(list, 'scroll', () => {
                this.updateNavVisibility(list, prevBtn, nextBtn);
            });

            // Window resize
            on(window, 'resize', debounce(() => {
                this.updateNavVisibility(list, prevBtn, nextBtn);
            }, 100));

            // Nav button clicks
            if (prevBtn) {
                on(prevBtn, 'click', (e) => {
                    e.preventDefault();
                    this.scrollList(list, -this.config.scrollAmount);
                });
            }

            if (nextBtn) {
                on(nextBtn, 'click', (e) => {
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

    // ========================================
    // EXPANDABLE LISTS (Popular Posts)
    // ========================================

    const ExpandableList = {
        init() {
            const toggles = $$('[data-expand-toggle]');

            toggles.forEach(btn => {
                on(btn, 'click', () => {
                    const widget = btn.closest('.sidebar-widget');
                    const list = $('[data-expandable]', widget);
                    const textEl = $('.popular-posts__toggle-text', btn);

                    if (!list) return;

                    const isExpanded = list.classList.toggle('is-expanded');
                    btn.classList.toggle('is-expanded', isExpanded);

                    // Update text
                    if (textEl) {
                        const collapsedText = textEl.dataset.textCollapsed || 'Show More';
                        const expandedText = textEl.dataset.textExpanded || 'Show Less';
                        textEl.textContent = isExpanded ? expandedText : collapsedText;
                    }
                });
            });
        }
    };

    // ========================================
    // COMMENTS (Star Rating, Expandable Form, AJAX)
    // ========================================

    const Comments = {
        STORAGE_KEY: 'acms-commenter',
        PENDING_COMMENTS_KEY: 'acms-pending-comments',

        state: {
            postId: null,
            currentSort: 'newest',
            currentPage: 1,
            totalPages: 1,
            isLoading: false,
            config: null,
            pendingComments: [],
        },

        init() {
            // Get config from localized script
            this.state.config = window.azsComments || null;
            const postIdFromConfig = this.state.config?.postId;
            const postIdFromElement = document.querySelector('[data-post-id]')?.dataset.postId;
            // Ensure postId is a valid number
            this.state.postId = parseInt(postIdFromConfig || postIdFromElement) || null;

            // Load pending comments from localStorage
            this.state.pendingComments = storage.get(this.PENDING_COMMENTS_KEY, []);

            this.initStarRating();
            this.initExpandableForm();
            this.initSortDropdown();
            this.initLoadMore();
            this.initAjaxSubmit();
            this.initReplyForm();
            this.setupGuestFormLayout();
            this.prefillGuestInfo();
            this.restorePendingComments();
        },

        // Setup guest form layout - wrap guest-fields + submit in a flex container
        setupGuestFormLayout() {
            const commentForm = $('.comment-form--featured');
            if (!commentForm) return;

            const guestFields = $('.comment-form__guest-fields', commentForm);
            const submitBtn = $('.comment-form__submit', commentForm);

            // Only for guest form (has guest-fields)
            if (!guestFields || !submitBtn) return;

            // Check if already wrapped
            if (submitBtn.parentElement?.classList.contains('comment-form__footer')) return;

            // Create wrapper for inline layout
            const footer = document.createElement('div');
            footer.className = 'comment-form__footer';

            // Move guest-fields and submit into footer
            guestFields.parentElement.insertBefore(footer, guestFields);
            footer.appendChild(guestFields);
            footer.appendChild(submitBtn);
        },

        // Get saved guest info from LocalStorage
        getGuestInfo() {
            return storage.get(this.STORAGE_KEY, { name: '', email: '', saveInfo: false });
        },

        // Save guest info to LocalStorage
        saveGuestInfo(name, email, saveInfo = true) {
            if (saveInfo && name && email) {
                storage.set(this.STORAGE_KEY, { name, email, saveInfo: true });
            } else if (!saveInfo) {
                // Clear saved info if user unchecked
                storage.remove(this.STORAGE_KEY);
            }
        },

        // Subscribe commenter to newsletter (silent, non-blocking)
        async subscribeToNewsletter(name, email) {
            try {
                const config = window.acmsData;
                if (!config || !config.restUrl) return;

                await fetch(`${config.restUrl}newsletter`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': config.nonce,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        email: email,
                        name: name,
                        source_url: window.location.href,
                    }),
                });
                // Silent - don't show any notification
            } catch (error) {
                // Silent fail - newsletter subscription is non-critical
            }
        },

        // Check if user has saved info (treated as "remembered guest")
        hasGuestInfo() {
            const { name, email } = this.getGuestInfo();
            return !!(name && email);
        },

        // Prefill form fields with saved guest info and update UI
        prefillGuestInfo() {
            const { name, email, saveInfo } = this.getGuestInfo();
            const hasSavedInfo = !!(name && email);

            // Main comment form
            const authorInput = $('#author');
            const emailInput = $('#email');
            const cookiesCheckbox = $('#wp-comment-cookies-consent');
            const commentForm = $('.comment-form--featured');

            if (name && authorInput && !authorInput.value) authorInput.value = name;
            if (email && emailInput && !emailInput.value) emailInput.value = email;
            if (saveInfo && cookiesCheckbox) cookiesCheckbox.checked = true;

            // Update form UI for saved guest (show as "logged in" style)
            if (hasSavedInfo && commentForm) {
                commentForm.classList.add('has-saved-info');
                commentForm.dataset.guestName = name;

                // Update compact view to show user info
                const compactText = $('.comment-form__compact-text', commentForm);
                if (compactText) {
                    compactText.innerHTML = `<strong>${this.escapeHTML(name)}</strong>, share your thoughts...`;
                }

                // Replace guest fields with saved guest info
                const guestFields = $('.comment-form__guest-fields', commentForm);
                if (guestFields && !$('.comment-form__saved-guest', commentForm)) {
                    const avatarColor = this.getAvatarColor(name);
                    const initials = this.getInitials(name);

                    // Create saved guest HTML (same position as guest-fields)
                    const savedGuestHtml = `
                        <div class="comment-form__saved-guest">
                            <span class="comment-form__guest-avatar" style="background-color: ${avatarColor}">${this.escapeHTML(initials)}</span>
                            <span class="comment-form__user-name">${this.escapeHTML(name)}</span>
                            <button type="button" class="comment-form__not-you" data-action="clear-guest-info">Not you?</button>
                        </div>
                    `;

                    // Insert after guest-fields, then hide guest-fields
                    guestFields.insertAdjacentHTML('afterend', savedGuestHtml);
                    guestFields.classList.add('is-hidden');

                    // Bind click handler
                    const notYouBtn = $('.comment-form__not-you', commentForm);
                    if (notYouBtn) {
                        notYouBtn.addEventListener('click', (e) => {
                            e.preventDefault();
                            this.clearGuestInfo();
                        });
                    }
                }
            }

            // Reply forms
            if (name) {
                $$('.comment__reply-form-input[name="author"]').forEach(input => {
                    if (!input.value) input.value = name;
                });
            }
            if (email) {
                $$('.comment__reply-form-input[name="email"]').forEach(input => {
                    if (!input.value) input.value = email;
                });
            }

            // Update reply forms for saved guest
            if (hasSavedInfo) {
                $$('.comment__reply-form').forEach(form => {
                    form.classList.add('has-saved-info');

                    // Hide guest fields (correct class name)
                    const guestFields = $('.comment__reply-form-guest', form);
                    if (guestFields) {
                        guestFields.classList.add('is-hidden');
                    }

                    // Update avatar and context to show saved guest name
                    this.updateReplyFormForGuest(form, name);
                });
            }
        },

        // Update reply form header to show saved guest info
        updateReplyFormForGuest(form, name) {
            // Check if already updated
            if (form.dataset.guestUpdated) return;
            form.dataset.guestUpdated = 'true';

            const avatarContainer = $('.comment__reply-form-avatar', form);
            const contextContainer = $('.comment__reply-form-context', form);

            if (avatarContainer) {
                const avatarColor = this.getAvatarColor(name);
                const initials = this.getInitials(name);
                avatarContainer.innerHTML = `<span class="comment__reply-form-guest-avatar" style="background-color: ${avatarColor}">${this.escapeHTML(initials)}</span>`;
            }

            if (contextContainer) {
                // Get the reply-to name from existing content
                const replyToName = contextContainer.querySelector('strong:last-child')?.textContent || '';
                contextContainer.innerHTML = `
                    <strong>${this.escapeHTML(name)}</strong>
                    replying to
                    <strong>${this.escapeHTML(replyToName)}</strong>
                    <button type="button" class="comment__reply-form-not-you" data-action="clear-guest-info">Not you?</button>
                `;

                // Bind click handler for Not you button
                const notYouBtn = $('.comment__reply-form-not-you', form);
                if (notYouBtn) {
                    notYouBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.clearGuestInfo();
                    });
                }
            }

            // Make submit button full width
            const submitBtn = $('.comment__reply-form-submit', form);
            if (submitBtn) {
                submitBtn.classList.add('comment__reply-form-submit--full');
            }
        },

        // Generate avatar color from name (consistent colors)
        getAvatarColor(name) {
            const colors = [
                '#0D7377', '#E07A5F', '#81B29A', '#F2CC8F', '#3D405B',
                '#5E60CE', '#48CAE4', '#F77F00', '#D62828', '#2A9D8F',
            ];
            let hash = 0;
            for (let i = 0; i < name.length; i++) {
                hash = name.charCodeAt(i) + ((hash << 5) - hash);
            }
            return colors[Math.abs(hash) % colors.length];
        },

        // Get initials from name (first 2 chars)
        getInitials(name) {
            return name.substring(0, 2).toUpperCase();
        },


        // Clear saved guest info and reset form
        clearGuestInfo() {
            storage.remove(this.STORAGE_KEY);

            // Reset form UI
            const commentForm = $('.comment-form--featured');
            if (commentForm) {
                commentForm.classList.remove('has-saved-info');
                delete commentForm.dataset.guestName;

                // Reset compact text
                const compactText = $('.comment-form__compact-text', commentForm);
                if (compactText) {
                    compactText.textContent = 'Share your thoughts...';
                }

                // Show guest fields
                const guestFields = $('.comment-form__guest-fields', commentForm);
                if (guestFields) {
                    guestFields.classList.remove('is-hidden');
                }

                // Remove saved guest info section
                const savedGuestSection = $('.comment-form__saved-guest', commentForm);
                if (savedGuestSection) {
                    savedGuestSection.remove();
                }

                // Remove full-width class from submit button
                const submitBtn = $('.comment-form__submit', commentForm);
                if (submitBtn) {
                    submitBtn.classList.remove('comment-form__submit--full');
                }

                // Clear input values
                const authorInput = $('#author');
                const emailInput = $('#email');
                if (authorInput) authorInput.value = '';
                if (emailInput) emailInput.value = '';
            }

            // Reset reply forms
            $$('.comment__reply-form').forEach(form => {
                form.classList.remove('has-saved-info');
                delete form.dataset.guestUpdated;

                // Reset avatar to default icon
                const avatarContainer = $('.comment__reply-form-avatar', form);
                if (avatarContainer) {
                    avatarContainer.innerHTML = '<i class="bi bi-person"></i>';
                }

                // Reset context - remove "Not you?" button and restore original text
                const contextContainer = $('.comment__reply-form-context', form);
                if (contextContainer) {
                    // Get parent comment author name from the comment
                    const parentComment = form.closest('.comment');
                    const parentAuthor = parentComment?.querySelector('.comment__author')?.textContent || '';
                    const i18n = this.state.config?.i18n || {};
                    contextContainer.innerHTML = `${i18n.replyingTo || 'Replying to'} <strong>${this.escapeHTML(parentAuthor)}</strong>`;
                }

                // Show guest fields
                const guestFields = $('.comment__reply-form-guest', form);
                if (guestFields) {
                    guestFields.classList.remove('is-hidden');
                }

                // Remove full-width class from submit button
                const submitBtn = $('.comment__reply-form-submit', form);
                if (submitBtn) {
                    submitBtn.classList.remove('comment__reply-form-submit--full');
                }

                // Clear inputs
                $$('input', form).forEach(input => {
                    if (input.name === 'author' || input.name === 'email') {
                        input.value = '';
                    }
                });
            });
        },

        // Star Rating - Interactive selector
        initStarRating() {
            $$('.star-rating').forEach(rating => {
                const stars = $$('.star-rating__star', rating);
                const form = rating.closest('form');
                const hiddenInput = form?.querySelector('#acms-rating-input') ||
                                   $('#acms-rating-input');

                stars.forEach(star => {
                    on(star, 'click', (e) => {
                        e.preventDefault();
                        const value = parseInt(star.dataset.value);
                        rating.dataset.rating = value;

                        // Sync with hidden input
                        if (hiddenInput) {
                            hiddenInput.value = value;
                        }

                        // Clear any validation error when user selects a rating
                        this.clearRatingError();

                        // Update star icons
                        stars.forEach((s, index) => {
                            const icon = $('i', s);
                            if (index < value) {
                                icon.classList.remove('bi-star');
                                icon.classList.add('bi-star-fill');
                                s.classList.add('is-active');
                            } else {
                                icon.classList.remove('bi-star-fill');
                                icon.classList.add('bi-star');
                                s.classList.remove('is-active');
                            }
                        });
                    });

                    on(star, 'mouseenter', () => {
                        const value = parseInt(star.dataset.value);
                        stars.forEach((s, index) => {
                            if (index < value) s.classList.add('is-hover');
                        });
                    });

                    on(star, 'mouseleave', () => {
                        stars.forEach(s => s.classList.remove('is-hover'));
                    });
                });
            });
        },

        // Expandable Comment Form
        initExpandableForm() {
            $$('.comment-form--featured').forEach(form => {
                const compactView = $('.comment-form__compact', form);
                const expandedView = $('.comment-form__expanded', form);
                const closeBtn = $('[data-action="collapse-form"]', form);

                const expandForm = () => {
                    form.dataset.expanded = 'true';
                    const textarea = $('textarea', expandedView);
                    if (textarea) setTimeout(() => textarea.focus(), 100);
                };

                const collapseForm = () => {
                    form.dataset.expanded = 'false';
                };

                if (compactView) {
                    on(compactView, 'click', expandForm);
                }

                if (closeBtn) {
                    on(closeBtn, 'click', (e) => {
                        e.stopPropagation();
                        collapseForm();
                    });
                }
            });
        },

        // Sort Dropdown with AJAX
        initSortDropdown() {
            const resetBtn = $('.comment-list__reset');

            $$('.comment-list__sort').forEach(sortContainer => {
                const toggle = $('.comment-list__sort-toggle', sortContainer);
                const options = $$('.comment-list__sort-option', sortContainer);
                const valueDisplay = $('.comment-list__sort-value', sortContainer);

                if (toggle) {
                    on(toggle, 'click', (e) => {
                        e.stopPropagation();
                        sortContainer.classList.toggle('is-open');
                        toggle.setAttribute('aria-expanded', sortContainer.classList.contains('is-open'));
                    });
                }

                options.forEach(option => {
                    on(option, 'click', async () => {
                        const value = option.dataset.value;

                        // Update UI
                        options.forEach(opt => opt.classList.remove('is-active'));
                        option.classList.add('is-active');

                        const labels = { oldest: 'Oldest', newest: 'Newest', rating: 'Top' };
                        if (valueDisplay) {
                            valueDisplay.textContent = labels[value] || value;
                        }

                        sortContainer.classList.remove('is-open');
                        toggle?.setAttribute('aria-expanded', 'false');

                        // Show/hide reset button based on sort value
                        if (resetBtn) {
                            resetBtn.classList.toggle('is-hidden', value === 'newest');
                        }

                        // AJAX fetch
                        this.state.currentSort = value;
                        this.state.currentPage = 1;
                        await this.fetchComments(true);
                    });
                });

                // Close on outside click
                on(document, 'click', (e) => {
                    if (!sortContainer.contains(e.target)) {
                        sortContainer.classList.remove('is-open');
                        toggle?.setAttribute('aria-expanded', 'false');
                    }
                });

                // Reset button click
                if (resetBtn) {
                    on(resetBtn, 'click', async () => {
                        // Reset to newest
                        options.forEach(opt => opt.classList.remove('is-active'));
                        const newestOption = $$('.comment-list__sort-option[data-value="newest"]', sortContainer)[0];
                        if (newestOption) newestOption.classList.add('is-active');

                        if (valueDisplay) {
                            valueDisplay.textContent = 'Newest';
                        }

                        resetBtn.classList.add('is-hidden');

                        // AJAX fetch
                        this.state.currentSort = 'newest';
                        this.state.currentPage = 1;
                        await this.fetchComments(true);
                    });
                }
            });
        },

        // Load More - use event delegation for dynamically created buttons
        initLoadMore() {
            on(document, 'click', '.comment-list__more-btn', async (e) => {
                e.preventDefault();
                const btn = e.target.closest('.comment-list__more-btn');
                if (!btn || this.state.isLoading) return;

                this.state.currentPage++;
                await this.fetchComments(false);
            });
        },

        // AJAX Form Submit
        initAjaxSubmit() {
            const form = $('.comment-form__form');
            if (!form || !this.state.config) return;

            const self = this; // Store reference for use in event handler
            let lastSubmitTime = 0;
            const SUBMIT_COOLDOWN = 10000; // 10 seconds between submits

            on(form, 'submit', async (e) => {
                e.preventDefault();
                if (self.state.isLoading) return;

                const i18n = self.state.config.i18n || {};

                // Client-side rate limiting - prevent rapid submissions
                const now = Date.now();
                const timeSinceLastSubmit = now - lastSubmitTime;
                if (lastSubmitTime > 0 && timeSinceLastSubmit < SUBMIT_COOLDOWN) {
                    const waitSeconds = Math.ceil((SUBMIT_COOLDOWN - timeSinceLastSubmit) / 1000);
                    self.showNotification(`Please wait ${waitSeconds} seconds before submitting again.`, 'error');
                    return;
                }

                const ratingInput = $('#acms-rating-input');
                const ratingValue = parseInt(ratingInput?.value) || 0;

                // Validate rating - must select 1-5 stars
                if (ratingValue < 1 || ratingValue > 5) {
                    self.showRatingError(i18n.ratingRequired || 'Please select a rating before submitting.');
                    return;
                }

                // Clear any previous rating error
                self.clearRatingError();

                const submitBtn = $('.comment-form__submit', form);
                const originalText = submitBtn.innerHTML;

                self.state.isLoading = true;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="bi bi-arrow-repeat spin"></i> ${i18n.submitting || 'Submitting...'}`
                lastSubmitTime = now; // Record submit time

                try {
                    const formData = new FormData(form);

                    const response = await fetch(`${self.state.config.restUrl}comments`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': self.state.config.nonce,
                        },
                        body: JSON.stringify({
                            post_id: parseInt(self.state.postId),
                            content: formData.get('comment'),
                            author_name: formData.get('author') || '',
                            author_email: formData.get('email') || '',
                            rating: ratingValue,
                            parent: 0,
                        }),
                    });

                    const data = await response.json();

                    // Handle rate limit (429) - show toast only, no console error
                    if (response.status === 429) {
                        const retryAfter = data.retry_after || 60;
                        self.showNotification(data.message || `Too many requests. Please wait ${retryAfter} seconds.`, 'error');
                        return;
                    }

                    if (data.success) {
                        const commentList = $('.comment-list__items');
                        if (commentList && data.comment) {
                            const newCommentHTML = self.createCommentHTML(data.comment);
                            commentList.insertAdjacentHTML('afterbegin', newCommentHTML);

                            const newComment = commentList.firstElementChild;
                            newComment.classList.add('comment--new');
                            setTimeout(() => newComment.classList.remove('comment--new'), 500);

                            // Save pending comment to localStorage for persistence
                            if (!data.comment.is_approved) {
                                self.savePendingComment(data.comment);
                            }
                        }

                        self.updateCommentCount(1);

                        // Save guest info to LocalStorage
                        const authorName = formData.get('author');
                        const authorEmail = formData.get('email');
                        // Check if checkbox is checked (value is 'yes' when checked, null when unchecked)
                        const cookiesCheckbox = $('#wp-comment-cookies-consent');
                        // Default to save if checkbox doesn't exist (logged in user) or is checked
                        const saveInfo = !cookiesCheckbox || cookiesCheckbox.checked;

                        // Save guest info if provided
                        if (authorName && authorEmail) {
                            self.saveGuestInfo(authorName, authorEmail, saveInfo);

                            // Also subscribe to newsletter (silent, non-blocking)
                            if (saveInfo) {
                                self.subscribeToNewsletter(authorName, authorEmail);
                            }
                        }

                        form.reset();
                        // Re-fill saved info after reset
                        self.prefillGuestInfo();
                        self.resetStarRating();
                        $('.comment-form--featured').dataset.expanded = 'false';

                        self.showNotification(data.message, 'success');
                        $('.comment-list--empty')?.remove();
                    } else {
                        self.showNotification(data.message || i18n.error, 'error');
                    }
                } catch (error) {
                    self.showNotification(self.state.config.i18n?.error || 'An error occurred.', 'error');
                } finally {
                    self.state.isLoading = false;
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        },

        // Fetch comments via API
        async fetchComments(replace = false) {
            // Validate required data before making API call
            if (!this.state.postId || !this.state.config || this.state.isLoading) {
                return;
            }

            this.state.isLoading = true;
            const commentList = $('.comment-list__items');
            const loadMoreBtn = $('.comment-list__more-btn');
            const i18n = this.state.config.i18n || {};

            if (replace && commentList) {
                commentList.classList.add('is-loading');
            }
            if (loadMoreBtn) {
                loadMoreBtn.classList.add('is-loading');
                loadMoreBtn.innerHTML = `<i class="bi bi-arrow-repeat spin"></i> ${i18n.loading || 'Loading...'}`;
            }

            try {
                const url = new URL(`${this.state.config.restUrl}comments`, window.location.origin);
                url.searchParams.set('post_id', this.state.postId);
                url.searchParams.set('page', this.state.currentPage);
                url.searchParams.set('per_page', '10');
                url.searchParams.set('orderby', this.state.currentSort);

                const response = await fetch(url, {
                    headers: { 'X-WP-Nonce': this.state.config.nonce },
                });

                const data = await response.json();

                if (data.success && commentList) {
                    const commentsHTML = data.comments.map(c => this.createCommentHTML(c)).join('');

                    if (replace) {
                        commentList.innerHTML = commentsHTML;
                    } else {
                        commentList.insertAdjacentHTML('beforeend', commentsHTML);
                    }

                    // Re-init event handlers for new comments (reply form uses delegation, so just need to prefill guest info)
                    this.prefillGuestInfo();

                    // Restore liked state for new comments
                    if (typeof Reactions !== 'undefined' && Reactions.restoreLikedComments) {
                        Reactions.restoreLikedComments();
                    }

                    this.state.totalPages = data.pages;

                    if (loadMoreBtn) {
                        if (this.state.currentPage >= data.pages) {
                            loadMoreBtn.disabled = true;
                            loadMoreBtn.classList.add('is-complete');
                            loadMoreBtn.classList.remove('is-loading');
                            loadMoreBtn.innerHTML = `<i class="bi bi-dash-circle"></i> ${i18n.noMore || 'No more comments'}`;
                        } else {
                            loadMoreBtn.disabled = false;
                            loadMoreBtn.classList.remove('is-loading', 'is-complete');
                            loadMoreBtn.innerHTML = `<i class="bi bi-arrow-down-circle"></i> ${i18n.loadMore || 'Load More Comments'}`;
                        }
                    }
                }
            } catch (error) {
                this.showNotification(i18n.error || 'Failed to load comments.', 'error');
                // Revert page increment on error
                if (this.state.currentPage > 1) {
                    this.state.currentPage--;
                }
            } finally {
                this.state.isLoading = false;
                commentList?.classList.remove('is-loading');
                // Always restore button state (unless completed)
                if (loadMoreBtn && !loadMoreBtn.classList.contains('is-complete')) {
                    loadMoreBtn.classList.remove('is-loading');
                    loadMoreBtn.innerHTML = `<i class="bi bi-arrow-down-circle"></i> ${i18n.loadMore || 'Load More Comments'}`;
                }
            }
        },

        // Create comment HTML
        createCommentHTML(comment) {
            const i18n = this.state.config?.i18n || {};
            const isPending = !comment.is_approved;

            const ratingHTML = comment.rating ? `
                <div class="comment__rating-inline">
                    <div class="comment__stars">
                        <div class="post-card__stars">${this.createStarsHTML(comment.rating)}</div>
                    </div>
                    ${comment.sentiment ? `
                    <span class="comment__rating-text comment__rating-text--${comment.sentiment.class}">
                        ${comment.sentiment.emoji} ${comment.sentiment.text}
                    </span>` : ''}
                </div>
            ` : '';

            // Pending badge for comments awaiting moderation
            const pendingBadge = isPending ? `
                <span class="comment__pending-badge">
                    <i class="bi bi-clock"></i> ${i18n.awaitingModeration || 'Awaiting moderation'}
                </span>
            ` : '';

            const verifiedHTML = comment.author.is_verified
                ? '<span class="comment__verified" title="Verified User"><i class="bi bi-patch-check-fill"></i></span>'
                : '';

            const likeCount = comment.likes || 0;
            const canReply = comment.can_reply !== false;

            const actionsHTML = `
                <div class="comment__actions comment__actions--top">
                    <button class="comment__action comment__action--like" data-comment-id="${comment.id}">
                        <i class="bi bi-hand-thumbs-up"></i>
                        <span class="comment__like-count">${likeCount > 0 ? likeCount : '0'}</span>
                    </button>
                    ${canReply ? `
                    <button class="comment__action" data-action="toggle-reply">
                        <i class="bi bi-reply"></i> ${i18n.reply || 'Reply'}
                    </button>
                    ` : ''}
                </div>
            `;

            const footerHTML = canReply ? `
                <div class="comment__footer">
                    <div class="comment__actions comment__actions--bottom">
                        <button class="comment__action" data-action="toggle-reply">
                            <i class="bi bi-reply"></i> ${i18n.reply || 'Reply'}
                        </button>
                    </div>
                </div>
            ` : '';

            const replyFormHTML = canReply ? `
                <div class="comment__reply-form" data-parent-id="${comment.id}">
                    <div class="comment__reply-form-header">
                        <div class="comment__reply-form-avatar">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="comment__reply-form-context">
                            ${i18n.replyingTo || 'Replying to'} <strong>${this.escapeHTML(comment.author.name)}</strong>
                        </div>
                    </div>
                    <div class="comment__reply-form-body">
                        <textarea class="comment__reply-form-textarea" name="comment" placeholder="${i18n.writeReply || 'Write your reply...'}" rows="2" required></textarea>
                    </div>
                    <div class="comment__reply-form-footer">
                        <div class="comment__reply-form-guest">
                            <input type="text" class="comment__reply-form-input" name="author" placeholder="${i18n.yourName || 'Your name'}" required>
                            <input type="email" class="comment__reply-form-input" name="email" placeholder="${i18n.yourEmail || 'Your email'}" required>
                        </div>
                        <button type="button" class="comment__reply-form-submit" data-action="submit-reply">
                            <i class="bi bi-send-fill"></i> ${i18n.postReply || 'Post Reply'}
                        </button>
                    </div>
                </div>
            ` : '';

            return `
                <li id="comment-${comment.id}" class="comment comment--featured${isPending ? ' comment--pending' : ''}" data-comment-id="${comment.id}">
                    <div class="comment__main">
                        <div class="comment__top">
                            <div class="comment__avatar comment__avatar--initial"
                                 data-initial="${comment.author.initial}"
                                 style="--avatar-bg: ${comment.author.avatar_color}">
                                ${comment.author.initial}
                            </div>
                            <div class="comment__info">
                                <div class="comment__header">
                                    <span class="comment__author">${this.escapeHTML(comment.author.name)}</span>
                                    ${verifiedHTML}
                                    <time class="comment__date" datetime="${comment.date_iso}">${comment.date}</time>
                                    ${pendingBadge}
                                </div>
                                ${ratingHTML}
                            </div>
                            ${isPending ? '' : actionsHTML}
                        </div>
                        <div class="comment__body">${comment.content}</div>
                        ${isPending ? '' : footerHTML}
                        ${isPending ? '' : replyFormHTML}
                    </div>
                    ${comment.replies && comment.replies.length > 0 ? `
                        <ul class="children comment__replies">
                            ${comment.replies.map(reply => this.createReplyHTML(reply)).join('')}
                        </ul>
                    ` : ''}
                </li>
            `;
        },

        createStarsHTML(rating) {
            let html = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= rating) {
                    html += '<i class="bi bi-star-fill"></i>';
                } else if (i - 0.5 <= rating) {
                    html += '<i class="bi bi-star-half"></i>';
                } else {
                    html += '<i class="bi bi-star"></i>';
                }
            }
            return html;
        },

        resetStarRating() {
            const ratingInput = $('#acms-rating-input');
            if (ratingInput) ratingInput.value = '0';

            $$('.comment-form--featured .star-rating').forEach(rating => {
                rating.dataset.rating = '0';
                $$('.star-rating__star', rating).forEach(star => {
                    const icon = $('i', star);
                    icon.classList.remove('bi-star-fill');
                    icon.classList.add('bi-star');
                    star.classList.remove('is-active');
                });
            });
        },

        // Show rating validation error
        showRatingError(message) {
            const ratingContainer = $('.comment-form__rating-select');
            const starRating = $('.comment-form--featured .star-rating');

            if (!ratingContainer || !starRating) return;

            // Add shake animation
            starRating.classList.add('has-error');
            setTimeout(() => starRating.classList.remove('has-error'), 400);

            // Remove existing error
            this.clearRatingError();

            // Add error message
            const errorEl = document.createElement('span');
            errorEl.className = 'star-rating__error';
            errorEl.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${this.escapeHTML(message)}`;
            ratingContainer.appendChild(errorEl);

            // Scroll to rating section
            ratingContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        },

        // Clear rating error
        clearRatingError() {
            const existingError = $('.star-rating__error');
            if (existingError) existingError.remove();
        },

        updateCommentCount(delta) {
            const countEl = $('.comment-list__count');
            if (countEl) {
                const current = parseInt(countEl.textContent) || 0;
                countEl.textContent = current + delta;
            }
        },

        showNotification(message, type = 'info') {
            $$('.acms-notification').forEach(n => n.remove());

            const notification = document.createElement('div');
            notification.className = `acms-notification acms-notification--${type}`;

            const icons = {
                success: 'bi-check-circle-fill',
                error: 'bi-exclamation-circle-fill',
                info: 'bi-info-circle-fill',
            };

            notification.innerHTML = `
                <i class="bi ${icons[type] || icons.info}"></i>
                <span>${this.escapeHTML(message)}</span>
                <button class="acms-notification__close" aria-label="Close">
                    <i class="bi bi-x"></i>
                </button>
            `;

            document.body.appendChild(notification);
            requestAnimationFrame(() => notification.classList.add('is-visible'));

            const autoRemove = setTimeout(() => this.removeNotification(notification), 5000);

            const closeBtn = $('.acms-notification__close', notification);
            if (closeBtn) {
                on(closeBtn, 'click', () => {
                    clearTimeout(autoRemove);
                    this.removeNotification(notification);
                });
            }
        },

        removeNotification(notification) {
            notification.classList.remove('is-visible');
            setTimeout(() => notification.remove(), 300);
        },

        escapeHTML(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        },

        // Save pending comment to localStorage
        savePendingComment(comment) {
            // Only save comments for current post
            const pendingComment = {
                ...comment,
                postId: this.state.postId,
                savedAt: Date.now(),
            };

            // Remove any existing pending comment with same ID
            this.state.pendingComments = this.state.pendingComments.filter(c => c.id !== comment.id);

            // Add new pending comment
            this.state.pendingComments.push(pendingComment);

            // Keep only last 20 pending comments to avoid localStorage bloat
            if (this.state.pendingComments.length > 20) {
                this.state.pendingComments = this.state.pendingComments.slice(-20);
            }

            storage.set(this.PENDING_COMMENTS_KEY, this.state.pendingComments);
        },

        // Remove pending comment from localStorage (when approved)
        removePendingComment(commentId) {
            this.state.pendingComments = this.state.pendingComments.filter(c => c.id !== commentId);
            storage.set(this.PENDING_COMMENTS_KEY, this.state.pendingComments);
        },

        // Restore pending comments on page load
        restorePendingComments() {
            if (!this.state.postId || !this.state.pendingComments.length) return;

            const commentList = $('.comment-list__items');
            if (!commentList) return;

            const i18n = this.state.config?.i18n || {};

            // Filter pending comments for this post
            const postPendingComments = this.state.pendingComments.filter(
                c => c.postId === this.state.postId
            );

            postPendingComments.forEach(pendingComment => {
                // Check if comment is already displayed (might have been approved)
                // Check by ID attribute (server-rendered uses id="comment-XXX")
                // Search from document since replies may be nested inside .children containers
                // AND check it's NOT a pending comment (to avoid matching our own pending element)
                const selector = `#comment-${pendingComment.id}:not(.comment--pending)`;
                const existingComment = document.querySelector(selector);
                if (existingComment) {
                    // Comment is now approved and visible, remove from pending
                    this.removePendingComment(pendingComment.id);
                    return;
                }

                // Check if pending comment is old (more than 7 days)
                const maxAge = 7 * 24 * 60 * 60 * 1000; // 7 days
                if (Date.now() - pendingComment.savedAt > maxAge) {
                    this.removePendingComment(pendingComment.id);
                    return;
                }

                // Display pending comment at top of list
                const pendingHTML = this.createPendingCommentHTML(pendingComment);

                if (pendingComment.parent_id) {
                    // Reply - find parent comment (check both id attribute and data-comment-id)
                    const parentComment = $(`#comment-${pendingComment.parent_id}`, commentList) ||
                                          $(`[data-comment-id="${pendingComment.parent_id}"]`, commentList);
                    if (parentComment) {
                        let repliesContainer = $(':scope > .children, :scope > .comment__replies', parentComment);
                        if (!repliesContainer) {
                            repliesContainer = document.createElement('ul');
                            repliesContainer.className = 'children comment__replies';
                            const commentMain = $('.comment__main', parentComment);
                            if (commentMain) {
                                commentMain.after(repliesContainer);
                            } else {
                                parentComment.appendChild(repliesContainer);
                            }
                        }
                        repliesContainer.insertAdjacentHTML('afterbegin', pendingHTML);
                    }
                } else {
                    // Top-level comment
                    commentList.insertAdjacentHTML('afterbegin', pendingHTML);
                }
            });
        },

        // Create HTML for pending comment display (matches createCommentHTML structure)
        createPendingCommentHTML(comment) {
            const i18n = this.state.config?.i18n || {};

            const ratingHTML = comment.rating ? `
                <div class="comment__rating-inline">
                    <div class="comment__stars">
                        <div class="post-card__stars">${this.createStarsHTML(comment.rating)}</div>
                    </div>
                </div>
            ` : '';

            // Use content_raw if available (plain text), otherwise use content (already has HTML)
            const contentText = comment.content_raw || comment.content || '';
            const contentHTML = comment.content_raw
                ? `<p>${this.escapeHTML(contentText)}</p>`
                : contentText;

            // Get author info
            const authorName = comment.author?.name || i18n.anonymous || 'Anonymous';
            const authorInitial = comment.author?.initial || authorName.substring(0, 2).toUpperCase();
            const avatarColor = comment.author?.avatar_color || '#6B7280';

            return `
                <li id="comment-${comment.id}" class="comment comment--featured comment--pending" data-comment-id="${comment.id}">
                    <div class="comment__main">
                        <div class="comment__top">
                            <div class="comment__avatar comment__avatar--initial"
                                 data-initial="${authorInitial}"
                                 style="--avatar-bg: ${avatarColor}">
                                ${authorInitial}
                            </div>
                            <div class="comment__info">
                                <div class="comment__header">
                                    <span class="comment__author">${this.escapeHTML(authorName)}</span>
                                    <time class="comment__date">${comment.date || i18n.justNow || 'Just now'}</time>
                                    <span class="comment__pending-badge">
                                        <i class="bi bi-clock"></i> ${i18n.awaitingModeration || 'Awaiting moderation'}
                                    </span>
                                </div>
                                ${ratingHTML}
                            </div>
                        </div>
                        <div class="comment__body">
                            ${contentHTML}
                        </div>
                        <div class="comment__pending-note">
                            <i class="bi bi-info-circle"></i>
                            ${i18n.pendingNote || 'Your comment is awaiting moderation and will appear once approved.'}
                        </div>
                    </div>
                </li>
            `;
        },

        // Inline Reply Form Toggle - uses event delegation
        initReplyForm() {
            // Only bind once using event delegation
            if (this._replyFormInitialized) return;
            this._replyFormInitialized = true;

            const self = this;

            // Helper functions
            const updateButtonState = (comment, isOpen) => {
                const i18n = self.state.config?.i18n || {};
                $$('[data-action="toggle-reply"]', comment).forEach(btn => {
                    if (isOpen) {
                        btn.classList.add('is-replying');
                        btn.innerHTML = `<i class="bi bi-x-lg"></i> ${i18n.close || 'Close'}`;
                    } else {
                        btn.classList.remove('is-replying');
                        btn.innerHTML = `<i class="bi bi-reply"></i> ${i18n.reply || 'Reply'}`;
                    }
                });
            };

            const closeAllForms = (exceptComment = null) => {
                $$('.comment--featured').forEach(comment => {
                    if (comment !== exceptComment) {
                        const form = $('.comment__reply-form', comment);
                        if (form?.classList.contains('is-open')) {
                            form.classList.remove('is-open');
                            updateButtonState(comment, false);
                        }
                    }
                });
            };

            // Event delegation for toggle-reply buttons
            on(document, 'click', (e) => {
                const btn = e.target.closest('[data-action="toggle-reply"]');
                if (!btn) return;

                e.preventDefault();
                const comment = btn.closest('.comment--featured');
                const replyForm = $('.comment__reply-form', comment);

                if (!comment || !replyForm) return;

                if (replyForm.classList.contains('is-open')) {
                    // Close
                    replyForm.classList.remove('is-open');
                    updateButtonState(comment, false);
                } else {
                    // Open
                    closeAllForms(comment);
                    replyForm.classList.add('is-open');
                    updateButtonState(comment, true);
                    // Prefill guest info
                    self.prefillGuestInfo();
                    const textarea = $('.comment__reply-form-textarea', replyForm);
                    if (textarea) setTimeout(() => textarea.focus(), 100);
                }
            });

            // Event delegation for submit-reply buttons
            on(document, 'click', async (e) => {
                const btn = e.target.closest('[data-action="submit-reply"]');
                if (!btn) return;

                e.preventDefault();

                const replyForm = btn.closest('.comment__reply-form');
                if (!replyForm || replyForm.classList.contains('is-submitting')) return;

                const parentId = replyForm.dataset.parentId;
                const textarea = $('.comment__reply-form-textarea', replyForm);
                const authorInput = $('input[name="author"]', replyForm);
                const emailInput = $('input[name="email"]', replyForm);

                const content = textarea?.value?.trim();
                const authorName = authorInput?.value?.trim() || '';
                const authorEmail = emailInput?.value?.trim() || '';

                // Validation
                const i18n = self.state.config?.i18n || {};
                if (!content) {
                    self.showNotification(i18n.contentRequired || 'Please write your reply.', 'error');
                    textarea?.focus();
                    return;
                }

                // Check if guest fields are visible (not logged in and no saved info)
                const guestFields = $('.comment__reply-form-guest', replyForm);
                const guestFieldsVisible = guestFields && !guestFields.classList.contains('is-hidden');

                if (guestFieldsVisible) {
                    if (!authorName) {
                        self.showNotification(i18n.nameRequired || 'Please enter your name.', 'error');
                        authorInput?.focus();
                        return;
                    }
                    if (!authorEmail) {
                        self.showNotification(i18n.emailRequired || 'Please enter your email.', 'error');
                        emailInput?.focus();
                        return;
                    }
                }

                // Get saved guest info if fields are hidden
                let finalAuthorName = authorName;
                let finalAuthorEmail = authorEmail;
                if (!guestFieldsVisible) {
                    const savedInfo = self.getGuestInfo();
                    finalAuthorName = savedInfo.name || authorName;
                    finalAuthorEmail = savedInfo.email || authorEmail;
                }

                // Show loading state
                const originalHTML = btn.innerHTML;
                replyForm.classList.add('is-submitting');
                btn.disabled = true;
                btn.innerHTML = `<i class="bi bi-arrow-repeat spin"></i> ${i18n.submitting || 'Submitting...'}`;

                try {
                    const response = await fetch(`${self.state.config.restUrl}comments`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': self.state.config.nonce,
                        },
                        body: JSON.stringify({
                            post_id: parseInt(self.state.postId),
                            content: content,
                            author_name: finalAuthorName,
                            author_email: finalAuthorEmail,
                            rating: 0, // Replies don't have ratings
                            parent_id: parseInt(parentId),
                        }),
                    });

                    const data = await response.json();

                    if (response.status === 429) {
                        const retryAfter = data.retry_after || 60;
                        self.showNotification(data.message || `Too many requests. Please wait ${retryAfter} seconds.`, 'error');
                        return;
                    }

                    if (data.success) {
                        // Insert reply under parent comment
                        const parentComment = replyForm.closest('.comment--featured');
                        if (parentComment && data.comment) {
                            // Create reply HTML (simpler than full comment)
                            const replyHTML = self.createReplyHTML(data.comment);

                            // Find or create replies container
                            // WordPress uses 'children' class for nested comments
                            let repliesContainer = $(':scope > .children, :scope > .comment__replies', parentComment);
                            if (!repliesContainer) {
                                repliesContainer = document.createElement('ul');
                                repliesContainer.className = 'children comment__replies';
                                // Append inside the <li>, after .comment__main
                                const commentMain = $('.comment__main', parentComment);
                                if (commentMain) {
                                    commentMain.after(repliesContainer);
                                } else {
                                    parentComment.appendChild(repliesContainer);
                                }
                            }

                            repliesContainer.insertAdjacentHTML('beforeend', replyHTML);

                            // Animate new reply
                            const newReply = repliesContainer.lastElementChild;
                            if (newReply) {
                                newReply.classList.add('comment--new');
                                setTimeout(() => newReply.classList.remove('comment--new'), 500);

                                // Scroll to new reply
                                newReply.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }

                            // Save pending reply to localStorage for persistence
                            if (!data.comment.is_approved) {
                                self.savePendingComment({
                                    ...data.comment,
                                    parent_id: parseInt(parentId),
                                });
                            }
                        }

                        // Save guest info if provided (like new form does)
                        if (finalAuthorName && finalAuthorEmail) {
                            self.saveGuestInfo(finalAuthorName, finalAuthorEmail, true);
                            // Also subscribe to newsletter (silent)
                            self.subscribeToNewsletter(finalAuthorName, finalAuthorEmail);
                            // Update all reply forms to show saved guest
                            self.prefillGuestInfo();
                        }

                        // Update comment count
                        self.updateCommentCount(1);

                        // Reset and close form
                        textarea.value = '';
                        replyForm.classList.remove('is-open');
                        updateButtonState(parentComment, false);

                        self.showNotification(data.message || i18n.replySuccess || 'Reply posted!', 'success');
                    } else {
                        self.showNotification(data.message || i18n.error || 'Failed to post reply.', 'error');
                    }
                } catch (error) {
                    self.showNotification(i18n.error || 'An error occurred.', 'error');
                } finally {
                    replyForm.classList.remove('is-submitting');
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            });
        },

        // Create reply HTML (simplified comment without rating)
        createReplyHTML(comment) {
            const i18n = this.state.config?.i18n || {};
            const isPending = !comment.is_approved;

            // Pending badge for comments awaiting moderation
            const pendingBadge = isPending ? `
                <span class="comment__pending-badge">
                    <i class="bi bi-clock"></i> ${i18n.awaitingModeration || 'Awaiting moderation'}
                </span>
            ` : '';

            // Actions for reply (like button)
            const likeCount = comment.likes || 0;
            const actionsHTML = !isPending ? `
                <div class="comment__actions comment__actions--reply">
                    <button class="comment__action comment__action--like" data-comment-id="${comment.id}">
                        <i class="bi bi-hand-thumbs-up"></i>
                        <span class="comment__like-count">${likeCount > 0 ? likeCount : '0'}</span>
                    </button>
                </div>
            ` : '';

            // Build nested replies if any
            const nestedRepliesHTML = comment.replies && comment.replies.length > 0 ? `
                <ul class="children comment__replies">
                    ${comment.replies.map(reply => this.createReplyHTML(reply)).join('')}
                </ul>
            ` : '';

            return `
                <li id="comment-${comment.id}" class="comment comment--featured comment--reply${isPending ? ' comment--pending' : ''}" data-comment-id="${comment.id}">
                    <div class="comment__main">
                        <div class="comment__top">
                            <div class="comment__avatar comment__avatar--initial"
                                 data-initial="${comment.author.initial}"
                                 style="--avatar-bg: ${comment.author.avatar_color}">
                                ${comment.author.initial}
                            </div>
                            <div class="comment__info">
                                <div class="comment__header">
                                    <span class="comment__author">${this.escapeHTML(comment.author.name)}</span>
                                    <time class="comment__date" datetime="${comment.date_iso}">${comment.date}</time>
                                    ${pendingBadge}
                                </div>
                            </div>
                            ${actionsHTML}
                        </div>
                        <div class="comment__body">${comment.content}</div>
                    </div>
                    ${nestedRepliesHTML}
                </li>
            `;
        }
    };

    // ========================================
    // RELATED SLIDER - Carousel
    // ========================================

    const RelatedSlider = {
        init() {
            const sliderContainers = $$('[data-related-slider]');
            if (!sliderContainers.length) return;

            sliderContainers.forEach(sliderContainer => this.setupSlider(sliderContainer));
        },

        setupSlider(sliderContainer) {
            const container = sliderContainer.closest('.related-content--slider');
            if (!container) return;

            const slider = $('.related-slider__track', sliderContainer);
            const dotsContainer = $('.related-slider__dots', container);
            const prevBtn = $('.related-slider__arrow--prev', sliderContainer);
            const nextBtn = $('.related-slider__arrow--next', sliderContainer);
            const slides = $$('.related-slider__slide', slider);

            if (!slider || !slides.length) return;

            let currentIndex = 0;
            let dots = [];
            let isMobile = window.innerWidth < 640;

            // Check if mobile
            const checkMobile = () => {
                const wasMobile = isMobile;
                isMobile = window.innerWidth < 640;

                if (isMobile) {
                    slider.classList.add('is-touch-enabled');
                } else {
                    slider.classList.remove('is-touch-enabled');
                    slider.style.transform = '';
                }

                return wasMobile !== isMobile;
            };

            // Get max scroll positions based on screen size
            const getMaxIndex = () => {
                const slidesPerView = window.innerWidth >= 1280 ? 4 :
                                      window.innerWidth >= 1024 ? 3 :
                                      window.innerWidth >= 640 ? 2 : 1;
                return Math.max(0, slides.length - slidesPerView);
            };

            // Create dots dynamically
            const createDots = () => {
                if (!dotsContainer) return;

                dotsContainer.innerHTML = '';
                const maxIndex = getMaxIndex();

                for (let i = 0; i <= maxIndex; i++) {
                    const dot = document.createElement('button');
                    dot.className = 'related-slider__dot' + (i === 0 ? ' is-active' : '');
                    dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
                    on(dot, 'click', () => goTo(i));
                    dotsContainer.appendChild(dot);
                }
                dots = $$('.related-slider__dot', dotsContainer);
            };

            // Go to specific index
            const goTo = (index) => {
                const maxIndex = getMaxIndex();
                currentIndex = Math.max(0, Math.min(index, maxIndex));

                const slideWidth = slides[0].offsetWidth + 16; // 16 = gap
                const offset = currentIndex * slideWidth;

                if (isMobile) {
                    // Mobile: use scroll
                    slider.scrollTo({ left: offset, behavior: 'smooth' });
                } else {
                    // PC: use transform (smoother)
                    slider.style.transform = `translateX(-${offset}px)`;
                }

                updateUI();
            };

            // Update dots and arrows
            const updateUI = () => {
                const maxIndex = getMaxIndex();

                // Update dots
                dots.forEach((dot, i) => {
                    dot.classList.toggle('is-active', i === currentIndex);
                });

                // Update arrows
                if (prevBtn) prevBtn.disabled = currentIndex === 0;
                if (nextBtn) nextBtn.disabled = currentIndex >= maxIndex;
            };

            // Arrow clicks
            if (prevBtn) {
                on(prevBtn, 'click', () => goTo(currentIndex - 1));
            }
            if (nextBtn) {
                on(nextBtn, 'click', () => goTo(currentIndex + 1));
            }

            // Sync on scroll/swipe (mobile only)
            let scrollTimeout;
            on(slider, 'scroll', () => {
                if (!isMobile) return;

                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    const slideWidth = slides[0].offsetWidth + 16;
                    const newIndex = Math.round(slider.scrollLeft / slideWidth);
                    if (newIndex !== currentIndex) {
                        currentIndex = Math.max(0, Math.min(newIndex, getMaxIndex()));
                        updateUI();
                    }
                }, 50);
            });

            // Recreate on resize
            on(window, 'resize', debounce(() => {
                checkMobile();
                createDots();
                goTo(0);
            }, 150));

            // Init
            checkMobile();
            createDots();
            updateUI();
        }
    };

    // ========================================
    // REACTIONS (Like Comments, Heart Posts)
    // ========================================

    const Reactions = {
        DEBOUNCE_DELAY: 500, // ms between clicks
        SAVED_POSTS_KEY: 'acms-saved-posts',
        LIKED_COMMENTS_KEY: 'acms-liked-comments',

        state: {
            config: null,
            lastClickTime: {},
            savedPosts: [],
            likedComments: [],
        },

        init() {
            this.state.config = window.azsComments || null;

            // Load saved posts from localStorage
            this.state.savedPosts = storage.get(this.SAVED_POSTS_KEY, []);

            // Load liked comments from localStorage
            this.state.likedComments = storage.get(this.LIKED_COMMENTS_KEY, []);

            // These require config
            if (this.state.config) {
                this.initCommentLikes();
                this.restoreLikedComments();
                this.initPostHeart();
            }

            // Share button works without config
            this.initPostFooterActions();
        },

        // Restore liked state for comments from localStorage (visual only)
        // Server count is already rendered by PHP, we just restore the "liked" visual state
        restoreLikedComments() {
            if (!this.state.likedComments.length) return;

            $$('.comment__action--like').forEach(btn => {
                const commentId = btn.dataset.commentId;
                if (!commentId) return;

                // Check if this comment is in our liked list
                if (this.state.likedComments.includes(String(commentId))) {
                    btn.classList.add('has-liked');
                    const icon = $('i', btn);
                    if (icon) {
                        icon.classList.remove('bi-hand-thumbs-up');
                        icon.classList.add('bi-hand-thumbs-up-fill');
                    }
                }
            });
        },

        // Save liked comment to localStorage
        saveLikedComment(commentId, liked) {
            // Always store as string for consistency
            const id = String(commentId);
            if (liked) {
                if (!this.state.likedComments.includes(id)) {
                    this.state.likedComments.push(id);
                }
            } else {
                this.state.likedComments = this.state.likedComments.filter(i => i !== id);
            }
            storage.set(this.LIKED_COMMENTS_KEY, this.state.likedComments);
        },

        // Debounce check - returns true if click should be ignored
        shouldDebounce(key) {
            const now = Date.now();
            const lastClick = this.state.lastClickTime[key] || 0;

            if (now - lastClick < this.DEBOUNCE_DELAY) {
                return true;
            }

            this.state.lastClickTime[key] = now;
            return false;
        },

        // Like Comment Buttons
        initCommentLikes() {
            on(document, 'click', '.comment__action--like', async (e) => {
                e.preventDefault();
                const btn = e.target.closest('.comment__action--like');
                if (!btn) return;

                const commentId = btn.dataset.commentId;
                if (!commentId) return;

                // Debounce check
                if (this.shouldDebounce(`like-${commentId}`)) {
                    return;
                }

                // Check current state from localStorage
                const isCurrentlyLiked = this.state.likedComments.includes(String(commentId));
                const action = isCurrentlyLiked ? 'unlike' : 'like';

                // Visual feedback
                btn.classList.add('is-loading');

                try {
                    const apiUrl = `${this.state.config.restUrl}comments/${commentId}/like`;

                    const response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': this.state.config.nonce,
                        },
                        body: JSON.stringify({ action }),
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Update count display
                        const countEl = $('.comment__like-count', btn);
                        if (countEl) {
                            countEl.textContent = this.formatCount(data.count);
                        }

                        // Toggle like state (API returns liked: true/false)
                        const icon = $('i', btn);
                        if (data.liked) {
                            btn.classList.add('has-liked');
                            if (icon) {
                                icon.classList.remove('bi-hand-thumbs-up');
                                icon.classList.add('bi-hand-thumbs-up-fill');
                            }
                        } else {
                            btn.classList.remove('has-liked');
                            if (icon) {
                                icon.classList.remove('bi-hand-thumbs-up-fill');
                                icon.classList.add('bi-hand-thumbs-up');
                            }
                        }

                        // Save to localStorage
                        this.saveLikedComment(commentId, data.liked);

                        // Animation
                        btn.classList.add('is-liked');
                        setTimeout(() => btn.classList.remove('is-liked'), 300);
                    }
                } catch (error) {
                } finally {
                    btn.classList.remove('is-loading');
                }
            });
        },

        // Heart Post Button (TOC Action Bar)
        initPostHeart() {
            const heartBtn = $('#likeBtn');
            if (!heartBtn) return;

            const postId = this.state.config.postId;
            if (!postId) return;

            // Load initial count
            this.loadPostHeartCount(heartBtn, postId);

            on(heartBtn, 'click', async (e) => {
                e.preventDefault();

                // Debounce check
                if (this.shouldDebounce(`heart-${postId}`)) {
                    return;
                }

                // Visual feedback
                heartBtn.classList.add('is-loading');

                try {
                    const response = await fetch(
                        `${this.state.config.restUrl}posts/${postId}/heart`,
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-WP-Nonce': this.state.config.nonce,
                            },
                        }
                    );

                    const data = await response.json();

                    if (data.success) {
                        // Update count display
                        const countEl = $('#likeCount');
                        if (countEl) {
                            countEl.textContent = data.count > 0 ? this.formatCount(data.count) : '0';
                        }

                        // Always show filled heart after clicking
                        const icon = $('i', heartBtn);
                        if (icon) {
                            icon.className = 'bi bi-heart-fill';
                        }
                        heartBtn.classList.add('has-hearts');

                        // Animation - pulse
                        heartBtn.classList.add('is-liked');
                        setTimeout(() => heartBtn.classList.remove('is-liked'), 500);
                    }
                } catch (error) {
                } finally {
                    heartBtn.classList.remove('is-loading');
                }
            });
        },

        // Load post heart count on page load
        async loadPostHeartCount(btn, postId) {
            try {
                const response = await fetch(
                    `${this.state.config.restUrl}posts/${postId}/heart`,
                    {
                        headers: {
                            'X-WP-Nonce': this.state.config.nonce,
                        },
                    }
                );

                const data = await response.json();

                if (data.success) {
                    // Update count
                    const countEl = $('#likeCount');
                    if (countEl) {
                        countEl.textContent = data.count > 0 ? this.formatCount(data.count) : '0';
                    }

                    // Update icon and class based on count
                    const icon = $('i', btn);
                    if (data.count > 0) {
                        btn.classList.add('has-hearts');
                        if (icon) {
                            icon.className = 'bi bi-heart-fill';
                        }
                    }
                }
            } catch (error) {
            }
        },

        // Format number (1K, 1.2M, etc)
        formatCount(count) {
            if (count >= 1000000) {
                return (count / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
            }
            if (count >= 1000) {
                return (count / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
            }
            return count.toString();
        },

        // Post Footer Actions (Like, Save, Share)
        initPostFooterActions() {
            const postId = this.state.config?.postId;

            // Like Button in Footer (requires postId)
            const footerLikeBtn = $('#postFooterLikeBtn');
            if (footerLikeBtn && postId) {
                on(footerLikeBtn, 'click', async (e) => {
                    e.preventDefault();
                    if (this.shouldDebounce(`footer-heart-${postId}`)) return;

                    footerLikeBtn.classList.add('is-animating');
                    setTimeout(() => footerLikeBtn.classList.remove('is-animating'), 300);

                    try {
                        const response = await fetch(
                            `${this.state.config.restUrl}posts/${postId}/heart`,
                            {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-WP-Nonce': this.state.config.nonce,
                                },
                            }
                        );

                        const data = await response.json();

                        if (data.success) {
                            // Update footer button
                            const countEl = $('.post-footer__action-count', footerLikeBtn);
                            if (countEl) {
                                countEl.textContent = this.formatCount(data.count);
                            }
                            const icon = $('i', footerLikeBtn);
                            if (icon) icon.className = 'bi bi-heart-fill';
                            footerLikeBtn.classList.add('has-hearts');

                            // Sync with TOC button
                            const tocBtn = $('#likeBtn');
                            if (tocBtn) {
                                const tocCount = $('#likeCount');
                                if (tocCount) tocCount.textContent = this.formatCount(data.count);
                                const tocIcon = $('i', tocBtn);
                                if (tocIcon) tocIcon.className = 'bi bi-heart-fill';
                                tocBtn.classList.add('has-hearts');
                            }
                        }
                    } catch (error) {
                        // Silent fail
                    }
                });
            }

            // Save Button (requires postId)
            const saveBtn = $('#postFooterSaveBtn');
            if (saveBtn && postId) {
                // Check if already saved
                if (this.state.savedPosts.includes(postId)) {
                    saveBtn.classList.add('is-saved');
                    const icon = $('i', saveBtn);
                    if (icon) icon.className = 'bi bi-bookmark-fill';
                }

                on(saveBtn, 'click', (e) => {
                    e.preventDefault();
                    const isSaved = this.state.savedPosts.includes(postId);

                    if (isSaved) {
                        // Remove from saved
                        this.state.savedPosts = this.state.savedPosts.filter(id => id !== postId);
                        saveBtn.classList.remove('is-saved');
                        const icon = $('i', saveBtn);
                        if (icon) icon.className = 'bi bi-bookmark';
                        Comments.showNotification('Removed from saved posts', 'success');
                    } else {
                        // Add to saved
                        this.state.savedPosts.push(postId);
                        saveBtn.classList.add('is-saved');
                        const icon = $('i', saveBtn);
                        if (icon) icon.className = 'bi bi-bookmark-fill';
                        Comments.showNotification('Saved to your collection', 'success');
                    }

                    // Persist to localStorage
                    storage.set(this.SAVED_POSTS_KEY, this.state.savedPosts);
                });
            }

            // Share Button
            const shareBtn = $('#postFooterShareBtn');
            if (shareBtn) {
                on(shareBtn, 'click', async (e) => {
                    e.preventDefault();

                    const shareData = {
                        title: document.title,
                        url: window.location.href,
                    };

                    // Try native share on mobile
                    if (navigator.share && /Mobi|Android/i.test(navigator.userAgent)) {
                        try {
                            await navigator.share(shareData);
                            return;
                        } catch (err) {
                            // User cancelled or error - fall through to copy
                        }
                    }

                    // Fallback: copy to clipboard
                    try {
                        await navigator.clipboard.writeText(window.location.href);
                        shareBtn.classList.add('is-copied');
                        const icon = $('i', shareBtn);
                        const span = $('span', shareBtn);
                        const originalIcon = icon?.className;
                        const originalText = span?.textContent;

                        if (icon) icon.className = 'bi bi-check-lg';
                        if (span) span.textContent = 'Copied!';

                        Comments.showNotification('Link copied to clipboard!', 'success');

                        setTimeout(() => {
                            shareBtn.classList.remove('is-copied');
                            if (icon) icon.className = originalIcon;
                            if (span) span.textContent = originalText;
                        }, 2000);
                    } catch (err) {
                        Comments.showNotification('Failed to copy link', 'error');
                    }
                });
            }
        }
    };

    // ========================================
    // VIEWS TRACKING (Async REST API)
    // ========================================

    const ViewsTracker = {
        STORAGE_KEY: 'acms_viewed_posts',
        STORAGE_EXPIRY: 3600000, // 1 hour in ms

        init() {
            const config = window.acmsData;
            if (!config || !config.trackViews || !config.postId) return;

            // Client-side rate limiting (1 hour)
            if (this.isRecentlyViewed(config.postId)) return;

            this.trackView(config.postId, config.restUrl);
        },

        async trackView(postId, restUrl) {
            try {
                const response = await fetch(`${restUrl}posts/${postId}/view`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.markAsViewed(postId);
                        this.updateViewDisplay(data.views_formatted);
                    }
                }
            } catch (e) {
                // Silent fail - view tracking is non-critical
            }
        },

        isRecentlyViewed(postId) {
            const viewed = storage.get(this.STORAGE_KEY, {});
            const viewedTime = viewed[postId];

            if (!viewedTime) return false;

            // Check if within expiry period
            return (Date.now() - viewedTime) < this.STORAGE_EXPIRY;
        },

        markAsViewed(postId) {
            const viewed = storage.get(this.STORAGE_KEY, {});
            viewed[postId] = Date.now();

            // Clean up old entries
            const now = Date.now();
            Object.keys(viewed).forEach(id => {
                if ((now - viewed[id]) > this.STORAGE_EXPIRY) {
                    delete viewed[id];
                }
            });

            storage.set(this.STORAGE_KEY, viewed);
        },

        updateViewDisplay(formattedViews) {
            // Update any view count displays on the page
            const viewElements = $$('[data-view-count]');
            viewElements.forEach(el => {
                el.textContent = formattedViews;
            });
        }
    };

    // ========================================
    // LOAD MORE POSTS (REST API)
    // ========================================

    const LoadMore = {
        init() {
            const loadMoreBtn = $('.load-more__btn');
            const postsContainer = $('#posts-container');

            if (!loadMoreBtn || !postsContainer) return;

            // Get archive data
            const archiveType = postsContainer.dataset.archiveType || '';
            const archiveValue = postsContainer.dataset.archiveValue || '';
            const archiveTaxonomy = postsContainer.dataset.archiveTaxonomy || '';
            const totalPages = parseInt(postsContainer.dataset.totalPages) || 1;
            const totalPosts = parseInt(postsContainer.dataset.totalPosts) || 0;

            let currentPage = 1;
            let isLoading = false;

            on(loadMoreBtn, 'click', async () => {
                if (isLoading || currentPage >= totalPages) return;

                isLoading = true;
                currentPage++;

                // Update button state
                loadMoreBtn.classList.add('is-loading');

                try {
                    const restUrl = window.acmsData?.restUrl;
                    if (!restUrl) {
                        return;
                    }

                    // Build query params based on archive type
                    const params = new URLSearchParams({
                        page: currentPage,
                        per_page: postsContainer.children.length > 0 ?
                            Math.min(12, totalPosts) : 12
                    });

                    // Add layout param for correct card rendering
                    const layout = postsContainer.dataset.layout || 'grid';
                    params.set('layout', layout);

                    // Add post_types param for content source
                    const postTypes = postsContainer.dataset.postTypes || 'post';
                    params.set('post_types', postTypes);

                    // Add archive-specific params
                    switch (archiveType) {
                        case 'category':
                            params.set('category', archiveValue);
                            break;
                        case 'tag':
                            params.set('tag', archiveValue);
                            break;
                        case 'author':
                            params.set('author', archiveValue);
                            break;
                        case 'search':
                            params.set('search', archiveValue);
                            break;
                        case 'date':
                            // Parse date value (Y-m-d, Y-m, or Y)
                            const dateParts = archiveValue.split('-');
                            if (dateParts[0]) params.set('year', dateParts[0]);
                            if (dateParts[1]) params.set('month', dateParts[1]);
                            break;
                        case 'taxonomy':
                            if (archiveTaxonomy) {
                                params.set('taxonomy', archiveTaxonomy);
                                params.set('term', archiveValue);
                            }
                            break;
                        case 'home':
                        default:
                            // No additional params needed for home/blog
                            break;
                    }

                    const response = await fetch(`${restUrl}posts?${params.toString()}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (data.success && data.posts && data.posts.length > 0) {
                        // Append new posts HTML
                        data.posts.forEach(post => {
                            const postEl = document.createElement('div');
                            postEl.innerHTML = post.html;
                            const postCard = postEl.firstElementChild;

                            // Add animation class
                            postCard.classList.add('post-card--new');
                            postsContainer.appendChild(postCard);

                            // Remove animation class after transition
                            setTimeout(() => {
                                postCard.classList.remove('post-card--new');
                            }, 500);
                        });

                        // Update counter
                        const loadedCount = postsContainer.children.length;
                        const countEl = $('.load-more__count');
                        if (countEl) {
                            countEl.textContent = loadedCount;
                        }

                        // Hide button if no more pages
                        if (currentPage >= totalPages) {
                            const loadMoreContainer = loadMoreBtn.closest('.load-more');
                            if (loadMoreContainer) {
                                loadMoreContainer.classList.add('is-complete');
                                loadMoreBtn.disabled = true;
                            }
                        }

                        // Update page data attribute
                        loadMoreBtn.dataset.page = currentPage;
                    }
                } catch (error) {
                    currentPage--; // Revert page on error
                } finally {
                    isLoading = false;
                    loadMoreBtn.classList.remove('is-loading');
                }
            });
        }
    };

    // ========================================
    // NEWSLETTER FORM
    // ========================================

    const Newsletter = {
        init() {
            const forms = $$('[data-newsletter-form]');
            if (!forms.length) return;

            forms.forEach(form => {
                this.initForm(form);
            });
        },

        initForm(form) {
            const emailInput = $('.newsletter-form__email', form);
            const nameInput = $('.newsletter-form__name', form);
            const sourceInput = $('input[name="source_url"]', form);
            const submitBtn = $('.newsletter-form__submit', form);
            const btnText = $('.newsletter-form__btn-text', submitBtn);
            const btnLoading = $('.newsletter-form__btn-loading', submitBtn);
            const successEl = $('.newsletter-form__success', form);

            if (!emailInput) return;

            // Set source URL
            if (sourceInput) {
                sourceInput.value = window.location.href;
            }

            // Expand name field when email is focused
            emailInput.addEventListener('focus', () => {
                form.classList.add('is-expanded');
            });

            // Handle form submission
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                // Check if external action (Mailchimp, etc)
                if (form.action && form.action !== window.location.href) {
                    form.submit();
                    return;
                }

                // Get config from acmsData
                const config = window.acmsData;
                if (!config || !config.restUrl) return;

                const email = emailInput.value.trim();
                const name = nameInput ? nameInput.value.trim() : '';
                const sourceUrl = sourceInput ? sourceInput.value : window.location.href;

                if (!email) return;

                // Show loading state
                submitBtn.classList.add('is-loading');
                if (btnText) btnText.style.display = 'none';
                if (btnLoading) btnLoading.style.display = 'inline-flex';

                try {
                    const response = await fetch(`${config.restUrl}newsletter`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': config.nonce,
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            email: email,
                            name: name,
                            source_url: sourceUrl,
                        }),
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Show success state
                        form.classList.add('is-success');
                        if (successEl) successEl.style.display = 'flex';
                    } else {
                        // Show error
                        alert(data.message || 'Failed to subscribe. Please try again.');
                    }
                } catch (error) {
                    alert('Failed to subscribe. Please try again.');
                } finally {
                    // Reset loading state
                    submitBtn.classList.remove('is-loading');
                    if (btnText) btnText.style.display = 'inline';
                    if (btnLoading) btnLoading.style.display = 'none';
                }
            });
        }
    };

    // ========================================
    // INITIALIZE ALL MODULES
    // ========================================

    const init = () => {
        ThemeToggle.init();
        Topbar.init();
        MobileNav.init();
        SearchModal.init();
        HeaderScroll.init();
        SmoothScroll.init();
        BackToTop.init();
        ScrollAnimations.init();
        DropdownHover.init();
        FormValidation.init();
        ExpandableList.init();
        TabsScroll.init();
        Comments.init();
        Reactions.init();
        RelatedSlider.init();
        ViewsTracker.init();
        LoadMore.init();
        Newsletter.init();

    };

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // ========================================
    // MEGA MENU - Auto Column Detection
    // ========================================

    const MegaMenu = {
        init() {
            // Find all mega menu submenus and add column count class
            const submenus = $$('.main-nav .submenu:not(.submenu--compact)');

            submenus.forEach(submenu => {
                const columns = $$('.submenu__column', submenu);
                const colCount = Math.min(Math.max(columns.length, 1), 6); // Clamp 1-6

                // Remove any existing column classes
                for (let i = 1; i <= 6; i++) {
                    submenu.classList.remove(`submenu--cols-${i}`);
                }

                // Add appropriate column class
                submenu.classList.add(`submenu--cols-${colCount}`);
            });
        }
    };

    // Initialize MegaMenu on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => MegaMenu.init());
    } else {
        MegaMenu.init();
    }

    // ========================================
    // CATEGORIES DIRECTORY
    // Search and filter for categories page
    // ========================================

    const CategoriesDirectory = {
        init() {
            const searchInput = $('#cat-search');
            const searchClear = $('#cat-search-clear');
            const treeView = $('#cat-tree-view');
            const azView = $('#cat-az-view');
            const noResults = $('#cat-no-results');
            const resultsCount = $('#cat-results-count');
            const filterBtns = $$('.cat-dir__filter-btn[data-filter]');
            const parentFilterBtn = $('#parent-filter-btn');
            const parentFilterMenu = $('#parent-filter-menu');
            const parentOptions = $$('.cat-dir__filter-option[data-parent]');

            if (!searchInput || !treeView) return;

            let currentView = 'tree'; // 'tree' or 'az'
            let currentParentFilter = 'all';
            let searchTerm = '';

            // Debounce helper
            const debounce = (fn, delay) => {
                let timeout;
                return (...args) => {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => fn(...args), delay);
                };
            };

            // Search functionality
            const performSearch = () => {
                searchTerm = searchInput.value.toLowerCase().trim();

                if (currentView === 'tree') {
                    filterTreeView();
                } else {
                    filterAzView();
                }

                updateResultsCount();
                toggleNoResults();
            };

            // Filter tree view
            const filterTreeView = () => {
                const groups = $$('.cat-tree__group', treeView);
                let visibleGroups = 0;

                groups.forEach(group => {
                    const parentId = group.dataset.parentId;
                    const parentLink = $('.cat-tree__parent-link', group);
                    const parentName = parentLink?.textContent.toLowerCase() || '';

                    // Check parent filter
                    if (currentParentFilter !== 'all' && parentId !== currentParentFilter) {
                        group.classList.add('is-hidden');
                        return;
                    }

                    let hasVisibleChildren = false;

                    // Filter children
                    const children = $$('.cat-tree__child', group);
                    children.forEach(child => {
                        const childName = child.textContent.toLowerCase();
                        const matchesSearch = !searchTerm || childName.includes(searchTerm);

                        if (matchesSearch) {
                            child.classList.remove('is-hidden');
                            hasVisibleChildren = true;
                            highlightText(child, searchTerm);
                        } else {
                            child.classList.add('is-hidden');
                        }

                        // Filter grandchildren
                        const grandchildren = $$('.cat-tree__grandchild', child);
                        grandchildren.forEach(gc => {
                            const gcName = gc.textContent.toLowerCase();
                            if (!searchTerm || gcName.includes(searchTerm)) {
                                gc.classList.remove('is-hidden');
                                if (searchTerm) {
                                    child.classList.remove('is-hidden');
                                    hasVisibleChildren = true;
                                }
                                highlightText(gc, searchTerm);
                            } else {
                                gc.classList.add('is-hidden');
                            }
                        });

                        // Filter level 4
                        const level4Items = $$('.cat-tree__level4-link', child);
                        level4Items.forEach(l4 => {
                            const l4Name = l4.textContent.toLowerCase();
                            if (!searchTerm || l4Name.includes(searchTerm)) {
                                l4.classList.remove('is-hidden');
                                if (searchTerm) {
                                    child.classList.remove('is-hidden');
                                    hasVisibleChildren = true;
                                }
                                highlightText(l4, searchTerm);
                            } else {
                                l4.classList.add('is-hidden');
                            }
                        });
                    });

                    // Show/hide parent group
                    const parentMatches = !searchTerm || parentName.includes(searchTerm);
                    if (parentMatches || hasVisibleChildren) {
                        group.classList.remove('is-hidden');
                        visibleGroups++;
                        if (searchTerm && parentMatches) {
                            highlightText(parentLink, searchTerm);
                        }
                    } else {
                        group.classList.add('is-hidden');
                    }
                });

                return visibleGroups;
            };

            // Filter A-Z view
            const filterAzView = () => {
                const groups = $$('.cat-az__group', azView);
                let visibleItems = 0;

                groups.forEach(group => {
                    const items = $$('.cat-az__item', group);
                    let hasVisibleItems = false;

                    items.forEach(item => {
                        const name = item.textContent.toLowerCase();
                        const matchesSearch = !searchTerm || name.includes(searchTerm);

                        if (matchesSearch) {
                            item.classList.remove('is-hidden');
                            hasVisibleItems = true;
                            visibleItems++;
                            highlightText(item, searchTerm);
                        } else {
                            item.classList.add('is-hidden');
                        }
                    });

                    if (hasVisibleItems) {
                        group.classList.remove('is-hidden');
                    } else {
                        group.classList.add('is-hidden');
                    }
                });

                return visibleItems;
            };

            // Highlight search term in text
            const highlightText = (element, term) => {
                const nameEl = $('.cat-tree__name, .cat-az__name', element);
                if (!nameEl) return;

                // Remove existing highlights
                nameEl.innerHTML = nameEl.textContent;

                if (!term) return;

                const regex = new RegExp(`(${term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                nameEl.innerHTML = nameEl.textContent.replace(regex, '<mark>$1</mark>');
            };

            // Update results count
            const updateResultsCount = () => {
                if (!resultsCount) return;

                let count = 0;
                if (currentView === 'tree') {
                    count = $$('.cat-tree__group:not(.is-hidden)', treeView).length;
                    count += $$('.cat-tree__child:not(.is-hidden)', treeView).length;
                } else {
                    count = $$('.cat-az__item:not(.is-hidden)', azView).length;
                }

                const text = count === 1 ? `${count} category` : `${count} categories`;
                resultsCount.textContent = text;
            };

            // Toggle no results message
            const toggleNoResults = () => {
                if (!noResults) return;

                let hasResults = false;
                if (currentView === 'tree') {
                    hasResults = $$('.cat-tree__group:not(.is-hidden)', treeView).length > 0;
                } else {
                    hasResults = $$('.cat-az__item:not(.is-hidden)', azView).length > 0;
                }

                noResults.style.display = hasResults ? 'none' : 'block';

                if (currentView === 'tree') {
                    treeView.style.display = hasResults ? 'flex' : 'none';
                } else {
                    azView.style.display = hasResults ? 'flex' : 'none';
                }
            };

            // Switch view (All/Tree vs A-Z)
            const switchView = (view) => {
                currentView = view;

                if (view === 'tree' || view === 'all') {
                    currentView = 'tree';
                    treeView.style.display = 'flex';
                    azView.style.display = 'none';
                } else if (view === 'az') {
                    treeView.style.display = 'none';
                    azView.style.display = 'flex';
                }

                performSearch();
            };

            // Event listeners
            on(searchInput, 'input', debounce(performSearch, 200));

            if (searchClear) {
                on(searchClear, 'click', () => {
                    searchInput.value = '';
                    performSearch();
                    searchInput.focus();
                });
            }

            // Filter buttons (All / A-Z)
            filterBtns.forEach(btn => {
                on(btn, 'click', () => {
                    filterBtns.forEach(b => b.classList.remove('is-active'));
                    btn.classList.add('is-active');
                    switchView(btn.dataset.filter);
                });
            });

            // Parent filter dropdown
            if (parentFilterBtn && parentFilterMenu) {
                const dropdown = parentFilterBtn.closest('.cat-dir__filter-dropdown');

                on(parentFilterBtn, 'click', (e) => {
                    e.stopPropagation();
                    dropdown.classList.toggle('is-open');
                });

                // Close dropdown on outside click
                on(document, 'click', () => {
                    dropdown.classList.remove('is-open');
                });

                // Parent filter options
                parentOptions.forEach(option => {
                    on(option, 'click', () => {
                        currentParentFilter = option.dataset.parent;

                        parentOptions.forEach(o => o.classList.remove('is-active'));
                        option.classList.add('is-active');

                        // Update button text
                        if (currentParentFilter === 'all') {
                            parentFilterBtn.innerHTML = `
                                <i class="bi bi-funnel"></i>
                                By Group
                                <i class="bi bi-chevron-down"></i>
                            `;
                            parentFilterBtn.classList.remove('is-active');
                        } else {
                            const icon = option.querySelector('i');
                            const iconClass = icon ? icon.className : 'bi bi-folder';
                            parentFilterBtn.innerHTML = `
                                <i class="${iconClass}"></i>
                                ${option.textContent.trim()}
                                <i class="bi bi-chevron-down"></i>
                            `;
                            parentFilterBtn.classList.add('is-active');
                        }

                        dropdown.classList.remove('is-open');
                        performSearch();
                    });
                });
            }

            // Keyboard shortcut: focus search with /
            on(document, 'keydown', (e) => {
                if (e.key === '/' && document.activeElement !== searchInput) {
                    e.preventDefault();
                    searchInput.focus();
                }
                // ESC to clear search
                if (e.key === 'Escape' && document.activeElement === searchInput) {
                    searchInput.value = '';
                    performSearch();
                    searchInput.blur();
                }
            });
        }
    };

    // Initialize Categories Directory on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => CategoriesDirectory.init());
    } else {
        CategoriesDirectory.init();
    }

})();

/**
 * AI Input - Enter to submit
 * Makes textarea submit on Enter (without Shift)
 */
(function() {
    'use strict';

    function initAiInput() {
        // Handle all AI input textareas
        document.querySelectorAll('.ai-input__field').forEach(textarea => {
            textarea.addEventListener('keydown', function(e) {
                // Submit on Enter (without Shift for new line)
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    const form = this.closest('form');
                    if (form) {
                        form.submit();
                    }
                }
            });

            // Auto-resize textarea
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            });
        });

        // Handle prompt buttons (fill textarea with prompt text)
        document.querySelectorAll('.ai-input__prompt').forEach(btn => {
            btn.addEventListener('click', function() {
                const form = this.closest('form');
                const textarea = form?.querySelector('.ai-input__field');
                if (textarea) {
                    // Remove quotes from prompt text
                    textarea.value = this.textContent.replace(/^["']|["']$/g, '');
                    textarea.focus();
                    // Trigger input event for auto-resize
                    textarea.dispatchEvent(new Event('input'));
                }
            });
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAiInput);
    } else {
        initAiInput();
    }
})();
