<?php
/**
 * Enqueue Scripts and Styles
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if we should use minified assets (production mode)
 */
function acms_use_minified_assets() {
    $minified_css = get_template_directory() . '/assets/dist/theme.min.css';

    // File must exist
    if (!file_exists($minified_css)) {
        return false;
    }

    // Force minified if ACMS_USE_MINIFIED is true
    if (defined('ACMS_USE_MINIFIED') && ACMS_USE_MINIFIED) {
        return true;
    }

    // Otherwise, use minified only when WP_DEBUG is false
    return !defined('WP_DEBUG') || !WP_DEBUG;
}

/**
 * Enqueue styles
 */
function acms_enqueue_styles() {
    // Google Fonts - Inter
    wp_enqueue_style(
        'acms-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
        [],
        null
    );

    // Bootstrap Icons
    wp_enqueue_style(
        'bootstrap-icons',
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
        [],
        '1.11.3'
    );

    // Main theme styles - use minified in production
    if (acms_use_minified_assets()) {
        wp_enqueue_style(
            'acms-main',
            ACMS_URI . '/assets/dist/theme.min.css',
            ['acms-google-fonts', 'bootstrap-icons'],
            ACMS_VERSION
        );
    } else {
        wp_enqueue_style(
            'acms-main',
            ACMS_URI . '/assets/css/main.css',
            ['acms-google-fonts', 'bootstrap-icons'],
            ACMS_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'acms_enqueue_styles');

/**
 * Enqueue scripts
 */
function acms_enqueue_scripts() {
    // Main theme script - use minified in production
    if (acms_use_minified_assets()) {
        wp_enqueue_script(
            'acms-theme',
            ACMS_URI . '/assets/dist/theme.min.js',
            [],
            ACMS_VERSION,
            true
        );
    } else {
        wp_enqueue_script(
            'acms-theme',
            ACMS_URI . '/assets/js/theme.js',
            [],
            ACMS_VERSION,
            true
        );

        // Priority Navigation (responsive menu overflow) - only in dev mode
        wp_enqueue_script(
            'acms-priority-nav',
            ACMS_URI . '/assets/js/modules/priority-nav.js',
            [],
            ACMS_VERSION . '.1',
            true
        );
    }

    // Localize script for REST API and dynamic data
    $localize_data = [
        'restUrl'  => rest_url('azs/v1/'),
        'nonce'    => wp_create_nonce('wp_rest'),
        'homeUrl'  => home_url('/'),
        'themeUrl' => ACMS_URI,
    ];

    // Add post ID for single posts and custom post types (for view tracking)
    if (is_singular(['post', 'acms_reviews', 'acms_deals', 'acms_guides'])) {
        $localize_data['postId'] = get_the_ID();
        $localize_data['trackViews'] = true;
    }

    wp_localize_script('acms-theme', 'acmsData', $localize_data);

    // Comment reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'acms_enqueue_scripts');

/**
 * Enqueue brand archive script for brand taxonomy pages
 */
function acms_enqueue_brand_archive_script() {
    // Only load on brand taxonomy pages
    if (!is_tax('acms_reviews_brand')) {
        return;
    }

    wp_enqueue_script(
        'acms-brand-archive',
        ACMS_URI . '/assets/js/brand-archive.js',
        [],
        ACMS_VERSION,
        true
    );

    // Localize script for AJAX
    wp_localize_script('acms-brand-archive', 'acmsBrandAjax', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('acms_brand_load_more'),
    ]);
}
add_action('wp_enqueue_scripts', 'acms_enqueue_brand_archive_script');

/**
 * Enqueue deals page script for deals page template
 */
function acms_enqueue_deals_page_script() {
    // Only load on deals page template
    if (!is_page_template('template-deals.php')) {
        return;
    }

    wp_enqueue_script(
        'acms-deals-page',
        ACMS_URI . '/assets/js/deals-page.js',
        [],
        ACMS_VERSION,
        true
    );

    // Localize script for AJAX
    wp_localize_script('acms-deals-page', 'acmsDealsAjax', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('acms_deals_load_more'),
    ]);
}
add_action('wp_enqueue_scripts', 'acms_enqueue_deals_page_script');

/**
 * Add preconnect for Google Fonts
 */
function acms_preconnect_google_fonts($urls, $relation_type) {
    if ('preconnect' === $relation_type) {
        $urls[] = [
            'href' => 'https://fonts.googleapis.com',
        ];
        $urls[] = [
            'href' => 'https://fonts.gstatic.com',
            'crossorigin' => 'anonymous',
        ];
    }
    return $urls;
}
add_filter('wp_resource_hints', 'acms_preconnect_google_fonts', 10, 2);

/**
 * Add theme color meta tag
 */
function acms_theme_color_meta() {
    echo '<meta name="theme-color" content="#14b8a6">' . "\n";
}
add_action('wp_head', 'acms_theme_color_meta', 1);

/**
 * Critical inline CSS for logo — prevents layout shift from large images
 * Loaded inline so it's not affected by CSS defer/combine plugins (LiteSpeed, etc.)
 */
function acms_critical_logo_css() {
    ?>
    <style id="acms-critical-logo">
    .header__logo{max-width:220px;overflow:hidden}
    .header__logo img,.header__logo .custom-logo,.custom-logo-link img{display:block;height:36px!important;width:auto!important;max-width:220px!important;max-height:36px!important;object-fit:contain}
    </style>
    <?php
}
add_action('wp_head', 'acms_critical_logo_css', 2);

/**
 * Enqueue admin styles for widgets page
 */
function acms_admin_widgets_styles($hook) {
    if ('widgets.php' !== $hook) {
        return;
    }

    // Inline CSS to highlight AffiliateCMS widgets
    $css = '
        /* AffiliateCMS Widgets Highlight */
        .widget[id*="acms_"] .widget-title h3,
        #available-widgets .widget[id*="acms_"] .widget-title {
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%) !important;
            color: #fff !important;
        }
        #available-widgets .widget[id*="acms_"] .widget-title::before {
            content: "";
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #fff;
            border-radius: 50%;
            margin-right: 8px;
            animation: acms-pulse 2s infinite;
        }
        @keyframes acms-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        #available-widgets .widget[id*="acms_"] {
            border-left: 3px solid #14b8a6;
        }
        .widget[id*="acms_"] .widget-inside {
            border-top: 2px solid #14b8a6;
        }
    ';

    wp_add_inline_style('widgets', $css);
}
add_action('admin_enqueue_scripts', 'acms_admin_widgets_styles');

