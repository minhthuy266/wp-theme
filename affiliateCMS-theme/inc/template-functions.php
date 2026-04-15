<?php
/**
 * Template Functions - Helper functions for templates
 *
 * @package AffiliateCMS
 * @since 4.0.0
*/

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get reading time for a post
 *
 * @param int|null $post_id Post ID (optional, defaults to current post)
 * @return int Reading time in minutes
 */
function acms_get_reading_time($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // 200 words per minute

    return max(1, $reading_time);
}

/**
 * Display reading time
 *
 * @param int|null $post_id Post ID
 */
function acms_reading_time($post_id = null) {
    $time = acms_get_reading_time($post_id);
    printf(
        '<span class="reading-time"><i class="bi bi-clock"></i> %d %s</span>',
        $time,
        _n('min read', 'min read', $time, 'affiliatecms')
    );
}

/**
 * Get post views count
 *
 * @param int|null $post_id Post ID
 * @return int View count
 */
function acms_get_views($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $views = get_post_meta($post_id, '_acms_views', true);
    return $views ? intval($views) : 0;
}

/**
 * Increment post views
 *
 * @param int|null $post_id Post ID
 */
function acms_increment_views($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    // Don't count admin views
    if (is_user_logged_in() && current_user_can('edit_posts')) {
        return;
    }

    $views = acms_get_views($post_id);
    update_post_meta($post_id, '_acms_views', $views + 1);
}

/**
 * Format number with K/M suffix
 *
 * @param int $number Number to format
 * @return string Formatted number
 */
function acms_format_number($number) {
    if ($number >= 1000000) {
        return round($number / 1000000, 1) . 'M';
    } elseif ($number >= 1000) {
        return round($number / 1000, 1) . 'K';
    }
    return $number;
}

/**
 * Display formatted views
 *
 * @param int|null $post_id Post ID
 */
function acms_display_views($post_id = null) {
    $views = acms_get_views($post_id);
    printf(
        '<span class="post-views"><i class="bi bi-eye"></i> %s %s</span>',
        acms_format_number($views),
        __('views', 'affiliatecms')
    );
}

/**
 * Get total views for all posts by an author
 *
 * @param int $author_id Author user ID
 * @return int Total views count
 */
