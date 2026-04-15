<?php
/**
 * ACMS Comments - Rating Class
 *
 * Handles star ratings, averages, and rating distribution.
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
 * Rating class
 */
class Rating {

    /**
     * Meta key for comment rating
     */
    const COMMENT_META_KEY = '_acms_rating';

    /**
     * Meta key for post average rating
     */
    const POST_META_KEY = '_acms_rating';

    /**
     * Meta key for post rating count
     */
    const POST_COUNT_KEY = '_acms_rating_count';

    /**
     * Constructor
     */
    public function __construct() {
        add_action('comment_post', [$this, 'save_rating'], 10, 1);
    }

    /**
     * Save rating when comment is submitted
     *
     * @param int $comment_id Comment ID
     */
    public function save_rating($comment_id) {
        // Verify nonce
        if (!isset($_POST['acms_comment_nonce']) ||
            !wp_verify_nonce($_POST['acms_comment_nonce'], 'acms_comment_action')) {
            return;
        }

        if (isset($_POST['acms_rating']) && !empty($_POST['acms_rating'])) {
            $rating = intval($_POST['acms_rating']);

            if ($this->is_valid_rating($rating)) {
                add_comment_meta($comment_id, self::COMMENT_META_KEY, $rating, true);

                // Update post average
                $comment = get_comment($comment_id);
                if ($comment) {
                    $this->update_post_average($comment->comment_post_ID);
                }
            }
        }
    }

    /**
     * Validate rating value
     *
     * @param int $rating Rating value
     * @return bool
     */
    public function is_valid_rating($rating) {
        return is_numeric($rating) && $rating >= 1 && $rating <= 5;
    }

    /**
     * Update post average rating
     *
     * @param int $post_id Post ID
     */
    public function update_post_average($post_id) {
        global $wpdb;

        $results = $wpdb->get_row($wpdb->prepare(
            "SELECT COUNT(*) as count, SUM(CAST(cm.meta_value AS UNSIGNED)) as total
             FROM {$wpdb->commentmeta} cm
             INNER JOIN {$wpdb->comments} c ON c.comment_ID = cm.comment_id
             WHERE c.comment_post_ID = %d
               AND c.comment_approved = '1'
               AND cm.meta_key = %s
               AND cm.meta_value != ''",
            $post_id,
            self::COMMENT_META_KEY
        ));

        if (!$results || $results->count == 0) {
            delete_post_meta($post_id, self::POST_META_KEY);
            delete_post_meta($post_id, self::POST_COUNT_KEY);
            return;
        }

        $average = round($results->total / $results->count, 1);
        update_post_meta($post_id, self::POST_META_KEY, $average);
        update_post_meta($post_id, self::POST_COUNT_KEY, $results->count);
    }

    /**
     * Get post rating data
     *
     * @param int|null $post_id Post ID
     * @return array Rating data
     */
    public function get_post_rating($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        $average = get_post_meta($post_id, self::POST_META_KEY, true);
        $count   = get_post_meta($post_id, self::POST_COUNT_KEY, true);
        $distribution = $this->get_distribution($post_id);

        return [
            'average'      => $average ? floatval($average) : 0,
            'count'        => $count ? intval($count) : 0,
            'distribution' => $distribution,
            'recommend'    => $this->calculate_recommend_percentage($distribution),
        ];
    }

    /**
     * Get rating distribution
     *
     * @param int $post_id Post ID
     * @return array Distribution [5 => count, 4 => count, ...]
     */
    public function get_distribution($post_id) {
        global $wpdb;

        $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT cm.meta_value as rating, COUNT(*) as count
             FROM {$wpdb->commentmeta} cm
             INNER JOIN {$wpdb->comments} c ON c.comment_ID = cm.comment_id
             WHERE c.comment_post_ID = %d
               AND c.comment_approved = '1'
               AND cm.meta_key = %s
               AND cm.meta_value != ''
             GROUP BY cm.meta_value",
            $post_id,
            self::COMMENT_META_KEY
        ));

        foreach ($results as $row) {
            $star = intval($row->rating);
            if (isset($distribution[$star])) {
                $distribution[$star] = intval($row->count);
            }
        }

        return $distribution;
    }

    /**
     * Calculate recommendation percentage (4-5 stars)
     *
     * @param array $distribution Rating distribution
     * @return int Percentage 0-100
     */
    public function calculate_recommend_percentage($distribution) {
        $total = array_sum($distribution);

        if ($total === 0) {
            return 0;
        }

        $recommend = ($distribution[5] ?? 0) + ($distribution[4] ?? 0);
        return round(($recommend / $total) * 100);
    }

    /**
     * Get rating sentiment text and styling
     *
     * @param int $rating Rating value 1-5
     * @return array Sentiment data
     */
    public function get_sentiment($rating) {
        $sentiments = [
            5 => [
                'text'  => __('Excellent!', 'affiliatecms'),
                'emoji' => '🤩',
                'class' => 'excellent',
            ],
            4 => [
                'text'  => __('Good', 'affiliatecms'),
                'emoji' => '😊',
                'class' => 'good',
            ],
            3 => [
                'text'  => __('Average', 'affiliatecms'),
                'emoji' => '😐',
                'class' => 'average',
            ],
            2 => [
                'text'  => __('Poor', 'affiliatecms'),
                'emoji' => '😕',
                'class' => 'poor',
            ],
            1 => [
                'text'  => __('Bad', 'affiliatecms'),
                'emoji' => '😞',
                'class' => 'bad',
            ],
        ];

        return $sentiments[$rating] ?? $sentiments[3];
    }

    /**
     * Check if user has already rated this post
     *
     * @param int|null $post_id Post ID
     * @return bool
     */
    public function user_has_rated($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        if (is_user_logged_in()) {
            $existing = get_comments([
                'post_id'  => $post_id,
                'user_id'  => get_current_user_id(),
                'meta_key' => self::COMMENT_META_KEY,
                'count'    => true,
            ]);
            return $existing > 0;
        }

        $commenter = wp_get_current_commenter();
        if (!empty($commenter['comment_author_email'])) {
            $existing = get_comments([
                'post_id'      => $post_id,
                'author_email' => $commenter['comment_author_email'],
                'meta_key'     => self::COMMENT_META_KEY,
                'count'        => true,
            ]);
            return $existing > 0;
        }

        return false;
    }

    /**
     * Get comment rating
     *
     * @param int $comment_id Comment ID
     * @return int|null Rating or null
     */
    public function get_comment_rating($comment_id) {
        $rating = get_comment_meta($comment_id, self::COMMENT_META_KEY, true);
        return $rating ? intval($rating) : null;
    }

    /**
     * Save comment rating via API
     *
     * @param int $comment_id Comment ID
     * @param int $rating Rating value
     * @param int $post_id Post ID
     * @return bool
     */
    public function save_rating_api($comment_id, $rating, $post_id) {
        if (!$this->is_valid_rating($rating)) {
            return false;
        }

        add_comment_meta($comment_id, self::COMMENT_META_KEY, $rating, true);
        $this->update_post_average($post_id);

        return true;
    }
}