/**
 * Fallback favicon if not set in Customizer
 */
function acms_fallback_favicon() {
    // Check if site icon is set
    if (has_site_icon()) {
        return;
    }

    // Output fallback favicon
    $favicon_url = ACMS_URI . '/assets/images/favicon.svg';
    ?>
    <link rel="icon" href="<?php echo esc_url($favicon_url); ?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="<?php echo esc_url($favicon_url); ?>">
    <?php
}
add_action('wp_head', 'acms_fallback_favicon', 5);

/**
 * Output Schema.org JSON-LD for single posts with ratings
 */
function acms_output_schema_jsonld() {
    // Support regular posts and ACMS custom post types
    $supported_types = ['post', 'acms_reviews', 'acms_deals', 'acms_guides'];
    if (!is_singular($supported_types)) {
        return;
    }

    $post_id = get_the_ID();
    $rating_data = acms_get_post_rating($post_id);

    // Get description with placeholder replacement (filter applies to get_the_excerpt)
    $description = get_the_excerpt();
    if (empty($description)) {
        // Fallback: trim content - apply_filters to ensure placeholders are replaced
        $content = apply_filters('the_content', get_the_content());
        $description = wp_trim_words(wp_strip_all_tags($content), 30, '...');
    }

    // Build Article schema
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => get_the_title(),
        'description' => $description,
        'url' => get_permalink(),
        'datePublished' => get_the_date('c'),
        'dateModified' => get_the_modified_date('c'),
        'author' => [
            '@type' => 'Person',
            'name' => get_the_author(),
            'url' => get_author_posts_url(get_the_author_meta('ID')),
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => home_url('/'),
        ],
    ];

    // Add featured image if exists
    if (has_post_thumbnail()) {
        $image_id = get_post_thumbnail_id();
        $image_data = wp_get_attachment_image_src($image_id, 'full');
        if ($image_data) {
            $schema['image'] = [
                '@type' => 'ImageObject',
                'url' => $image_data[0],
                'width' => $image_data[1],
                'height' => $image_data[2],
            ];
        }
    } else {
        // Fallback: try to get first image from rendered content (includes shortcodes)
        $content = apply_filters('the_content', get_the_content());
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', $content, $matches)) {
            $image_url = $matches[1];
            $schema['image'] = [
                '@type' => 'ImageObject',
                'url' => $image_url,
            ];
            // Try to get dimensions if it's a WordPress attachment
            $attachment_id = attachment_url_to_postid($image_url);
            if ($attachment_id) {
                $image_data = wp_get_attachment_image_src($attachment_id, 'full');
                if ($image_data) {
                    $schema['image']['width'] = $image_data[1];
                    $schema['image']['height'] = $image_data[2];
                }
            }
        } elseif (has_site_icon()) {
            // Fallback: use site icon
            $schema['image'] = [
                '@type' => 'ImageObject',
                'url' => get_site_icon_url(512),
                'width' => 512,
                'height' => 512,
            ];
        } elseif (has_custom_logo()) {
            // Fallback: use custom logo
            $logo_id = get_theme_mod('custom_logo');
            $logo_data = wp_get_attachment_image_src($logo_id, 'full');
            if ($logo_data) {
                $schema['image'] = [
                    '@type' => 'ImageObject',
                    'url' => $logo_data[0],
                    'width' => $logo_data[1],
                    'height' => $logo_data[2],
                ];
            }
        }
    }

    // Add logo to publisher if site icon exists
    if (has_site_icon()) {
        $schema['publisher']['logo'] = [
            '@type' => 'ImageObject',
            'url' => get_site_icon_url(512),
        ];
    }

    // Add AggregateRating if has real reviews
    if ($rating_data['count'] > 0) {
        $schema['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => number_format($rating_data['average'], 1),
            'bestRating' => '5',
            'worstRating' => '1',
            'ratingCount' => $rating_data['count'],
        ];
    }

    // Output schema
    echo '<script type="application/ld+json">' . "\n";
    echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    echo "\n</script>\n";
}
add_action('wp_head', 'acms_output_schema_jsonld', 20);