function acms_get_author_total_views($author_id) {
    global $wpdb;

    // Use direct query for performance (sum all _acms_views for author's posts)
    $total = $wpdb->get_var($wpdb->prepare("
        SELECT SUM(CAST(pm.meta_value AS UNSIGNED))
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE p.post_author = %d
        AND p.post_type = 'post'
        AND p.post_status = 'publish'
        AND pm.meta_key = '_acms_views'
    ", $author_id));

    return $total ? intval($total) : 0;
}

/**
 * Extract all image URLs from post content using regex patterns
 *
 * Returns array of all found image URLs in order of appearance.
 * Results are cached via transient for performance.
 *
 * @param int|null $post_id Post ID
 * @return array Array of image URLs (may be empty)
 */
function acms_get_content_images($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    // Check cache first
    $cache_key = 'acms_imgs_' . $post_id;
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    $post = get_post($post_id);
    if (!$post) {
        set_transient($cache_key, [], HOUR_IN_SECONDS);
        return [];
    }

    $content = $post->post_content;
    $images = [];

    // Patterns to extract image URLs (in priority order)
    $patterns = [
        // Standard img tags with src attribute
        '/<img\s[^>]*?src\s*=\s*["\']([^"\']+)["\'][^>]*>/i',
        // WordPress image blocks
        '/<!-- wp:image[^>]*-->.*?<img[^>]+src=["\']([^"\']+)["\'][^>]*>/is',
        // Figure tags with images
        '/<figure[^>]*>.*?<img[^>]+src=["\']([^"\']+)["\'][^>]*>/is',
        // Background images in style attributes
        '/style\s*=\s*["\'][^"\']*background(?:-image)?\s*:\s*url\(["\']?([^"\'()]+)["\']?\)/i',
        // Data-src attributes (lazy loading)
        '/<img[^>]+data-src=["\']([^"\']+)["\'][^>]*>/i',
        // Srcset - get the first image
        '/<img[^>]+srcset=["\']([^\s"\']+)/i',
        // Amazon image URLs
        '/(https?:\/\/[^\s"\'<>\[\]]*?(?:m\.media-amazon|images-amazon|images-na\.ssl-images-amazon)[^\s"\'<>\[\]]*?\.(?:jpg|jpeg|png|gif|webp))/i',
        // General image URLs in content
        '/(https?:\/\/[^\s"\'<>\[\]]+\.(?:jpg|jpeg|png|gif|webp)(?:\?[^\s"\'<>\[\]]*)?)/i',
        // Shortcode image attributes
        '/\[.*?(?:image|img|src|url)\s*=\s*["\']?([^"\'>\]\s]+\.(?:jpg|jpeg|png|gif|webp)[^"\'>\]\s]*)["\']?.*?\]/i',
    ];

    // Collect all unique image URLs
    foreach ($patterns as $pattern) {
        if (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[1] as $url) {
                if (!in_array($url, $images, true)) {
                    $images[] = $url;
                }
            }
        }
    }

    // Cache: found = 12 hours, empty = 5 minutes
    $ttl = !empty($images) ? 12 * HOUR_IN_SECONDS : 5 * MINUTE_IN_SECONDS;
    set_transient($cache_key, $images, $ttl);

    return $images;
}

/**
 * Get first hosted (local) image from post content
 *
 * Looks for images hosted on wp-content/uploads/ (WordPress media library).
 *
 * @param int|null $post_id Post ID
 * @return string|false Image URL or false
 */
function acms_get_first_hosted_image($post_id = null) {
    $images = acms_get_content_images($post_id);

    foreach ($images as $url) {
        if (strpos($url, '/wp-content/uploads/') !== false) {
            return $url;
        }
    }

    return false;
}

/**
 * Get first external image from post content
 *
 * Returns the first image that is NOT from wp-content/uploads/.
 *
 * @param int|null $post_id Post ID
 * @return string|false Image URL or false
 */
function acms_get_first_external_image($post_id = null) {
    $images = acms_get_content_images($post_id);

    foreach ($images as $url) {
        if (strpos($url, '/wp-content/uploads/') === false) {
            return $url;
        }
    }

    return false;
}

/**
 * Get first image URL from post content (any type)
 *
 * Backward-compatible wrapper. Returns the first image found regardless of type.
 *
 * @param int|null $post_id Post ID
 * @return string|false Image URL or false if not found
 */
function acms_get_first_content_image($post_id = null) {
    $images = acms_get_content_images($post_id);
    return !empty($images) ? $images[0] : false;
}

/**
 * Clear first image cache when post is saved/updated
 *
 * @param int $post_id Post ID
 */
function acms_clear_first_image_cache($post_id) {
    delete_transient('acms_img_' . $post_id);
    delete_transient('acms_imgs_' . $post_id);
}
add_action('save_post', 'acms_clear_first_image_cache');
add_action('edit_post', 'acms_clear_first_image_cache');

/**
 * One-time cleanup: flush all stale image cache transients
 * Runs once, then removes itself via option flag
 */
if (!get_option('acms_img_cache_flushed_v3')) {
    add_action('init', function () {
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_acms_img_%' OR option_name LIKE '_transient_timeout_acms_img_%' OR option_name LIKE '_transient_acms_imgs_%' OR option_name LIKE '_transient_timeout_acms_imgs_%'");
        update_option('acms_img_cache_flushed_v3', '1', true);
    }, 1);
}

/**
 * Get thumbnail URL with smart fallback
 *
 * Priority order:
 * 1. Featured image (user-uploaded post thumbnail)
 * 2. Hosted image in content (wp-content/uploads/ — user uploaded to media library)
 * 3. ACMS product image (first ASIN in shortcode from database)
 * 4. External image in content (Amazon, CDN, any external URL)
 * 5. Placeholder SVG
 *
 * @param int|null $post_id Post ID
 * @param string $size Image size (only applies to featured image)
 * @return string Image URL
 */
function acms_get_thumbnail_url($post_id = null, $size = 'acms-card') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    // Priority 1: Featured image (WordPress post thumbnail)
    if (has_post_thumbnail($post_id)) {
        $thumb_url = get_the_post_thumbnail_url($post_id, $size);
        if ($thumb_url) {
            return $thumb_url;
        }
    }

    // Priority 2: Hosted image in content (wp-content/uploads/)
    $hosted_image = acms_get_first_hosted_image($post_id);
    if ($hosted_image) {
        return $hosted_image;
    }

    // Priority 3: ACMS product image (first ASIN from shortcodes)
    $product_image = acms_get_first_product_image($post_id);
    if ($product_image) {
        return $product_image;
    }

    // Priority 4: Any external image in content
    $external_image = acms_get_first_external_image($post_id);
    if ($external_image) {
        return $external_image;
    }

    // Priority 5: Placeholder
    return ACMS_URI . '/assets/images/placeholder.svg';
}

