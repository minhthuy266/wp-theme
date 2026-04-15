<?php
/**
 * AffiliateCMS - Theme Functions
 *
 * A standalone WordPress theme for affiliate marketing.
 *
 * @package AffiliateCMS
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Theme constants
if (!defined('ACMS_VERSION')) {
    define('ACMS_VERSION', '1.2.1');
}
if (!defined('ACMS_DIR')) {
    define('ACMS_DIR', get_template_directory());
}
if (!defined('ACMS_URI')) {
    define('ACMS_URI', get_template_directory_uri());
}

/**
 * Include required files
 */
require_once ACMS_DIR . '/inc/setup.php';
require_once ACMS_DIR . '/inc/enqueue.php';
require_once ACMS_DIR . '/inc/template-functions.php';
require_once ACMS_DIR . '/inc/template-tags.php';
require_once ACMS_DIR . '/inc/walker-nav.php';
require_once ACMS_DIR . '/inc/widgets.php';
require_once ACMS_DIR . '/inc/customizer.php';
require_once ACMS_DIR . '/inc/rest-api.php';
require_once ACMS_DIR . '/inc/brand-ajax.php';

// Comments Module (OOP structure)
require_once ACMS_DIR . '/inc/comments/class-comments.php';

/**
 * Initialize Comments Module
 */
function acms_init_comments() {
    \AffiliateCMS\Comments\Comments::instance();
}
add_action('after_setup_theme', 'acms_init_comments');

// TOC Expandable Module
require_once ACMS_DIR . '/inc/toc-expandable/init.php';

// User profile fields (needed for both admin and frontend helpers)
require_once ACMS_DIR . '/inc/admin-user-fields.php';

// Theme auto-updater (checks AffiliateCMS server for updates)
require_once ACMS_DIR . '/inc/updater.php';

// Admin only
if (is_admin()) {
    require_once ACMS_DIR . '/inc/admin-menu-help.php';
    require_once ACMS_DIR . '/inc/admin-category-fields.php';
}
