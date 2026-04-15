<?php
/**
 * ACMS Tags Widget
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACMS_Tags_Widget extends WP_Widget {

    /**
     * Map post types to their tag taxonomies
     */
    private static $tag_taxonomies = [
        'post'         => 'post_tag',
        'acms_reviews' => 'acms_reviews_tag',
        'acms_deals'   => 'acms_deals_tag',
        'acms_guides'  => 'acms_guides_tag',
    ];

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'acms_tags',
            __('[AffiliateCMS] Tags', 'affiliatecms'),
            [
                'description' => __('Display popular tags as clickable badges.', 'affiliatecms'),
                'classname'   => 'sidebar-widget acms-tags-widget',
            ]
        );
    }

    /**
     * Front-end display
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Popular Tags', 'affiliatecms');
        $number = !empty($instance['number']) ? absint($instance['number']) : 8;
        $orderby = !empty($instance['orderby']) ? $instance['orderby'] : 'count';
        $post_type = !empty($instance['post_type']) ? $instance['post_type'] : 'post';

        // Get the correct taxonomy for this post type
        $taxonomy = isset(self::$tag_taxonomies[$post_type]) ? self::$tag_taxonomies[$post_type] : 'post_tag';

        $tags = get_terms([
            'taxonomy' => $taxonomy,
            'orderby'  => $orderby,
            'order'    => 'DESC',
            'number'   => $number,
            'hide_empty' => true,
        ]);

        // Handle WP_Error
        if (is_wp_error($tags)) {
            $tags = [];
        }

        echo $args['before_widget'];
        ?>
        <div class="sidebar-widget__header">
            <h3 class="sidebar-widget__title">
                <i class="bi bi-tags-fill"></i>
                <?php echo esc_html($title); ?>
            </h3>
        </div>
        <div class="sidebar-tags">
            <?php
            if (!empty($tags)) {
                foreach ($tags as $tag) {
                    ?>
                    <a href="<?php echo esc_url(get_term_link($tag)); ?>" class="sidebar-tag"><?php echo esc_html($tag->name); ?></a>
                    <?php
                }
            } else {
                // Demo tags
                $demo_tags = ['Walking Pads', 'Robot Vacuums', 'Air Purifiers', 'Soundbars', 'Mattresses', 'Smart Home', 'Budget Picks', 'Comparison'];
                $demo_tags = array_slice($demo_tags, 0, $number);

                foreach ($demo_tags as $tag) {
                    ?>
                    <a href="#" class="sidebar-tag"><?php echo esc_html($tag); ?></a>
                    <?php
                }
            }
            ?>
        </div>
        <?php
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Popular Tags', 'affiliatecms');
        $number = !empty($instance['number']) ? absint($instance['number']) : 8;
        $orderby = !empty($instance['orderby']) ? $instance['orderby'] : 'count';
        $post_type = !empty($instance['post_type']) ? $instance['post_type'] : 'post';

        // Get available post types
        $post_types = [
            'post' => __('Posts', 'affiliatecms'),
        ];
        if (post_type_exists('acms_reviews')) {
            $pt = get_post_type_object('acms_reviews');
            $post_types['acms_reviews'] = $pt->labels->name;
        }
        if (post_type_exists('acms_deals')) {
            $pt = get_post_type_object('acms_deals');
            $post_types['acms_deals'] = $pt->labels->name;
        }
        if (post_type_exists('acms_guides')) {
            $pt = get_post_type_object('acms_guides');
            $post_types['acms_guides'] = $pt->labels->name;
        }
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'affiliatecms'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('post_type')); ?>"><?php esc_html_e('Post Type:', 'affiliatecms'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('post_type')); ?>" name="<?php echo esc_attr($this->get_field_name('post_type')); ?>">
                <?php foreach ($post_types as $pt_value => $pt_label) : ?>
                    <option value="<?php echo esc_attr($pt_value); ?>" <?php selected($post_type, $pt_value); ?>><?php echo esc_html($pt_label); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e('Number of tags:', 'affiliatecms'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1" max="20" value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('orderby')); ?>"><?php esc_html_e('Order by:', 'affiliatecms'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('orderby')); ?>" name="<?php echo esc_attr($this->get_field_name('orderby')); ?>">
                <option value="count" <?php selected($orderby, 'count'); ?>><?php esc_html_e('Post Count', 'affiliatecms'); ?></option>
                <option value="name" <?php selected($orderby, 'name'); ?>><?php esc_html_e('Name', 'affiliatecms'); ?></option>
                <option value="id" <?php selected($orderby, 'id'); ?>><?php esc_html_e('ID', 'affiliatecms'); ?></option>
            </select>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values
     */
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 8;
        $instance['orderby'] = (!empty($new_instance['orderby'])) ? sanitize_text_field($new_instance['orderby']) : 'count';
        $allowed_types = ['post', 'acms_reviews', 'acms_deals', 'acms_guides'];
        $instance['post_type'] = (!empty($new_instance['post_type']) && in_array($new_instance['post_type'], $allowed_types, true)) ? $new_instance['post_type'] : 'post';
        return $instance;
    }
}