/**
 * Get first product image from ACMS shortcodes in post content
 *
 * Extracts ASINs from [acms_list], [acms_card], [acms_grid] shortcodes
 * and returns the first product's image_url from the database.
 *
 * @param int $post_id Post ID
 * @return string|null Image URL or null
 */
function acms_get_first_product_image($post_id) {
    global $wpdb;

    // Check if ACMS products table exists
    $table = $wpdb->prefix . 'acms_products';
    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) !== $table) {
        return null;
    }

    $content = get_post_field('post_content', $post_id);
    if (empty($content)) {
        return null;
    }

    // Extract ASINs from ACMS shortcodes (with or without quotes around asin value)
    if (preg_match_all('/\[acms_(?:card|grid|list|table)\s+[^\]]*asin=["\']?([^"\'>\]\s]+)["\']?/i', $content, $matches)) {
        // Collect all ASINs from all shortcodes
        $all_asins = [];
        foreach ($matches[1] as $asin_str) {
            $asins = array_map('trim', explode(',', $asin_str));
            $all_asins = array_merge($all_asins, $asins);
        }
        $all_asins = array_unique(array_filter($all_asins));

        if (!empty($all_asins)) {
            // Query first product that has an image, preserving shortcode ASIN order
            $placeholders = implode(',', array_fill(0, count($all_asins), '%s'));
            $field_placeholders = implode(',', array_fill(0, count($all_asins), '%s'));
            $image_url = $wpdb->get_var($wpdb->prepare(
                "SELECT image_url FROM {$table} WHERE asin IN ({$placeholders}) AND image_url IS NOT NULL AND image_url != '' ORDER BY FIELD(asin, {$field_placeholders}) LIMIT 1",
                ...array_merge($all_asins, $all_asins)
            ));

            if ($image_url) {
                return $image_url;
            }
        }
    }

    return null;
}

/**
 * Check if post has any thumbnail (featured or content image)
 *
 * @param int|null $post_id Post ID
 * @return bool
 */
function acms_has_thumbnail($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    if (has_post_thumbnail($post_id)) {
        return true;
    }

    $images = acms_get_content_images($post_id);
    if (!empty($images)) {
        return true;
    }

    return (bool) acms_get_first_product_image($post_id);
}

/**
 * Get category with icon (first category)
 *
 * @param int|null $post_id Post ID
 * @return object|null Category object with name, link, icon, term_id
 */
function acms_get_primary_category($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $categories = get_the_category($post_id);

    if (empty($categories)) {
        return null;
    }

    $category = $categories[0];

    // Get category icon from meta (can be set via customizer/options)
    $icon = get_term_meta($category->term_id, '_acms_icon', true);
    if (!$icon) {
        $icon = 'bi-folder'; // Default icon
    }

    // Add icon to category object
    $category->icon = $icon;

    return $category;
}

/**
 * Generate star rating HTML
 *
 * @param float $rating Rating value (0-5)
 * @param bool $show_count Whether to show rating count
 * @param int $count Rating count
 * @return string HTML output
 */
