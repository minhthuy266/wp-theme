<?php
/**
 * TOC Expandable Module - Initialization
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Load class
require_once __DIR__ . '/class-toc.php';

// Load template functions
require_once __DIR__ . '/template-toc.php';

/**
 * Initialize TOC Module
 */
function acms_toc_init() {
    \AffiliateCMS\TOC\TOC::instance();
}
add_action('after_setup_theme', 'acms_toc_init');

/**
 * Enqueue TOC assets
 * Note: We enqueue on all single posts, JS will handle if no TOC exists
 */
function acms_toc_enqueue_assets() {
    // Only on single posts
    if (!is_singular('post')) {
        return;
    }

    // CSS - always enqueue on single posts (CSS is harmless if not used)
    wp_enqueue_style(
        'acms-toc',
        get_template_directory_uri() . '/inc/toc-expandable/toc.css',
        [],
        ACMS_VERSION
    );

    // JS - load in footer after content is rendered
    wp_enqueue_script(
        'acms-toc',
        get_template_directory_uri() . '/inc/toc-expandable/toc.js',
        [],
        ACMS_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'acms_toc_enqueue_assets');

/**
 * Auto-insert TOC into post content
 * Runs at priority 20 (after process_content at 5)
 *
 * @param string $content Post content
 * @return string Modified content
 */
function acms_toc_auto_insert($content) {
    // Only on single posts, in main query
    if (!is_singular('post') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    // Check if auto-insert is enabled (can be disabled by filter)
    $auto_insert = apply_filters('acms_toc_auto_insert', true);
    if (!$auto_insert) {
        return $content;
    }

    $toc = \AffiliateCMS\TOC\TOC::instance();

    // Only if TOC should display (minimum 2 headings)
    if (!$toc->should_display()) {
        return $content;
    }

    // Get TOC HTML
    ob_start();
    acms_render_toc_inline();
    $toc_html = ob_get_clean();

    // Insert at the beginning of content
    return $toc_html . $content;
}
// Priority 20 ensures it runs AFTER process_content (priority 15)
add_filter('the_content', 'acms_toc_auto_insert', 20);

/**
 * Add floating bubble to footer
 */
function acms_toc_render_bubble() {
    if (!is_singular('post')) {
        return;
    }

    $toc = \AffiliateCMS\TOC\TOC::instance();

    if ($toc->should_display()) {
        acms_render_toc_bubble();
    }
}
add_action('wp_footer', 'acms_toc_render_bubble');
