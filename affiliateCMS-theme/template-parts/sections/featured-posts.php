<?php
/**
 * Template Part: Featured Posts Section
 * News-style layout with featured/sticky posts
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

// Get sticky posts first, then fill remaining slots with recent posts
$sticky_post_ids = get_option('sticky_posts');
$featured_posts = [];
$needed = 3;

// First: Get sticky posts (pinned posts)
if (!empty($sticky_post_ids)) {
    $sticky_query = new WP_Query([
        'posts_per_page'      => $needed,
        'post__in'            => $sticky_post_ids,
        'post_status'         => 'publish',
        'ignore_sticky_posts' => 1,
        'no_found_rows'       => true,
    ]);

    if ($sticky_query->have_posts()) {
        $featured_posts = $sticky_query->posts;
    }
    wp_reset_postdata();
}

// Second: Fill remaining slots with recent posts (excluding already fetched sticky posts)
$remaining = $needed - count($featured_posts);
if ($remaining > 0) {
    $exclude_ids = !empty($sticky_post_ids) ? $sticky_post_ids : [];

    $recent_query = new WP_Query([
        'posts_per_page' => $remaining,
        'post_status'    => 'publish',
        'post__not_in'   => $exclude_ids,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ]);

    if ($recent_query->have_posts()) {
        $featured_posts = array_merge($featured_posts, $recent_query->posts);
    }
    wp_reset_postdata();
}

// Exit if no posts found
if (empty($featured_posts)) {
    return;
}

$main_post = $featured_posts[0] ?? null;
$side_posts = array_slice($featured_posts, 1, 2);
?>

<section class="section featured-section">
    <div class="container">
        <div class="featured-grid">
            <?php if ($main_post) : setup_postdata($main_post); ?>
            <!-- Main Featured Post -->
            <article class="featured-card featured-card--main">
                <a href="<?php echo get_permalink($main_post); ?>" class="featured-card__link">
                    <div class="featured-card__image">
                        <?php if (has_post_thumbnail($main_post)) : ?>
                            <?php echo get_the_post_thumbnail($main_post, 'large', ['class' => 'featured-card__img']); ?>
                        <?php else :
                            // Fallback: first image from content or placeholder
                            $fallback_image = acms_get_thumbnail_url($main_post->ID, 'large');
                            $is_placeholder = (strpos($fallback_image, 'placeholder.svg') !== false);
                        ?>
                            <?php if ($is_placeholder) : ?>
                                <div class="featured-card__placeholder">
                                    <i class="bi bi-image"></i>
                                </div>
                            <?php else : ?>
                                <img src="<?php echo esc_url($fallback_image); ?>"
                                     alt="<?php echo esc_attr(get_the_title($main_post)); ?>"
                                     class="featured-card__img"
                                     loading="lazy">
                            <?php endif; ?>
                        <?php endif; ?>
                        <div class="featured-card__overlay"></div>
                    </div>
                    <div class="featured-card__content">
                        <?php
                        $categories = get_the_category($main_post->ID);
                        if (!empty($categories)) :
                        ?>
                            <span class="featured-card__category"><?php echo esc_html($categories[0]->name); ?></span>
                        <?php endif; ?>
                        <h3 class="featured-card__title"><?php echo get_the_title($main_post); ?></h3>
                        <p class="featured-card__excerpt"><?php echo wp_trim_words(get_the_excerpt($main_post), 20); ?></p>
                        <div class="featured-card__meta">
                            <time class="featured-card__date" datetime="<?php echo get_the_date('c', $main_post); ?>">
                                <?php echo get_the_date('', $main_post); ?>
                            </time>
                        </div>
                    </div>
                </a>
            </article>
            <?php endif; ?>

            <!-- Side Posts -->
            <div class="featured-grid__side">
                <?php foreach ($side_posts as $post) : setup_postdata($post); ?>
                <article class="featured-card featured-card--side">
                    <a href="<?php echo get_permalink($post); ?>" class="featured-card__link">
                        <div class="featured-card__image">
                            <?php if (has_post_thumbnail($post)) : ?>
                                <?php echo get_the_post_thumbnail($post, 'medium_large', ['class' => 'featured-card__img']); ?>
                            <?php else :
                                // Fallback: first image from content or placeholder
                                $fallback_image = acms_get_thumbnail_url($post->ID, 'medium_large');
                                $is_placeholder = (strpos($fallback_image, 'placeholder.svg') !== false);
                            ?>
                                <?php if ($is_placeholder) : ?>
                                    <div class="featured-card__placeholder">
                                        <i class="bi bi-image"></i>
                                    </div>
                                <?php else : ?>
                                    <img src="<?php echo esc_url($fallback_image); ?>"
                                         alt="<?php echo esc_attr(get_the_title($post)); ?>"
                                         class="featured-card__img"
                                         loading="lazy">
                                <?php endif; ?>
                            <?php endif; ?>
                            <div class="featured-card__overlay"></div>
                        </div>
                        <div class="featured-card__content">
                            <?php
                            $categories = get_the_category($post->ID);
                            if (!empty($categories)) :
                            ?>
                                <span class="featured-card__category"><?php echo esc_html($categories[0]->name); ?></span>
                            <?php endif; ?>
                            <h3 class="featured-card__title"><?php echo get_the_title($post); ?></h3>
                            <div class="featured-card__meta">
                                <time class="featured-card__date" datetime="<?php echo get_the_date('c', $post); ?>">
                                    <?php echo get_the_date('', $post); ?>
                                </time>
                            </div>
                        </div>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php wp_reset_postdata(); ?>