function acms_star_rating($rating = 5, $show_count = false, $count = 0) {
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5;
    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);

    $html = '<div class="post-card__stars">';

    // Full stars
    for ($i = 0; $i < $full_stars; $i++) {
        $html .= '<i class="bi bi-star-fill"></i>';
    }

    // Half star
    if ($half_star) {
        $html .= '<i class="bi bi-star-half"></i>';
    }

    // Empty stars
    for ($i = 0; $i < $empty_stars; $i++) {
        $html .= '<i class="bi bi-star"></i>';
    }

    $html .= '</div>';

    if ($show_count && $count > 0) {
        $html .= sprintf(
            '<span class="post-card__rating-count">(%s)</span>',
            acms_format_number($count)
        );
    }

    return $html;
}

/**
 * Display breadcrumb
 * Prioritizes RankMath SEO breadcrumb if available
 *
 * @param array $args Breadcrumb arguments
 */
function acms_breadcrumb($args = []) {
    $defaults = [
        'separator'   => '/',
        'home_text'   => __('Home', 'affiliatecms'),
        'class'       => 'breadcrumb',
        'show_current' => true, // Set to false on single posts to avoid duplicate with title
    ];

    $args = wp_parse_args($args, $defaults);

    // Custom post types that should use our custom breadcrumb (not RankMath)
    $custom_post_types = ['acms_reviews', 'acms_deals', 'acms_guides'];
    $is_custom_cpt = is_singular($custom_post_types) || is_post_type_archive($custom_post_types) || is_tax();

    // Use RankMath breadcrumb if available and enabled (but NOT for custom post types)
    if (function_exists('rank_math_the_breadcrumbs') && !$is_custom_cpt) {
        // Capture RankMath output to check if it's not empty
        ob_start();
        rank_math_the_breadcrumbs();
        $rankmath_breadcrumb = ob_get_clean();

        // Only use RankMath if it actually outputs content
        if (!empty(trim(strip_tags($rankmath_breadcrumb)))) {
            echo '<nav class="' . esc_attr($args['class']) . ' breadcrumb--rankmath" aria-label="' . esc_attr__('Breadcrumb', 'affiliatecms') . '">';
            echo $rankmath_breadcrumb;
            echo '</nav>';
            return;
        }
        // If RankMath outputs nothing, fall through to custom breadcrumb
    }

    // Fallback to custom breadcrumb
    $items = [];

    // Home
    $items[] = sprintf(
        '<a href="%s" class="breadcrumb__link"><i class="bi bi-house-fill"></i><span>%s</span></a>',
        esc_url(home_url('/')),
        esc_html($args['home_text'])
    );

    // Category archive
    if (is_category()) {
        $category = get_queried_object();
        $items[] = sprintf(
            '<span class="breadcrumb__current">%s</span>',
            esc_html($category->name)
        );
    }

    // Tag archive
    if (is_tag()) {
        $tag = get_queried_object();
        $items[] = sprintf(
            '<span class="breadcrumb__current">%s</span>',
            esc_html($tag->name)
        );
    }

    // Author archive
    if (is_author()) {
        $items[] = sprintf(
            '<span class="breadcrumb__current">%s</span>',
            get_the_author()
        );
    }

    // Single post
    if (is_singular('post')) {
        $categories = get_the_category();
        if (!empty($categories)) {
            // Get primary category (Yoast/RankMath compatible)
            $primary_cat = acms_get_primary_category();
            $cat = $primary_cat ?: $categories[0];

            $items[] = sprintf(
                '<a href="%s" class="breadcrumb__link">%s</a>',
                esc_url(get_category_link($cat->term_id)),
                esc_html($cat->name)
            );
        }
        // Only add current title if show_current is true
        if ($args['show_current']) {
            $items[] = sprintf(
                '<span class="breadcrumb__current">%s</span>',
                get_the_title()
            );
        }
    }

    // Custom post type single (acms_reviews, acms_deals, acms_guides)
    $custom_post_types = ['acms_reviews', 'acms_deals', 'acms_guides'];
    if (is_singular($custom_post_types)) {
        $post_type = get_post_type();
        $post_type_obj = get_post_type_object($post_type);

        // Always add post type archive link first (e.g., Reviews)
        if ($post_type_obj) {
            $items[] = sprintf(
                '<a href="%s" class="breadcrumb__link">%s</a>',
                esc_url(get_post_type_archive_link($post_type)),
                esc_html($post_type_obj->labels->name)
            );
        }

        // Get the category taxonomy for this post type (e.g., acms_reviews_category)
        $category_taxonomy = $post_type . '_category';

        // Get terms from the category taxonomy
        $terms = get_the_terms(get_the_ID(), $category_taxonomy);

        if (!empty($terms) && !is_wp_error($terms)) {
            // Get the first term (primary category)
            $term = $terms[0];

            $items[] = sprintf(
                '<a href="%s" class="breadcrumb__link">%s</a>',
                esc_url(get_term_link($term)),
                esc_html($term->name)
            );
        }

        // Only add current title if show_current is true
        if ($args['show_current']) {
            $items[] = sprintf(
                '<span class="breadcrumb__current">%s</span>',
                get_the_title()
            );
        }
    }

    // Custom post type archive (acms_reviews, acms_deals, acms_guides)
    if (is_post_type_archive($custom_post_types)) {
        $post_type_obj = get_queried_object();
        if ($post_type_obj) {
            $items[] = sprintf(
                '<span class="breadcrumb__current">%s</span>',
                esc_html($post_type_obj->label)
            );
        }
    }

    // Custom taxonomy archive (acms_reviews_category, acms_reviews_brand, etc.)
    if (is_tax()) {
        $term = get_queried_object();
        if ($term) {
            // Get the associated post type from taxonomy
            $taxonomy = get_taxonomy($term->taxonomy);
            if ($taxonomy && !empty($taxonomy->object_type)) {
                $post_type = $taxonomy->object_type[0];
                $post_type_obj = get_post_type_object($post_type);

                // Add post type archive link first
                if ($post_type_obj) {
                    $items[] = sprintf(
                        '<a href="%s" class="breadcrumb__link">%s</a>',
                        esc_url(get_post_type_archive_link($post_type)),
                        esc_html($post_type_obj->labels->name)
                    );
                }
            }

            // Add current term
            $items[] = sprintf(
                '<span class="breadcrumb__current">%s</span>',
                esc_html($term->name)
            );
        }
    }

    // Page
    if (is_page() && !is_front_page()) {
        if ($args['show_current']) {
            $items[] = sprintf(
                '<span class="breadcrumb__current">%s</span>',
                get_the_title()
            );
        }
    }

    // Search
    if (is_search()) {
        $items[] = sprintf(
            '<span class="breadcrumb__current">%s "%s"</span>',
            __('Search Results for', 'affiliatecms'),
            get_search_query()
        );
    }

    // 404
    if (is_404()) {
        $items[] = sprintf(
            '<span class="breadcrumb__current">%s</span>',
            __('Page Not Found', 'affiliatecms')
        );
    }

    // Output with Schema.org markup
    if (count($items) > 1) {
        echo '<nav class="' . esc_attr($args['class']) . '" aria-label="' . esc_attr__('Breadcrumb', 'affiliatecms') . '">';
        echo '<ol class="breadcrumb__list" itemscope itemtype="https://schema.org/BreadcrumbList">';

        foreach ($items as $index => $item) {
            $is_last = ($index === count($items) - 1);
            $position = $index + 1;

            echo '<li class="breadcrumb__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            echo $item;
            echo '<meta itemprop="position" content="' . $position . '">';
            if (!$is_last) {
                echo '<span class="breadcrumb__separator">' . $args['separator'] . '</span>';
            }
            echo '</li>';
        }

        echo '</ol>';
        echo '</nav>';
    }
}

