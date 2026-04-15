<?php
/**
 * Brand Archive & Deals Page AJAX Handlers
 * Load more products for brand taxonomy pages and deals page
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX handler for loading more brand products
 */
function acms_load_more_brand_products() {
    // Verify nonce
    check_ajax_referer('acms_brand_load_more', 'nonce');

    // Get parameters
    $brand_slug = sanitize_text_field($_POST['brand'] ?? '');
    $offset = absint($_POST['offset'] ?? 0);
    $per_page = absint($_POST['per_page'] ?? 12);

    if (empty($brand_slug)) {
        wp_send_json_error(['message' => __('Brand not specified', 'affiliatecms')]);
    }

    // Get brand term
    $brand_term = get_term_by('slug', $brand_slug, 'acms_reviews_brand');
    if (!$brand_term) {
        wp_send_json_error(['message' => __('Brand not found', 'affiliatecms')]);
    }

    $brand_name = $brand_term->name;

    // Query products from database
    global $wpdb;
    $products_table = $wpdb->prefix . 'acms_products';

    $products = $wpdb->get_results($wpdb->prepare(
        "SELECT asin FROM {$products_table}
         WHERE brand = %s AND status = 'scraped'
         ORDER BY score DESC, created_at DESC
         LIMIT %d OFFSET %d",
        $brand_name,
        $per_page,
        $offset
    ), ARRAY_A);

    if (empty($products)) {
        wp_send_json_success([
            'html' => '',
            'has_more' => false,
            'new_offset' => $offset,
            'message' => __('No more products', 'affiliatecms'),
        ]);
    }

    // Get ASINs
    $asins = array_column($products, 'asin');

    // Render products using shortcode
    $html = do_shortcode('[acms_list asin="' . implode(',', $asins) . '" numbered="true"]');

    // Calculate if there are more products
    $new_offset = $offset + count($products);
    $total_products = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$products_table} WHERE brand = %s AND status = 'scraped'",
        $brand_name
    ));
    $has_more = $new_offset < $total_products;

    wp_send_json_success([
        'html' => $html,
        'has_more' => $has_more,
        'new_offset' => $new_offset,
        'loaded_count' => count($products),
        'total_count' => $total_products,
    ]);
}
add_action('wp_ajax_acms_load_more_brand_products', 'acms_load_more_brand_products');
add_action('wp_ajax_nopriv_acms_load_more_brand_products', 'acms_load_more_brand_products');

/**
 * AJAX handler for loading more deals products
 */
function acms_load_more_deals() {
    // Verify nonce
    check_ajax_referer('acms_deals_load_more', 'nonce');

    // Get parameters
    $offset = absint($_POST['offset'] ?? 0);
    $per_page = absint($_POST['per_page'] ?? 24);

    // Query products with discounts from database
    global $wpdb;
    $products_table = $wpdb->prefix . 'acms_products';

    $products = $wpdb->get_results($wpdb->prepare(
        "SELECT asin,
                price,
                original_price,
                ROUND(((original_price - price) / original_price) * 100, 0) as discount_percent
         FROM {$products_table}
         WHERE status = 'scraped'
         AND original_price IS NOT NULL
         AND original_price > 0
         AND price IS NOT NULL
         AND price > 0
         AND original_price > price
         ORDER BY discount_percent DESC, created_at DESC
         LIMIT %d OFFSET %d",
        $per_page,
        $offset
    ), ARRAY_A);

    if (empty($products)) {
        wp_send_json_success([
            'html' => '',
            'has_more' => false,
            'new_offset' => $offset,
            'message' => __('No more deals', 'affiliatecms'),
        ]);
    }

    // Get ASINs
    $asins = array_column($products, 'asin');

    // Render products using shortcode
    $html = do_shortcode('[acms_list asin="' . implode(',', $asins) . '" numbered="true"]');

    // Calculate if there are more products
    $new_offset = $offset + count($products);
    $total_products = $wpdb->get_var(
        "SELECT COUNT(*) FROM {$products_table}
         WHERE status = 'scraped'
         AND original_price IS NOT NULL
         AND original_price > 0
         AND price IS NOT NULL
         AND price > 0
         AND original_price > price"
    );
    $has_more = $new_offset < $total_products;

    wp_send_json_success([
        'html' => $html,
        'has_more' => $has_more,
        'new_offset' => $new_offset,
        'loaded_count' => count($products),
        'total_count' => $total_products,
    ]);
}
add_action('wp_ajax_acms_load_more_deals', 'acms_load_more_deals');
add_action('wp_ajax_nopriv_acms_load_more_deals', 'acms_load_more_deals');