/**
 * Output WebSite and Organization Schema (global - all pages)
 */
function acms_output_global_schema() {
    // Only output once on the page
    static $output = false;
    if ($output) {
        return;
    }
    $output = true;

    $schemas = [];

    // ========================================
    // WebSite Schema with SearchAction
    // ========================================
    $website_schema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => home_url('/#website'),
        'name' => get_bloginfo('name'),
        'url' => home_url('/'),
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => home_url('/?s={search_term_string}'),
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];

    // Add description if set
    $description = get_bloginfo('description');
    if ($description) {
        $website_schema['description'] = $description;
    }

    // Add publisher reference
    $website_schema['publisher'] = [
        '@id' => home_url('/#organization'),
    ];

    $schemas[] = $website_schema;

    // ========================================
    // Organization Schema
    // ========================================
    $org_schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        '@id' => home_url('/#organization'),
        'name' => get_bloginfo('name'),
        'url' => home_url('/'),
    ];

    // Add logo if site icon exists
    if (has_site_icon()) {
        $org_schema['logo'] = [
            '@type' => 'ImageObject',
            '@id' => home_url('/#logo'),
            'url' => get_site_icon_url(512),
            'width' => 512,
            'height' => 512,
            'caption' => get_bloginfo('name'),
        ];
        $org_schema['image'] = ['@id' => home_url('/#logo')];
    }

    // Add social profiles from Customizer
    $social_profiles = [];
    $social_keys = [
        'acms_social_facebook' => 'facebook',
        'acms_social_twitter' => 'twitter',
        'acms_social_instagram' => 'instagram',
        'acms_social_youtube' => 'youtube',
        'acms_social_linkedin' => 'linkedin',
        'acms_social_pinterest' => 'pinterest',
        'acms_social_tiktok' => 'tiktok',
    ];

    foreach ($social_keys as $key => $name) {
        $url = get_theme_mod($key, '');
        if ($url) {
            $social_profiles[] = $url;
        }
    }

    if (!empty($social_profiles)) {
        $org_schema['sameAs'] = $social_profiles;
    }

    // Add contact info from Customizer (optional)
    $contact_email = get_theme_mod('acms_contact_email', '');
    if ($contact_email) {
        $org_schema['email'] = $contact_email;
    }

    $schemas[] = $org_schema;

    // Output all schemas
    foreach ($schemas as $schema) {
        echo '<script type="application/ld+json">' . "\n";
        echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "\n</script>\n";
    }
}
add_action('wp_head', 'acms_output_global_schema', 5);
