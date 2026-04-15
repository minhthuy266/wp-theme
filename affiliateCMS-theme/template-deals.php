<?php
/**
 * Template Name: Deals Page
 * Template Post Type: page
 *
 * Displays all products with active discounts
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

get_header();

// Initial load: 24 products
$per_page = 24;

// Query products with discounts from AffiliateCMS Pro database
global $wpdb;
$products_table = $wpdb->prefix . 'acms_products';

// Count total products with discounts
$total_products = $wpdb->get_var(
    "SELECT COUNT(*) FROM {$products_table}
     WHERE status = 'scraped'
     AND original_price IS NOT NULL
     AND original_price > 0
     AND price IS NOT NULL
     AND price > 0
     AND original_price > price"
);

// Get initial 24 products with highest discount percentage
$products = $wpdb->get_results($wpdb->prepare(
    "SELECT asin,
            price,
            original_price,
            ROUND(((original_price - price) / original_price) * 100, 0) as discount_percent
     FROM {$products_table}
     WHERE status = 'scraped'
     AND original_price IS NOT NULL
     AND original_price > 0
     AND price IS NOT NULL
     AND price > 0
     AND original_price > price
     ORDER BY discount_percent DESC, created_at DESC
     LIMIT %d",
    $per_page
), ARRAY_A);

// Get ASINs for shortcode
$asins = array_column($products, 'asin');
$has_more = $total_products > $per_page;
?>

<main id="content" class="site-main">

    <!-- Deals Header - Centered Style -->
    <section class="cat-header--centered">
        <div class="container">
            <!-- Icon -->
            <div class="cat-header__icon">
                <i class="bi bi-tag-fill"></i>
            </div>

            <!-- Title -->
            <h1 class="cat-header__title">
                <?php esc_html_e('Hot Deals', 'affiliatecms'); ?>
            </h1>

            <!-- Breadcrumb -->
            <?php acms_breadcrumb(); ?>

            <!-- Description -->
            <p class="cat-header__description">
                <?php esc_html_e('Discover the best deals and discounts on top-rated products. Updated daily with the latest price drops!', 'affiliatecms'); ?>
            </p>

            <!-- Meta -->
            <div class="cat-header__meta">
                <div class="cat-header__meta-item">
                    <i class="bi bi-percent"></i>
                    <?php
                    printf(
                        esc_html(_n('%d Deal', '%d Deals', $total_products, 'affiliatecms')),
                        $total_products
                    );
                    ?>
                </div>
                <div class="cat-header__meta-item">
                    <i class="bi bi-clock"></i>
                    <?php esc_html_e('Updated hourly', 'affiliatecms'); ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Content Layout: Main + Sidebar -->
    <section class="posts-section">
        <div class="container">
            <div class="content-layout">
                <!-- Main Content -->
                <div class="content-layout__main">

                    <?php if (!empty($asins)) : ?>
                        <!-- Products List -->
                        <div class="deals-products-list" id="dealsProductsList"
                             data-offset="<?php echo esc_attr($per_page); ?>"
                             data-per-page="<?php echo esc_attr($per_page); ?>"
                             data-total="<?php echo esc_attr($total_products); ?>">
                            <?php
                            // Render products using acms_list shortcode
                            echo do_shortcode('[acms_list asin="' . implode(',', $asins) . '" numbered="true"]');
                            ?>
                        </div>

                        <!-- Load More Button -->
                        <?php if ($has_more) : ?>
                        <div class="load-more">
                            <button type="button" class="load-more__btn" id="dealsLoadMore" aria-label="<?php esc_attr_e('Load more deals', 'affiliatecms'); ?>">
                                <span class="load-more__text"><?php esc_html_e('Load More Deals', 'affiliatecms'); ?></span>
                                <span class="load-more__loading"><?php esc_html_e('Loading...', 'affiliatecms'); ?></span>
                                <span class="load-more__complete"><?php esc_html_e('All Deals Loaded', 'affiliatecms'); ?></span>
                                <i class="bi bi-arrow-down-circle load-more__icon"></i>
                                <i class="bi bi-arrow-repeat load-more__spinner"></i>
                                <i class="bi bi-check-circle load-more__check"></i>
                            </button>
                        </div>
                        <?php endif; ?>

                    <?php else : ?>
                        <!-- No Deals -->
                        <div class="no-results">
                            <div class="no-results__icon">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <h2 class="no-results__title">
                                <?php esc_html_e('No active deals found', 'affiliatecms'); ?>
                            </h2>
                            <p class="no-results__description">
                                <?php esc_html_e('Check back soon for amazing discounts on top products!', 'affiliatecms'); ?>
                            </p>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Sidebar -->
                <?php get_sidebar(); ?>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <?php get_template_part('template-parts/sections/about'); ?>

</main>

<?php
get_footer();
