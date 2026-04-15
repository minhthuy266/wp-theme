<?php
/**
 * Template Part: Mobile Navigation
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

$cta_text = get_theme_mod('acms_header_cta_text', __("Today's Deals", 'affiliatecms'));
$cta_url = get_theme_mod('acms_header_cta_url', '#');
?>

<div class="mobile-nav" id="mobile-navigation">
    <div class="mobile-nav__header">
        <div class="mobile-nav__brand">
            <div class="mobile-nav__icon">
                <i class="bi bi-list"></i>
            </div>
            <span class="mobile-nav__title"><?php esc_html_e('Menu', 'affiliatecms'); ?></span>
        </div>
        <button class="mobile-nav__close" aria-label="<?php esc_attr_e('Close menu', 'affiliatecms'); ?>" data-mobile-menu-close>
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="mobile-nav__content">
        <?php
        if (has_nav_menu('mobile')) {
            wp_nav_menu([
                'theme_location' => 'mobile',
                'menu_class'     => 'mobile-nav__menu',
                'container'      => false,
                'fallback_cb'    => false,
                'walker'         => new ACMS_Mobile_Menu_Walker(),
            ]);
        } elseif (has_nav_menu('primary')) {
            wp_nav_menu([
                'theme_location' => 'primary',
                'menu_class'     => 'mobile-nav__menu',
                'container'      => false,
                'fallback_cb'    => false,
                'walker'         => new ACMS_Mobile_Menu_Walker(),
            ]);
        } else {
            // Demo mobile menu
            ?>
            <ul class="mobile-nav__menu">
                <li class="mobile-nav__item">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="mobile-nav__link"><?php esc_html_e('Home', 'affiliatecms'); ?></a>
                </li>
                <li class="mobile-nav__item">
                    <a href="#" class="mobile-nav__link"><?php esc_html_e('Reviews', 'affiliatecms'); ?></a>
                </li>
                <li class="mobile-nav__item">
                    <a href="#" class="mobile-nav__link"><?php esc_html_e('Guides', 'affiliatecms'); ?></a>
                </li>
                <li class="mobile-nav__item">
                    <a href="#" class="mobile-nav__link"><?php esc_html_e('Deals', 'affiliatecms'); ?></a>
                </li>
                <li class="mobile-nav__item">
                    <a href="<?php echo esc_url(home_url('/about/')); ?>" class="mobile-nav__link"><?php esc_html_e('About', 'affiliatecms'); ?></a>
                </li>
                <li class="mobile-nav__item">
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="mobile-nav__link"><?php esc_html_e('Contact', 'affiliatecms'); ?></a>
                </li>
            </ul>
            <?php
        }
        ?>
    </div>

    <div class="mobile-nav__footer">
        <!-- Row 1: AI Search + Theme Toggle -->
        <div class="mobile-nav__footer-row">
            <button class="mobile-nav__ai-btn" data-search-toggle>
                <i class="bi bi-robot"></i>
                <span><?php esc_html_e('Search', 'affiliatecms'); ?></span>
            </button>
            <button class="mobile-nav__theme-toggle" data-theme-toggle>
                <!-- Light mode view: Moon + Dark -->
                <span class="theme-toggle__state theme-toggle__state--light">
                    <i class="bi bi-moon-fill"></i>
                    <span><?php esc_html_e('Dark', 'affiliatecms'); ?></span>
                </span>
                <!-- Dark mode view: Sun + Light -->
                <span class="theme-toggle__state theme-toggle__state--dark">
                    <i class="bi bi-sun-fill"></i>
                    <span><?php esc_html_e('Light', 'affiliatecms'); ?></span>
                </span>
            </button>
        </div>
        <!-- Row 2: Deals CTA -->
        <?php if ($cta_text && $cta_url) : ?>
            <a href="<?php echo esc_url($cta_url); ?>" class="btn btn--primary btn--block mobile-nav__cta">
                <i class="bi bi-tag-fill"></i>
                <?php echo esc_html($cta_text); ?>
            </a>
        <?php endif; ?>
    </div>
</div>
<div class="mobile-nav-overlay" data-mobile-nav-overlay></div>
