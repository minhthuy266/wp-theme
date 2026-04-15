<?php
/**
 * Template Part: Post Card - Minimal Style
 * Used in: Search results, archive pages with 4 columns
 * Simplified card with image, category badge, title only
 *
 * @deprecated 4.0.0 Use post-card.php with ['show_excerpt' => false, 'show_footer' => false] instead
 * @package AffiliateCMS
 * @since 4.0.0
 */

// Redirect to unified component
get_template_part('template-parts/content/post-card', null, [
    'show_excerpt' => false,
    'show_footer'  => false,
]);
return;

// Get category info
$category = acms_get_primary_category();
?>

<article class="post-card post-card--minimal">
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
    </div>
</article>
