/**
 * Deals Page - Load More Products
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

(function() {
    'use strict';

    const dealsLoadMore = {
        init() {
            this.container = document.getElementById('dealsProductsList');
            this.button = document.getElementById('dealsLoadMore');

            if (!this.container || !this.button) {
                return;
            }

            this.offset = parseInt(this.container.dataset.offset) || 24;
            this.perPage = parseInt(this.container.dataset.perPage) || 24;
            this.total = parseInt(this.container.dataset.total) || 0;
            this.loading = false;

            this.bindEvents();
        },

        bindEvents() {
            this.button.addEventListener('click', () => this.loadMore());
        },

        async loadMore() {
            if (this.loading) return;

            this.loading = true;
            this.button.classList.add('is-loading');

            try {
                const formData = new FormData();
                formData.append('action', 'acms_load_more_deals');
                formData.append('nonce', acmsDealsAjax.nonce);
                formData.append('offset', this.offset);
                formData.append('per_page', this.perPage);

                const response = await fetch(acmsDealsAjax.ajaxUrl, {
                    method: 'POST',
                    body: formData,
                });

                const data = await response.json();

                if (data.success) {
                    // Insert new products HTML
                    if (data.data.html) {
                        // Get the list container (acms-list div)
                        const listContainer = this.container.querySelector('.acms-list');
                        if (listContainer) {
                            // Create temporary div to parse new HTML
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data.data.html;

                            // Get new product items
                            const newItems = tempDiv.querySelectorAll('.acms-list__item');

                            // Append each new item to the existing list
                            newItems.forEach(item => {
                                listContainer.appendChild(item);
                            });
                        }
                    }

                    // Update offset
                    this.offset = data.data.new_offset;
                    this.container.dataset.offset = this.offset;

                    // Check if there are more products
                    if (!data.data.has_more) {
                        this.button.classList.add('is-complete');
                        this.button.disabled = true;
                    }
                } else {
                    console.error('Load more error:', data.data.message);
                }
            } catch (error) {
                console.error('Load more failed:', error);
            } finally {
                this.loading = false;
                this.button.classList.remove('is-loading');
            }
        },
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => dealsLoadMore.init());
    } else {
        dealsLoadMore.init();
    }
})();
