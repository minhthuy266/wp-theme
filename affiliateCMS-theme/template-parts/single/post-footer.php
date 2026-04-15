<?php
/**
 * Template Part: Post Footer - Tags, Actions, Author
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

// Get tags based on post type
$post_type = get_post_type();
$tags = false;

// Map post types to their tag taxonomies
$tag_taxonomies = [
    'post' => 'post_tag',
    'acms_reviews' => 'acms_reviews_tag',
    'acms_deals' => 'acms_deals_tag',
    'acms_guides' => 'acms_guides_tag',
];

// Get tags for the current post type
if (isset($tag_taxonomies[$post_type])) {
    $tags = get_the_terms(get_the_ID(), $tag_taxonomies[$post_type]);
    // get_the_terms returns false if no terms, or WP_Error on error
    if (is_wp_error($tags)) {
        $tags = false;
    }
}

$author_id = get_the_author_meta('ID');
$author_bio = get_the_author_meta('description');
$post_id = get_the_ID();
$hearts_count = (int) get_post_meta($post_id, '_acms_hearts', true);
?>

<aside class="post-footer post-footer--recommended">
    <div class="container">
        <!-- Top Row: Tags (left) + Share (right) -->
        <div class="post-footer__top-row">
            <!-- Tags: Icon + Tags: tag1, tag2, ... -->
            <div class="post-footer__tags">
                <i class="bi bi-tags-fill"></i>
                <span class="post-footer__tags-label"><?php esc_html_e('Tags:', 'affiliatecms'); ?></span>
                <?php if ($tags) : ?>
                    <span class="post-footer__tags-list">
                        <?php
                        $tag_links = [];
                        foreach ($tags as $tag) {
                            $tag_links[] = '<a href="' . esc_url(get_tag_link($tag->term_id)) . '">' . esc_html($tag->name) . '</a>';
                        }
                        echo implode(', ', $tag_links);
                        ?>
                    </span>
                <?php else : ?>
                    <span class="post-footer__tags-empty"><?php esc_html_e('No tags', 'affiliatecms'); ?></span>
                <?php endif; ?>
            </div>

            <!-- Share Button -->
            <button class="post-footer__share-btn"
                    data-action="share"
                    id="postFooterShareBtn">
                <i class="bi bi-share"></i>
                <span><?php esc_html_e('Share', 'affiliatecms'); ?></span>
            </button>
        </div>

        <!-- Author Section (Centered) -->
        <div class="post-footer__author-centered">
            <span class="post-footer__author-label"><?php esc_html_e('Written by', 'affiliatecms'); ?></span>
            <?php echo get_avatar($author_id, 100, '', get_the_author(), ['class' => 'post-footer__author-avatar']); ?>
            <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>" class="post-footer__author-name">
                <?php the_author(); ?>
                <i class="bi bi-patch-check-fill"></i>
            </a>
            <p class="post-footer__author-role">
                <?php
                $user_info = get_userdata($author_id);
                $role = !empty($user_info->roles) ? ucfirst($user_info->roles[0]) : __('Author', 'affiliatecms');
                echo esc_html($role);
                ?>
            </p>
            <?php if ($author_bio) : ?>
                <p class="post-footer__author-bio"><?php echo esc_html($author_bio); ?></p>
            <?php endif; ?>

            <!-- Social Links -->
            <div class="post-footer__author-social">
                <?php
                $social_links = acms_get_user_socials($author_id);

                if (!empty($social_links)) :
                    foreach ($social_links as $social) :
                ?>
                    <a href="<?php echo esc_url($social['url']); ?>" class="post-footer__author-social-link" aria-label="<?php echo esc_attr($social['label']); ?>" target="_blank" rel="nofollow">
                        <i class="<?php echo esc_attr($social['icon']); ?>"></i>
                    </a>
                <?php
                    endforeach;
                endif;
                ?>
            </div>

            <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>" class="post-footer__author-link">
                <?php esc_html_e('View all posts', 'affiliatecms'); ?>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</aside>
