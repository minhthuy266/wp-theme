<?php
/**
 * Template Part: Site Header
 *
 * @package AffiliateCMS
 * @since 1.0.0
 */

$cta_text = get_theme_mod('acms_header_cta_text', __("Today's Deals", 'affiliatecms'));
$cta_url = get_theme_mod('acms_header_cta_url', '#');
?>

<header class="site-header" id="masthead">
    <div class="container">
        <div class="header__inner">
            <!-- Logo -->
            <div class="header__logo">
                <?php acms_site_logo(); ?>
            </div>

            <!-- Desktop Navigation (inline in header) -->
            <nav class="header__nav" id="site-navigation" aria-label="<?php esc_attr_e('Main navigation', 'affiliatecms'); ?>">
                <?php
                if (has_nav_menu('primary')) {
                    wp_nav_menu([
                        'theme_location' => 'primary',
                        'menu_class'     => 'header__menu',
                        'container'      => false,
                        'fallback_cb'    => false,
                        'depth'          => 2,
                        'items_wrap'     => '<ul id="%1$s" class="%2$s" data-priority-nav>%3$s<li class="menu-item menu-item--more"><button type="button" aria-expanded="false" aria-haspopup="true"><i class="bi bi-three-dots"></i><span class="screen-reader-text">' . esc_html__('More', 'affiliatecms') . '</span></button><ul class="more-menu__dropdown"></ul></li></ul>',
                    ]);
                } else {
                    // Demo menu
                    ?>
                    <ul class="header__menu" data-priority-nav>
                        <li class="menu-item"><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'affiliatecms'); ?></a></li>
                        <li class="menu-item"><a href="#"><?php esc_html_e('Reviews', 'affiliatecms'); ?></a></li>
                        <li class="menu-item"><a href="#"><?php esc_html_e('Guides', 'affiliatecms'); ?></a></li>
                        <li class="menu-item"><a href="#"><?php esc_html_e('Deals', 'affiliatecms'); ?></a></li>
                        <li class="menu-item"><a href="<?php echo esc_url(home_url('/about/')); ?>"><?php esc_html_e('About', 'affiliatecms'); ?></a></li>
                        <li class="menu-item menu-item--more">
                            <button type="button" aria-expanded="false" aria-haspopup="true">
                                <i class="bi bi-three-dots"></i>
                                <span class="screen-reader-text"><?php esc_html_e('More', 'affiliatecms'); ?></span>
                            </button>
                            <ul class="more-menu__dropdown"></ul>
                        </li>
                    </ul>
                    <?php
                }
                ?>
            </nav>

            <!-- Mobile Icons -->
            <div class="header__mobile-icons">
                <button class="header__icon-btn" aria-label="<?php esc_attr_e('Search', 'affiliatecms'); ?>" data-search-toggle>
                    <i class="bi bi-search"></i>
                </button>
                <button class="header__icon-btn mobile-menu-toggle" aria-label="<?php esc_attr_e('Menu', 'affiliatecms'); ?>" aria-expanded="false" data-mobile-menu-toggle>
                    <i class="bi bi-list icon-menu"></i>
                    <i class="bi bi-x-lg icon-close"></i>
                </button>
            </div>

            <!-- Header Actions (Desktop) -->
            <div class="header__actions">
                <!-- Search Icon -->
                <button class="header__action-btn" aria-label="<?php esc_attr_e('Search', 'affiliatecms'); ?>" data-search-toggle>
                    <i class="bi bi-search"></i>
                </button>

                <!-- Theme Toggle -->
                <button class="header__action-btn theme-toggle" aria-label="<?php esc_attr_e('Toggle theme', 'affiliatecms'); ?>" data-theme-toggle>
                    <i class="bi bi-moon-fill theme-toggle__icon theme-toggle__icon--moon"></i>
                    <i class="bi bi-sun-fill theme-toggle__icon theme-toggle__icon--sun"></i>
                </button>

                <!-- CTA Button -->
                <?php if ($cta_text && $cta_url) : ?>
                    <a href="<?php echo esc_url($cta_url); ?>" class="btn btn--primary btn--sm header__cta">
                        <i class="bi bi-tag-fill"></i>
                        <?php echo esc_html($cta_text); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
