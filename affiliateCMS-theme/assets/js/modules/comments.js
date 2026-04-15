/**
 * Comments Module - Hybrid Enhancement
 *
 * Features:
 * - Star rating selector (syncs with hidden input)
 * - Expandable comment form
 * - AJAX sort (newest/oldest/rating)
 * - AJAX load more
 * - AJAX form submit (real-time)
 * - Inline reply
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

// State management
const state = {
    postId: null,
    currentSort: 'newest',
    currentPage: 1,
    totalPages: 1,
    isLoading: false,
    config: null,
};

/**
 * Initialize all comment functionality
 */
export function init() {
    // Get config from localized script
    state.config = window.azsComments || null;
    state.postId = state.config?.postId || document.querySelector('[data-post-id]')?.dataset.postId;

    initStarRating();
    initExpandableForm();
    initSortDropdown();
    initInlineReply();
    initLoadMore();
    initAjaxSubmit();
}

/**
 * Star Rating - Interactive rating selector
 * Syncs rating value with hidden input for form submission
 */
function initStarRating() {
    document.querySelectorAll('.star-rating').forEach(rating => {
        const stars = rating.querySelectorAll('.star-rating__star');
        const form = rating.closest('form');
        const hiddenInput = form?.querySelector('#acms-rating-input') ||
                           document.getElementById('acms-rating-input');

        stars.forEach(star => {
            star.addEventListener('click', (e) => {
                e.preventDefault();
                const value = parseInt(star.dataset.value);
                rating.dataset.rating = value;

                // Sync with hidden input for form submission
                if (hiddenInput) {
                    hiddenInput.value = value;
                }

                // Update star icons
                stars.forEach((s, index) => {
                    const icon = s.querySelector('i');
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

            star.addEventListener('mouseenter', () => {
                const value = parseInt(star.dataset.value);
                stars.forEach((s, index) => {
                    if (index < value) {
                        s.classList.add('is-hover');
                    }
                });
            });

            star.addEventListener('mouseleave', () => {
                stars.forEach(s => s.classList.remove('is-hover'));
            });
        });
    });
}

/**
 * Expandable Comment Form - Toggle between compact and expanded views
 */
function initExpandableForm() {
    document.querySelectorAll('.comment-form--featured').forEach(form => {
        const compactView = form.querySelector('.comment-form__compact');
        const expandedView = form.querySelector('.comment-form__expanded');
        const closeBtn = form.querySelector('[data-action="collapse-form"]');
        const cancelBtn = form.querySelector('.comment-form__cancel');

        function expandForm() {
            form.dataset.expanded = 'true';
            expandedView?.querySelector('textarea')?.focus();
        }

        function collapseForm() {
            form.dataset.expanded = 'false';
        }

        if (compactView) {
            compactView.addEventListener('click', expandForm);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', collapseForm);
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', collapseForm);
        }
    });
}

/**
 * Sort Dropdown with AJAX
 */
function initSortDropdown() {
    document.querySelectorAll('.comment-list__sort').forEach(sortContainer => {
        const toggle = sortContainer.querySelector('.comment-list__sort-toggle');
        const options = sortContainer.querySelectorAll('.comment-list__sort-option');
        const valueDisplay = sortContainer.querySelector('.comment-list__sort-value');

        // Toggle dropdown
        toggle?.addEventListener('click', (e) => {
            e.stopPropagation();
            sortContainer.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', sortContainer.classList.contains('is-open'));
        });

        // Select option with AJAX
        options.forEach(option => {
            option.addEventListener('click', async () => {
                const value = option.dataset.value;

                // Update active state
                options.forEach(opt => opt.classList.remove('is-active'));
                option.classList.add('is-active');

                // Update display value
                const labels = { oldest: 'Oldest', newest: 'Newest', rating: 'Top' };
                if (valueDisplay) {
                    valueDisplay.textContent = labels[value] || value;
                }

                // Close dropdown
                sortContainer.classList.remove('is-open');
                toggle?.setAttribute('aria-expanded', 'false');

                // AJAX fetch sorted comments
                state.currentSort = value;
                state.currentPage = 1;
                await fetchComments(true);
            });
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!sortContainer.contains(e.target)) {
                sortContainer.classList.remove('is-open');
                toggle?.setAttribute('aria-expanded', 'false');
            }
        });
    });
}

/**
 * Load More with AJAX
 */
function initLoadMore() {
    document.querySelectorAll('.comment-list__more-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            if (state.isLoading) return;

            state.currentPage++;
            await fetchComments(false);
        });
    });
}

