<?php
/**
 * Affiliate Disclosure Alert
 *
 * Displayed above post content. Configurable via Plugin Settings > Display > Post Disclosure.
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Read from plugin settings (acms_general_settings option)
$acms_settings = get_option('acms_general_settings', []);

$disclosure_text = $acms_settings['post_disclosure_text'] ?? __('We independently review everything we recommend. When you buy through our links, we may earn a commission. As an Amazon Associate we earn from qualifying purchases.', 'affiliatecms');
$link_url = $acms_settings['post_disclosure_link_url'] ?? '';
$link_text = $acms_settings['post_disclosure_link_text'] ?? __('Learn more', 'affiliatecms');
$promo1 = $acms_settings['post_disclosure_promo1'] ?? '';
$promo2 = $acms_settings['post_disclosure_promo2'] ?? '';

if (!$disclosure_text) {
    return;
}
?>

<div class="post-disclosure">
    <div class="container">
        <div class="post-disclosure__main">
            <i class="bi bi-info-circle post-disclosure__icon"></i>
            <p class="post-disclosure__text">
                <?php echo esc_html($disclosure_text); ?>
                <?php if ($link_url) : ?>
                    <a href="<?php echo esc_url($link_url); ?>" class="post-disclosure__link" rel="nofollow"><?php echo esc_html($link_text); ?> &rsaquo;</a>
                <?php endif; ?>
            </p>
        </div>
        <?php if ($promo1 || $promo2) : ?>
            <div class="post-disclosure__promos">
                <?php if ($promo1) : ?>
                    <p class="post-disclosure__promo"><?php echo wp_kses_post($promo1); ?></p>
                <?php endif; ?>
                <?php if ($promo2) : ?>
                    <p class="post-disclosure__promo"><?php echo wp_kses_post($promo2); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
