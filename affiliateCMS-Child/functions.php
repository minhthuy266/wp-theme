<?php
/**
 * AffiliateCMS Child Theme Functions
 *
 * Add your custom functions, hooks, and overrides here.
 * This file is loaded AFTER the parent theme's functions.php.
 *
 * @package AffiliateCMS_Child
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue parent + child theme styles and scripts
 */
function affiliatecms_child_enqueue_styles(): void
{
    // Parent theme styles (already enqueued by parent, just declare dependency)
    $parentHandle = 'acms-main';

    // Google Fonts - Premium fonts for Home Office theme
    wp_enqueue_style(
        'ho-google-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&family=Fira+Code:wght@400;500&display=swap',
        [],
        null
    );

    // Child theme main styles
    wp_enqueue_style(
        'affiliatecms-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        [$parentHandle, 'ho-google-fonts'],
        wp_get_theme()->get('Version')
    );

    // Additional Home Office specific styles
    wp_enqueue_style(
        'ho-premium-styles',
        get_stylesheet_directory_uri() . '/assets/css/home-office.css',
        ['affiliatecms-child-style'],
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'affiliatecms_child_enqueue_styles', 20);

/* ==========================================================================
   Custom Functions - Home Office Theme Features
   ========================================================================== */

/**
 * Register additional image sizes for product reviews
 */
function ho_register_image_sizes(): void
{
    add_image_size('product-card', 600, 400, true);
    add_image_size('review-hero', 1200, 600, true);
    add_image_size('comparison-thumb', 300, 200, true);
}
add_action('after_setup_theme', 'ho_register_image_sizes');

/**
 * Add custom body classes for home office theme
 */
function ho_add_body_classes(array $classes): array
{
    $classes[] = 'ho-premium-theme';
    
    if (is_singular('post')) {
        $classes[] = 'ho-single-post';
    }
    
    if (function_exists('is_product') && is_product()) {
        $classes[] = 'ho-product-page';
    }
    
    return $classes;
}
add_filter('body_class', 'ho_add_body_classes');

/**
 * Customize excerpt length for product reviews
 */
function ho_custom_excerpt_length(int $length): int
{
    if (is_admin()) {
        return $length;
    }
    return 25;
}
add_filter('excerpt_length', 'ho_custom_excerpt_length', 999);

/**
 * Add "Continue Reading" button to excerpts
 */
function ho_excerpt_more(string $more): string
{
    if (is_admin()) {
        return $more;
    }
    return sprintf(
        ' <a href="%s" class="btn btn-outline">%s</a>',
        esc_url(get_permalink()),
        __('Read More', 'affiliatecms-child')
    );
}
add_filter('excerpt_more', 'ho_excerpt_more');

/**
 * Register widget areas for home office theme
 */
function ho_widgets_init(): void
{
    register_widget_area([
        'name'          => __('Sidebar Product Reviews', 'affiliatecms-child'),
        'id'            => 'sidebar-product-reviews',
        'description'   => __('Widgets in this area will be shown on product review pages.', 'affiliatecms-child'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
    
    register_widget_area([
        'name'          => __('Footer Column 1', 'affiliatecms-child'),
        'id'            => 'footer-column-1',
        'description'   => __('First footer column.', 'affiliatecms-child'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ]);
    
    register_widget_area([
        'name'          => __('Footer Column 2', 'affiliatecms-child'),
        'id'            => 'footer-column-2',
        'description'   => __('Second footer column.', 'affiliatecms-child'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ]);
}
add_action('widgets_init', 'ho_widgets_init');

/**
 * Add schema markup for product reviews
 */
function ho_add_review_schema(): void
{
    if (!is_singular('post')) {
        return;
    }
    
    global $post;
    
    $schema = [
        '@context' => 'https://schema.org/',
        '@type'    => 'Review',
        'itemReviewed' => [
            '@type' => 'Product',
            'name'  => get_the_title(),
        ],
        'author' => [
            '@type' => 'Person',
            'name'  => get_the_author(),
        ],
        'datePublished' => get_the_date('c'),
    ];
    
    echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
}
add_action('wp_head', 'ho_add_review_schema');

/**
 * Custom navigation menu registration
 */
function ho_register_menus(): void
{
    register_nav_menu('primary', __('Primary Menu', 'affiliatecms-child'));
    register_nav_menu('footer', __('Footer Menu', 'affiliatecms-child'));
    register_nav_menu('mobile', __('Mobile Menu', 'affiliatecms-child'));
}
add_action('after_setup_theme', 'ho_register_menus');

// Theme Settings page (Custom CSS + Code Injection)
require_once get_stylesheet_directory() . '/inc/theme-settings.php';
