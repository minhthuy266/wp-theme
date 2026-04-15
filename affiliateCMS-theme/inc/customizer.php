<?php
/**
 * Theme Customizer
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register customizer settings
 */
function acms_customize_register($wp_customize) {

    // ========================================
    // COLORS & APPEARANCE
    // ========================================
    $wp_customize->add_section('acms_colors', [
        'title'       => __('Colors & Appearance', 'affiliatecms'),
        'description' => __('Customize the color scheme of your site.', 'affiliatecms'),
        'priority'    => 20,
    ]);

    // Primary Color (Teal by default - matches tokens.css)
    $wp_customize->add_setting('acms_color_primary', [
        'default'           => '#0D7377',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ]);

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'acms_color_primary', [
        'label'       => __('Primary Color', 'affiliatecms'),
        'description' => __('Main brand color used for buttons, links, accents.', 'affiliatecms'),
        'section'     => 'acms_colors',
    ]));

    // Primary Hover Color (darker - matches tokens.css)
    $wp_customize->add_setting('acms_color_primary_hover', [
        'default'           => '#0A5C5F',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ]);

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'acms_color_primary_hover', [
        'label'       => __('Primary Hover Color', 'affiliatecms'),
        'description' => __('Darker shade for hover states.', 'affiliatecms'),
        'section'     => 'acms_colors',
    ]));

    // Accent Color (Coral - matches tokens.css)
    $wp_customize->add_setting('acms_color_accent', [
        'default'           => '#E07A5F',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ]);

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'acms_color_accent', [
        'label'       => __('Accent Color', 'affiliatecms'),
        'description' => __('Secondary accent for highlights, badges, CTAs.', 'affiliatecms'),
        'section'     => 'acms_colors',
    ]));

    // Surface Color (Card backgrounds)
    $wp_customize->add_setting('acms_color_surface', [
        'default'           => '#FFFFFF',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ]);

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'acms_color_surface', [
        'label'       => __('Surface Color', 'affiliatecms'),
        'description' => __('Background color for cards, modals, dropdowns.', 'affiliatecms'),
        'section'     => 'acms_colors',
    ]));

    // Background Color (Warm off-white - matches tokens.css)
    $wp_customize->add_setting('acms_color_background', [
        'default'           => '#FDFBF7',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ]);

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'acms_color_background', [
        'label'       => __('Background Color', 'affiliatecms'),
        'description' => __('Main page background color.', 'affiliatecms'),
        'section'     => 'acms_colors',
    ]));

    // Text Color (matches tokens.css)
    $wp_customize->add_setting('acms_color_text', [
        'default'           => '#1A1D21',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ]);

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'acms_color_text', [
        'label'       => __('Text Color', 'affiliatecms'),
        'description' => __('Main body text color.', 'affiliatecms'),
        'section'     => 'acms_colors',
    ]));

    // Text Muted Color (matches tokens.css)
    $wp_customize->add_setting('acms_color_text_muted', [
        'default'           => '#6B7280',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ]);

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'acms_color_text_muted', [
        'label'       => __('Muted Text Color', 'affiliatecms'),
        'description' => __('Secondary text, meta info, placeholders.', 'affiliatecms'),
        'section'     => 'acms_colors',
    ]));

    // Text Secondary Color (matches tokens.css)
    $wp_customize->add_setting('acms_color_text_secondary', [
        'default'           => '#4A5056',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ]);

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'acms_color_text_secondary', [
        'label'       => __('Secondary Text Color', 'affiliatecms'),
        'description' => __('Subheadings, labels, secondary content.', 'affiliatecms'),
        'section'     => 'acms_colors',
    ]));

    // Border Color (matches tokens.css)
    $wp_customize->add_setting('acms_color_border', [
        'default'           => '#E5E2DC',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ]);

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'acms_color_border', [
        'label'       => __('Border Color', 'affiliatecms'),
        'description' => __('Default border color for cards, inputs.', 'affiliatecms'),
        'section'     => 'acms_colors',
    ]));

    // Background Alt Color (matches tokens.css)
    $wp_customize->add_setting('acms_color_bg_alt', [
        'default'           => '#F7F5F0',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ]);

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'acms_color_bg_alt', [
        'label'       => __('Alternate Background', 'affiliatecms'),
        'description' => __('For alternating sections, zebra stripes.', 'affiliatecms'),
        'section'     => 'acms_colors',
    ]));

    // Color Presets
    $wp_customize->add_setting('acms_color_preset', [
        'default'           => 'teal',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('acms_color_preset', [
        'label'       => __('Color Preset', 'affiliatecms'),
        'description' => __('Quick presets. Custom colors above will override.', 'affiliatecms'),
        'section'     => 'acms_colors',
        'type'        => 'select',
        'choices'     => [
            'teal'   => __('Teal (Default)', 'affiliatecms'),
            'blue'   => __('Blue', 'affiliatecms'),
            'purple' => __('Purple', 'affiliatecms'),
            'green'  => __('Green', 'affiliatecms'),
            'red'    => __('Red', 'affiliatecms'),
            'orange' => __('Orange', 'affiliatecms'),
            'pink'   => __('Pink', 'affiliatecms'),
            'dark'   => __('Dark Mode', 'affiliatecms'),
        ],
    ]);

    // ========================================
    // LAYOUT SETTINGS
    // ========================================
    $wp_customize->add_section('acms_layout', [
        'title'       => __('Layout Settings', 'affiliatecms'),
        'description' => __('Configure default layout options.', 'affiliatecms'),
        'priority'    => 25,
    ]);

    // Default Sidebar Position
    $wp_customize->add_setting('acms_sidebar_position', [
        'default'           => 'right',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('acms_sidebar_position', [
        'label'   => __('Default Sidebar Position', 'affiliatecms'),
        'section' => 'acms_layout',
        'type'    => 'select',
        'choices' => [
            'right' => __('Right Sidebar', 'affiliatecms'),
            'left'  => __('Left Sidebar', 'affiliatecms'),
            'none'  => __('No Sidebar (Full Width)', 'affiliatecms'),
        ],
    ]);

    // Single Post Layout
    $wp_customize->add_setting('acms_single_layout', [
        'default'           => 'full',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('acms_single_layout', [
        'label'       => __('Single Post Layout', 'affiliatecms'),
        'description' => __('Layout for individual post pages.', 'affiliatecms'),
        'section'     => 'acms_layout',
        'type'        => 'select',
        'choices'     => [
            'full'    => __('Full Width (Centered)', 'affiliatecms'),
            'sidebar' => __('With Sidebar', 'affiliatecms'),
        ],
    ]);

    // Archive Layout
    $wp_customize->add_setting('acms_archive_layout', [
        'default'           => 'grid',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('acms_archive_layout', [
        'label'       => __('Archive/Category Layout', 'affiliatecms'),
        'description' => __('Layout for archive, category, tag pages.', 'affiliatecms'),
        'section'     => 'acms_layout',
        'type'        => 'select',
        'choices'     => [
            'grid'      => __('Grid (3 columns)', 'affiliatecms'),
            'grid-2col' => __('Grid (2 columns)', 'affiliatecms'),
            'list'      => __('List View', 'affiliatecms'),
        ],
    ]);

    // Posts Per Page (Archive)
    $wp_customize->add_setting('acms_posts_per_page', [
        'default'           => 12,
        'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_control('acms_posts_per_page', [
        'label'       => __('Posts Per Page', 'affiliatecms'),
        'description' => __('Number of posts on archive pages.', 'affiliatecms'),
        'section'     => 'acms_layout',
        'type'        => 'number',
        'input_attrs' => [
            'min'  => 6,
            'max'  => 24,
            'step' => 3,
        ],
    ]);

    // Card Style
    $wp_customize->add_setting('acms_card_style', [
        'default'           => 'shadow',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('acms_card_style', [
        'label'   => __('Card Style', 'affiliatecms'),
        'section' => 'acms_layout',
        'type'    => 'select',
        'choices' => [
            'shadow'  => __('Shadow (Elevated)', 'affiliatecms'),
            'border'  => __('Border (Flat)', 'affiliatecms'),
            'minimal' => __('Minimal (No border)', 'affiliatecms'),
        ],
    ]);

    // Card Hover Effect
    $wp_customize->add_setting('acms_card_hover', [
        'default'           => 'lift',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('acms_card_hover', [
        'label'   => __('Card Hover Effect', 'affiliatecms'),
        'section' => 'acms_layout',
        'type'    => 'select',
        'choices' => [
            'lift'   => __('Lift Up', 'affiliatecms'),
            'shadow' => __('Shadow Increase', 'affiliatecms'),
            'glow'   => __('Glow Border', 'affiliatecms'),
            'none'   => __('None', 'affiliatecms'),
        ],
    ]);

    // Container Width
    $wp_customize->add_setting('acms_container_width', [
        'default'           => '1280',
        'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_control('acms_container_width', [
        'label'       => __('Container Max Width (px)', 'affiliatecms'),
        'description' => __('Maximum width of the content area.', 'affiliatecms'),
        'section'     => 'acms_layout',
        'type'        => 'number',
        'input_attrs' => [
            'min'  => 1000,
            'max'  => 1600,
            'step' => 40,
        ],
    ]);

    // Border Radius
    $wp_customize->add_setting('acms_border_radius', [
        'default'           => 'medium',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('acms_border_radius', [
        'label'   => __('Border Radius', 'affiliatecms'),
        'section' => 'acms_layout',
        'type'    => 'select',
        'choices' => [
            'none'   => __('None (Square)', 'affiliatecms'),
            'small'  => __('Small (4px)', 'affiliatecms'),
            'medium' => __('Medium (8px)', 'affiliatecms'),
            'large'  => __('Large (12px)', 'affiliatecms'),
            'xl'     => __('Extra Large (16px)', 'affiliatecms'),
        ],
    ]);

    // ========================================
    // HEADER SECTION
    // ========================================
    $wp_customize->add_section('acms_header', [
        'title'    => __('Header Settings', 'affiliatecms'),
        'priority' => 30,
    ]);

    // CTA button text
    $wp_customize->add_setting('acms_header_cta_text', [
        'default'           => __("Today's Deals", 'affiliatecms'),
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('acms_header_cta_text', [
        'label'   => __('Header CTA Text', 'affiliatecms'),
        'section' => 'acms_header',
        'type'    => 'text',
    ]);

    // CTA button URL
    $wp_customize->add_setting('acms_header_cta_url', [
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
    ]);

    $wp_customize->add_control('acms_header_cta_url', [
        'label'   => __('Header CTA URL', 'affiliatecms'),
        'section' => 'acms_header',
        'type'    => 'url',
    ]);

    // ========================================
    // FOOTER SECTION
    // ========================================
    $wp_customize->add_section('acms_footer', [
        'title'    => __('Footer Settings', 'affiliatecms'),
        'priority' => 50,
    ]);

    // Footer logo (separate from header logo)
    $wp_customize->add_setting('acms_footer_logo', [
        'default'           => '',
        'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'acms_footer_logo', [
        'label'       => __('Footer Logo', 'affiliatecms'),
        'description' => __('Leave empty to use site name as text logo.', 'affiliatecms'),
        'section'     => 'acms_footer',
        'mime_type'   => 'image',
    ]));

    // Footer logo icon (Bootstrap icon class)
    $wp_customize->add_setting('acms_footer_logo_icon', [
        'default'           => 'bi-bag-check',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('acms_footer_logo_icon', [
        'label'       => __('Footer Logo Icon', 'affiliatecms'),
        'description' => __('Bootstrap icon class (e.g., bi-bag-check). Used when no logo image.', 'affiliatecms'),
        'section'     => 'acms_footer',
        'type'        => 'text',
    ]);

    // Footer description
    $wp_customize->add_setting('acms_footer_description', [
        'default'           => __('Your trusted destination for smart shopping insights and top-tier product recommendations.', 'affiliatecms'),
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);

    $wp_customize->add_control('acms_footer_description', [
        'label'   => __('Footer Description', 'affiliatecms'),
        'section' => 'acms_footer',
        'type'    => 'textarea',
    ]);

    // Affiliate disclosure
    $wp_customize->add_setting('acms_footer_disclosure', [
        'default'           => __('As Amazon Associates, we earn from qualifying purchases. This means we may receive a small commission at no extra cost to you if you click through and make a purchase.', 'affiliatecms'),
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);

    $wp_customize->add_control('acms_footer_disclosure', [
        'label'   => __('Affiliate Disclosure', 'affiliatecms'),
        'section' => 'acms_footer',
        'type'    => 'textarea',
    ]);

    // Disclosure link text
    $wp_customize->add_setting('acms_footer_disclosure_link_text', [
        'default'           => __('Learn more', 'affiliatecms'),
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('acms_footer_disclosure_link_text', [
        'label'   => __('Disclosure Link Text', 'affiliatecms'),
        'section' => 'acms_footer',
        'type'    => 'text',
    ]);

    // Disclosure link URL
    $wp_customize->add_setting('acms_footer_disclosure_link_url', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);

    $wp_customize->add_control('acms_footer_disclosure_link_url', [
        'label'       => __('Disclosure Link URL', 'affiliatecms'),
        'description' => __('Link to full affiliate disclosure page.', 'affiliatecms'),
        'section'     => 'acms_footer',
        'type'        => 'url',
    ]);

    // Copyright text
    $wp_customize->add_setting('acms_footer_copyright', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('acms_footer_copyright', [
        'label'       => __('Copyright Text', 'affiliatecms'),
        'description' => __('Leave empty for default. Use {year} for current year.', 'affiliatecms'),
        'section'     => 'acms_footer',
        'type'        => 'text',
    ]);

    // Contact Email (for Schema.org)
    $wp_customize->add_setting('acms_contact_email', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_email',
    ]);

    $wp_customize->add_control('acms_contact_email', [
        'label'       => __('Contact Email', 'affiliatecms'),
        'description' => __('Used in Schema.org markup for SEO. Optional.', 'affiliatecms'),
        'section'     => 'acms_footer',
        'type'        => 'email',
    ]);

    // ========================================
    // SOCIAL LINKS SECTION
    // ========================================
    $wp_customize->add_section('acms_social', [
        'title'    => __('Social Links', 'affiliatecms'),
        'priority' => 55,
    ]);

    $social_networks = [
        'facebook'  => 'Facebook',
        'twitter'   => 'Twitter/X',
        'instagram' => 'Instagram',
        'youtube'   => 'YouTube',
        'pinterest' => 'Pinterest',
        'linkedin'  => 'LinkedIn',
        'tiktok'    => 'TikTok',
    ];

    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting('acms_social_' . $network, [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control('acms_social_' . $network, [
            'label'   => $label,
            'section' => 'acms_social',
            'type'    => 'url',
        ]);
    }

    // Custom Social Links (textarea format)
    $wp_customize->add_setting('acms_social_custom', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);

    $wp_customize->add_control('acms_social_custom', [
        'label'       => __('Custom Social Links', 'affiliatecms'),
        'description' => __('One link per line. Format: url|icon|label<br>Example:<br>https://discord.gg/xxx|bi-discord|Discord<br>https://t.me/xxx|bi-telegram|Telegram<br><br>Icons: bi-discord, bi-telegram, bi-whatsapp, bi-reddit, bi-twitch, bi-globe, bi-link-45deg<br>Full list: icons.getbootstrap.com', 'affiliatecms'),
        'section'     => 'acms_social',
        'type'        => 'textarea',
    ]);

    // ========================================
    // LATEST REVIEWS SECTION (Homepage)
    // ========================================
    // Note: Hero and Categories sections were removed (unused templates)
    $wp_customize->add_section('acms_latest_reviews', [
        'title'    => __('Latest Reviews Section', 'affiliatecms'),
        'priority' => 41,
    ]);

    // Section icon
    $wp_customize->add_setting('acms_latest_icon', [
        'default'           => 'bi-lightning-fill',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('acms_latest_icon', [
        'label'       => __('Section Icon', 'affiliatecms'),
        'description' => __('Bootstrap icon class (e.g., bi-lightning-fill)', 'affiliatecms'),
        'section'     => 'acms_latest_reviews',
        'type'        => 'text',
    ]);

    // Section title
    $wp_customize->add_setting('acms_latest_title', [
        'default'           => __('Latest Reviews', 'affiliatecms'),
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('acms_latest_title', [
        'label'   => __('Section Title', 'affiliatecms'),
        'section' => 'acms_latest_reviews',
        'type'    => 'text',
    ]);

    // Section subtitle
    $wp_customize->add_setting('acms_latest_subtitle', [
        'default'           => __('Expert opinions on the newest products', 'affiliatecms'),
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('acms_latest_subtitle', [
        'label'   => __('Section Subtitle', 'affiliatecms'),
        'section' => 'acms_latest_reviews',
        'type'    => 'text',
    ]);

    // View All text
    $wp_customize->add_setting('acms_latest_viewall_text', [
        'default'           => __('View All', 'affiliatecms'),
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('acms_latest_viewall_text', [
        'label'   => __('View All Link Text', 'affiliatecms'),
        'section' => 'acms_latest_reviews',
        'type'    => 'text',
    ]);

    // View All URL
    $wp_customize->add_setting('acms_latest_viewall_url', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);

    $wp_customize->add_control('acms_latest_viewall_url', [
        'label'       => __('View All URL', 'affiliatecms'),
        'description' => __('Leave empty to link to blog page', 'affiliatecms'),
        'section'     => 'acms_latest_reviews',
        'type'        => 'url',
    ]);

    // Number of posts
    $wp_customize->add_setting('acms_latest_count', [
        'default'           => 6,
        'sanitize_callback' => 'absint',
    ]);

    $wp_customize->add_control('acms_latest_count', [
        'label'   => __('Number of Posts', 'affiliatecms'),
        'section' => 'acms_latest_reviews',
        'type'    => 'number',
        'input_attrs' => [
            'min' => 3,
            'max' => 12,
            'step' => 1,
        ],
    ]);

    // Enable Load More
    $wp_customize->add_setting('acms_latest_load_more', [
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ]);

    $wp_customize->add_control('acms_latest_load_more', [
        'label'       => __('Enable Load More', 'affiliatecms'),
        'description' => __('Show Load More button to load additional posts', 'affiliatecms'),
        'section'     => 'acms_latest_reviews',
        'type'        => 'checkbox',
    ]);

    // Card Layout Style
    $wp_customize->add_setting('acms_latest_layout', [
        'default'           => 'grid',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('acms_latest_layout', [
        'label'       => __('Card Layout', 'affiliatecms'),
        'description' => __('Choose how posts are displayed', 'affiliatecms'),
        'section'     => 'acms_latest_reviews',
        'type'        => 'select',
        'choices'     => [
            'grid' => __('Grid Cards (3 columns)', 'affiliatecms'),
            'list' => __('List Cards (horizontal)', 'affiliatecms'),
        ],
    ]);

    // Post Types to Display
    $wp_customize->add_setting('acms_latest_post_types', [
        'default'           => 'all',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('acms_latest_post_types', [
        'label'       => __('Content Source', 'affiliatecms'),
        'description' => __('Which content types to display', 'affiliatecms'),
        'section'     => 'acms_latest_reviews',
        'type'        => 'select',
        'choices'     => [
            'post'         => __('Posts only', 'affiliatecms'),
            'acms_reviews' => __('Reviews only (CPT)', 'affiliatecms'),
            'both'         => __('Posts + Reviews (mixed)', 'affiliatecms'),
            'all'          => __('All content types', 'affiliatecms'),
        ],
    ]);

}
add_action('customize_register', 'acms_customize_register');

/**
 * Get social links
 *
 * @return array Array of social links
 */
function acms_get_social_links() {
    $networks = ['facebook', 'twitter', 'instagram', 'youtube', 'pinterest', 'linkedin', 'tiktok'];
    $links = [];

    $icons = [
        'facebook'  => 'bi-facebook',
        'twitter'   => 'bi-twitter-x',
        'instagram' => 'bi-instagram',
        'youtube'   => 'bi-youtube',
        'pinterest' => 'bi-pinterest',
        'linkedin'  => 'bi-linkedin',
        'tiktok'    => 'bi-tiktok',
    ];

    // Preset social networks
    foreach ($networks as $network) {
        $url = get_theme_mod('acms_social_' . $network, '');
        if ($url) {
            $links[$network] = [
                'url'   => $url,
                'icon'  => $icons[$network],
                'label' => ucfirst($network),
            ];
        }
    }

    // Custom social links (parsed from textarea)
    $custom_links = get_theme_mod('acms_social_custom', '');
    if ($custom_links) {
        $lines = explode("\n", $custom_links);
        $i = 1;
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = explode('|', $line);
            if (count($parts) >= 2) {
                $url   = esc_url(trim($parts[0]));
                $icon  = sanitize_text_field(trim($parts[1]));
                $label = isset($parts[2]) ? sanitize_text_field(trim($parts[2])) : __('Social Link', 'affiliatecms');

                if ($url && $icon) {
                    $links['custom_' . $i] = [
                        'url'   => $url,
                        'icon'  => $icon,
                        'label' => $label,
                    ];
                    $i++;
                }
            }
        }
    }

    return $links;
}

/**
 * Get copyright text
 *
 * @return string Copyright text
 */
function acms_get_copyright() {
    $text = get_theme_mod('acms_footer_copyright', '');

    if (empty($text)) {
        $text = sprintf(
            __('&copy; %s %s. All rights reserved.', 'affiliatecms'),
            date('Y'),
            get_bloginfo('name')
        );
    } else {
        $text = str_replace('{year}', date('Y'), $text);
    }

    return $text;
}

/**
 * Output custom CSS variables from Customizer settings
 */
function acms_customizer_css() {
    // Color settings (defaults match tokens.css)
    $colors = [
        'primary'        => ['value' => get_theme_mod('acms_color_primary', '#0D7377'), 'default' => '#0D7377'],
        'primary_hover'  => ['value' => get_theme_mod('acms_color_primary_hover', '#0A5C5F'), 'default' => '#0A5C5F'],
        'accent'         => ['value' => get_theme_mod('acms_color_accent', '#E07A5F'), 'default' => '#E07A5F'],
        'surface'        => ['value' => get_theme_mod('acms_color_surface', '#FFFFFF'), 'default' => '#FFFFFF'],
        'background'     => ['value' => get_theme_mod('acms_color_background', '#FDFBF7'), 'default' => '#FDFBF7'],
        'bg_alt'         => ['value' => get_theme_mod('acms_color_bg_alt', '#F7F5F0'), 'default' => '#F7F5F0'],
        'text'           => ['value' => get_theme_mod('acms_color_text', '#1A1D21'), 'default' => '#1A1D21'],
        'text_secondary' => ['value' => get_theme_mod('acms_color_text_secondary', '#4A5056'), 'default' => '#4A5056'],
        'text_muted'     => ['value' => get_theme_mod('acms_color_text_muted', '#6B7280'), 'default' => '#6B7280'],
        'border'         => ['value' => get_theme_mod('acms_color_border', '#E5E2DC'), 'default' => '#E5E2DC'],
    ];

    // Layout settings
    $container_width = get_theme_mod('acms_container_width', '1280');
    $border_radius   = get_theme_mod('acms_border_radius', 'medium');
    $card_style      = get_theme_mod('acms_card_style', 'shadow');
    $card_hover      = get_theme_mod('acms_card_hover', 'lift');

    // Border radius values
    $radius_map = [
        'none'   => '0',
        'small'  => '4px',
        'medium' => '8px',
        'large'  => '12px',
        'xl'     => '16px',
    ];
    $radius_value = isset($radius_map[$border_radius]) ? $radius_map[$border_radius] : '8px';

    // Check if any colors are different from defaults
    $has_custom = false;
    foreach ($colors as $color) {
        if (strtoupper($color['value']) !== strtoupper($color['default'])) {
            $has_custom = true;
            break;
        }
    }
    $has_custom = $has_custom || $container_width !== '1280' || $border_radius !== 'medium';

    // Only output if there are custom settings
    if (!$has_custom) {
        return;
    }

    $css = ':root {';

    // Primary color and variants
    if (strtoupper($colors['primary']['value']) !== strtoupper($colors['primary']['default'])) {
        $primary = $colors['primary']['value'];
        $css .= '--color-primary: ' . esc_attr($primary) . ';';
        $css .= '--color-primary-rgb: ' . acms_hex_to_rgb($primary) . ';';
        $css .= '--color-primary-dark: ' . acms_adjust_brightness($primary, -20) . ';';
        $css .= '--color-primary-light: ' . acms_adjust_brightness($primary, 85, 0.15) . ';';
        $css .= '--shadow-primary: 0 4px 14px rgba(' . acms_hex_to_rgb($primary) . ', 0.2);';
    }

    // Primary hover
    if (strtoupper($colors['primary_hover']['value']) !== strtoupper($colors['primary_hover']['default'])) {
        $css .= '--color-primary-hover: ' . esc_attr($colors['primary_hover']['value']) . ';';
    }

    // Accent color and variants
    if (strtoupper($colors['accent']['value']) !== strtoupper($colors['accent']['default'])) {
        $accent = $colors['accent']['value'];
        $css .= '--color-accent: ' . esc_attr($accent) . ';';
        $css .= '--color-accent-rgb: ' . acms_hex_to_rgb($accent) . ';';
        $css .= '--color-accent-hover: ' . acms_adjust_brightness($accent, -15) . ';';
        $css .= '--color-accent-light: ' . acms_adjust_brightness($accent, 85, 0.12) . ';';
        $css .= '--shadow-accent: 0 4px 14px rgba(' . acms_hex_to_rgb($accent) . ', 0.2);';
    }

    // Surface colors
    if (strtoupper($colors['surface']['value']) !== strtoupper($colors['surface']['default'])) {
        $surface = $colors['surface']['value'];
        $css .= '--color-surface: ' . esc_attr($surface) . ';';
        $css .= '--color-surface-raised: ' . esc_attr($surface) . ';';
        $css .= '--color-surface-hover: ' . acms_adjust_brightness($surface, -3) . ';';
    }

    // Background colors
    if (strtoupper($colors['background']['value']) !== strtoupper($colors['background']['default'])) {
        $css .= '--color-bg: ' . esc_attr($colors['background']['value']) . ';';
    }
    if (strtoupper($colors['bg_alt']['value']) !== strtoupper($colors['bg_alt']['default'])) {
        $css .= '--color-bg-alt: ' . esc_attr($colors['bg_alt']['value']) . ';';
    }

    // Text colors
    if (strtoupper($colors['text']['value']) !== strtoupper($colors['text']['default'])) {
        $css .= '--color-text: ' . esc_attr($colors['text']['value']) . ';';
    }
    if (strtoupper($colors['text_secondary']['value']) !== strtoupper($colors['text_secondary']['default'])) {
        $css .= '--color-text-secondary: ' . esc_attr($colors['text_secondary']['value']) . ';';
    }
    if (strtoupper($colors['text_muted']['value']) !== strtoupper($colors['text_muted']['default'])) {
        $css .= '--color-text-muted: ' . esc_attr($colors['text_muted']['value']) . ';';
    }

    // Border colors
    if (strtoupper($colors['border']['value']) !== strtoupper($colors['border']['default'])) {
        $border = $colors['border']['value'];
        $css .= '--color-border: ' . esc_attr($border) . ';';
        $css .= '--color-border-subtle: ' . acms_adjust_brightness($border, 5) . ';';
        $css .= '--color-border-strong: ' . acms_adjust_brightness($border, -10) . ';';
    }

    // Layout
    if ($container_width !== '1280') {
        $css .= '--container-max: ' . esc_attr($container_width) . 'px;';
    }
    if ($border_radius !== 'medium') {
        $css .= '--radius-md: ' . esc_attr($radius_value) . ';';
    }

    $css .= '}';

    // Card styles
    if ($card_style !== 'shadow') {
        if ($card_style === 'border') {
            $css .= '.post-card { box-shadow: none; border: 1px solid var(--color-border); }';
        } elseif ($card_style === 'minimal') {
            $css .= '.post-card { box-shadow: none; border: none; }';
        }
    }

    // Card hover effects
    if ($card_hover !== 'lift') {
        if ($card_hover === 'shadow') {
            $css .= '.post-card:hover { transform: none; box-shadow: var(--shadow-lg); }';
        } elseif ($card_hover === 'glow') {
            $css .= '.post-card:hover { transform: none; box-shadow: 0 0 0 3px var(--color-primary); }';
        } elseif ($card_hover === 'none') {
            $css .= '.post-card:hover { transform: none; box-shadow: var(--shadow-sm); }';
        }
    }

    // Generate dark mode CSS when custom colors are used
    // This ensures dark mode works with all presets, not just Teal
    if ($has_custom) {
        $primary = $colors['primary']['value'];
        $accent = $colors['accent']['value'];

        // Dark mode colors - generated from light mode colors
        $css .= '[data-theme="dark"] {';

        // Primary - lighter/brighter for dark backgrounds
        $primary_dark = acms_adjust_brightness($primary, 35);
        $css .= '--color-primary: ' . esc_attr($primary_dark) . ';';
        $css .= '--color-primary-rgb: ' . acms_hex_to_rgb($primary_dark) . ';';
        $css .= '--color-primary-hover: ' . acms_adjust_brightness($primary, 20) . ';';
        $css .= '--color-primary-dark: ' . esc_attr($primary) . ';';
        $css .= '--color-primary-light: rgba(' . acms_hex_to_rgb($primary_dark) . ', 0.15);';
        $css .= '--shadow-primary: 0 4px 14px rgba(' . acms_hex_to_rgb($primary_dark) . ', 0.25);';

        // Accent - slightly lighter for dark mode
        $accent_dark = acms_adjust_brightness($accent, 25);
        $css .= '--color-accent: ' . esc_attr($accent_dark) . ';';
        $css .= '--color-accent-rgb: ' . acms_hex_to_rgb($accent_dark) . ';';
        $css .= '--color-accent-hover: ' . esc_attr($accent) . ';';
        $css .= '--color-accent-light: rgba(' . acms_hex_to_rgb($accent_dark) . ', 0.15);';
        $css .= '--shadow-accent: 0 4px 14px rgba(' . acms_hex_to_rgb($accent_dark) . ', 0.25);';

        // Dark surfaces and backgrounds
        $css .= '--color-bg: #0C0E12;';
        $css .= '--color-bg-alt: #12151A;';
        $css .= '--color-surface: #1A1D24;';
        $css .= '--color-surface-raised: #22262F;';
        $css .= '--color-surface-hover: #252A34;';

        // Light text for dark backgrounds
        $css .= '--color-text: #F5F5F5;';
        $css .= '--color-text-secondary: #D4D4D8;';
        $css .= '--color-text-muted: #A1A1AA;';
        $css .= '--color-text-subtle: #71717A;';

        // Dark borders
        $css .= '--color-border: #2D323C;';
        $css .= '--color-border-subtle: #23272F;';
        $css .= '--color-border-strong: #3D4450;';

        // Overlay
        $css .= '--color-overlay: rgba(0, 0, 0, 0.6);';

        $css .= '}';
    }

    echo '<style id="acms-customizer-css">' . $css . '</style>';
}
add_action('wp_head', 'acms_customizer_css', 100);

/**
 * Convert hex color to RGB values
 *
 * @param string $hex Hex color code
 * @return string RGB values (e.g., "20, 184, 166")
 */
function acms_hex_to_rgb($hex) {
    $hex = ltrim($hex, '#');

    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }

    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    return "{$r}, {$g}, {$b}";
}

/**
 * Adjust color brightness/lightness
 *
 * @param string $hex Hex color code
 * @param int $percent Percentage to adjust (-100 to 100, negative = darker)
 * @param float $blend_white Optional: blend with white for light variants (0-1)
 * @return string Adjusted hex color
 */
function acms_adjust_brightness($hex, $percent, $blend_white = 0) {
    $hex = ltrim($hex, '#');

    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }

    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    // Blend with white for light variants
    if ($blend_white > 0) {
        $r = round($r + (255 - $r) * $blend_white);
        $g = round($g + (255 - $g) * $blend_white);
        $b = round($b + (255 - $b) * $blend_white);
    }

    // Adjust brightness
    $r = max(0, min(255, $r + round($r * $percent / 100)));
    $g = max(0, min(255, $g + round($g * $percent / 100)));
    $b = max(0, min(255, $b + round($b * $percent / 100)));

    return sprintf('#%02x%02x%02x', $r, $g, $b);
}

/**
 * Enqueue Customizer live preview script
 */
function acms_customizer_preview_js() {
    wp_enqueue_script(
        'acms-customizer-preview',
        ACMS_URI . '/assets/js/customizer-preview.js',
        ['customize-preview'],
        ACMS_VERSION,
        true
    );
}
add_action('customize_preview_init', 'acms_customizer_preview_js');

/**
 * Enqueue Customizer controls script (for presets)
 */
function acms_customizer_controls_js() {
    wp_enqueue_script(
        'acms-customizer-controls',
        ACMS_URI . '/assets/js/customizer-controls.js',
        ['customize-controls', 'jquery'],
        ACMS_VERSION,
        true
    );
}
add_action('customize_controls_enqueue_scripts', 'acms_customizer_controls_js');

/**
 * Get color preset values
 *
 * @param string $preset Preset name
 * @return array Color values
 */
function acms_get_color_preset($preset) {
    $presets = [
        'teal' => [
            'primary'        => '#0D7377',
            'primary_hover'  => '#0A5C5F',
            'accent'         => '#E07A5F',
            'surface'        => '#FFFFFF',
            'background'     => '#FDFBF7',
            'bg_alt'         => '#F7F5F0',
            'text'           => '#1A1D21',
            'text_secondary' => '#4A5056',
            'text_muted'     => '#6B7280',
            'border'         => '#E5E2DC',
        ],
        'blue' => [
            'primary'        => '#3B82F6',
            'primary_hover'  => '#2563EB',
            'accent'         => '#F59E0B',
            'surface'        => '#FFFFFF',
            'background'     => '#F8FAFC',
            'bg_alt'         => '#F1F5F9',
            'text'           => '#0F172A',
            'text_secondary' => '#334155',
            'text_muted'     => '#64748B',
            'border'         => '#E2E8F0',
        ],
        'purple' => [
            'primary'        => '#8B5CF6',
            'primary_hover'  => '#7C3AED',
            'accent'         => '#EC4899',
            'surface'        => '#FFFFFF',
            'background'     => '#FAF5FF',
            'bg_alt'         => '#F3E8FF',
            'text'           => '#1E1B4B',
            'text_secondary' => '#4C1D95',
            'text_muted'     => '#7C3AED',
            'border'         => '#E9D5FF',
        ],
        'green' => [
            'primary'        => '#22C55E',
            'primary_hover'  => '#16A34A',
            'accent'         => '#F97316',
            'surface'        => '#FFFFFF',
            'background'     => '#F0FDF4',
            'bg_alt'         => '#DCFCE7',
            'text'           => '#14532D',
            'text_secondary' => '#166534',
            'text_muted'     => '#4ADE80',
            'border'         => '#BBF7D0',
        ],
        'red' => [
            'primary'        => '#EF4444',
            'primary_hover'  => '#DC2626',
            'accent'         => '#F59E0B',
            'surface'        => '#FFFFFF',
            'background'     => '#FEF2F2',
            'bg_alt'         => '#FEE2E2',
            'text'           => '#450A0A',
            'text_secondary' => '#7F1D1D',
            'text_muted'     => '#B91C1C',
            'border'         => '#FECACA',
        ],
        'orange' => [
            'primary'        => '#F97316',
            'primary_hover'  => '#EA580C',
            'accent'         => '#14B8A6',
            'surface'        => '#FFFFFF',
            'background'     => '#FFF7ED',
            'bg_alt'         => '#FFEDD5',
            'text'           => '#431407',
            'text_secondary' => '#7C2D12',
            'text_muted'     => '#C2410C',
            'border'         => '#FED7AA',
        ],
        'pink' => [
            'primary'        => '#EC4899',
            'primary_hover'  => '#DB2777',
            'accent'         => '#8B5CF6',
            'surface'        => '#FFFFFF',
            'background'     => '#FDF2F8',
            'bg_alt'         => '#FCE7F3',
            'text'           => '#500724',
            'text_secondary' => '#831843',
            'text_muted'     => '#BE185D',
            'border'         => '#FBCFE8',
        ],
        'dark' => [
            'primary'        => '#3B82F6',
            'primary_hover'  => '#60A5FA',
            'accent'         => '#F59E0B',
            'surface'        => '#1E293B',
            'background'     => '#0F172A',
            'bg_alt'         => '#1E293B',
            'text'           => '#F1F5F9',
            'text_secondary' => '#CBD5E1',
            'text_muted'     => '#94A3B8',
            'border'         => '#334155',
        ],
    ];

    return isset($presets[$preset]) ? $presets[$preset] : $presets['teal'];
}
