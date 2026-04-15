<?php
/**
 * Widget Areas Registration & Custom Widgets
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Load custom widget classes
 */
function acms_load_widgets() {
    // Load widget classes
    require_once ACMS_DIR . '/inc/widgets/class-popular-posts-widget.php';
    require_once ACMS_DIR . '/inc/widgets/class-categories-widget.php';
    require_once ACMS_DIR . '/inc/widgets/class-newsletter-widget.php';
    require_once ACMS_DIR . '/inc/widgets/class-tags-widget.php';
    require_once ACMS_DIR . '/inc/widgets/class-cta-widget.php';

    // Register widgets
    register_widget('ACMS_Popular_Posts_Widget');
    register_widget('ACMS_Categories_Widget');
    register_widget('ACMS_Newsletter_Widget');
    register_widget('ACMS_Tags_Widget');
    register_widget('ACMS_CTA_Widget');
}
add_action('widgets_init', 'acms_load_widgets', 5);

/**
 * Register widget areas
 */
function acms_widgets_init() {
    // Main Sidebar
    register_sidebar([
        'name'          => __('Main Sidebar', 'affiliatecms'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here to appear in the main sidebar.', 'affiliatecms'),
        'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<div class="sidebar-widget__header"><h3 class="sidebar-widget__title">',
        'after_title'   => '</h3></div>',
    ]);

    // Post Sidebar
    register_sidebar([
        'name'          => __('Post Sidebar', 'affiliatecms'),
        'id'            => 'sidebar-post',
        'description'   => __('Widgets here appear on single post pages. Falls back to Main Sidebar if empty.', 'affiliatecms'),
        'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<div class="sidebar-widget__header"><h3 class="sidebar-widget__title">',
        'after_title'   => '</h3></div>',
    ]);

    // Footer Widget Areas
    register_sidebar([
        'name'          => __('Footer Column 1', 'affiliatecms'),
        'id'            => 'footer-1',
        'description'   => __('Add widgets here for footer column 1.', 'affiliatecms'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget__title">',
        'after_title'   => '</h4>',
    ]);

    register_sidebar([
        'name'          => __('Footer Column 2', 'affiliatecms'),
        'id'            => 'footer-2',
        'description'   => __('Add widgets here for footer column 2.', 'affiliatecms'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget__title">',
        'after_title'   => '</h4>',
    ]);

    register_sidebar([
        'name'          => __('Footer Column 3', 'affiliatecms'),
        'id'            => 'footer-3',
        'description'   => __('Add widgets here for footer column 3.', 'affiliatecms'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget__title">',
        'after_title'   => '</h4>',
    ]);

    register_sidebar([
        'name'          => __('Footer Column 4', 'affiliatecms'),
        'id'            => 'footer-4',
        'description'   => __('Add widgets here for footer column 4.', 'affiliatecms'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget__title">',
        'after_title'   => '</h4>',
    ]);
}
add_action('widgets_init', 'acms_widgets_init');
