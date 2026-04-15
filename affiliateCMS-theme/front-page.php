<?php
/**
 * Front Page Template (Homepage)
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

get_header();
?>

<main id="content" class="site-main">
    <?php
    // Featured Posts Section
    get_template_part('template-parts/sections/featured-posts');

    // Latest Posts Section (with sidebar)
    get_template_part('template-parts/sections/latest-posts');
    ?>

</main>

<?php
get_footer();
