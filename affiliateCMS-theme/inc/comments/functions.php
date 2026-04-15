<?php
/**
 * ACMS Comments - Helper Functions
 *
 * Global functions for easy access to comment features.
 *
 * @package AffiliateCMS
 * @subpackage Comments
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get main Comments instance
 *
 * @return \AffiliateCMS\Comments\Comments
 */
function acms_comments() {
    return \AffiliateCMS\Comments\Comments::instance();
}

/**
 * Get post rating data
 *
 * @param int|null $post_id Post ID
 * @return array Rating data
 */
function acms_get_post_rating($post_id = null) {
    return acms_comments()->rating->get_post_rating($post_id);
}

/**
 * Get rating sentiment
 *
 * @param int $rating Rating value 1-5
 * @return array Sentiment data
 */
function acms_get_rating_sentiment($rating) {
    return acms_comments()->rating->get_sentiment($rating);
}

/**
 * Check if user has rated this post
 *
 * @param int|null $post_id Post ID
 * @return bool
 */
function acms_user_has_rated($post_id = null) {
    return acms_comments()->rating->user_has_rated($post_id);
}

/**
 * Get total comment count (excluding pings)
 *
 * @param int|null $post_id Post ID
 * @return int
 */
function acms_get_comment_count($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    return get_comments([
        'post_id' => $post_id,
        'status'  => 'approve',
        'type'    => 'comment',
        'count'   => true,
    ]);
}

/**
 * Get comment like count
 *
 * @param int $comment_id Comment ID
 * @return int
 */
function acms_get_comment_likes($comment_id) {
    return acms_comments()->reactions->get_comment_likes($comment_id);
}

/**
 * Get post heart count
 *
 * @param int|null $post_id Post ID
 * @return int
 */
function acms_get_post_hearts($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return acms_comments()->reactions->get_post_hearts($post_id);
}

/**
 * Format reaction count for display (e.g., 1.2K)
 *
 * @param int $count Count
 * @return string
 */
function acms_format_reaction_count($count) {
    return acms_comments()->reactions->format_count($count);
}

/**
 * Generate consistent avatar color based on email
 *
 * @param string $email User email
 * @return string Hex color code
 */
function acms_get_avatar_color($email) {
    $colors = [
        '#0D7377', // Teal
        '#E07A5F', // Coral
        '#81B29A', // Sage
        '#F2CC8F', // Sand
        '#3D405B', // Charcoal
        '#5E60CE', // Purple
        '#48CAE4', // Sky
        '#F77F00', // Orange
        '#D62828', // Red
        '#2A9D8F', // Cyan
    ];

    $index = abs(crc32($email)) % count($colors);
    return $colors[$index];
}

/**
 * Get initial comments for server-side rendering
 *
 * @param int|null $post_id Post ID
 * @param int $limit Number of comments
 * @param string $orderby Order by: newest, oldest, rating
 * @return array Comments
 */
function acms_get_initial_comments($post_id = null, $limit = 10, $orderby = 'newest') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $args = [
        'post_id' => $post_id,
        'status'  => 'approve',
        'number'  => $limit,
        'parent'  => 0,
        'type'    => 'comment',
    ];

    switch ($orderby) {
        case 'oldest':
            $args['orderby'] = 'comment_date';
            $args['order'] = 'ASC';
            break;
        case 'rating':
            $args['meta_key'] = '_acms_rating';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        default:
            $args['orderby'] = 'comment_date';
            $args['order'] = 'DESC';
            break;
    }

    return get_comments($args);
}
