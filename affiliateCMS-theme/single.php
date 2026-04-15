<?php
/**
 * Single Post Template
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

get_header();

// Views are now tracked asynchronously via REST API (ViewsTracker in theme.js)
// This provides better performance and accurate bot filtering

// Get post meta
$reading_time = acms_get_reading_time();
$views = acms_get_views(get_the_ID()); // Get current count for initial display

// Get real rating data from comments (no fake fallback)
$rating_data = acms_get_post_rating();
$rating = $rating_data['average'];
$rating_count = $rating_data['count'];
$comment_count = acms_get_comment_count();

// Layout settings (apply to all post types)
$single_layout = get_theme_mod('acms_single_layout', 'full');
$sidebar_position = get_theme_mod('acms_sidebar_position', 'right');

// Determine if sidebar should be shown
$has_sidebar = ($single_layout === 'sidebar') && ($sidebar_position !== 'none');
$sidebar_class = $has_sidebar ? 'content-layout--sidebar-' . $sidebar_position : '';
?>

<main id="content" class="site-main site-main--post <?php echo $has_sidebar ? 'site-main--post-sidebar' : ''; ?>">

    <!-- Post Hero -->
    <section class="post-hero post-hero--centered">
        <div class="post-hero__background">
            <div class="post-hero__gradient"></div>
            <div class="post-hero__pattern"></div>
        </div>
        <div class="container">
            <div class="post-hero__content">
                <!-- Breadcrumb (centered, hide current title to avoid duplicate) -->
                <?php acms_breadcrumb(['class' => 'breadcrumb breadcrumb--centered', 'show_current' => false]); ?>

                <!-- Title -->
                <h1 class="post-hero__title"><?php the_title(); ?></h1>

                <!-- Excerpt -->
                <?php if (has_excerpt()) : ?>
                    <p class="post-hero__excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
                <?php endif; ?>

                <!-- Meta Info -->
                <div class="post-hero__meta">
                    <div class="post-hero__author">
                        <?php echo get_avatar(get_the_author_meta('ID'), 60, '', get_the_author(), ['class' => 'post-hero__author-avatar']); ?>
                        <div class="post-hero__author-info">
                            <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="post-hero__author-name">
                                <?php the_author(); ?>
                            </a>
                        </div>
                    </div>
                    <div class="post-hero__details">
                        <span class="post-hero__date">
                            <i class="bi bi-calendar3"></i>
                            <?php echo esc_html(get_the_date('M j, Y')); ?>
                        </span>
                        <?php
                        // Show updated date if different from published (using product scrape time)
                        $effective_modified = function_exists('acms_get_effective_modified_date')
                            ? acms_get_effective_modified_date(get_the_ID(), 'M j, Y')
                            : get_the_modified_date('M j, Y');
                        if ($effective_modified !== get_the_date('M j, Y')) :
                        ?>
                        <span class="post-hero__updated">
                            <i class="bi bi-arrow-repeat"></i>
                            <?php
                            /* translators: %s: date */
                            printf(esc_html__('Updated %s', 'affiliatecms'), esc_html($effective_modified));
                            ?>
                        </span>
                        <?php endif; ?>
                        <span class="post-hero__reading-time">
                            <i class="bi bi-clock"></i>
                            <?php echo esc_html($reading_time); ?> <?php esc_html_e('min read', 'affiliatecms'); ?>
                        </span>
                        <span class="post-hero__views">
                            <i class="bi bi-eye"></i>
                            <?php echo esc_html(acms_format_number($views)); ?> <?php esc_html_e('views', 'affiliatecms'); ?>
                        </span>
                    </div>
                </div>

                <!-- Rating Summary - Links to Reviews Section -->
                <?php if ($rating_count > 0) : ?>
                <a href="#comments" class="post-hero__rating">
                    <div class="post-hero__stars">
                        <?php echo acms_star_rating(floatval($rating)); ?>
                    </div>
                    <span class="post-hero__rating-score"><?php echo esc_html(number_format($rating, 1)); ?></span>
                    <span class="post-hero__rating-count">
                        <?php
                        printf(
                            /* translators: %s: number of reviews */
                            esc_html__('Based on %s reviews', 'affiliatecms'),
                            esc_html($rating_count)
                        );
                        ?>
                    </span>
                </a>
                <?php elseif ($comment_count > 0) : ?>
                <a href="#comments" class="post-hero__rating">
                    <i class="bi bi-chat-square-text"></i>
                    <span class="post-hero__rating-count">
                        <?php
                        printf(
                            /* translators: %s: number of comments */
                            esc_html(_n('%s comment', '%s comments', $comment_count, 'affiliatecms')),
                            esc_html($comment_count)
                        );
                        ?>
                    </span>
                </a>
                <?php else : ?>
                <a href="#comments" class="post-hero__rating post-hero__rating--empty">
                    <i class="bi bi-chat-square-text"></i>
                    <span class="post-hero__rating-count"><?php esc_html_e('Be the first to review', 'affiliatecms'); ?></span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php if ($has_sidebar) : ?>
    <!-- Sidebar Layout -->
    <div class="container">
        <div class="content-layout <?php echo esc_attr($sidebar_class); ?>">
            <div class="content-layout__main">

                <!-- Article Content -->
                <article class="post-article post-article--sidebar">
                    <div class="post-content">
                        <?php
                        while (have_posts()) {
                            the_post();
                            the_content();
                        }
                        ?>
                    </div>
                </article>

                <!-- Post Footer: Tags, Actions, Author -->
                <?php get_template_part('template-parts/single/post-footer'); ?>

                <!-- Comments Section -->
                <?php
                if (comments_open() || get_comments_number()) {
                    get_template_part('template-parts/single/comments-section');
                }
                ?>

            </div>
            <?php get_sidebar(); ?>
        </div>
    </div>

    <?php else : ?>
    <!-- Full Width Layout -->

    <!-- Article Content -->
    <article class="post-article">
        <div class="container">
            <div class="post-content">
                <?php
                while (have_posts()) {
                    the_post();
                    the_content();
                }
                ?>
            </div>
        </div>
    </article>

    <!-- Post Footer: Tags, Actions, Author -->
    <?php get_template_part('template-parts/single/post-footer'); ?>

    <!-- Comments Section -->
    <?php
    if (comments_open() || get_comments_number()) {
        get_template_part('template-parts/single/comments-section');
    }
    ?>

    <?php endif; ?>

    <!-- Related Posts (always full width) -->
    <?php get_template_part('template-parts/single/related-posts'); ?>

</main>

<?php
get_footer();
