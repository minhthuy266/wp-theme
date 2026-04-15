<?php
/**
 * Template Part: Footer (2-Column Layout)
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

// Get footer settings from Customizer
$footer_logo_id = get_theme_mod('acms_footer_logo', '');
$footer_logo_icon = get_theme_mod('acms_footer_logo_icon', 'bi-bag-check');
$footer_description = get_theme_mod('acms_footer_description', __('Your trusted destination for smart shopping insights and top-tier product recommendations.', 'affiliatecms'));
$social_links = acms_get_social_links();
?>

<footer class="site-footer">
    <div class="container">
        <!-- Main 2-Column Section -->
        <div class="footer__main">
            <!-- Left: Logo + Description -->
            <div class="footer__brand">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="footer__logo">
                    <?php if ($footer_logo_id) : ?>
                        <?php echo wp_get_attachment_image($footer_logo_id, 'medium', false, ['class' => 'footer__logo-img', 'alt' => get_bloginfo('name')]); ?>
                    <?php else : ?>
                        <span class="footer__logo-icon">
                            <i class="bi <?php echo esc_attr($footer_logo_icon); ?>"></i>
                        </span>
                        <span class="footer__logo-text"><?php bloginfo('name'); ?></span>
                    <?php endif; ?>
                </a>
                <?php if ($footer_description) : ?>
                    <p class="footer__description"><?php echo esc_html($footer_description); ?></p>
                <?php endif; ?>
            </div>

            <!-- Right: Social + Disclosure -->
            <div class="footer__info">
                <?php if (!empty($social_links)) : ?>
                    <div class="footer__social">
                        <?php foreach ($social_links as $network => $data) : ?>
                            <a href="<?php echo esc_url($data['url']); ?>" class="footer__social-link" aria-label="<?php echo esc_attr($data['label']); ?>" target="_blank" rel="nofollow">
                                <i class="bi <?php echo esc_attr($data['icon']); ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <!-- Demo social links -->
                    <div class="footer__social">
                        <a href="#" class="footer__social-link" aria-label="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="footer__social-link" aria-label="Twitter">
                            <i class="bi bi-twitter-x"></i>
                        </a>
                        <a href="#" class="footer__social-link" aria-label="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="footer__social-link" aria-label="YouTube">
                            <i class="bi bi-youtube"></i>
                        </a>
                    </div>
                <?php endif; ?>

                <?php
                $footer_disclosure_text = get_theme_mod('acms_footer_disclosure', __('As Amazon Associates, we earn from qualifying purchases. This means we may receive a small commission at no extra cost to you if you click through and make a purchase.', 'affiliatecms'));
                if (!empty($footer_disclosure_text)) :
                    $disclosure_link_text = get_theme_mod('acms_footer_disclosure_link_text', '');
                    $disclosure_link_url = get_theme_mod('acms_footer_disclosure_link_url', '');
                ?>
                <p class="footer__disclosure">
                    <?php echo esc_html($footer_disclosure_text); ?>
                    <?php if (!empty($disclosure_link_url) && !empty($disclosure_link_text)) : ?>
                        <a href="<?php echo esc_url($disclosure_link_url); ?>" class="footer__disclosure-link"><?php echo esc_html($disclosure_link_text); ?></a>
                    <?php endif; ?>
                </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Bottom: Links + Copyright -->
        <div class="footer__bottom">
            <nav class="footer__links">
                <?php
                if (has_nav_menu('footer-policy')) {
                    wp_nav_menu([
                        'theme_location' => 'footer-policy',
                        'container'      => false,
                        'items_wrap'     => '%3$s',
                        'fallback_cb'    => false,
                        'walker'         => new class extends Walker_Nav_Menu {
                            public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
                                $output .= '<a href="' . esc_url($item->url) . '" class="footer__link">' . esc_html($item->title) . '</a>';
                            }
                            public function end_el(&$output, $item, $depth = 0, $args = null) {}
                            public function start_lvl(&$output, $depth = 0, $args = null) {}
                            public function end_lvl(&$output, $depth = 0, $args = null) {}
                        },
                    ]);
                } else {
                    // Demo links
                    ?>
                    <a href="#" class="footer__link"><?php esc_html_e('Privacy Policy', 'affiliatecms'); ?></a>
                    <a href="#" class="footer__link"><?php esc_html_e('Terms of Service', 'affiliatecms'); ?></a>
                    <a href="#" class="footer__link"><?php esc_html_e('Affiliate Disclosure', 'affiliatecms'); ?></a>
                    <a href="#" class="footer__link"><?php esc_html_e('Contact', 'affiliatecms'); ?></a>
                    <?php
                }
                ?>
            </nav>
            <p class="footer__copyright"><?php echo wp_kses_post(acms_get_copyright()); ?></p>
        </div>
    </div>
</footer>
