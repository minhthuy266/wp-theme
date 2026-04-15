<?php
/**
 * Template Part: Latest Posts Section (with Sidebar)
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

// Get Customizer settings
$section_icon = get_theme_mod('acms_latest_icon', 'bi-lightning-fill');
$section_title = get_theme_mod('acms_latest_title', __('Latest Reviews', 'affiliatecms'));
$section_subtitle = get_theme_mod('acms_latest_subtitle', __('Expert opinions on the newest products', 'affiliatecms'));
$viewall_text = get_theme_mod('acms_latest_viewall_text', __('View All', 'affiliatecms'));
$viewall_url = get_theme_mod('acms_latest_viewall_url', '');
$posts_count = get_theme_mod('acms_latest_count', 6);
$enable_load_more = get_theme_mod('acms_latest_load_more', true);
$card_layout = get_theme_mod('acms_latest_layout', 'grid');
$post_types_setting = get_theme_mod('acms_latest_post_types', 'all');

// Determine layout classes and card variant
$is_list_layout = ($card_layout === 'list');
$container_class = $is_list_layout ? 'posts-list' : 'posts-grid-v2 posts-grid-v2--3col';
$card_variant = $is_list_layout ? 'list' : 'grid-v2';

// Determine which post types to query
switch ($post_types_setting) {
    case 'acms_reviews':
        $query_post_types = 'acms_reviews';
        break;
    case 'both':
        $query_post_types = ['post', 'acms_reviews'];
        break;
    case 'all':
        $query_post_types = ['post', 'acms_reviews', 'acms_deals', 'acms_guides'];
        break;
    case 'post':
    default:
        $query_post_types = 'post';
        break;
}

// Default to blog page if no URL set
if (empty($viewall_url)) {
    $viewall_url = get_permalink(get_option('page_for_posts'));
}

// Get sidebar position setting (applies to home page)
$sidebar_position = get_theme_mod('acms_sidebar_position', 'right');
$has_sidebar = ($sidebar_position !== 'none');
$sidebar_class = $has_sidebar ? 'content-layout--sidebar-' . $sidebar_position : 'content-layout--no-sidebar';

// Get total posts count for Load More (across selected post types)
$total_posts = 0;
$post_types_array = is_array($query_post_types) ? $query_post_types : [$query_post_types];
foreach ($post_types_array as $pt) {
    $counts = wp_count_posts($pt);
    $total_posts += isset($counts->publish) ? $counts->publish : 0;
}
$max_pages = ceil($total_posts / $posts_count);

// Get latest posts
$latest_posts = new WP_Query([
    'post_type'      => $query_post_types,
    'posts_per_page' => $posts_count,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
]);
?>

<section class="section posts-section">
    <div class="container">
        <!-- Content Layout: Main + Sidebar (position controlled by Customizer) -->
        <div class="content-layout <?php echo esc_attr($sidebar_class); ?>">
            <!-- Main Content -->
            <div class="content-layout__main">
                <!-- Section Header V2 -->
                <div class="section-header-v2">
                    <div class="section-header-v2__left">
                        <?php if ($section_icon) : ?>
                        <div class="section-header-v2__icon">
                            <i class="bi <?php echo esc_attr($section_icon); ?>"></i>
                        </div>
                        <?php endif; ?>
                        <div class="section-header-v2__text">
                            <?php if ($section_title) : ?>
                                <h3 class="section-header-v2__title"><?php echo esc_html($section_title); ?></h3>
                            <?php endif; ?>
                            <?php if ($section_subtitle) : ?>
                                <p class="section-header-v2__subtitle"><?php echo esc_html($section_subtitle); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($viewall_text && $viewall_url) : ?>
                    <a href="<?php echo esc_url($viewall_url); ?>" class="section-header-v2__link">
                        <?php echo esc_html($viewall_text); ?>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Posts Container - Grid or List -->
                <div id="posts-container"
                     class="<?php echo esc_attr($container_class); ?>"
                     data-total-pages="<?php echo esc_attr($max_pages); ?>"
                     data-total-posts="<?php echo esc_attr($total_posts); ?>"
                     data-archive-type="home"
                     data-layout="<?php echo esc_attr($card_layout); ?>"
                     data-post-types="<?php echo esc_attr($post_types_setting); ?>">
                    <?php
                    if ($latest_posts->have_posts()) {
                        while ($latest_posts->have_posts()) {
                            $latest_posts->the_post();
                            get_template_part('template-parts/content/post-card', null, [
                                'variant'        => $card_variant,
                                'show_category'  => $is_list_layout, // Show category on list layout
                                'show_indicator' => $is_list_layout,
                                'show_rating'    => false,
                                'show_views'     => false,
                                'footer_cols'    => $is_list_layout ? '3col' : '2col',
                                'excerpt_words'  => $is_list_layout ? 25 : 15,
                            ]);
                        }
                        wp_reset_postdata();
                    } else {
                        // Demo cards
                        for ($i = 1; $i <= $posts_count; $i++) {
                            get_template_part('template-parts/content/post-card', 'demo', ['index' => $i]);
                        }
                    }
                    ?>
                </div>

                <?php if ($enable_load_more && $max_pages > 1) : ?>
                <!-- Load More -->
                <div class="load-more">
                    <button type="button" class="load-more__btn" aria-label="<?php esc_attr_e('Load more posts', 'affiliatecms'); ?>">
                        <span class="load-more__text"><?php esc_html_e('Load More', 'affiliatecms'); ?></span>
                        <span class="load-more__loading"><?php esc_html_e('Loading...', 'affiliatecms'); ?></span>
                        <span class="load-more__complete"><?php esc_html_e('All Posts Loaded', 'affiliatecms'); ?></span>
                        <i class="bi bi-arrow-down-circle load-more__icon"></i>
                        <i class="bi bi-arrow-repeat load-more__spinner"></i>
                        <i class="bi bi-check-circle load-more__check"></i>
                    </button>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar (only show if sidebar position is not 'none') -->
            <?php if ($has_sidebar) : ?>
                <?php get_sidebar(); ?>
            <?php endif; ?>
        </div>
    </div>
</section>
