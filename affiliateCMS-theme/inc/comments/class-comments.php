<?php
/**
 * ACMS Comments - Main Class
 *
 * Central controller for the comment system module.
 * Initializes all sub-components and manages hooks.
 * REST API is handled by AffiliateCMS API plugin.
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
 * Main Comments class
 */
class Comments {

    /**
     * Module version
     */
    const VERSION = '1.0.0';

    /**
     * Single instance
     *
     * @var Comments|null
     */
    private static $instance = null;

    /**
     * Rating handler instance
     *
     * @var Rating|null
     */
    public $rating = null;

    /**
     * Reactions handler instance (likes/hearts)
     *
     * @var Reactions|null
     */
    public $reactions = null;

    /**
     * Get single instance
     *
     * @return Comments
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor - private to enforce singleton
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_components();
        $this->init_hooks();
    }

    /**
     * Load required files
     */
    private function load_dependencies() {
        $path = ACMS_DIR . '/inc/comments/';

        require_once $path . 'class-rating.php';
        require_once $path . 'class-reactions.php';
        require_once $path . 'functions.php';
    }

    /**
     * Initialize component instances
     */
    private function init_components() {
        $this->rating    = new Rating();
        $this->reactions = new Reactions();
    }

    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Add hidden fields to comment form
        add_action('comment_form', [$this, 'render_form_fields']);

        // Localize scripts for frontend
        add_action('wp_enqueue_scripts', [$this, 'localize_scripts'], 20);
    }

    /**
     * Render hidden fields for comment form
     */
    public function render_form_fields() {
        ?>
        <input type="hidden" name="acms_rating" id="acms-rating-input" value="0">
        <?php wp_nonce_field('acms_comment_action', 'acms_comment_nonce'); ?>
        <?php
    }

    /**
     * Localize scripts for AJAX
     */
    public function localize_scripts() {
        // Heart reactions need config on all singular posts (even if comments closed)
        if (!is_singular('post')) {
            return;
        }

        wp_localize_script('acms-theme', 'azsComments', [
            'restUrl'       => rest_url('azs/v1/'),
            'nonce'         => wp_create_nonce('wp_rest'),
            'postId'        => get_the_ID(),
            'isLoggedIn'    => is_user_logged_in(),
            'commentsOpen'  => comments_open(),
            'i18n'          => [
                'loading'        => __('Loading...', 'affiliatecms'),
                'loadMore'       => __('Load More Comments', 'affiliatecms'),
                'noMore'         => __('No more comments', 'affiliatecms'),
                'error'          => __('An error occurred. Please try again.', 'affiliatecms'),
                'submitting'     => __('Submitting...', 'affiliatecms'),
                'reply'          => __('Reply', 'affiliatecms'),
                'close'          => __('Close', 'affiliatecms'),
                'ratingRequired' => __('Please select a rating before submitting your review.', 'affiliatecms'),
            ],
        ]);
    }

    /**
     * Check if comment system is enabled
     *
     * @return bool
     */
    public function is_enabled() {
        return apply_filters('acms_comments_enabled', true);
    }

    /**
     * Get module version
     *
     * @return string
     */
    public function get_version() {
        return self::VERSION;
    }
}
