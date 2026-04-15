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
 * Enqueue parent + child theme styles
 */
function affiliatecms_child_enqueue_styles(): void
{
    // Parent theme styles (already enqueued by parent, just declare dependency)
    $parentHandle = 'acms-main';

    // Child theme styles (loads after parent)
    wp_enqueue_style(
        'affiliatecms-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        [$parentHandle],
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'affiliatecms_child_enqueue_styles', 20);

/* ==========================================================================
   Custom Functions - Add your code below
   ========================================================================== */

// Theme Settings page (Custom CSS + Code Injection)
require_once get_stylesheet_directory() . '/inc/theme-settings.php';
