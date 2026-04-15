<?php
/**
 * Template Tags - Display functions for templates
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display post meta (author, date, reading time)
 *
 * @param array $args Arguments
 */
function acms_post_meta($args = []) {
    $defaults = [
        'show_author'  => true,
        'show_date'    => true,
        'show_reading' => true,
        'show_views'   => false,
        'class'        => 'post-meta',
    ];

    $args = wp_parse_args($args, $defaults);

    echo '<div class="' . esc_attr($args['class']) . '">';

    if ($args['show_author']) {
        printf(
            '<span class="post-meta__author"><i class="bi bi-person-fill"></i> %s</span>',
            get_the_author()
        );
    }

    if ($args['show_date']) {
        printf(
            '<span class="post-meta__date"><i class="bi bi-clock-history"></i> %s</span>',
            get_the_date()
        );
    }

    if ($args['show_reading']) {
        acms_reading_time();
    }

    if ($args['show_views']) {
        acms_display_views();
    }

    echo '</div>';
}

/**
 * Display post card
 *
 * @param array $args Card arguments
 */
function acms_post_card($args = []) {
    $defaults = [
        'variant'       => '',
        'class'         => '',
        'show_excerpt'  => true,
        'show_footer'   => true,
        'show_rating'   => false,
        'show_views'    => false,
        'footer_cols'   => '3col',
        'excerpt_words' => 12,
    ];

    $args = wp_parse_args($args, $defaults);

    // Use unified template part with args
    get_template_part('template-parts/content/post-card', null, $args);
}

/**
 * Display author box
 *
 * @param int|null $author_id Author ID
 */
function acms_author_box($author_id = null) {
    if (!$author_id) {
        $author_id = get_the_author_meta('ID');
    }

    get_template_part('template-parts/single/author-box', null, [
        'author_id' => $author_id,
    ]);
}

/**
 * Display related posts
 *
 * @param int $count Number of posts
 */
function acms_related_posts($count = 3) {
    get_template_part('template-parts/single/related-posts', null, [
        'count' => $count,
    ]);
}

/**
 * Display post navigation (prev/next)
 */
function acms_post_navigation() {
    $prev_post = get_previous_post();
    $next_post = get_next_post();

    if (!$prev_post && !$next_post) {
        return;
    }

    echo '<nav class="post-navigation" aria-label="' . esc_attr__('Post Navigation', 'affiliatecms') . '">';
    echo '<div class="post-navigation__inner">';

    if ($prev_post) {
        printf(
            '<a href="%s" class="post-navigation__link post-navigation__link--prev">
                <span class="post-navigation__label"><i class="bi bi-arrow-left"></i> %s</span>
                <span class="post-navigation__title">%s</span>
            </a>',
            esc_url(get_permalink($prev_post)),
            esc_html__('Previous', 'affiliatecms'),
            esc_html(get_the_title($prev_post))
        );
    }

    if ($next_post) {
        printf(
            '<a href="%s" class="post-navigation__link post-navigation__link--next">
                <span class="post-navigation__label">%s <i class="bi bi-arrow-right"></i></span>
                <span class="post-navigation__title">%s</span>
            </a>',
            esc_url(get_permalink($next_post)),
            esc_html__('Next', 'affiliatecms'),
            esc_html(get_the_title($next_post))
        );
    }

    echo '</div>';
    echo '</nav>';
}

// Note: acms_pagination() is defined in template-functions.php

/**
 * Display share buttons
 */
function acms_share_buttons() {
    $url = urlencode(get_permalink());
    $title = urlencode(get_the_title());

    $networks = [
        'facebook' => [
            'url'   => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
            'icon'  => 'bi-facebook',
            'label' => __('Share on Facebook', 'affiliatecms'),
        ],
        'twitter' => [
            'url'   => 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title,
            'icon'  => 'bi-twitter-x',
            'label' => __('Share on Twitter', 'affiliatecms'),
        ],
        'linkedin' => [
            'url'   => 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '&title=' . $title,
            'icon'  => 'bi-linkedin',
            'label' => __('Share on LinkedIn', 'affiliatecms'),
        ],
        'pinterest' => [
            'url'   => 'https://pinterest.com/pin/create/button/?url=' . $url . '&description=' . $title,
            'icon'  => 'bi-pinterest',
            'label' => __('Share on Pinterest', 'affiliatecms'),
        ],
    ];

    echo '<div class="share-buttons">';

    foreach ($networks as $network => $data) {
        printf(
            '<a href="%s" class="share-button share-button--%s" target="_blank" rel="nofollow" aria-label="%s">
                <i class="bi %s"></i>
            </a>',
            esc_url($data['url']),
            esc_attr($network),
            esc_attr($data['label']),
            esc_attr($data['icon'])
        );
    }

    // Copy link button
    printf(
        '<button type="button" class="share-button share-button--copy" data-url="%s" aria-label="%s">
            <i class="bi bi-link-45deg"></i>
        </button>',
        esc_url(get_permalink()),
        esc_attr__('Copy link', 'affiliatecms')
    );

    echo '</div>';
}

/**
 * Display site logo
 * On homepage: wrap with <h1> for SEO (Bing requires H1)
 * On other pages: use <div> so H1 is reserved for content title
 */
function acms_site_logo() {
    $tag = (is_front_page() || is_home()) ? 'h1' : 'div';

    if (has_custom_logo()) {
        printf('<%s class="site-logo-wrap">', $tag);
        the_custom_logo();
        printf('</%s>', $tag);
    } else {
        printf(
            '<%1$s class="site-logo-wrap">
            <a href="%2$s" class="site-logo" aria-label="%3$s">
                <svg class="site-logo__icon" viewBox="0 0 40 40" width="40" height="40">
                    <rect width="40" height="40" rx="8" fill="var(--color-primary)"/>
                    <text x="50%%" y="55%%" dominant-baseline="middle" text-anchor="middle" fill="white" font-weight="700" font-size="16">AZ</text>
                </svg>
                <span class="site-logo__text">%4$s</span>
            </a>
            </%1$s>',
            $tag,
            esc_url(home_url('/')),
            esc_attr(get_bloginfo('name')),
            esc_html(get_bloginfo('name'))
        );
    }
}
