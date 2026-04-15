<?php
/**
 * Template Part: Post Card - Slider Style
 * Used in: Related Posts slider
 *
 * @deprecated 4.0.0 Use post-card.php with ['variant' => 'slider'] instead
 * @package AffiliateCMS
 * @since 4.0.0
 */

// Redirect to unified component
get_template_part('template-parts/content/post-card', null, [
    'variant'        => 'slider',
    'class'          => 'related-slider__slide',
    'show_category'  => false,
    'show_indicator' => false,
]);
return;

// Get category info
$category = acms_get_primary_category();
$views = acms_get_views(get_the_ID());
?>

<article class="post-card post-card--slider related-slider__slide">
    <div class="post-card__image">
        <?php if ($category) : ?>
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

        <span class="post-card__click-indicator">
            <i class="bi bi-arrow-right"></i>
        </span>
    </div>

    <div class="post-card__content">
        <h3 class="post-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <?php if (has_excerpt()) : ?>
            <p class="post-card__excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
        <?php else : ?>
            <p class="post-card__excerpt"><?php echo esc_html(wp_trim_words(wp_strip_all_tags(apply_filters('the_content', get_the_content())), 12, '...')); ?></p>
        <?php endif; ?>

        <!-- Footer - 3 columns -->
        <div class="post-card__footer-grid post-card__footer-grid--3col">
            <span class="post-card__author-text">
                <i class="bi bi-person-fill"></i>
                <?php echo esc_html(get_the_author()); ?>
            </span>
            <span class="post-card__footer-item post-card__footer-item--views">
                <i class="bi bi-eye"></i>
                <?php echo esc_html(acms_format_number($views)); ?>
            </span>
            <span class="post-card__footer-item">
                <i class="bi bi-clock-history"></i>
                <?php echo esc_html(get_the_date('M j, Y')); ?>
            </span>
        </div>
    </div>
</article>
