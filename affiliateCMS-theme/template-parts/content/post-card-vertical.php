<?php
/**
 * Template Part: Post Card - Vertical Style
 * Used in: search results, simple listings
 *
 * @deprecated 4.0.0 Use post-card.php with ['variant' => 'vertical'] instead
 * @package AffiliateCMS
 * @since 4.0.0
 */

// Redirect to unified component
get_template_part('template-parts/content/post-card', null, [
    'variant'      => 'vertical',
    'footer_cols'  => '2col',
    'excerpt_words' => 20,
]);
return;

// Get category info
$category = acms_get_primary_category();
?>

<article class="post-card post-card--vertical">
    <a href="<?php the_permalink(); ?>" class="post-card__image-link">
        <div class="post-card__image">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('acms-card', [
                    'alt' => get_the_title(),
                    'loading' => 'lazy'
                ]); ?>
            <?php else :
                // Fallback: first image from content or placeholder
                $fallback_image = acms_get_thumbnail_url(get_the_ID(), 'acms-card');
            ?>
                <img src="<?php echo esc_url($fallback_image); ?>"
                     alt="<?php echo esc_attr(get_the_title()); ?>"
                     loading="lazy">
            <?php endif; ?>
        </div>
    </a>
    <div class="post-card__content">
        <div class="post-card__meta">
            <?php if ($category) : ?>
                <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="post-card__category">
                    <?php echo esc_html($category->name); ?>
                </a>
            <?php endif; ?>
            <span class="post-card__date"><?php echo esc_html(get_the_date('M j, Y')); ?></span>
        </div>
        <h2 class="post-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>
        <?php if (has_excerpt()) : ?>
            <p class="post-card__excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
        <?php else : ?>
            <p class="post-card__excerpt"><?php echo esc_html(wp_trim_words(wp_strip_all_tags(apply_filters('the_content', get_the_content())), 20, '...')); ?></p>
        <?php endif; ?>
    </div>
</article>
