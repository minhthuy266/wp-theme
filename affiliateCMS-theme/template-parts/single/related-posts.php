<?php
/**
 * Template Part: Related Posts Section
 *
 * Smart Query Logic:
 * 1. Priority: Posts in same category
 * 2. Fallback: Posts with same tags
 * 3. Final fallback: Latest posts of same type
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

$current_post_id = get_the_ID();
$current_post_type = get_post_type();
$posts_per_page = 10; // Show up to 10 for slider
$related_posts = [];

// Taxonomy mapping for custom post types
$category_taxonomies = [
    'post'         => 'category',
    'acms_reviews' => 'acms_reviews_category',
    'acms_deals'   => 'acms_deals_category',
    'acms_guides'  => 'acms_guides_category',
];
$tag_taxonomies = [
    'post'         => 'post_tag',
    'acms_reviews' => 'acms_reviews_tag',
    'acms_deals'   => 'acms_deals_tag',
    'acms_guides'  => 'acms_guides_tag',
];

$cat_taxonomy = isset($category_taxonomies[$current_post_type]) ? $category_taxonomies[$current_post_type] : 'category';
$tag_taxonomy = isset($tag_taxonomies[$current_post_type]) ? $tag_taxonomies[$current_post_type] : 'post_tag';

// ========================================
// 1. PRIORITY: Posts in same category
// ========================================
$categories = get_the_terms($current_post_id, $cat_taxonomy);
if ($categories && !is_wp_error($categories)) {
    $cat_ids = wp_list_pluck($categories, 'term_id');

    $cat_query = new WP_Query([
        'post_type'      => $current_post_type,
        'posts_per_page' => $posts_per_page,
        'post__not_in'   => [$current_post_id],
        'tax_query'      => [
            [
                'taxonomy' => $cat_taxonomy,
                'field'    => 'term_id',
                'terms'    => $cat_ids,
            ],
        ],
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ]);

    if ($cat_query->have_posts()) {
        while ($cat_query->have_posts()) {
            $cat_query->the_post();
            $related_posts[] = get_the_ID();
        }
        wp_reset_postdata();
    }
}

// ========================================
// 2. FALLBACK: Posts with same tags
// ========================================
if (count($related_posts) < $posts_per_page) {
    $tags = get_the_terms($current_post_id, $tag_taxonomy);
    if ($tags && !is_wp_error($tags)) {
        $tag_ids = wp_list_pluck($tags, 'term_id');
        $remaining = $posts_per_page - count($related_posts);
        $exclude_ids = array_merge([$current_post_id], $related_posts);

        $tag_query = new WP_Query([
            'post_type'      => $current_post_type,
            'posts_per_page' => $remaining,
            'post__not_in'   => $exclude_ids,
            'tax_query'      => [
                [
                    'taxonomy' => $tag_taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $tag_ids,
                ],
            ],
            'orderby'        => 'date',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ]);

        if ($tag_query->have_posts()) {
            while ($tag_query->have_posts()) {
                $tag_query->the_post();
                $related_posts[] = get_the_ID();
            }
            wp_reset_postdata();
        }
    }
}

// ========================================
// 3. FINAL FALLBACK: Latest posts of same type
// ========================================
if (count($related_posts) < 4) { // Need at least 4 for decent display
    $remaining = $posts_per_page - count($related_posts);
    $exclude_ids = array_merge([$current_post_id], $related_posts);

    $latest_query = new WP_Query([
        'post_type'      => $current_post_type,
        'posts_per_page' => $remaining,
        'post__not_in'   => $exclude_ids,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ]);

    if ($latest_query->have_posts()) {
        while ($latest_query->have_posts()) {
            $latest_query->the_post();
            $related_posts[] = get_the_ID();
        }
        wp_reset_postdata();
    }
}

// Exit if no related posts found
if (empty($related_posts)) {
    return;
}

// Get primary category for "View All" link
$view_all_link = '';
$view_categories = get_the_terms($current_post_id, $cat_taxonomy);
if ($view_categories && !is_wp_error($view_categories)) {
    $view_all_link = get_term_link($view_categories[0]);
    if (is_wp_error($view_all_link)) {
        $view_all_link = '';
    }
}
if (empty($view_all_link)) {
    $view_all_link = get_permalink(get_option('page_for_posts'));
}
?>

<section class="related-content related-content--slider">
    <div class="container">
        <div class="related-content__header">
            <h3 class="related-content__title">
                <i class="bi bi-bookmark-star-fill"></i>
                <?php esc_html_e('Related Articles', 'affiliatecms'); ?>
            </h3>
            <?php if ($view_all_link) : ?>
                <a href="<?php echo esc_url($view_all_link); ?>" class="related-content__link">
                    <?php esc_html_e('View All', 'affiliatecms'); ?> <i class="bi bi-arrow-right"></i>
                </a>
            <?php endif; ?>
        </div>

        <!-- Slider Container -->
        <div class="related-slider" data-related-slider>
            <!-- Overlay Navigation Arrows -->
            <button class="related-slider__arrow related-slider__arrow--prev" data-slide="prev" aria-label="<?php esc_attr_e('Previous', 'affiliatecms'); ?>">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button class="related-slider__arrow related-slider__arrow--next" data-slide="next" aria-label="<?php esc_attr_e('Next', 'affiliatecms'); ?>">
                <i class="bi bi-chevron-right"></i>
            </button>

            <!-- Slider Wrapper -->
            <div class="related-slider__wrapper">
                <div class="related-slider__track">
                    <?php
                    // Create new query with our collected post IDs
                    $final_query = new WP_Query([
                        'post_type'      => $current_post_type,
                        'posts_per_page' => count($related_posts),
                        'post__in'       => $related_posts,
                        'orderby'        => 'post__in', // Maintain our priority order
                        'no_found_rows'  => true,
                    ]);

                    while ($final_query->have_posts()) {
                        $final_query->the_post();
                        get_template_part('template-parts/content/post-card', null, [
                            'variant'        => 'slider',
                            'class'          => 'related-slider__slide',
                            'show_category'  => false,
                            'show_indicator' => false,
                        ]);
                    }
                    wp_reset_postdata();
                    ?>
                </div>
            </div><!-- /.related-slider__wrapper -->
        </div>

        <!-- Slider Dots (auto-generated by JS) -->
        <div class="related-slider__dots"></div>
    </div>
</section>
