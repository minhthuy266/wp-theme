<?php
/**
 * ACMS Categories Widget
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACMS_Categories_Widget extends WP_Widget {

    /**
     * Default category icons mapping
     */
    private $default_icons = [
        'electronics'      => 'bi-cpu-fill',
        'home-kitchen'     => 'bi-house-fill',
        'health-beauty'    => 'bi-heart-fill',
        'sports-outdoors'  => 'bi-bicycle',
        'baby-kids'        => 'bi-balloon-fill',
        'fashion'          => 'bi-bag-fill',
        'automotive'       => 'bi-car-front-fill',
        'books'            => 'bi-book-fill',
        'toys-games'       => 'bi-controller',
        'garden'           => 'bi-flower1',
    ];

    /**
     * Map post types to their category taxonomies
     */
    private static $category_taxonomies = [
        'post'         => 'category',
        'acms_reviews' => 'acms_reviews_category',
        'acms_deals'   => 'acms_deals_category',
        'acms_guides'  => 'acms_guides_category',
    ];

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'acms_categories',
            __('[AffiliateCMS] Categories', 'affiliatecms'),
            [
                'description' => __('Display categories with icons and post counts.', 'affiliatecms'),
                'classname'   => 'sidebar-widget acms-categories-widget',
            ]
        );
    }

    /**
     * Front-end display
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Categories', 'affiliatecms');
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $show_count = isset($instance['show_count']) ? (bool) $instance['show_count'] : true;
        $show_icon = isset($instance['show_icon']) ? (bool) $instance['show_icon'] : true;
        $orderby = !empty($instance['orderby']) ? $instance['orderby'] : 'count';
        $post_type = !empty($instance['post_type']) ? $instance['post_type'] : 'post';

        // Get the correct taxonomy for this post type
        $taxonomy = isset(self::$category_taxonomies[$post_type]) ? self::$category_taxonomies[$post_type] : 'category';

        $categories = get_terms([
            'taxonomy' => $taxonomy,
            'orderby'  => $orderby,
            'order'    => 'DESC',
            'number'   => $number,
            'hide_empty' => true,
        ]);

        // Handle WP_Error
        if (is_wp_error($categories)) {
            $categories = [];
        }

        echo $args['before_widget'];
        ?>
        <div class="sidebar-widget__header">
            <h3 class="sidebar-widget__title">
                <i class="bi bi-folder-fill"></i>
                <?php echo esc_html($title); ?>
            </h3>
        </div>
        <div class="sidebar-categories">
            <?php
            if (!empty($categories)) {
                foreach ($categories as $category) {
                    // Get icon from term meta or use default based on slug
                    $icon = get_term_meta($category->term_id, '_acms_icon', true);
                    if (!$icon) {
                        $icon = isset($this->default_icons[$category->slug]) ? $this->default_icons[$category->slug] : 'bi-folder-fill';
                    }
                    ?>
                    <a href="<?php echo esc_url(get_term_link($category)); ?>" class="sidebar-category">
                        <span class="sidebar-category__name">
                            <?php if ($show_icon) : ?>
                                <i class="bi <?php echo esc_attr($icon); ?>"></i>
                            <?php endif; ?>
                            <?php echo esc_html($category->name); ?>
                        </span>
                        <?php if ($show_count) : ?>
                            <span class="sidebar-category__count"><?php echo esc_html($category->count); ?></span>
                        <?php endif; ?>
                    </a>
                    <?php
                }
            } else {
                // Demo categories
                $demo_cats = [
                    ['name' => 'Electronics', 'icon' => 'bi-cpu-fill', 'count' => 245],
                    ['name' => 'Home & Kitchen', 'icon' => 'bi-house-fill', 'count' => 189],
                    ['name' => 'Health & Beauty', 'icon' => 'bi-heart-fill', 'count' => 156],
                    ['name' => 'Sports & Outdoors', 'icon' => 'bi-bicycle', 'count' => 134],
                    ['name' => 'Baby & Kids', 'icon' => 'bi-balloon-fill', 'count' => 98],
                ];

                $demo_cats = array_slice($demo_cats, 0, $number);

                foreach ($demo_cats as $cat) {
                    ?>
                    <a href="#" class="sidebar-category">
                        <span class="sidebar-category__name">
                            <?php if ($show_icon) : ?>
                                <i class="bi <?php echo esc_attr($cat['icon']); ?>"></i>
                            <?php endif; ?>
                            <?php echo esc_html($cat['name']); ?>
                        </span>
                        <?php if ($show_count) : ?>
                            <span class="sidebar-category__count"><?php echo esc_html($cat['count']); ?></span>
                        <?php endif; ?>
                    </a>
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
        $title = !empty($instance['title']) ? $instance['title'] : __('Categories', 'affiliatecms');
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $show_count = isset($instance['show_count']) ? (bool) $instance['show_count'] : true;
        $show_icon = isset($instance['show_icon']) ? (bool) $instance['show_icon'] : true;
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
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e('Number of categories:', 'affiliatecms'); ?></label>
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
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_icon); ?> id="<?php echo esc_attr($this->get_field_id('show_icon')); ?>" name="<?php echo esc_attr($this->get_field_name('show_icon')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('show_icon')); ?>"><?php esc_html_e('Show icons', 'affiliatecms'); ?></label>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_count); ?> id="<?php echo esc_attr($this->get_field_id('show_count')); ?>" name="<?php echo esc_attr($this->get_field_name('show_count')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('show_count')); ?>"><?php esc_html_e('Show post count', 'affiliatecms'); ?></label>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values
     */
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 5;
        $instance['orderby'] = (!empty($new_instance['orderby'])) ? sanitize_text_field($new_instance['orderby']) : 'count';
        $allowed_types = ['post', 'acms_reviews', 'acms_deals', 'acms_guides'];
        $instance['post_type'] = (!empty($new_instance['post_type']) && in_array($new_instance['post_type'], $allowed_types, true)) ? $new_instance['post_type'] : 'post';
        $instance['show_icon'] = (!empty($new_instance['show_icon'])) ? true : false;
        $instance['show_count'] = (!empty($new_instance['show_count'])) ? true : false;
        return $instance;
    }
}
