<?php
/**
 * 404 Error Page Template
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

get_header();
?>

<main id="content" class="site-main">
    <div class="error-page">
        <div class="container">
            <div class="error-page__content">
                <!-- Icon -->
                <div class="error-page__icon">
                    <i class="bi bi-compass"></i>
                </div>

                <!-- Text -->
                <span class="error-page__code">404</span>
                <h1 class="error-page__title"><?php esc_html_e('Lost in the wilderness', 'affiliatecms'); ?></h1>
                <p class="error-page__description">
                    <?php esc_html_e("The page you're looking for doesn't exist or has been moved.", 'affiliatecms'); ?>
                </p>

                <!-- Search -->
                <form class="error-page__search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="search" name="s" placeholder="<?php esc_attr_e('Search...', 'affiliatecms'); ?>" class="error-page__search-input">
                    <button type="submit" class="error-page__search-btn">
                        <i class="bi bi-search"></i>
                    </button>
                </form>

                <!-- Actions -->
                <div class="error-page__actions">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn--primary">
                        <i class="bi bi-house"></i>
                        <?php esc_html_e('Go Home', 'affiliatecms'); ?>
                    </a>
                    <button onclick="history.back()" class="btn btn--outline">
                        <i class="bi bi-arrow-left"></i>
                        <?php esc_html_e('Go Back', 'affiliatecms'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();