/**
 * Get theme option
 *
 * @param string $key Option key
 * @param mixed $default Default value
 * @return mixed Option value
 */
function acms_get_option($key, $default = '') {
    return get_theme_mod('acms_' . $key, $default);
}

/**
 * Custom comment callback for styled comments (Featured Style)
 *
 * @param WP_Comment $comment Comment object
 * @param array $args Arguments
 * @param int $depth Depth
 */
function acms_comment_callback($comment, $args, $depth) {
    $tag = ($args['style'] === 'div') ? 'div' : 'li';
    $comment_class = comment_class('comment comment--featured', $comment, null, false);

    // Get author initial (first 2 characters)
    $author_name = get_comment_author($comment);
    $author_initial = strtoupper(mb_substr($author_name, 0, 2));

    // Get consistent avatar color based on email
    $avatar_color = acms_get_avatar_color($comment->comment_author_email);

    // Get rating if exists
    $rating = get_comment_meta($comment->comment_ID, '_acms_rating', true);
    $rating_int = $rating ? intval($rating) : 0;
    $sentiment = $rating_int > 0 ? acms_get_rating_sentiment($rating_int) : null;
    ?>
    <<?php echo esc_attr($tag); ?> id="comment-<?php comment_ID(); ?>" <?php echo $comment_class; ?>>
        <div class="comment__main">
            <div class="comment__top">
                <div class="comment__avatar comment__avatar--initial"
                     data-initial="<?php echo esc_attr($author_initial); ?>"
                     style="--avatar-bg: <?php echo esc_attr($avatar_color); ?>">
                    <?php echo esc_html($author_initial); ?>
                </div>
                <div class="comment__info">
                    <div class="comment__header">
                        <span class="comment__author"><?php echo esc_html($author_name); ?></span>
                        <?php if ($comment->user_id) : ?>
                        <span class="comment__verified" title="<?php esc_attr_e('Verified User', 'affiliatecms'); ?>">
                            <i class="bi bi-patch-check-fill"></i>
                        </span>
                        <?php endif; ?>
                        <time class="comment__date" datetime="<?php echo esc_attr(get_comment_date('c', $comment)); ?>">
                            <?php echo esc_html(get_comment_date('M j, Y - H:i', $comment)); ?>
                        </time>
                    </div>
                    <?php if ($rating_int > 0) : ?>
                    <div class="comment__rating-inline">
                        <div class="comment__stars">
                            <?php echo acms_star_rating(floatval($rating)); ?>
                        </div>
                        <?php if ($sentiment) : ?>
                        <span class="comment__rating-text comment__rating-text--<?php echo esc_attr($sentiment['class']); ?>">
                            <?php echo esc_html($sentiment['emoji'] . ' ' . $sentiment['text']); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <!-- Top Actions (visible on desktop) -->
                <?php $like_count = acms_get_comment_likes($comment->comment_ID); ?>
                <div class="comment__actions comment__actions--top">
                    <button class="comment__action comment__action--like" data-comment-id="<?php echo esc_attr($comment->comment_ID); ?>">
                        <i class="bi bi-hand-thumbs-up"></i>
                        <span class="comment__like-count"><?php echo esc_html(acms_format_reaction_count($like_count)); ?></span>
                    </button>
                    <?php if ($depth < $args['max_depth'] && comments_open()) : ?>
                    <button class="comment__action" data-action="toggle-reply">
                        <i class="bi bi-reply"></i> <?php esc_html_e('Reply', 'affiliatecms'); ?>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="comment__body">
                <?php
                if ($comment->comment_approved === '0') {
                    echo '<p class="comment__moderation"><i class="bi bi-clock"></i> ' . esc_html__('Your comment is awaiting moderation.', 'affiliatecms') . '</p>';
                } else {
                    comment_text($comment);
                }
                ?>
            </div>
            <div class="comment__footer">
                <div class="comment__actions comment__actions--bottom">
                    <button class="comment__action comment__action--like" data-comment-id="<?php echo esc_attr($comment->comment_ID); ?>">
                        <i class="bi bi-hand-thumbs-up"></i>
                        <span class="comment__like-count"><?php echo esc_html(acms_format_reaction_count($like_count)); ?></span>
                    </button>
                    <?php if ($depth < $args['max_depth'] && comments_open()) : ?>
                    <button class="comment__action" data-action="toggle-reply">
                        <i class="bi bi-reply"></i> <?php esc_html_e('Reply', 'affiliatecms'); ?>
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($depth < $args['max_depth'] && comments_open()) :
                $reply_user = wp_get_current_user();
                $reply_logged_in = is_user_logged_in();
            ?>
            <!-- Inline Reply Form -->
            <div class="comment__reply-form" data-parent-id="<?php echo esc_attr($comment->comment_ID); ?>">
                <div class="comment__reply-form-header">
                    <div class="comment__reply-form-avatar">
                        <?php if ($reply_logged_in) : ?>
                            <?php echo get_avatar($reply_user->ID, 24, '', '', ['class' => 'comment__reply-form-user-avatar']); ?>
                        <?php else : ?>
                            <i class="bi bi-person"></i>
                        <?php endif; ?>
                    </div>
                    <div class="comment__reply-form-context">
                        <?php if ($reply_logged_in) : ?>
                            <strong><?php echo esc_html($reply_user->display_name); ?></strong>
                            <?php esc_html_e('replying to', 'affiliatecms'); ?>
                            <strong><?php echo esc_html($author_name); ?></strong>
                        <?php else : ?>
                            <?php printf(
                                /* translators: %s: comment author name */
                                esc_html__('Replying to %s', 'affiliatecms'),
                                '<strong>' . esc_html($author_name) . '</strong>'
                            ); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="comment__reply-form-body">
                    <textarea name="comment" class="comment__reply-form-textarea" placeholder="<?php esc_attr_e('Write your reply...', 'affiliatecms'); ?>" rows="2" required></textarea>
                </div>
                <div class="comment__reply-form-footer">
                    <?php if (!$reply_logged_in) : ?>
                    <div class="comment__reply-form-guest">
                        <input type="text" name="author" class="comment__reply-form-input" placeholder="<?php esc_attr_e('Your name', 'affiliatecms'); ?>" required>
                        <input type="email" name="email" class="comment__reply-form-input" placeholder="<?php esc_attr_e('Your email', 'affiliatecms'); ?>" required>
                    </div>
                    <?php endif; ?>
                    <button type="button" class="comment__reply-form-submit<?php echo $reply_logged_in ? ' comment__reply-form-submit--full' : ''; ?>" data-action="submit-reply">
                        <i class="bi bi-send-fill"></i>
                        <?php esc_html_e('Post Reply', 'affiliatecms'); ?>
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    <?php
}

