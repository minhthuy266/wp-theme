<?php
/**
 * The main template file (fallback)
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

get_header();
?>

<main id="content" class="site-main">
    <div class="container">
        <div class="content-layout">
            <div class="content-layout__main">

                <?php if (have_posts()) :
                    global $wp_query;
                    $total_pages = $wp_query->max_num_pages;
                    $total_posts = $wp_query->found_posts;
                ?>

                    <div class="section-header-v2">
                        <div class="section-header-v2__left">
                            <div class="section-header-v2__icon">
                                <i class="bi bi-newspaper"></i>
                            </div>
                            <div class="section-header-v2__text">
                                <h1 class="section-header-v2__title"><?php esc_html_e('Latest Posts', 'affiliatecms'); ?></h1>
                                <p class="section-header-v2__subtitle"><?php esc_html_e('Browse our latest articles', 'affiliatecms'); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="posts-grid-v2 posts-grid-v2--3col" id="posts-container"
                         data-archive-type="home"
                         data-archive-value=""
                         data-total-pages="<?php echo esc_attr($total_pages); ?>"
                         data-total-posts="<?php echo esc_attr($total_posts); ?>">
                        <?php
                        while (have_posts()) :
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
                        endwhile;
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

                    <div class="no-results">
                        <h2><?php esc_html_e('No posts found', 'affiliatecms'); ?></h2>
                        <p><?php esc_html_e('Sorry, no posts matched your criteria.', 'affiliatecms'); ?></p>
                    </div>

                <?php endif; ?>

            </div>

            <?php get_sidebar(); ?>
        </div>
    </div>
</main>

<?php
get_footer();