/**
 * AJAX Form Submit
 */
function initAjaxSubmit() {
    const form = document.querySelector('.comment-form__form');
    if (!form || !state.config) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (state.isLoading) return;

        const submitBtn = form.querySelector('.comment-form__submit');
        const originalText = submitBtn.innerHTML;
        const i18n = state.config.i18n || {};

        // Show loading state
        state.isLoading = true;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="bi bi-arrow-repeat spin"></i> ${i18n.submitting || 'Submitting...'}`;

        try {
            const formData = new FormData(form);
            const ratingInput = document.getElementById('acms-rating-input');

            const response = await fetch(`${state.config.restUrl}comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': state.config.nonce,
                },
                body: JSON.stringify({
                    post_id: parseInt(state.postId),
                    content: formData.get('comment'),
                    author_name: formData.get('author') || '',
                    author_email: formData.get('email') || '',
                    rating: parseInt(ratingInput?.value) || 0,
                    parent: 0,
                }),
            });

            const data = await response.json();

            if (data.success) {
                // Prepend new comment to list
                const commentList = document.querySelector('.comment-list__items');
                if (commentList && data.comment) {
                    const newCommentHTML = createCommentHTML(data.comment);
                    commentList.insertAdjacentHTML('afterbegin', newCommentHTML);

                    // Animate new comment
                    const newComment = commentList.firstElementChild;
                    newComment.classList.add('comment--new');
                    setTimeout(() => newComment.classList.remove('comment--new'), 500);
                }

                // Update comment count
                updateCommentCount(1);

                // Reset form
                form.reset();
                resetStarRating();
                document.querySelector('.comment-form--featured').dataset.expanded = 'false';

                // Show success message
                showNotification(data.message, 'success');

                // Remove empty state if exists
                document.querySelector('.comment-list--empty')?.remove();

            } else {
                showNotification(data.message || i18n.error, 'error');
            }
        } catch (error) {
            showNotification(state.config.i18n?.error || 'An error occurred.', 'error');
        } finally {
            state.isLoading = false;
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

/**
 * Fetch comments via REST API
 *
 * @param {boolean} replace - Replace existing comments or append
 */
async function fetchComments(replace = false) {
    if (!state.postId || !state.config || state.isLoading) return;

    state.isLoading = true;
    const commentList = document.querySelector('.comment-list__items');
    const loadMoreBtn = document.querySelector('.comment-list__more-btn');
    const i18n = state.config.i18n || {};

    // Show loading state
    if (replace && commentList) {
        commentList.classList.add('is-loading');
    }
    if (loadMoreBtn) {
        loadMoreBtn.classList.add('is-loading');
        loadMoreBtn.innerHTML = `<i class="bi bi-arrow-repeat spin"></i> ${i18n.loading || 'Loading...'}`;
    }

    try {
        const url = new URL(`${state.config.restUrl}comments`, window.location.origin);
        url.searchParams.set('post_id', state.postId);
        url.searchParams.set('page', state.currentPage);
        url.searchParams.set('per_page', '10');
        url.searchParams.set('orderby', state.currentSort);

        const response = await fetch(url, {
            headers: {
                'X-WP-Nonce': state.config.nonce,
            },
        });

        const data = await response.json();

        if (data.success && commentList) {
            const commentsHTML = data.comments.map(c => createCommentHTML(c)).join('');

            if (replace) {
                commentList.innerHTML = commentsHTML;
            } else {
                commentList.insertAdjacentHTML('beforeend', commentsHTML);
            }

            // Update pagination state
            state.totalPages = data.pages;

            // Update load more button
            if (loadMoreBtn) {
                if (state.currentPage >= data.pages) {
                    loadMoreBtn.style.display = 'none';
                } else {
                    loadMoreBtn.style.display = '';
                    loadMoreBtn.classList.remove('is-loading');
                    loadMoreBtn.innerHTML = `<i class="bi bi-arrow-down-circle"></i> ${i18n.loadMore || 'Load More Comments'}`;
                }
            }

            // Re-init inline reply for new comments
            initInlineReply();
        }
    } catch (error) {
        showNotification(i18n.error || 'Failed to load comments.', 'error');
    } finally {
        state.isLoading = false;
        commentList?.classList.remove('is-loading');
        if (loadMoreBtn) {
            loadMoreBtn.classList.remove('is-loading');
        }
    }
}

/**
 * Create comment HTML from API data
 *
 * @param {Object} comment - Comment data from API
 * @returns {string} HTML string
 */
function createCommentHTML(comment) {
    const ratingHTML = comment.rating ? `
        <div class="comment__rating-inline">
            <div class="comment__stars">
                <div class="post-card__stars">
                    ${createStarsHTML(comment.rating)}
                </div>
            </div>
            ${comment.sentiment ? `
            <span class="comment__rating-text comment__rating-text--${comment.sentiment.class}">
                ${comment.sentiment.emoji} ${comment.sentiment.text}
            </span>
            ` : ''}
        </div>
    ` : '';

    const verifiedHTML = comment.author.is_verified
        ? '<span class="comment__verified" title="Verified User"><i class="bi bi-patch-check-fill"></i></span>'
        : '';

    const repliesHTML = comment.replies && comment.replies.length > 0
        ? `<ul class="children">${comment.replies.map(r => createReplyHTML(r)).join('')}</ul>`
        : '';

    return `
        <li id="comment-${comment.id}" class="comment comment--featured">
            <div class="comment__main">
                <div class="comment__top">
                    <div class="comment__avatar comment__avatar--initial"
                         data-initial="${comment.author.initial}"
                         style="--avatar-bg: ${comment.author.avatar_color}">
                        ${comment.author.initial}
                    </div>
                    <div class="comment__info">
                        <div class="comment__header">
                            <span class="comment__author">${escapeHTML(comment.author.name)}</span>
                            ${verifiedHTML}
                            <time class="comment__date" datetime="${comment.date_iso}">
                                ${comment.date}
                            </time>
                        </div>
                        ${ratingHTML}
                    </div>
                </div>
                <div class="comment__body">
                    ${comment.content}
                </div>
                <div class="comment__footer">
                    <div class="comment__actions comment__actions--bottom">
                        <button class="comment__action" data-action="toggle-reply" data-comment-id="${comment.id}">
                            <i class="bi bi-reply"></i> Reply
                        </button>
                    </div>
                </div>
            </div>
            ${repliesHTML}
        </li>
    `;
}

/**
 * Create reply HTML (simplified, no nested replies)
 *
 * @param {Object} reply - Reply data from API
 * @returns {string} HTML string
 */
function createReplyHTML(reply) {
    const verifiedHTML = reply.author.is_verified
        ? '<span class="comment__verified"><i class="bi bi-patch-check-fill"></i></span>'
        : '';

    return `
        <li id="comment-${reply.id}" class="comment comment--featured comment--reply">
            <div class="comment__main">
                <div class="comment__top">
                    <div class="comment__avatar comment__avatar--initial"
                         data-initial="${reply.author.initial}"
                         style="--avatar-bg: ${reply.author.avatar_color}">
                        ${reply.author.initial}
                    </div>
                    <div class="comment__info">
                        <div class="comment__header">
                            <span class="comment__author">${escapeHTML(reply.author.name)}</span>
                            ${verifiedHTML}
                            <time class="comment__date" datetime="${reply.date_iso}">
                                ${reply.date}
                            </time>
                        </div>
                    </div>
                </div>
                <div class="comment__body">
                    ${reply.content}
                </div>
            </div>
        </li>
    `;
}

/**
 * Create stars HTML
 *
 * @param {number} rating - Rating value (1-5)
 * @returns {string} HTML string
 */
function createStarsHTML(rating) {
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
}

/**
 * Inline Reply Form Toggle
 */
function initInlineReply() {
    document.querySelectorAll('.comment--featured').forEach(comment => {
        const replyButtons = comment.querySelectorAll('[data-action="toggle-reply"]');
        const replyForm = comment.querySelector('.comment__reply-form');
        const i18n = state.config?.i18n || {};

        function updateButtonState(isOpen) {
            replyButtons.forEach(btn => {
                if (isOpen) {
                    btn.classList.add('is-replying');
                    btn.innerHTML = `<i class="bi bi-x-lg"></i> ${i18n.close || 'Close'}`;
                } else {
                    btn.classList.remove('is-replying');
                    btn.innerHTML = `<i class="bi bi-reply"></i> ${i18n.reply || 'Reply'}`;
                }
            });
        }

        function closeAllOtherForms() {
            document.querySelectorAll('.comment--featured').forEach(otherComment => {
                if (otherComment !== comment) {
                    const otherForm = otherComment.querySelector('.comment__reply-form');
                    const otherButtons = otherComment.querySelectorAll('[data-action="toggle-reply"]');
                    if (otherForm?.classList.contains('is-open')) {
                        otherForm.classList.remove('is-open');
                        otherButtons.forEach(btn => {
                            btn.classList.remove('is-replying');
                            btn.innerHTML = `<i class="bi bi-reply"></i> ${i18n.reply || 'Reply'}`;
                        });
                    }
                }
            });
        }

        function openReplyForm() {
            if (replyForm) {
                closeAllOtherForms();
                replyForm.classList.add('is-open');
                updateButtonState(true);
                replyForm.querySelector('.comment__reply-form-textarea')?.focus();
            }
        }

        function closeReplyForm() {
            if (replyForm) {
                replyForm.classList.remove('is-open');
                updateButtonState(false);
            }
        }

        replyButtons.forEach(btn => {
            // Remove existing listeners to prevent duplicates
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);

            newBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (replyForm?.classList.contains('is-open')) {
                    closeReplyForm();
                } else {
                    openReplyForm();
                }
            });
        });
    });
}

