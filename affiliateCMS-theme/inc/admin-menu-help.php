<?php
/**
 * Admin Menu Help
 *
 * @package AffiliateCMS
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Theme Guide submenu page
 * DISABLED - Menu hidden per user request
 */
function acms_add_theme_guide_page() {
    add_theme_page(
        __('AffiliateCMS Guide', 'affiliatecms'),
        __('Theme Guide', 'affiliatecms'),
        'edit_theme_options',
        'acms-theme-guide',
        'acms_render_theme_guide_page'
    );
}
// add_action('admin_menu', 'acms_add_theme_guide_page'); // Disabled

/**
 * Render theme guide page
 */
function acms_render_theme_guide_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('AffiliateCMS Theme Guide', 'affiliatecms'); ?></h1>

        <div class="acms-guide-wrapper" style="max-width: 900px; margin-top: 20px;">

            <!-- Menu Setup Section -->
            <div class="card" style="margin-bottom: 20px; padding: 20px;">
                <h2 style="margin-top: 0;">
                    <span class="dashicons dashicons-menu" style="margin-right: 8px;"></span>
                    <?php _e('Menu Setup', 'affiliatecms'); ?>
                </h2>

                <p><?php _e('AffiliateCMS uses a simple, clean navigation system in the header.', 'affiliatecms'); ?></p>

                <h3><?php _e('Menu Locations', 'affiliatecms'); ?></h3>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th><?php _e('Location', 'affiliatecms'); ?></th>
                            <th><?php _e('Description', 'affiliatecms'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Primary Menu</strong></td>
                            <td><?php _e('Main navigation in header (supports dropdown submenus)', 'affiliatecms'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Mobile Menu</strong></td>
                            <td><?php _e('Mobile sidebar navigation (auto-uses Primary if not set)', 'affiliatecms'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Footer Menu</strong></td>
                            <td><?php _e('Links in footer area', 'affiliatecms'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Topbar Links</strong></td>
                            <td><?php _e('Quick links in topbar', 'affiliatecms'); ?></td>
                        </tr>
                    </tbody>
                </table>

                <p style="margin-top: 20px;">
                    <a href="<?php echo admin_url('nav-menus.php'); ?>" class="button button-primary">
                        <?php _e('Go to Menus', 'affiliatecms'); ?>
                    </a>
                </p>
            </div>

            <!-- Customizer Section -->
            <div class="card" style="margin-bottom: 20px; padding: 20px;">
                <h2 style="margin-top: 0;">
                    <span class="dashicons dashicons-admin-customizer" style="margin-right: 8px;"></span>
                    <?php _e('Theme Customization', 'affiliatecms'); ?>
                </h2>

                <p><?php _e('Use the WordPress Customizer to adjust theme settings:', 'affiliatecms'); ?></p>

                <ul style="list-style: disc; padding-left: 20px;">
                    <li><?php _e('Site logo and identity', 'affiliatecms'); ?></li>
                    <li><?php _e('Header CTA button text and link', 'affiliatecms'); ?></li>
                    <li><?php _e('Footer content', 'affiliatecms'); ?></li>
                    <li><?php _e('Colors and typography', 'affiliatecms'); ?></li>
                </ul>

                <p style="margin-top: 20px;">
                    <a href="<?php echo admin_url('customize.php'); ?>" class="button button-primary">
                        <?php _e('Open Customizer', 'affiliatecms'); ?>
                    </a>
                </p>
            </div>

        </div>
    </div>
    <?php
}
