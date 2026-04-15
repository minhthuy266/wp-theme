<?php
/**
 * Brand Taxonomy Archive Template
 * Displays products from this brand using AffiliateCMS Pro list template
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

get_header();

// Get current brand
$brand = get_queried_object();
$brand_name = $brand->name;
$brand_slug = $brand->slug;
$brand_description = term_description($brand->term_id);

// Get brand icon (if custom meta exists)
$brand_icon = get_term_meta($brand->term_id, '_acms_icon', true) ?: 'bi-award';

// Initial load: 12 products
$per_page = 12;

// Query products by brand from AffiliateCMS Pro database
global $wpdb;
$products_table = $wpdb->prefix . 'acms_products';

// Count total products
$total_products = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$products_table} WHERE brand = %s AND status = 'scraped'",
    $brand_name
));

// Get initial 12 products
$products = $wpdb->get_results($wpdb->prepare(
    "SELECT asin FROM {$products_table}
     WHERE brand = %s AND status = 'scraped'
     ORDER BY score DESC, created_at DESC
     LIMIT %d",
    $brand_name,
    $per_page
), ARRAY_A);

// Get ASINs for shortcode
$asins = array_column($products, 'asin');
$has_more = $total_products > $per_page;
?>

<main id="content" class="site-main">

    <!-- Brand Header - Centered Style -->
    <section class="cat-header--centered">
        <div class="container">
            <!-- Icon -->
            <div class="cat-header__icon">
                <i class="bi <?php echo esc_attr($brand_icon); ?>"></i>
            </div>

            <!-- Title -->
            <h1 class="cat-header__title">
                <?php echo esc_html($brand_name); ?>
                <?php esc_html_e('Products', 'affiliatecms'); ?>
            </h1>

            <!-- Breadcrumb -->
            <?php acms_breadcrumb(); ?>

            <!-- Description -->
            <?php if ($brand_description) : ?>
                <p class="cat-header__description"><?php echo wp_kses_post($brand_description); ?></p>
            <?php endif; ?>

            <!-- Meta -->
            <div class="cat-header__meta">
                <div class="cat-header__meta-item">
                    <i class="bi bi-box-seam"></i>
                    <?php
                    printf(
                        esc_html(_n('%d Product', '%d Products', $total_products, 'affiliatecms')),
                        $total_products
                    );
                    ?>
                </div>
                <div class="cat-header__meta-item">
                    <i class="bi bi-clock"></i>
                    <?php esc_html_e('Updated daily', 'affiliatecms'); ?>
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
                        <div class="brand-products-list" id="brandProductsList"
                             data-brand="<?php echo esc_attr($brand_slug); ?>"
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
                            <button type="button" class="load-more__btn" id="brandLoadMore" aria-label="<?php esc_attr_e('Load more products', 'affiliatecms'); ?>">
                                <span class="load-more__text"><?php esc_html_e('Load More Products', 'affiliatecms'); ?></span>
                                <span class="load-more__loading"><?php esc_html_e('Loading...', 'affiliatecms'); ?></span>
                                <span class="load-more__complete"><?php esc_html_e('All Products Loaded', 'affiliatecms'); ?></span>
                                <i class="bi bi-arrow-down-circle load-more__icon"></i>
                                <i class="bi bi-arrow-repeat load-more__spinner"></i>
                                <i class="bi bi-check-circle load-more__check"></i>
                            </button>
                        </div>
                        <?php endif; ?>

                    <?php else : ?>
                        <!-- No Products -->
                        <div class="no-results">
                            <div class="no-results__icon">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <h2 class="no-results__title">
                                <?php
                                printf(
                                    esc_html__('No %s products found', 'affiliatecms'),
                                    esc_html($brand_name)
                                );
                                ?>
                            </h2>
                            <p class="no-results__description">
                                <?php esc_html_e('We haven\'t added products from this brand yet. Check back soon!', 'affiliatecms'); ?>
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
