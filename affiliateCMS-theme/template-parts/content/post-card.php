<?php
/**
 * Template Part: Post Card - Unified Component
 *
 * A single, reusable card component with configurable options.
 * Use $args to customize appearance.
 *
 * @package AffiliateCMS
 * @since 4.0.0
 *
 * @param array $args {
 *     Optional. Card configuration.
 *     @type string $variant        Card style modifier class. Default ''.
 *     @type string $class          Additional CSS classes.
 *     @type bool   $show_category  Show category badge overlay on image. Default true.
 *     @type bool   $show_indicator Show click indicator arrow on hover. Default true.
 *     @type bool   $show_excerpt   Show excerpt. Default true.
 *     @type bool   $show_footer    Show footer with author/views/date. Default true.
 *     @type bool   $show_rating    Show star rating (grid-v2 style). Default false.
 *     @type bool   $show_views     Show views count separately (grid-v2 style). Default false.
 *     @type string $footer_cols    Footer columns: '2col' or '3col'. Default '3col'.
 *     @type int    $excerpt_words  Number of words in excerpt. Default 12.
 * }
 */

// Default args
$defaults = [
    'variant'        => '',
    'class'          => '',
    'show_category'  => true,
    'show_indicator' => true,
    'show_excerpt'   => true,
    'show_footer'    => true,
    'show_rating'    => false,
    'show_views'     => false,
    'footer_cols'    => '3col',
    'excerpt_words'  => 12,
];

$args = wp_parse_args($args ?? [], $defaults);

// Get post data
$category = acms_get_primary_category();
$views = acms_get_views(get_the_ID());

// Build classes
$classes = ['post-card'];
if (!empty($args['variant'])) {
    $classes[] = 'post-card--' . $args['variant'];
}
if (!empty($args['class'])) {
    $classes[] = $args['class'];
}

// Get rating data if needed (show 0 stars if no data - never use fake data)
if ($args['show_rating']) {
    $rating = get_post_meta(get_the_ID(), '_acms_rating', true);
    $rating = $rating ? floatval($rating) : 0;
    $rating_count = get_post_meta(get_the_ID(), '_acms_rating_count', true);
    $rating_count = $rating_count ? intval($rating_count) : 0;
}
?>

<article class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <div class="post-card__image">
        <?php if ($args['show_category'] && $category) : ?>
            <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="post-card__image-category">
                <?php if (!empty($category->icon)) : ?>
                    <i class="bi <?php echo esc_attr($category->icon); ?>"></i>
                <?php endif; ?>
                <?php echo esc_html($category->name); ?>
            </a>
        <?php endif; ?>

        <?php if (has_post_thumbnail()) : ?>
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('acms-card', [
                    'alt' => get_the_title(),
                    'loading' => 'lazy'
                ]); ?>
            </a>
        <?php else :
            // Fallback: first image from content or placeholder
            $fallback_image = acms_get_thumbnail_url(get_the_ID(), 'acms-card');
        ?>
            <a href="<?php the_permalink(); ?>">
                <img src="<?php echo esc_url($fallback_image); ?>"
                     alt="<?php echo esc_attr(get_the_title()); ?>"
                     loading="lazy">
            </a>
        <?php endif; ?>

        <?php if ($args['show_indicator']) : ?>
            <span class="post-card__click-indicator">
                <i class="bi bi-arrow-right"></i>
            </span>
        <?php endif; ?>
    </div>

    <div class="post-card__content">
        <h3 class="post-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <?php if ($args['show_excerpt']) : ?>
            <?php if (has_excerpt()) : ?>
                <p class="post-card__excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
            <?php else : ?>
                <p class="post-card__excerpt"><?php echo esc_html(wp_trim_words(wp_strip_all_tags(apply_filters('the_content', get_the_content())), $args['excerpt_words'], '...')); ?></p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($args['show_rating']) : ?>
            <!-- Star Rating -->
            <div class="post-card__stars-row">
                <div class="post-card__stars">
                    <?php echo acms_star_rating($rating); ?>
                </div>
                <span class="post-card__rating-count">(<?php echo esc_html($rating_count); ?>)</span>
            </div>
        <?php endif; ?>

        <?php if ($args['show_views']) : ?>
            <!-- Views (standalone) -->
            <div class="post-card__views">
                <i class="bi bi-eye"></i>
                <?php echo esc_html(acms_format_number($views)); ?> <?php esc_html_e('views', 'affiliatecms'); ?>
            </div>
        <?php endif; ?>

        <?php if ($args['show_footer']) : ?>
            <!-- Footer -->
            <div class="post-card__footer-grid<?php echo $args['footer_cols'] === '3col' ? ' post-card__footer-grid--3col' : ''; ?>">
                <span class="post-card__author-text">
                    <i class="bi bi-person-fill"></i>
                    <?php echo esc_html(get_the_author()); ?>
                </span>
                <?php if ($args['footer_cols'] === '3col') : ?>
                    <span class="post-card__footer-item post-card__footer-item--views">
                        <i class="bi bi-eye"></i>
                        <?php echo esc_html(acms_format_number($views)); ?>
                    </span>
                <?php endif; ?>
                <span class="post-card__footer-item">
                    <i class="bi bi-clock-history"></i>
                    <?php echo esc_html(get_the_date('M j, Y')); ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
</article>
