<?php
/**
 * Theme Setup
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function acms_theme_setup() {
    // Make theme available for translation
    load_theme_textdomain('affiliatecms', ACMS_DIR . '/languages');

    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails
    add_theme_support('post-thumbnails');

    // Custom image sizes
    add_image_size('acms-card', 400, 250, true);           // Post card thumbnail
    add_image_size('acms-card-small', 128, 128, true);     // Sidebar thumbnail
    add_image_size('acms-hero', 1200, 600, true);          // Hero/Featured image
    add_image_size('acms-featured', 800, 500, true);       // Featured card

    // Register navigation menus
    register_nav_menus([
        'primary'       => __('Primary Menu', 'affiliatecms'),
        'mobile'        => __('Mobile Menu', 'affiliatecms'),
        'footer'        => __('Footer Menu', 'affiliatecms'),
        'footer-policy' => __('Footer Policy Links', 'affiliatecms'),
        'topbar'        => __('Topbar Links', 'affiliatecms'),
    ]);

    // Switch default core markup to output valid HTML5
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);

    // Custom logo support
    add_theme_support('custom-logo', [
        'height'      => 40,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    // Add support for responsive embeds
    add_theme_support('responsive-embeds');

    // Add support for Block Styles
    add_theme_support('wp-block-styles');

    // Add support for full and wide align images
    add_theme_support('align-wide');

    // Add support for editor styles
    add_theme_support('editor-styles');

    // Disable custom colors in block editor (use theme tokens)
    add_theme_support('disable-custom-colors');

    // Disable custom font sizes (use theme tokens)
    add_theme_support('disable-custom-font-sizes');
}
add_action('after_setup_theme', 'acms_theme_setup');

/**
 * Set the content width based on the theme's design
 */
function acms_content_width() {
    $GLOBALS['content_width'] = apply_filters('acms_content_width', 720);
}
add_action('after_setup_theme', 'acms_content_width', 0);

/**
 * Add custom body classes
 */
function acms_body_class($classes) {
    // Add class if sidebar is active
    if (is_active_sidebar('sidebar-1')) {
        $classes[] = 'has-sidebar';
    }

    // Add class for single post
    if (is_singular('post')) {
        $classes[] = 'single-post-page';
    }

    // Add class for front page
    if (is_front_page()) {
        $classes[] = 'front-page';
    }

    return $classes;
}
add_filter('body_class', 'acms_body_class');

/**
 * Customize excerpt length
 */
function acms_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'acms_excerpt_length');

/**
 * Customize excerpt more string
 */
function acms_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'acms_excerpt_more');

/**
 * Enable CSS Classes and Description fields in Menu Admin
 * Cần thiết để sử dụng mega menu với icons
 */
function acms_enable_menu_fields($classes) {
    $classes[] = 'link-target';
    $classes[] = 'css-classes';
    $classes[] = 'description';
    return $classes;
}
add_filter('manage_nav-menus_columns', 'acms_enable_menu_fields', 99);

/**
 * Default screen options for menu page
 */
function acms_menu_screen_options() {
    $user_id = get_current_user_id();

    // Check if already set
    $hidden = get_user_meta($user_id, 'managenav-menuscolumnshidden', true);

    if (empty($hidden)) {
        // Ensure CSS Classes and Description are visible
        update_user_meta($user_id, 'managenav-menuscolumnshidden', []);
    }
}
add_action('admin_init', 'acms_menu_screen_options');

/**
 * Include custom post types in author archive queries
 */
function acms_author_archive_post_types($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->is_author()) {
        $post_types = ['post'];

        // Add enabled CPTs that support 'author'
        $cpt_slugs = ['acms_reviews', 'acms_deals', 'acms_guides'];
        foreach ($cpt_slugs as $cpt) {
            if (post_type_exists($cpt)) {
                $post_types[] = $cpt;
            }
        }

        $query->set('post_type', $post_types);
    }
}
add_action('pre_get_posts', 'acms_author_archive_post_types');
