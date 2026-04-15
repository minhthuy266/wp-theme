<?php
/**
 * Archive Template (Categories, Tags, Date Archives)
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

get_header();

// Get current archive info
$queried_object = get_queried_object();
$archive_icon = 'bi-folder-fill';
$archive_description = '';

// Category taxonomies (for child categories and icon)
$category_taxonomies = ['category', 'acms_reviews_category', 'acms_deals_category', 'acms_guides_category'];
$tag_taxonomies = ['post_tag', 'acms_reviews_tag', 'acms_deals_tag', 'acms_guides_tag'];

if (is_category()) {
    $archive_icon = get_term_meta($queried_object->term_id, '_acms_icon', true) ?: 'bi-folder-fill';
    $archive_description = category_description();
} elseif (is_tag()) {
    $archive_icon = 'bi-tag-fill';
    $archive_description = tag_description();
} elseif (is_tax()) {
    $current_taxonomy = $queried_object->taxonomy ?? '';
    if (in_array($current_taxonomy, $tag_taxonomies, true)) {
        $archive_icon = 'bi-tag-fill';
    } else {
        $archive_icon = get_term_meta($queried_object->term_id, '_acms_icon', true) ?: 'bi-folder-fill';
    }
    $archive_description = term_description($queried_object->term_id, $current_taxonomy);
} elseif (is_date()) {
    $archive_icon = 'bi-calendar3';
}
?>

<main id="content" class="site-main">

    <!-- Category Header - Centered Style -->
    <section class="cat-header--centered">
        <div class="container">
            <!-- Icon -->
            <div class="cat-header__icon">
                <i class="bi <?php echo esc_attr($archive_icon); ?>"></i>
            </div>

            <!-- Title -->
            <h1 class="cat-header__title">
                <?php
                if (is_category()) {
                    single_cat_title();
                } elseif (is_tag()) {
                    single_tag_title();
                } elseif (is_tax()) {
                    single_term_title();
                } elseif (is_date()) {
                    if (is_day()) {
                        printf(esc_html__('Archives: %s', 'affiliatecms'), get_the_date());
                    } elseif (is_month()) {
                        printf(esc_html__('Archives: %s', 'affiliatecms'), get_the_date('F Y'));
                    } elseif (is_year()) {
                        printf(esc_html__('Archives: %s', 'affiliatecms'), get_the_date('Y'));
                    }
                } else {
                    esc_html_e('Archives', 'affiliatecms');
                }
                ?>
            </h1>

            <!-- Breadcrumb -->
            <?php acms_breadcrumb(); ?>

            <!-- Description -->
            <?php if ($archive_description) : ?>
                <p class="cat-header__description"><?php echo wp_kses_post($archive_description); ?></p>
            <?php endif; ?>

            <!-- Meta -->
            <div class="cat-header__meta">
                <div class="cat-header__meta-item">
                    <i class="bi bi-file-earmark-text"></i>
                    <?php
                    global $wp_query;
                    printf(
                        esc_html(_n('%d+ Review', '%d+ Reviews', $wp_query->found_posts, 'affiliatecms')),
                        $wp_query->found_posts
                    );
                    ?>
                </div>
                <div class="cat-header__meta-item">
                    <i class="bi bi-clock"></i>
                    <?php esc_html_e('Updated daily', 'affiliatecms'); ?>
                </div>
            </div>

            <?php
            // Show child categories if on a category or custom category taxonomy archive
            $is_category_archive = is_category() || (is_tax() && in_array($queried_object->taxonomy ?? '', $category_taxonomies, true));
            if ($is_category_archive) {
                $child_taxonomy = is_category() ? 'category' : $queried_object->taxonomy;
                $child_cats = get_terms([
                    'taxonomy'   => $child_taxonomy,
                    'parent'     => $queried_object->term_id,
                    'hide_empty' => false,
                    'number'     => 6,
                ]);

                if (!empty($child_cats) && !is_wp_error($child_cats)) :
                ?>
                    <!-- Child Categories -->
                    <div class="cat-children">
                        <?php foreach ($child_cats as $child) :
                            $child_icon = get_term_meta($child->term_id, '_acms_icon', true) ?: 'bi-folder-fill';
                            $child_link = get_term_link($child);
                            if (is_wp_error($child_link)) continue;
                        ?>
                            <a href="<?php echo esc_url($child_link); ?>" class="cat-child">
                                <div class="cat-child__icon">
                                    <i class="bi <?php echo esc_attr($child_icon); ?>"></i>
                                </div>
                                <div class="cat-child__info">
                                    <h3 class="cat-child__name"><?php echo esc_html($child->name); ?></h3>
                                    <span class="cat-child__count">
                                        <?php
                                        printf(
                                            esc_html(_n('%d Article', '%d Articles', $child->count, 'affiliatecms')),
                                            $child->count
                                        );
                                        ?>
                                    </span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php
                endif;
            }
            ?>
        </div>
    </section>

    <!-- Content Layout: Main + Sidebar -->
    <section class="posts-section">
        <div class="container">
            <?php
            // Get sidebar position setting (applies to all post types)
            $sidebar_position = get_theme_mod('acms_sidebar_position', 'right');
            $has_sidebar = ($sidebar_position !== 'none');
            $sidebar_class = $has_sidebar ? 'content-layout--sidebar-' . $sidebar_position : 'content-layout--no-sidebar';
            ?>
            <div class="content-layout <?php echo esc_attr($sidebar_class); ?>">
                <!-- Main Content -->
                <div class="content-layout__main">

                    <?php if (have_posts()) :
                        global $wp_query;
                        $total_pages = $wp_query->max_num_pages;
                        $total_posts = $wp_query->found_posts;

                        // Get archive layout from customizer (applies to all post types)
                        $archive_layout = get_theme_mod('acms_archive_layout', 'grid');

                        // Map layout to CSS classes and card variant
                        $layout_classes = [
                            'grid'      => 'posts-grid-v2 posts-grid-v2--3col',
                            'grid-2col' => 'posts-grid-v2 posts-grid-v2--2col',
                            'list'      => 'posts-list',
                        ];
                        $card_variants = [
                            'grid'      => 'grid-v2',
                            'grid-2col' => 'grid-v2',
                            'list'      => 'list',
                        ];

                        $container_class = $layout_classes[$archive_layout] ?? $layout_classes['grid'];
                        $card_variant = $card_variants[$archive_layout] ?? $card_variants['grid'];

                        // Determine archive type for Load More
                        $archive_type = '';
                        $archive_value = '';
                        $archive_taxonomy = '';
                        if (is_category()) {
                            $archive_type = 'category';
                            $archive_value = $queried_object->slug;
                        } elseif (is_tag()) {
                            $archive_type = 'tag';
                            $archive_value = $queried_object->slug;
                        } elseif (is_tax()) {
                            $archive_type = 'taxonomy';
                            $archive_value = $queried_object->slug;
                            $archive_taxonomy = $queried_object->taxonomy;
                        } elseif (is_date()) {
                            $archive_type = 'date';
                            $archive_value = is_year() ? get_the_date('Y') : (is_month() ? get_the_date('Y-m') : get_the_date('Y-m-d'));
                        }
                    ?>
                        <!-- Posts Container - Layout controlled by Customizer (applies to all post types) -->
                        <div class="<?php echo esc_attr($container_class); ?>" id="posts-container"
                             data-archive-type="<?php echo esc_attr($archive_type); ?>"
                             data-archive-value="<?php echo esc_attr($archive_value); ?>"
                             <?php if (!empty($archive_taxonomy)) : ?>data-archive-taxonomy="<?php echo esc_attr($archive_taxonomy); ?>"<?php endif; ?>
                             data-total-pages="<?php echo esc_attr($total_pages); ?>"
                             data-total-posts="<?php echo esc_attr($total_posts); ?>">
                            <?php
                            while (have_posts()) {
                                the_post();

                                // Card configuration based on layout
                                $card_config = [
                                    'variant'        => $card_variant,
                                    'show_category'  => ($card_variant === 'list'),
                                    'show_indicator' => ($card_variant === 'list'),
                                    'show_rating'    => false,
                                    'show_views'     => ($card_variant === 'list'),
                                    'footer_cols'    => ($card_variant === 'list') ? '3col' : '2col',
                                    'excerpt_words'  => ($card_variant === 'list') ? 25 : 15,
                                ];

                                get_template_part('template-parts/content/post-card', null, $card_config);
                            }
                            ?>
                        </div>

                        <!-- Load More Button -->
                        <?php if ($total_pages > 1) : ?>
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

                    <?php else : ?>
                        <!-- No Posts -->
                        <div class="no-results">
                            <div class="no-results__icon">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <h2 class="no-results__title"><?php esc_html_e('No posts yet', 'affiliatecms'); ?></h2>
                            <p class="no-results__description">
                                <?php esc_html_e('Check back soon for expert reviews and buying guides.', 'affiliatecms'); ?>
                            </p>
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

    <!-- About Section -->
    <?php get_template_part('template-parts/sections/about'); ?>

</main>

<?php
get_footer();
