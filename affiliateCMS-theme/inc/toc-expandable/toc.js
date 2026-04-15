/**
 * TOC Expandable Module
 * AffiliateCMS
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // ========================================
    // ELEMENTS
    // ========================================
    const expandableCard = document.getElementById('tocExpandableCard');
    const expandBtn = document.getElementById('tocExpandBtn');
    const tocExpandable = document.getElementById('tocExpandable');
    const progressBar = document.getElementById('tocProgressBar');

    const actionBar = document.getElementById('tocActionBar');
    const bubble = document.getElementById('tocBubble');
    const bubbleTrigger = document.getElementById('tocBubbleTrigger');
    const bubblePanel = document.getElementById('tocBubblePanel');
    const bubbleClose = document.getElementById('tocBubbleClose');
    const bubbleBadge = document.getElementById('tocBubbleBadge');
    const bubbleProgressBar = document.getElementById('tocBubbleProgressBar');
    const bubbleProgressText = document.getElementById('tocBubbleProgressText');
    const backTopBtn = document.getElementById('tocBackTop');

    // Note: likeBtn/likeCount now handled by Reactions module in theme.js

    // Exit if main elements not found
    if (!tocExpandable) {
        return;
    }

    // Get article content container
    const articleContent = document.querySelector('.entry-content, .post-content, article');

    // Collect all heading targets (all H2 and H3)
    const headingIds = [];
    document.querySelectorAll('.toc-expandable__link, .toc-bubble__link').forEach(link => {
        const href = link.getAttribute('href');
        if (href && href.startsWith('#')) {
            headingIds.push(href.substring(1));
        }
    });

    // Collect only H2 IDs within content container for countdown
    const h2Ids = [];
    if (articleContent) {
        articleContent.querySelectorAll('h2[id]').forEach(h2 => {
            h2Ids.push(h2.id);
        });
    }
    const totalH2Sections = h2Ids.length;

    // ========================================
    // EXPAND/COLLAPSE INLINE TOC
    // ========================================
    if (expandBtn && expandableCard) {
        expandBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            expandableCard.classList.toggle('is-expanded');
        });
    }

    // ========================================
    // BUBBLE PANEL TOGGLE
    // ========================================
    if (bubbleTrigger && bubblePanel) {
        bubbleTrigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            bubblePanel.classList.toggle('is-open');
            bubbleTrigger.classList.toggle('is-open');
        });

        if (bubbleClose) {
            bubbleClose.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                bubblePanel.classList.remove('is-open');
                bubbleTrigger.classList.remove('is-open');
            });
        }

        // Close panel when clicking outside
        document.addEventListener('click', function(e) {
            if (bubble && !bubble.contains(e.target)) {
                bubblePanel.classList.remove('is-open');
                bubbleTrigger.classList.remove('is-open');
            }
        });
    }

    // ========================================
    // BACK TO TOP
    // ========================================
    if (backTopBtn) {
        backTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
            if (bubblePanel) {
                bubblePanel.classList.remove('is-open');
                bubbleTrigger.classList.remove('is-open');
            }
        });
    }

    // ========================================
    // LIKE BUTTON - Now handled by Reactions module in theme.js
    // ========================================

    // ========================================
    // SHOW/HIDE ACTION BAR ON SCROLL
    // ========================================
    function checkBubbleVisibility() {
        if (!actionBar || !tocExpandable) return;

        const tocRect = tocExpandable.getBoundingClientRect();

        // Show action bar when scrolled past TOC
        if (tocRect.bottom < 0) {
            actionBar.classList.add('is-visible');
        } else {
            actionBar.classList.remove('is-visible');
            if (bubblePanel) {
                bubblePanel.classList.remove('is-open');
                bubbleTrigger.classList.remove('is-open');
            }
        }
    }

    // ========================================
    // UPDATE PROGRESS
    // ========================================
    function updateProgress() {
        if (!articleContent) return;

        const articleRect = articleContent.getBoundingClientRect();
        const articleTop = articleContent.offsetTop;
        const articleHeight = articleContent.offsetHeight;
        const scrollTop = window.scrollY;
        const windowHeight = window.innerHeight;

        // Calculate progress
        const startScroll = articleTop - windowHeight * 0.3;
        const endScroll = articleTop + articleHeight - windowHeight * 0.5;
        const progress = Math.min(100, Math.max(0, ((scrollTop - startScroll) / (endScroll - startScroll)) * 100));

        // Update inline progress bar (using transform for better perf)
        if (progressBar) {
            progressBar.style.transform = 'scaleX(' + (progress / 100) + ')';
        }

        // Update bubble ring (circumference = 2 * PI * 30 = 188.5)
        if (bubbleProgressBar) {
            const offset = 188.5 - (188.5 * progress / 100);
            bubbleProgressBar.style.strokeDashoffset = offset;
        }

        // Update text
        if (bubbleProgressText) {
            bubbleProgressText.textContent = Math.round(progress) + '% completed';
        }
    }

    // ========================================
    // ACTIVE SECTION TRACKING
    // ========================================
    function updateActiveSection() {
        const scrollTop = window.scrollY;
        const offset = 100; // Offset from top
        let activeId = null;
        let activeH2Index = 0;

        // Find the current section (for TOC highlighting - all headings)
        for (let i = headingIds.length - 1; i >= 0; i--) {
            const heading = document.getElementById(headingIds[i]);
            if (heading) {
                const headingTop = heading.getBoundingClientRect().top + scrollTop;
                if (scrollTop >= headingTop - offset) {
                    activeId = headingIds[i];
                    break;
                }
            }
        }

        // Find current H2 index (for countdown - only H2 in content)
        for (let i = h2Ids.length - 1; i >= 0; i--) {
            const h2 = document.getElementById(h2Ids[i]);
            if (h2) {
                const h2Top = h2.getBoundingClientRect().top + scrollTop;
                if (scrollTop >= h2Top - offset) {
                    activeH2Index = i;
                    break;
                }
            }
        }

        // Default to first section if at top
        if (!activeId && headingIds.length > 0) {
            activeId = headingIds[0];
        }

        if (activeId) {
            // Update all TOC links
            document.querySelectorAll('.toc-expandable__link, .toc-bubble__link').forEach(link => {
                const href = link.getAttribute('href');
                if (href === '#' + activeId) {
                    link.classList.add('is-active');
                } else {
                    link.classList.remove('is-active');
                }
            });
        }

        // Update badge with COUNTDOWN (H2 sections remaining)
        if (bubbleBadge && totalH2Sections > 0) {
            const remaining = Math.max(0, totalH2Sections - activeH2Index - 1);
            bubbleBadge.textContent = remaining;
        }
    }

    // ========================================
    // TOC LINK CLICK HANDLER
    // ========================================
    const allTocLinks = document.querySelectorAll('.toc-expandable__link, .toc-bubble__link');

    allTocLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const href = link.getAttribute('href');
            const target = document.querySelector(href);

            if (target) {
                // Scroll to target with offset
                const offset = 80;
                const targetTop = target.getBoundingClientRect().top + window.scrollY - offset;
                window.scrollTo({ top: targetTop, behavior: 'smooth' });
            }

            // Update active state
            document.querySelectorAll('.toc-expandable__link').forEach(l => l.classList.remove('is-active'));
            document.querySelectorAll('.toc-bubble__link').forEach(l => l.classList.remove('is-active'));

            // Activate matching links
            const section = link.getAttribute('data-section');
            document.querySelectorAll('[data-section="' + section + '"]').forEach(function(l) {
                l.classList.add('is-active');
            });

            // Update badge with countdown (H2 sections remaining)
            if (bubbleBadge && totalH2Sections > 0) {
                // Find H2 index for this section
                const h2Index = h2Ids.indexOf(section);
                if (h2Index >= 0) {
                    const remaining = Math.max(0, totalH2Sections - h2Index - 1);
                    bubbleBadge.textContent = remaining;
                }
            }

            // Close bubble panel
            if (bubblePanel) {
                bubblePanel.classList.remove('is-open');
                bubbleTrigger.classList.remove('is-open');
            }
        });
    });

    // ========================================
    // SCROLL EVENTS (THROTTLED)
    // ========================================
    let ticking = false;
    let lastScrollY = 0;
    const SCROLL_THRESHOLD = 20; // Only update if scrolled more than 20px

    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(function() {
                const currentScrollY = window.scrollY;
                const scrollDelta = Math.abs(currentScrollY - lastScrollY);

                // Always update visibility and progress (fast operations)
                checkBubbleVisibility();
                updateProgress();

                // Only update active section if scrolled significantly (reduces DOM queries)
                if (scrollDelta > SCROLL_THRESHOLD) {
                    updateActiveSection();
                    lastScrollY = currentScrollY;
                }

                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });

    // ========================================
    // INITIAL STATE
    // ========================================
    checkBubbleVisibility();
    updateProgress();
    updateActiveSection();

});
