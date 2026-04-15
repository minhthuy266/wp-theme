<?php
/**
 * ACMS Comments - Reactions Class
 *
 * Simple reaction system for likes (comments) and hearts (posts).
 * Allows unlimited clicks - no user tracking, only counts.
 *
 * @package AffiliateCMS
 * @subpackage Comments
 * @since 4.0.0
 */

namespace AffiliateCMS\Comments;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Reactions class
 */
class Reactions {

    /**
     * Meta key for comment likes
     */
    const COMMENT_LIKES_KEY = '_acms_comment_likes';

    /**
     * Meta key for post hearts
     */
    const POST_HEARTS_KEY = '_acms_post_hearts';

    /**
     * Constructor
     */
    public function __construct() {
        // No hooks needed - all via REST API
    }

    /**
     * Add like to comment (increment only)
     *
     * @param int $comment_id Comment ID
     * @return array Result
     */
    public function add_comment_like($comment_id) {
        $comment = get_comment($comment_id);

        if (!$comment) {
            return [
                'success' => false,
                'message' => __('Comment not found.', 'affiliatecms'),
            ];
        }

        $count = $this->get_comment_likes($comment_id);
        $new_count = $count + 1;

        update_comment_meta($comment_id, self::COMMENT_LIKES_KEY, $new_count);

        return [
            'success' => true,
            'count'   => $new_count,
            'message' => __('Liked!', 'affiliatecms'),
        ];
    }

    /**
     * Get like count for comment
     *
     * @param int $comment_id Comment ID
     * @return int Like count
     */
    public function get_comment_likes($comment_id) {
        $count = get_comment_meta($comment_id, self::COMMENT_LIKES_KEY, true);
        return $count ? intval($count) : 0;
    }

    /**
     * Add heart to post (increment only)
     *
     * @param int $post_id Post ID
     * @return array Result
     */
    public function add_post_heart($post_id) {
        $post = get_post($post_id);

        if (!$post) {
            return [
                'success' => false,
                'message' => __('Post not found.', 'affiliatecms'),
            ];
        }

        $count = $this->get_post_hearts($post_id);
        $new_count = $count + 1;

        update_post_meta($post_id, self::POST_HEARTS_KEY, $new_count);

        return [
            'success' => true,
            'count'   => $new_count,
            'message' => __('Loved!', 'affiliatecms'),
        ];
    }

    /**
     * Get heart count for post
     *
     * @param int $post_id Post ID
     * @return int Heart count
     */
    public function get_post_hearts($post_id) {
        $count = get_post_meta($post_id, self::POST_HEARTS_KEY, true);
        return $count ? intval($count) : 0;
    }

    /**
     * Get likes for multiple comments (bulk)
     *
     * @param array $comment_ids Comment IDs
     * @return array [comment_id => count]
     */
    public function get_comment_likes_bulk($comment_ids) {
        $counts = [];

        foreach ($comment_ids as $id) {
            $counts[$id] = $this->get_comment_likes($id);
        }

        return $counts;
    }

    /**
     * Format count for display (e.g., 1.2K, 5M)
     *
     * @param int $count Count
     * @return string Formatted count
     */
    public function format_count($count) {
        if ($count >= 1000000) {
            return round($count / 1000000, 1) . 'M';
        }

        if ($count >= 1000) {
            return round($count / 1000, 1) . 'K';
        }

        return (string) $count;
    }
}
