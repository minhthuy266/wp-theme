<?php
/**
 * Template Name: About Page
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

get_header();
?>

<main id="content" class="site-main">

    <!-- About Section (Hero Style) -->
    <section class="about-section">
        <div class="about-section__background">
            <div class="about-section__pattern"></div>
            <div class="about-section__pattern about-section__pattern--left"></div>
        </div>
        <div class="container">
            <div class="about-section__content">
                <div class="about-section__badge">
                    <i class="bi bi-patch-check-fill"></i>
                    <?php esc_html_e('Trusted Since 2018', 'affiliatecms'); ?>
                </div>
                <h1 class="about-section__title">
                    <?php esc_html_e('About', 'affiliatecms'); ?> <span class="about-section__brand"><?php bloginfo('name'); ?></span>
                </h1>
                <p class="about-section__tagline">
                    <?php esc_html_e('Your trusted destination for smart shopping insights and top-tier product recommendations.', 'affiliatecms'); ?>
                </p>
                <div class="about-section__divider"><span></span></div>

                <?php
                // Check if there's page content
                if (have_posts()) {
                    while (have_posts()) {
                        the_post();
                        if (get_the_content()) {
                            echo '<div class="about-section__description">';
                            the_content();
                            echo '</div>';
                        } else {
                            // Default content
                            ?>
                            <p class="about-section__description">
                                <?php esc_html_e('We offer expert reviews and guides on Electronics, Home Improvement, Kitchen Appliances, Lawn & Garden, Automotive, and Daily Deals. Our goal is to simplify your buying decisions with clear, reliable insights.', 'affiliatecms'); ?>
                            </p>
                            <?php
                        }
                    }
                }
                ?>

                <div class="about-section__cta">
                    <a href="<?php echo esc_url(home_url('/reviews/')); ?>" class="about-section__btn about-section__btn--primary">
                        <i class="bi bi-arrow-right-circle"></i>
                        <?php esc_html_e('Explore Reviews', 'affiliatecms'); ?>
                    </a>
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="about-section__btn about-section__btn--secondary">
                        <i class="bi bi-envelope"></i>
                        <?php esc_html_e('Contact Us', 'affiliatecms'); ?>
                    </a>
                </div>

                <div class="about-section__trust">
                    <div class="about-section__trust-item">
                        <i class="bi bi-shield-check"></i>
                        <span><?php esc_html_e('Unbiased Reviews', 'affiliatecms'); ?></span>
                    </div>
                    <div class="about-section__trust-item">
                        <i class="bi bi-cash-coin"></i>
                        <span><?php esc_html_e('Best Value Picks', 'affiliatecms'); ?></span>
                    </div>
                    <div class="about-section__trust-item">
                        <i class="bi bi-clock-history"></i>
                        <span><?php esc_html_e('Updated Weekly', 'affiliatecms'); ?></span>
                    </div>
                </div>

                <!-- Stats -->
                <div class="about-section__stats">
                    <div class="about-section__stat">
                        <span class="about-section__stat-number"><?php echo esc_html(wp_count_posts()->publish); ?>+</span>
                        <span class="about-section__stat-label"><?php esc_html_e('Expert Reviews', 'affiliatecms'); ?></span>
                    </div>
                    <div class="about-section__stat">
                        <span class="about-section__stat-number">2.5M+</span>
                        <span class="about-section__stat-label"><?php esc_html_e('Monthly Readers', 'affiliatecms'); ?></span>
                    </div>
                    <div class="about-section__stat">
                        <span class="about-section__stat-number"><?php echo esc_html(wp_count_terms('category')); ?>+</span>
                        <span class="about-section__stat-label"><?php esc_html_e('Categories', 'affiliatecms'); ?></span>
                    </div>
                    <div class="about-section__stat">
                        <span class="about-section__stat-number"><?php esc_html_e('Daily', 'affiliatecms'); ?></span>
                        <span class="about-section__stat-label"><?php esc_html_e('Updates', 'affiliatecms'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
