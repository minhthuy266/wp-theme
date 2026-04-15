<?php
/**
 * Author Archive Template
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

get_header();

// Get author info
$author_id = get_queried_object_id();
$author_name = get_the_author_meta('display_name', $author_id);
$author_bio = get_the_author_meta('description', $author_id);
$author_role = get_the_author_meta('role', $author_id);
$post_count = count_user_posts($author_id, 'post');
// Include custom post types in count
foreach (['acms_reviews', 'acms_deals', 'acms_guides'] as $cpt) {
    if (post_type_exists($cpt)) {
        $post_count += count_user_posts($author_id, $cpt);
    }
}

// Get custom fields
$expertise = acms_get_user_expertise($author_id);
$socials = acms_get_user_socials($author_id);

// Get total views (real data, not fake)
$total_views = acms_get_author_total_views($author_id);
?>

<main id="content" class="site-main">

    <!-- Author Header -->
    <section class="author-header author-header--centered">
        <div class="container">
            <!-- Avatar -->
            <div class="author-header__avatar">
                <?php echo get_avatar($author_id, 150, '', $author_name); ?>
            </div>

            <!-- Name & Role -->
            <h1 class="author-header__name">
                <?php echo esc_html($author_name); ?>
                <i class="bi bi-patch-check-fill author-header__verified"></i>
            </h1>
            <p class="author-header__role"><?php echo esc_html($author_role ?: __('Staff Writer', 'affiliatecms')); ?></p>

            <?php if ($author_bio) : ?>
                <p class="author-header__bio"><?php echo esc_html($author_bio); ?></p>
            <?php endif; ?>

            <!-- Social Links -->
            <?php if (!empty($socials)) : ?>
                <div class="author-header__social">
                    <?php foreach ($socials as $social) : ?>
                        <a href="<?php echo esc_url($social['url']); ?>"
                           class="author-header__social-link"
                           aria-label="<?php echo esc_attr($social['label']); ?>"
                           target="_blank"
                           rel="nofollow">
                            <i class="bi <?php echo esc_attr($social['icon']); ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Stats & Details -->
            <div class="author-header__details">
                <div class="author-header__stats">
                    <div class="author-header__stat">
                        <span class="author-header__stat-number"><?php echo esc_html($post_count); ?></span>
                        <span class="author-header__stat-label"><?php esc_html_e('Articles', 'affiliatecms'); ?></span>
                    </div>
                    <div class="author-header__stat">
                        <span class="author-header__stat-number"><?php echo esc_html(acms_format_number($total_views)); ?></span>
                        <span class="author-header__stat-label"><?php esc_html_e('Total Views', 'affiliatecms'); ?></span>
                    </div>
                </div>

                <!-- Expertise Tags -->
                <?php if (!empty($expertise)) : ?>
                    <div class="author-header__expertise-section">
                        <span class="author-header__expertise-label"><?php esc_html_e('Expertise', 'affiliatecms'); ?></span>
                        <div class="author-header__expertise">
                            <?php foreach ($expertise as $item) : ?>
                                <span class="author-header__expertise-tag">
                                    <i class="bi <?php echo esc_attr($item['icon']); ?>"></i>
                                    <?php echo esc_html($item['label']); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Content Layout: Main + Sidebar -->
    <section class="posts-section">
        <div class="container">
            <div class="content-layout">
                <!-- Main Content -->
                <div class="content-layout__main">
                    <!-- Section Header -->
                    <div class="section-header-v2">
                        <div class="section-header-v2__left">
                            <div class="section-header-v2__icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="section-header-v2__text">
                                <h3 class="section-header-v2__title">
                                    <?php
                                    printf(
                                        /* translators: %s: author name */
                                        esc_html__('Articles by %s', 'affiliatecms'),
                                        esc_html($author_name)
                                    );
                                    ?>
                                </h3>
                                <p class="section-header-v2__subtitle">
                                    <?php
                                    printf(
                                        /* translators: %d: number of posts */
                                        esc_html(_n('%d article published', '%d articles published', $post_count, 'affiliatecms')),
                                        $post_count
                                    );
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <?php if (have_posts()) :
                        global $wp_query;
                        $total_pages = $wp_query->max_num_pages;
                        $total_posts = $wp_query->found_posts;
                    ?>
                        <!-- Posts Grid -->
                        <div class="posts-grid-v2 posts-grid-v2--3col" id="posts-container"
                             data-archive-type="author"
                             data-archive-value="<?php echo esc_attr($author_id); ?>"
                             data-total-pages="<?php echo esc_attr($total_pages); ?>"
                             data-total-posts="<?php echo esc_attr($total_posts); ?>">
                            <?php
                            while (have_posts()) {
                                the_post();
                                get_template_part('template-parts/content/post-card', null, [
                                    'variant'        => 'grid-v2',
                                    'show_category'  => false,
                                    'show_indicator' => false,
                                    'show_rating'    => false,
                                    'show_views'     => false,
                                    'footer_cols'    => '2col',
                                    'excerpt_words'  => 15,
                                ]);
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
                            <h2 class="no-results__title"><?php esc_html_e('No articles yet', 'affiliatecms'); ?></h2>
                            <p class="no-results__description">
                                <?php esc_html_e('This author has not published any articles yet.', 'affiliatecms'); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <?php get_sidebar(); ?>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
