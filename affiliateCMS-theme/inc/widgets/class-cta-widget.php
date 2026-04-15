<?php
/**
 * ACMS CTA (Call-to-Action) Widget
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACMS_CTA_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'acms_cta',
            __('[AffiliateCMS] CTA Box', 'affiliatecms'),
            [
                'description' => __('A call-to-action box with title, description, and button.', 'affiliatecms'),
                'classname'   => 'sidebar-widget cta-widget acms-cta-widget',
            ]
        );
    }

    /**
     * Front-end display
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __("Today's Best Deals", 'affiliatecms');
        $description = !empty($instance['description']) ? $instance['description'] : __("Don't miss out on limited-time discounts from top retailers.", 'affiliatecms');
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : __('View All Deals', 'affiliatecms');
        $button_url = !empty($instance['button_url']) ? $instance['button_url'] : '#';
        $button_icon = !empty($instance['button_icon']) ? $instance['button_icon'] : 'bi-tag-fill';
        $button_style = !empty($instance['button_style']) ? $instance['button_style'] : 'primary';
        $open_new_tab = !empty($instance['open_new_tab']) ? true : false;

        echo $args['before_widget'];
        ?>
        <h3 class="cta-widget__title"><?php echo esc_html($title); ?></h3>
        <p class="cta-widget__text"><?php echo esc_html($description); ?></p>
        <a href="<?php echo esc_url($button_url); ?>" class="btn btn--<?php echo esc_attr($button_style); ?> btn--block"<?php echo $open_new_tab ? ' target="_blank" rel="nofollow"' : ''; ?>>
            <?php if ($button_icon) : ?>
                <i class="bi <?php echo esc_attr($button_icon); ?>"></i>
            <?php endif; ?>
            <?php echo esc_html($button_text); ?>
        </a>
        <?php
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __("Today's Best Deals", 'affiliatecms');
        $description = !empty($instance['description']) ? $instance['description'] : __("Don't miss out on limited-time discounts from top retailers.", 'affiliatecms');
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : __('View All Deals', 'affiliatecms');
        $button_url = !empty($instance['button_url']) ? $instance['button_url'] : '';
        $button_icon = !empty($instance['button_icon']) ? $instance['button_icon'] : 'bi-tag-fill';
        $button_style = !empty($instance['button_style']) ? $instance['button_style'] : 'primary';
        $open_new_tab = !empty($instance['open_new_tab']) ? true : false;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'affiliatecms'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('description')); ?>"><?php esc_html_e('Description:', 'affiliatecms'); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('description')); ?>" name="<?php echo esc_attr($this->get_field_name('description')); ?>" rows="3"><?php echo esc_textarea($description); ?></textarea>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_text')); ?>"><?php esc_html_e('Button Text:', 'affiliatecms'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('button_text')); ?>" name="<?php echo esc_attr($this->get_field_name('button_text')); ?>" type="text" value="<?php echo esc_attr($button_text); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_url')); ?>"><?php esc_html_e('Button URL:', 'affiliatecms'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('button_url')); ?>" name="<?php echo esc_attr($this->get_field_name('button_url')); ?>" type="url" value="<?php echo esc_attr($button_url); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_icon')); ?>"><?php esc_html_e('Button Icon (Bootstrap Icons):', 'affiliatecms'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('button_icon')); ?>" name="<?php echo esc_attr($this->get_field_name('button_icon')); ?>" type="text" value="<?php echo esc_attr($button_icon); ?>">
            <small><?php esc_html_e('e.g., bi-tag-fill, bi-lightning-fill, bi-gift-fill', 'affiliatecms'); ?></small>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_style')); ?>"><?php esc_html_e('Button Style:', 'affiliatecms'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('button_style')); ?>" name="<?php echo esc_attr($this->get_field_name('button_style')); ?>">
                <option value="primary" <?php selected($button_style, 'primary'); ?>><?php esc_html_e('Primary', 'affiliatecms'); ?></option>
                <option value="secondary" <?php selected($button_style, 'secondary'); ?>><?php esc_html_e('Secondary', 'affiliatecms'); ?></option>
                <option value="success" <?php selected($button_style, 'success'); ?>><?php esc_html_e('Success', 'affiliatecms'); ?></option>
                <option value="warning" <?php selected($button_style, 'warning'); ?>><?php esc_html_e('Warning', 'affiliatecms'); ?></option>
                <option value="danger" <?php selected($button_style, 'danger'); ?>><?php esc_html_e('Danger', 'affiliatecms'); ?></option>
            </select>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($open_new_tab); ?> id="<?php echo esc_attr($this->get_field_id('open_new_tab')); ?>" name="<?php echo esc_attr($this->get_field_name('open_new_tab')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('open_new_tab')); ?>"><?php esc_html_e('Open link in new tab', 'affiliatecms'); ?></label>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values
     */
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['description'] = (!empty($new_instance['description'])) ? sanitize_textarea_field($new_instance['description']) : '';
        $instance['button_text'] = (!empty($new_instance['button_text'])) ? sanitize_text_field($new_instance['button_text']) : '';
        $instance['button_url'] = (!empty($new_instance['button_url'])) ? esc_url_raw($new_instance['button_url']) : '';
        $instance['button_icon'] = (!empty($new_instance['button_icon'])) ? sanitize_text_field($new_instance['button_icon']) : '';
        $instance['button_style'] = (!empty($new_instance['button_style'])) ? sanitize_text_field($new_instance['button_style']) : 'primary';
        $instance['open_new_tab'] = (!empty($new_instance['open_new_tab'])) ? true : false;
        return $instance;
    }
}