/**
 * Pagination
 *
 * @param WP_Query|null $query Custom query (optional)
 */
function acms_pagination($query = null) {
    if (!$query) {
        global $wp_query;
        $query = $wp_query;
    }

    $total_pages = $query->max_num_pages;

    if ($total_pages <= 1) {
        return;
    }

    $current_page = max(1, get_query_var('paged', 1));

    $pagination = paginate_links([
        'total' => $total_pages,
        'current' => $current_page,
        'prev_text' => '<i class="bi bi-chevron-left"></i>',
        'next_text' => '<i class="bi bi-chevron-right"></i>',
        'type' => 'array',
    ]);

    if ($pagination) {
        echo '<nav class="pagination" aria-label="' . esc_attr__('Posts navigation', 'affiliatecms') . '">';
        foreach ($pagination as $page) {
            // Add appropriate classes
            $page = str_replace('page-numbers', 'pagination__item', $page);
            $page = str_replace('current', 'pagination__item--current', $page);
            $page = str_replace('prev', 'pagination__item--prev', $page);
            $page = str_replace('next', 'pagination__item--next', $page);
            echo $page;
        }
        echo '</nav>';
    }
}

/**
 * Get category icon from term meta
 *
 * @param int $term_id Category term ID
 * @param string $default Default icon class if none set
 * @return string Icon class
 */
function acms_get_category_icon($term_id, $default = 'bi-folder-fill') {
    $icon = get_term_meta($term_id, 'category_icon', true);
    return $icon ? $icon : $default;
}