/**
 * Reset star rating to empty state
 */
function resetStarRating() {
    const ratingInput = document.getElementById('acms-rating-input');
    if (ratingInput) {
        ratingInput.value = '0';
    }

    document.querySelectorAll('.comment-form--featured .star-rating').forEach(rating => {
        rating.dataset.rating = '0';
        rating.querySelectorAll('.star-rating__star').forEach(star => {
            const icon = star.querySelector('i');
            icon.classList.remove('bi-star-fill');
            icon.classList.add('bi-star');
            star.classList.remove('is-active');
        });
    });
}

/**
 * Update comment count display
 *
 * @param {number} delta - Change in count (+1 or -1)
 */
function updateCommentCount(delta) {
    const countEl = document.querySelector('.comment-list__count');
    if (countEl) {
        const current = parseInt(countEl.textContent) || 0;
        countEl.textContent = current + delta;
    }
}

/**
 * Show notification toast
 *
 * @param {string} message - Message to display
 * @param {string} type - 'success', 'error', 'info'
 */
function showNotification(message, type = 'info') {
    // Remove existing notifications
    document.querySelectorAll('.acms-notification').forEach(n => n.remove());

    const notification = document.createElement('div');
    notification.className = `acms-notification acms-notification--${type}`;

    const icons = {
        success: 'bi-check-circle-fill',
        error: 'bi-exclamation-circle-fill',
        info: 'bi-info-circle-fill',
    };

    notification.innerHTML = `
        <i class="bi ${icons[type] || icons.info}"></i>
        <span>${escapeHTML(message)}</span>
        <button class="acms-notification__close" aria-label="Close">
            <i class="bi bi-x"></i>
        </button>
    `;

    document.body.appendChild(notification);

    // Trigger animation
    requestAnimationFrame(() => {
        notification.classList.add('is-visible');
    });

    // Auto remove after 5s
    const autoRemove = setTimeout(() => {
        removeNotification(notification);
    }, 5000);

    // Manual close
    notification.querySelector('.acms-notification__close')?.addEventListener('click', () => {
        clearTimeout(autoRemove);
        removeNotification(notification);
    });
}

/**
 * Remove notification with animation
 *
 * @param {HTMLElement} notification - Notification element
 */
function removeNotification(notification) {
    notification.classList.remove('is-visible');
    setTimeout(() => notification.remove(), 300);
}

/**
 * Escape HTML to prevent XSS
 *
 * @param {string} str - String to escape
 * @returns {string} Escaped string
 */
function escapeHTML(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

export default { init };
