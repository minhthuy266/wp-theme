<?php
/**
 * Template Part: Search Modal (Simple)
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */
?>

<div class="search-modal" id="search-modal" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Search', 'affiliatecms'); ?>">
    <div class="search-modal__backdrop" data-search-close></div>
    <div class="search-modal__content">
        <form class="search-modal__form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <div class="search-modal__input-wrap">
                <i class="bi bi-search search-modal__icon"></i>
                <input
                    type="search"
                    class="search-modal__input"
                    name="s"
                    placeholder="<?php esc_attr_e('Search...', 'affiliatecms'); ?>"
                    aria-label="<?php esc_attr_e('Search', 'affiliatecms'); ?>"
                    autocomplete="off"
                    spellcheck="false"
                >
                <kbd class="search-modal__shortcut">ESC</kbd>
            </div>
        </form>
    </div>
</div>
