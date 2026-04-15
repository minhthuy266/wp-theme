<?php
/**
 * ACMS Popular Posts Widget
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACMS_Popular_Posts_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'acms_popular_posts',
            __('[AffiliateCMS] Popular Posts', 'affiliatecms'),
            [
                'description' => __('Display popular posts with thumbnails and ranking numbers.', 'affiliatecms'),
                'classname'   => 'sidebar-widget acms-popular-posts-widget',
            ]
        );
    }

    /**
     * Front-end display
     */
    /**
     * Map post types to their category taxonomies
     */
    private static $category_taxonomies = [
        'post'         => 'category',
        'acms_reviews' => 'acms_reviews_category',
        'acms_deals'   => 'acms_deals_category',
        'acms_guides'  => 'acms_guides_category',
    ];

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Popular This Week', 'affiliatecms');
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $orderby = !empty($instance['orderby']) ? $instance['orderby'] : 'comment_count';
        $post_type = !empty($instance['post_type']) ? $instance['post_type'] : 'post';
        $view_all_text = !empty($instance['view_all_text']) ? $instance['view_all_text'] : __('View All', 'affiliatecms');
        $view_all_url = !empty($instance['view_all_url']) ? $instance['view_all_url'] : '';
        $show_expand = !empty($instance['show_expand']) ? true : false;

        // Query args based on orderby
        $query_args = [
            'posts_per_page' => $number,
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'orderby'        => $orderby,
            'order'          => 'DESC',
        ];

        // If using views, use meta_key
        if ($orderby === 'views') {
            $query_args['meta_key'] = '_acms_views';
            $query_args['orderby'] = 'meta_value_num';
        }

        $popular_query = new WP_Query($query_args);

        echo $args['before_widget'];
        ?>
        <div class="sidebar-widget__header">
            <h3 class="sidebar-widget__title">
                <i class="bi bi-fire"></i>
                <?php echo esc_html($title); ?>
            </h3>
            <?php if ($view_all_url) : ?>
                <a href="<?php echo esc_url($view_all_url); ?>" class="sidebar-widget__link"><?php echo esc_html($view_all_text); ?></a>
            <?php endif; ?>
        </div>
        <div class="popular-posts"<?php echo $show_expand ? ' data-expandable' : ''; ?>>
            <?php
            if ($popular_query->have_posts()) {
                $count = 1;
                while ($popular_query->have_posts()) {
                    $popular_query->the_post();
                    ?>
                    <a href="<?php the_permalink(); ?>" class="popular-post<?php echo ($show_expand && $count > 5) ? ' popular-post--hidden' : ''; ?>">
                        <span class="popular-post__number"><?php echo esc_html($count); ?></span>
                        <div class="popular-post__image">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('thumbnail', ['alt' => get_the_title()]); ?>
                            <?php else :
                                $fallback_image = acms_get_thumbnail_url(get_the_ID(), 'thumbnail');
                            ?>
                                <img src="<?php echo esc_url($fallback_image); ?>"
                                     alt="<?php echo esc_attr(get_the_title()); ?>"
                                     loading="lazy">
                            <?php endif; ?>
                        </div>
                        <div class="popular-post__content">
                            <h4 class="popular-post__title"><?php the_title(); ?></h4>
                            <span class="popular-post__meta">
                                <?php
                                $cat_taxonomy = isset(self::$category_taxonomies[$post_type]) ? self::$category_taxonomies[$post_type] : 'category';
                                $terms = get_the_terms(get_the_ID(), $cat_taxonomy);
                                if (!empty($terms) && !is_wp_error($terms)) {
                                    echo esc_html($terms[0]->name) . ' &bull; ';
                                }
                                if (function_exists('acms_get_views')) {
                                    echo esc_html(acms_format_number(acms_get_views())) . ' ' . esc_html__('views', 'affiliatecms');
                                } else {
                                    echo esc_html(get_comments_number()) . ' ' . esc_html__('comments', 'affiliatecms');
                                }
                                ?>
                            </span>
                        </div>
                    </a>
                    <?php
                    $count++;
                }
                wp_reset_postdata();
            } else {
                // Demo posts when no posts exist
                for ($i = 1; $i <= $number; $i++) {
                    ?>
                    <a href="#" class="popular-post">
                        <span class="popular-post__number"><?php echo esc_html($i); ?></span>
                        <div class="popular-post__image">
                            <img src="https://picsum.photos/128/128?random=<?php echo esc_attr($i + 20); ?>" alt="<?php esc_attr_e('Demo post', 'affiliatecms'); ?>" loading="lazy">
                        </div>
                        <div class="popular-post__content">
                            <h4 class="popular-post__title"><?php esc_html_e('Best Budget Robot Vacuums Under $300', 'affiliatecms'); ?></h4>
                            <span class="popular-post__meta"><?php esc_html_e('Robot Vacuums', 'affiliatecms'); ?> &bull; 15.2K <?php esc_html_e('views', 'affiliatecms'); ?></span>
                        </div>
                    </a>
                    <?php
                }
            }
            ?>
        </div>
        <?php if ($show_expand) : ?>
            <button type="button" class="popular-posts__toggle" data-expand-toggle>
                <span class="popular-posts__toggle-text" data-text-collapsed="<?php esc_attr_e('Show More', 'affiliatecms'); ?>" data-text-expanded="<?php esc_attr_e('Show Less', 'affiliatecms'); ?>"><?php esc_html_e('Show More', 'affiliatecms'); ?></span>
                <i class="bi bi-chevron-down"></i>
            </button>
        <?php endif; ?>
        <?php
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Popular This Week', 'affiliatecms');
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $orderby = !empty($instance['orderby']) ? $instance['orderby'] : 'comment_count';
        $post_type = !empty($instance['post_type']) ? $instance['post_type'] : 'post';
        $view_all_text = !empty($instance['view_all_text']) ? $instance['view_all_text'] : __('View All', 'affiliatecms');
        $view_all_url = !empty($instance['view_all_url']) ? $instance['view_all_url'] : '';
        $show_expand = !empty($instance['show_expand']) ? true : false;

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
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e('Number of posts:', 'affiliatecms'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1" max="10" value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('orderby')); ?>"><?php esc_html_e('Order by:', 'affiliatecms'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('orderby')); ?>" name="<?php echo esc_attr($this->get_field_name('orderby')); ?>">
                <option value="comment_count" <?php selected($orderby, 'comment_count'); ?>><?php esc_html_e('Comment Count', 'affiliatecms'); ?></option>
                <option value="views" <?php selected($orderby, 'views'); ?>><?php esc_html_e('Views', 'affiliatecms'); ?></option>
                <option value="date" <?php selected($orderby, 'date'); ?>><?php esc_html_e('Date', 'affiliatecms'); ?></option>
                <option value="rand" <?php selected($orderby, 'rand'); ?>><?php esc_html_e('Random', 'affiliatecms'); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('view_all_text')); ?>"><?php esc_html_e('View All Text:', 'affiliatecms'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('view_all_text')); ?>" name="<?php echo esc_attr($this->get_field_name('view_all_text')); ?>" type="text" value="<?php echo esc_attr($view_all_text); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('view_all_url')); ?>"><?php esc_html_e('View All URL:', 'affiliatecms'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('view_all_url')); ?>" name="<?php echo esc_attr($this->get_field_name('view_all_url')); ?>" type="url" value="<?php echo esc_attr($view_all_url); ?>">
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_expand); ?> id="<?php echo esc_attr($this->get_field_id('show_expand')); ?>" name="<?php echo esc_attr($this->get_field_name('show_expand')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('show_expand')); ?>"><?php esc_html_e('Show expand/collapse button', 'affiliatecms'); ?></label>
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
        $instance['orderby'] = (!empty($new_instance['orderby'])) ? sanitize_text_field($new_instance['orderby']) : 'comment_count';
        $allowed_types = ['post', 'acms_reviews', 'acms_deals', 'acms_guides'];
        $instance['post_type'] = (!empty($new_instance['post_type']) && in_array($new_instance['post_type'], $allowed_types, true)) ? $new_instance['post_type'] : 'post';
        $instance['view_all_text'] = (!empty($new_instance['view_all_text'])) ? sanitize_text_field($new_instance['view_all_text']) : '';
        $instance['view_all_url'] = (!empty($new_instance['view_all_url'])) ? esc_url_raw($new_instance['view_all_url']) : '';
        $instance['show_expand'] = (!empty($new_instance['show_expand'])) ? true : false;
        return $instance;
    }
}
