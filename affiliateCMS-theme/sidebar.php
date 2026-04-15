<?php
/**
 * The sidebar template
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

// Determine which sidebar to use
$sidebar_id = 'sidebar-1';
if (is_single() && is_active_sidebar('sidebar-post')) {
    $sidebar_id = 'sidebar-post';
}

// Check if sidebar has widgets
if (is_active_sidebar($sidebar_id)) {
    ?>
    <aside class="content-layout__sidebar">
        <div class="sidebar">
            <?php dynamic_sidebar($sidebar_id); ?>
        </div>
    </aside>
    <?php
} else {
    // Show demo sidebar when no widgets are configured
    ?>
    <aside class="content-layout__sidebar">
        <div class="sidebar">

            <!-- Popular Posts Widget (Demo) -->
            <div class="sidebar-widget">
                <div class="sidebar-widget__header">
                    <h3 class="sidebar-widget__title">
                        <i class="bi bi-fire"></i>
                        <?php esc_html_e('Popular This Week', 'affiliatecms'); ?>
                    </h3>
                    <a href="#" class="sidebar-widget__link"><?php esc_html_e('View All', 'affiliatecms'); ?></a>
                </div>
                <div class="popular-posts" data-expandable>
                    <?php
                    // Get popular posts (by comment count as fallback)
                    $popular_query = new WP_Query([
                        'posts_per_page' => 5,
                        'orderby'        => 'comment_count',
                        'order'          => 'DESC',
                        'post_status'    => 'publish',
                    ]);

                    if ($popular_query->have_posts()) {
                        $count = 1;
                        while ($popular_query->have_posts()) {
                            $popular_query->the_post();
                            ?>
                            <a href="<?php the_permalink(); ?>" class="popular-post">
                                <span class="popular-post__number"><?php echo esc_html($count); ?></span>
                                <div class="popular-post__image">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('acms-card-small', ['alt' => get_the_title()]); ?>
                                    <?php else :
                                        $fallback_image = acms_get_thumbnail_url(get_the_ID(), 'thumbnail');
                                    ?>
                                        <img src="<?php echo esc_url($fallback_image); ?>"
                                             alt="<?php echo esc_attr(get_the_title()); ?>"
                                             loading="lazy">
                                    <?php endif; ?>
                                </div>
                                <div class="popular-post__content">
                                    <h4 class="popular-post__title"><?php the_title(); ?></h4>
                                    <span class="popular-post__meta">
                                        <?php
                                        $categories = get_the_category();
                                        if (!empty($categories)) {
                                            echo esc_html($categories[0]->name) . ' &bull; ';
                                        }
                                        echo esc_html(acms_format_number(acms_get_views())) . ' ' . esc_html__('views', 'affiliatecms');
                                        ?>
                                    </span>
                                </div>
                            </a>
                            <?php
                            $count++;
                        }
                        wp_reset_postdata();
                    } else {
                        // Demo posts
                        for ($i = 1; $i <= 5; $i++) {
                            ?>
                            <a href="#" class="popular-post">
                                <span class="popular-post__number"><?php echo esc_html($i); ?></span>
                                <div class="popular-post__image">
                                    <img src="https://picsum.photos/128/128?random=<?php echo esc_attr($i + 20); ?>" alt="<?php esc_attr_e('Demo post', 'affiliatecms'); ?>" loading="lazy">
                                </div>
                                <div class="popular-post__content">
                                    <h4 class="popular-post__title"><?php esc_html_e('Best Budget Robot Vacuums Under $300', 'affiliatecms'); ?></h4>
                                    <span class="popular-post__meta"><?php esc_html_e('Robot Vacuums', 'affiliatecms'); ?> &bull; 15.2K <?php esc_html_e('views', 'affiliatecms'); ?></span>
                                </div>
                            </a>
                            <?php
                        }
                    }
                    ?>
                </div>
                <button type="button" class="popular-posts__toggle" data-expand-toggle>
                    <span class="popular-posts__toggle-text" data-text-collapsed="<?php esc_attr_e('Show More', 'affiliatecms'); ?>" data-text-expanded="<?php esc_attr_e('Show Less', 'affiliatecms'); ?>"><?php esc_html_e('Show More', 'affiliatecms'); ?></span>
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>

            <!-- Categories Widget (Demo) -->
            <div class="sidebar-widget">
                <div class="sidebar-widget__header">
                    <h3 class="sidebar-widget__title">
                        <i class="bi bi-folder-fill"></i>
                        <?php esc_html_e('Categories', 'affiliatecms'); ?>
                    </h3>
                </div>
                <div class="sidebar-categories">
                    <?php
                    $categories = get_categories([
                        'orderby' => 'count',
                        'order'   => 'DESC',
                        'number'  => 5,
                    ]);

                    $category_icons = [
                        'electronics'      => 'bi-cpu-fill',
                        'home-kitchen'     => 'bi-house-fill',
                        'health-beauty'    => 'bi-heart-fill',
                        'sports-outdoors'  => 'bi-bicycle',
                        'baby-kids'        => 'bi-balloon-fill',
                    ];

                    if (!empty($categories)) {
                        foreach ($categories as $category) {
                            $icon = isset($category_icons[$category->slug]) ? $category_icons[$category->slug] : 'bi-folder-fill';
                            ?>
                            <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="sidebar-category">
                                <span class="sidebar-category__name">
                                    <i class="bi <?php echo esc_attr($icon); ?>"></i>
                                    <?php echo esc_html($category->name); ?>
                                </span>
                                <span class="sidebar-category__count"><?php echo esc_html($category->count); ?></span>
                            </a>
                            <?php
                        }
                    } else {
                        // Demo categories
                        $demo_cats = [
                            ['name' => 'Electronics', 'icon' => 'bi-cpu-fill', 'count' => 245],
                            ['name' => 'Home & Kitchen', 'icon' => 'bi-house-fill', 'count' => 189],
                            ['name' => 'Health & Beauty', 'icon' => 'bi-heart-fill', 'count' => 156],
                            ['name' => 'Sports & Outdoors', 'icon' => 'bi-bicycle', 'count' => 134],
                            ['name' => 'Baby & Kids', 'icon' => 'bi-balloon-fill', 'count' => 98],
                        ];
                        foreach ($demo_cats as $cat) {
                            ?>
                            <a href="#" class="sidebar-category">
                                <span class="sidebar-category__name">
                                    <i class="bi <?php echo esc_attr($cat['icon']); ?>"></i>
                                    <?php echo esc_html($cat['name']); ?>
                                </span>
                                <span class="sidebar-category__count"><?php echo esc_html($cat['count']); ?></span>
                            </a>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>

            <!-- Newsletter Widget (Demo) -->
            <div class="sidebar-widget newsletter-widget">
                <h3 class="newsletter-widget__title"><?php esc_html_e('Get Deal Alerts', 'affiliatecms'); ?></h3>
                <p class="newsletter-widget__text">
                    <?php esc_html_e('Subscribe to receive the best deals and product recommendations directly to your inbox.', 'affiliatecms'); ?>
                </p>
                <form class="newsletter-form">
                    <input type="email" class="newsletter-form__input" placeholder="<?php esc_attr_e('Enter your email', 'affiliatecms'); ?>">
                    <button type="submit" class="btn btn--primary btn--block">
                        <i class="bi bi-send-fill"></i>
                        <?php esc_html_e('Subscribe', 'affiliatecms'); ?>
                    </button>
                </form>
            </div>

            <!-- Tags Widget (Demo) -->
            <div class="sidebar-widget">
                <div class="sidebar-widget__header">
                    <h3 class="sidebar-widget__title">
                        <i class="bi bi-tags-fill"></i>
                        <?php esc_html_e('Popular Tags', 'affiliatecms'); ?>
                    </h3>
                </div>
                <div class="sidebar-tags">
                    <?php
                    $tags = get_tags([
                        'orderby' => 'count',
                        'order'   => 'DESC',
                        'number'  => 8,
                    ]);

                    if (!empty($tags)) {
                        foreach ($tags as $tag) {
                            ?>
                            <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="sidebar-tag"><?php echo esc_html($tag->name); ?></a>
                            <?php
                        }
                    } else {
                        // Demo tags
                        $demo_tags = ['Walking Pads', 'Robot Vacuums', 'Air Purifiers', 'Soundbars', 'Mattresses', 'Smart Home', 'Budget Picks', 'Comparison'];
                        foreach ($demo_tags as $tag) {
                            ?>
                            <a href="#" class="sidebar-tag"><?php echo esc_html($tag); ?></a>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>

            <!-- CTA Widget (Demo) -->
            <div class="sidebar-widget cta-widget">
                <h3 class="cta-widget__title"><?php esc_html_e("Today's Best Deals", 'affiliatecms'); ?></h3>
                <p class="cta-widget__text">
                    <?php esc_html_e("Don't miss out on limited-time discounts from top retailers.", 'affiliatecms'); ?>
                </p>
                <a href="#" class="btn btn--primary btn--block">
                    <i class="bi bi-tag-fill"></i>
                    <?php esc_html_e('View All Deals', 'affiliatecms'); ?>
                </a>
            </div>

        </div>
    </aside>
    <?php
}
?>
