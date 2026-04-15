<?php
/**
 * REST API Endpoints
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register REST API routes
 */
function acms_register_rest_routes() {
    $namespace = 'azs/v1';

    // Posts endpoint for Load More
    register_rest_route($namespace, '/posts', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'acms_rest_get_posts',
        'permission_callback' => '__return_true',
        'args'                => [
            'page' => [
                'default'           => 1,
                'sanitize_callback' => 'absint',
            ],
            'per_page' => [
                'default'           => 12,
                'sanitize_callback' => 'absint',
            ],
            'category' => [
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'tag' => [
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'author' => [
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'search' => [
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'year' => [
                'default'           => '',
                'sanitize_callback' => 'absint',
            ],
            'month' => [
                'default'           => '',
                'sanitize_callback' => 'absint',
            ],
            'layout' => [
                'default'           => 'grid',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'post_types' => [
                'default'           => 'post',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'taxonomy' => [
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'term' => [
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
            ],
        ],
    ]);

    // Post view tracking
    register_rest_route($namespace, '/posts/(?P<id>\d+)/view', [
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'acms_rest_track_view',
        'permission_callback' => '__return_true',
        'args'                => [
            'id' => [
                'required'          => true,
                'validate_callback' => function($param) {
                    return is_numeric($param);
                },
            ],
        ],
    ]);

    // Post heart/like
    register_rest_route($namespace, '/posts/(?P<id>\d+)/heart', [
        'methods'             => [WP_REST_Server::READABLE, WP_REST_Server::CREATABLE],
        'callback'            => 'acms_rest_post_heart',
        'permission_callback' => '__return_true',
        'args'                => [
            'id' => [
                'required'          => true,
                'validate_callback' => function($param) {
                    return is_numeric($param);
                },
            ],
        ],
    ]);

    // Comments - submit new comment
    register_rest_route($namespace, '/comments', [
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'acms_rest_create_comment',
        'permission_callback' => '__return_true',
        'args'                => [
            'post_id' => [
                'required'          => true,
                'sanitize_callback' => 'absint',
            ],
            'content' => [
                'required'          => true,
                'sanitize_callback' => 'sanitize_textarea_field',
            ],
            'author_name' => [
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'author_email' => [
                'default'           => '',
                'sanitize_callback' => 'sanitize_email',
            ],
            'rating' => [
                'default'           => 0,
                'sanitize_callback' => 'absint',
            ],
            'parent' => [
                'default'           => 0,
                'sanitize_callback' => 'absint',
            ],
        ],
    ]);

    // Comments - fetch list
    register_rest_route($namespace, '/comments', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'acms_rest_get_comments',
        'permission_callback' => '__return_true',
        'args'                => [
            'post_id' => [
                'required'          => true,
                'sanitize_callback' => 'absint',
            ],
            'page' => [
                'default'           => 1,
                'sanitize_callback' => 'absint',
            ],
            'per_page' => [
                'default'           => 10,
                'sanitize_callback' => 'absint',
            ],
            'orderby' => [
                'default'           => 'newest',
                'sanitize_callback' => 'sanitize_text_field',
            ],
        ],
    ]);
}
add_action('rest_api_init', 'acms_register_rest_routes');

/**
 * Get posts for Load More
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function acms_rest_get_posts($request) {
    $page       = $request->get_param('page');
    $per_page   = min($request->get_param('per_page'), 24); // Max 24 per request
    $category   = $request->get_param('category');
    $tag        = $request->get_param('tag');
    $author     = $request->get_param('author');
    $search     = $request->get_param('search');
    $year       = $request->get_param('year');
    $month      = $request->get_param('month');
    $layout     = $request->get_param('layout');
    $post_types = $request->get_param('post_types');
    $taxonomy   = $request->get_param('taxonomy');
    $term       = $request->get_param('term');

    // Determine card variant based on layout
    $is_list_layout = ($layout === 'list');
    $card_variant = $is_list_layout ? 'list' : 'grid-v2';

    // Determine which post types to query
    $allowed_types = ['post', 'acms_reviews', 'acms_deals', 'acms_guides'];
    switch ($post_types) {
        case 'acms_reviews':
            $query_post_types = 'acms_reviews';
            break;
        case 'both':
            $query_post_types = ['post', 'acms_reviews'];
            break;
        case 'all':
            $query_post_types = $allowed_types;
            break;
        case 'post':
        default:
            $query_post_types = 'post';
            break;
    }

    // Build query args
    $args = [
        'post_type'      => $query_post_types,
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'paged'          => $page,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    // Category filter (by slug or ID)
    if (!empty($category)) {
        if (is_numeric($category)) {
            $args['cat'] = absint($category);
        } else {
            $args['category_name'] = $category;
        }
    }

    // Tag filter (by slug or ID)
    if (!empty($tag)) {
        if (is_numeric($tag)) {
            $args['tag_id'] = absint($tag);
        } else {
            $args['tag'] = $tag;
        }
    }

    // Custom taxonomy filter
    if (!empty($taxonomy) && !empty($term)) {
        $allowed_taxonomies = [
            'category', 'post_tag',
            'acms_reviews_category', 'acms_reviews_tag',
            'acms_deals_category', 'acms_deals_tag',
            'acms_guides_category', 'acms_guides_tag',
        ];
        if (in_array($taxonomy, $allowed_taxonomies, true)) {
            $args['tax_query'] = [
                [
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => $term,
                ],
            ];
            // Auto-detect post type from taxonomy
            $taxonomy_post_type_map = [
                'acms_reviews_category' => 'acms_reviews',
                'acms_reviews_tag'      => 'acms_reviews',
                'acms_deals_category'   => 'acms_deals',
                'acms_deals_tag'        => 'acms_deals',
                'acms_guides_category'  => 'acms_guides',
                'acms_guides_tag'       => 'acms_guides',
            ];
            if (isset($taxonomy_post_type_map[$taxonomy])) {
                $args['post_type'] = $taxonomy_post_type_map[$taxonomy];
            }
        }
    }

    // Author filter (by ID or nicename)
    if (!empty($author)) {
        if (is_numeric($author)) {
            $args['author'] = absint($author);
        } else {
            $args['author_name'] = $author;
        }
    }

    // Search
    if (!empty($search)) {
        $args['s'] = $search;
    }

    // Date filters
    if (!empty($year)) {
        $args['year'] = $year;
    }
    if (!empty($month)) {
        $args['monthnum'] = $month;
    }

    $query = new WP_Query($args);
    $posts = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            // Capture the post card HTML
            ob_start();
            get_template_part('template-parts/content/post-card', null, [
                'variant'        => $card_variant,
                'show_category'  => $is_list_layout,
                'show_indicator' => $is_list_layout,
                'show_rating'    => false,
                'show_views'     => false,
                'footer_cols'    => $is_list_layout ? '3col' : '2col',
                'excerpt_words'  => $is_list_layout ? 25 : 15,
            ]);
            $html = ob_get_clean();

            $posts[] = [
                'id'    => get_the_ID(),
                'title' => get_the_title(),
                'html'  => $html,
            ];
        }
        wp_reset_postdata();
    }

    return rest_ensure_response([
        'success'     => true,
        'posts'       => $posts,
        'total'       => $query->found_posts,
        'total_pages' => $query->max_num_pages,
        'current_page' => $page,
    ]);
}

/**
 * Track post view
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function acms_rest_track_view($request) {
    $post_id = absint($request->get_param('id'));

    if (!$post_id || !get_post($post_id)) {
        return new WP_Error('invalid_post', 'Invalid post ID', ['status' => 404]);
    }

    // Increment view count
    $views = (int) get_post_meta($post_id, '_acms_views', true);
    $views++;
    update_post_meta($post_id, '_acms_views', $views);

    return rest_ensure_response([
        'success'         => true,
        'views'           => $views,
        'views_formatted' => acms_format_number($views),
    ]);
}

/**
 * Get or toggle post heart/like
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function acms_rest_post_heart($request) {
    $post_id = absint($request->get_param('id'));

    if (!$post_id || !get_post($post_id)) {
        return new WP_Error('invalid_post', 'Invalid post ID', ['status' => 404]);
    }

    $hearts = (int) get_post_meta($post_id, '_acms_hearts', true);

    // GET - just return current count
    if ($request->get_method() === 'GET') {
        return rest_ensure_response([
            'success' => true,
            'hearts'  => $hearts,
        ]);
    }

    // POST - toggle heart (increment)
    // In a real app, you'd track user's heart state via cookie/user meta
    $hearts++;
    update_post_meta($post_id, '_acms_hearts', $hearts);

    return rest_ensure_response([
        'success' => true,
        'hearts'  => $hearts,
        'hearted' => true,
    ]);
}

/**
 * Create a new comment via REST API
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response|WP_Error
 */
function acms_rest_create_comment($request) {
    $post_id      = $request->get_param('post_id');
    $content      = $request->get_param('content');
    $author_name  = $request->get_param('author_name');
    $author_email = $request->get_param('author_email');
    $rating       = $request->get_param('rating');
    $parent       = $request->get_param('parent') ?: $request->get_param('parent_id');

    // Validate post
    $post = get_post($post_id);
    if (!$post || !comments_open($post_id)) {
        return new WP_Error('comments_closed', __('Comments are closed.', 'affiliatecms'), ['status' => 403]);
    }

    // Validate content
    if (empty(trim($content))) {
        return new WP_Error('empty_comment', __('Please write a comment.', 'affiliatecms'), ['status' => 400]);
    }

    // Build comment data
    $comment_data = [
        'comment_post_ID' => $post_id,
        'comment_content' => $content,
        'comment_parent'  => $parent,
        'comment_type'    => 'comment',
    ];

    // Logged-in user
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        $comment_data['user_id']              = $user->ID;
        $comment_data['comment_author']       = $user->display_name;
        $comment_data['comment_author_email'] = $user->user_email;
        $comment_data['comment_author_url']   = $user->user_url;
    } else {
        // Guest - validate name/email
        $require_name_email = get_option('require_name_email');
        if ($require_name_email && (empty($author_name) || empty($author_email))) {
            return new WP_Error('missing_fields', __('Name and email are required.', 'affiliatecms'), ['status' => 400]);
        }
        $comment_data['comment_author']       = $author_name;
        $comment_data['comment_author_email'] = $author_email;
    }

    // Determine approval based on WordPress settings
    $approved = wp_allow_comment($comment_data, true);
    if (is_wp_error($approved)) {
        $error_code = $approved->get_error_code();
        if ($error_code === 'comment_duplicate') {
            return new WP_Error('duplicate_comment', __('Duplicate comment detected.', 'affiliatecms'), ['status' => 409]);
        }
        return new WP_Error('comment_flood', __('You are posting too quickly. Please try again later.', 'affiliatecms'), ['status' => 429]);
    }
    $comment_data['comment_approved'] = $approved;

    // Insert comment
    $comment_id = wp_insert_comment($comment_data);

    if (!$comment_id) {
        return new WP_Error('insert_failed', __('Failed to submit comment.', 'affiliatecms'), ['status' => 500]);
    }

    // Save rating if provided
    if ($rating >= 1 && $rating <= 5 && function_exists('acms_comments')) {
        acms_comments()->rating->save_rating_api($comment_id, $rating, $post_id);
    }

    // Build response
    $comment = get_comment($comment_id);
    $comment_response = acms_format_comment_for_api($comment);

    $message = ($approved === 1 || $approved === '1')
        ? __('Your review has been posted!', 'affiliatecms')
        : __('Your review is pending moderation.', 'affiliatecms');

    return rest_ensure_response([
        'success' => true,
        'message' => $message,
        'comment' => $comment_response,
    ]);
}

/**
 * Get comments via REST API
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function acms_rest_get_comments($request) {
    $post_id  = $request->get_param('post_id');
    $page     = max(1, $request->get_param('page'));
    $per_page = min($request->get_param('per_page'), 50);
    $orderby  = $request->get_param('orderby');

    // Validate post
    if (!get_post($post_id)) {
        return new WP_Error('invalid_post', __('Invalid post.', 'affiliatecms'), ['status' => 404]);
    }

    // Build query args for top-level comments
    $args = [
        'post_id' => $post_id,
        'status'  => 'approve',
        'parent'  => 0,
        'type'    => 'comment',
        'number'  => $per_page,
        'offset'  => ($page - 1) * $per_page,
    ];

    switch ($orderby) {
        case 'oldest':
            $args['orderby'] = 'comment_date';
            $args['order']   = 'ASC';
            break;
        case 'rating':
            $args['meta_key'] = '_acms_rating';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            break;
        default: // newest
            $args['orderby'] = 'comment_date';
            $args['order']   = 'DESC';
            break;
    }

    $comments = get_comments($args);

    // Get total count for pagination
    $total = get_comments([
        'post_id' => $post_id,
        'status'  => 'approve',
        'parent'  => 0,
        'type'    => 'comment',
        'count'   => true,
    ]);
    $pages = ceil($total / $per_page);

    // Format comments with replies
    $formatted = [];
    foreach ($comments as $comment) {
        $formatted[] = acms_format_comment_for_api($comment, true);
    }

    return rest_ensure_response([
        'success'  => true,
        'comments' => $formatted,
        'pages'    => (int) $pages,
        'total'    => (int) $total,
    ]);
}

/**
 * Format a comment object for API response
 *
 * @param WP_Comment $comment Comment object
 * @param bool $include_replies Whether to include replies
 * @return array Formatted comment data
 */
function acms_format_comment_for_api($comment, $include_replies = false) {
    $author_name  = get_comment_author($comment);
    $author_email = $comment->comment_author_email;
    $initial      = strtoupper(mb_substr($author_name, 0, 2));
    $avatar_color = function_exists('acms_get_avatar_color')
        ? acms_get_avatar_color($author_email)
        : '#3D405B';

    $rating = get_comment_meta($comment->comment_ID, '_acms_rating', true);
    $rating_int = $rating ? intval($rating) : 0;

    $sentiment = null;
    if ($rating_int > 0 && function_exists('acms_get_rating_sentiment')) {
        $sentiment = acms_get_rating_sentiment($rating_int);
    }

    $data = [
        'id'      => (int) $comment->comment_ID,
        'content' => apply_filters('comment_text', $comment->comment_content, $comment),
        'author'  => [
            'name'         => $author_name,
            'email'        => $author_email,
            'initial'      => $initial,
            'avatar_color' => $avatar_color,
            'is_verified'  => !empty($comment->user_id),
        ],
        'rating'    => $rating_int,
        'sentiment' => $sentiment,
        'date'      => get_comment_date('M j, Y', $comment),
        'date_iso'  => get_comment_date('c', $comment),
        'replies'   => [],
    ];

    // Include replies if requested
    if ($include_replies) {
        $replies = get_comments([
            'parent'  => $comment->comment_ID,
            'status'  => 'approve',
            'type'    => 'comment',
            'orderby' => 'comment_date',
            'order'   => 'ASC',
        ]);

        foreach ($replies as $reply) {
            $data['replies'][] = acms_format_comment_for_api($reply, false);
        }
    }

    return $data;
}
