<?php
/**
 * Template Name: Full Width
 *
 * Simple full-width template - no header, no container constraints.
 * Perfect for pages with custom HTML blocks.
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

get_header();
?>

<main id="content" class="site-main site-main--fullwidth">
    <?php
    while (have_posts()) {
        the_post();
        the_content();
    }
    ?>
</main>

<?php
get_footer();
