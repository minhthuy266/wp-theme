<?php
/**
 * Search Results Template
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

get_header();
?>

<main id="content" class="site-main">
    <!-- Search Results Header -->
    <section class="category-header category-header--minimal">
        <div class="container">
            <div class="category-header__content">
                <?php acms_breadcrumb(); ?>

                <h1 class="category-header__title">
                    <i class="bi bi-search"></i>
                    <?php
                    printf(
                        /* translators: %s: search query */
                        esc_html__('Search Results for "%s"', 'affiliatecms'),
                        get_search_query()
                    );
                    ?>
                </h1>
                <p class="category-header__count">
                    <?php
                    global $wp_query;
                    printf(
                        /* translators: %d: number of results */
                        esc_html(_n('%d result found', '%d results found', $wp_query->found_posts, 'affiliatecms')),
                        $wp_query->found_posts
                    );
                    ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Search Results Grid -->
    <section class="category-content">
        <div class="container">
            <?php if (have_posts()) :
                global $wp_query;
                $total_pages = $wp_query->max_num_pages;
                $total_posts = $wp_query->found_posts;
                $search_query = get_search_query();
            ?>
                <div class="posts-grid-v2 posts-grid-v2--4col" id="posts-container"
                     data-archive-type="search"
                     data-archive-value="<?php echo esc_attr($search_query); ?>"
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
                <!-- No Results -->
                <div class="no-results">
                    <div class="no-results__icon">
                        <i class="bi bi-search"></i>
                    </div>
                    <h2 class="no-results__title"><?php esc_html_e('No results found', 'affiliatecms'); ?></h2>
                    <p class="no-results__description">
                        <?php esc_html_e('Sorry, no results were found for your search. Try different keywords or browse our categories.', 'affiliatecms'); ?>
                    </p>

                    <!-- Search Form -->
                    <div class="no-results__search">
                        <form class="ai-input" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                            <div class="ai-input__wrapper">
                                <div class="ai-input__icon">
                                    <i class="bi bi-robot"></i>
                                </div>
                                <textarea class="ai-input__field" name="s" placeholder="<?php esc_attr_e('Try a different search...', 'affiliatecms'); ?>" rows="1"><?php echo esc_attr(get_search_query()); ?></textarea>
                                <button type="submit" class="ai-input__send" aria-label="<?php esc_attr_e('Search', 'affiliatecms'); ?>">
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Popular Categories -->
                    <div class="no-results__categories">
                        <h3><?php esc_html_e('Browse Popular Categories', 'affiliatecms'); ?></h3>
                        <div class="no-results__tags">
                            <?php
                            $categories = get_categories([
                                'orderby' => 'count',
                                'order' => 'DESC',
                                'number' => 6,
                            ]);

                            foreach ($categories as $cat) {
                                printf(
                                    '<a href="%s" class="no-results__tag">%s</a>',
                                    esc_url(get_category_link($cat->term_id)),
                                    esc_html($cat->name)
                                );
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
get_footer();
