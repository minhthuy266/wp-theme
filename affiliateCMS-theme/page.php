<?php
/**
 * Default Page Template
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

get_header();
?>

<main id="content" class="site-main site-main--page">
    <!-- Page Header -->
    <section class="page-header">
        <div class="page-header__background">
            <div class="page-header__gradient"></div>
            <div class="page-header__pattern"></div>
        </div>
        <div class="container">
            <div class="page-header__content">
                <?php acms_breadcrumb(['class' => 'breadcrumb breadcrumb--centered', 'show_current' => true]); ?>
                <h1 class="page-header__title"><?php the_title(); ?></h1>
            </div>
        </div>
    </section>

    <!-- Page Content -->
    <article class="page-article">
        <div class="container">
            <div class="page-content post-content">
                <?php
                while (have_posts()) {
                    the_post();
                    the_content();
                }
                ?>
            </div>
        </div>
    </article>

    <?php
    // If comments are open or we have at least one comment
    if (comments_open() || get_comments_number()) :
    ?>
    <!-- Comments Section -->
    <section class="page-comments-section">
        <div class="container">
            <div class="page-comments">
                <?php comments_template(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php
get_footer();
