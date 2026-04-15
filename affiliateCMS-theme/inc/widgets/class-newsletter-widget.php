<?php
/**
 * ACMS Newsletter Widget
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACMS_Newsletter_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'acms_newsletter',
            __('[AffiliateCMS] Newsletter', 'affiliatecms'),
            [
                'description' => __('Email subscription form with customizable text.', 'affiliatecms'),
                'classname'   => 'sidebar-widget newsletter-widget acms-newsletter-widget',
            ]
        );
    }

    /**
     * Front-end display
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Get Deal Alerts', 'affiliatecms');
        $description = !empty($instance['description']) ? $instance['description'] : __('Subscribe to receive the best deals and product recommendations directly to your inbox.', 'affiliatecms');
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : __('Subscribe', 'affiliatecms');
        $placeholder = !empty($instance['placeholder']) ? $instance['placeholder'] : __('Enter your email', 'affiliatecms');
        $form_action = !empty($instance['form_action']) ? $instance['form_action'] : '';
        $success_message = !empty($instance['success_message']) ? $instance['success_message'] : __('Thank you for subscribing!', 'affiliatecms');

        echo $args['before_widget'];
        ?>
        <h3 class="newsletter-widget__title"><?php echo esc_html($title); ?></h3>
        <p class="newsletter-widget__text"><?php echo esc_html($description); ?></p>
        <form class="newsletter-form" <?php echo $form_action ? 'action="' . esc_url($form_action) . '" method="post"' : ''; ?> data-newsletter-form data-success-message="<?php echo esc_attr($success_message); ?>">
            <div class="newsletter-form__fields">
                <input type="email" name="email" class="newsletter-form__input newsletter-form__email" placeholder="<?php echo esc_attr($placeholder); ?>" required>
                <div class="newsletter-form__name-wrapper">
                    <input type="text" name="name" class="newsletter-form__input newsletter-form__name" placeholder="<?php esc_attr_e('Your name', 'affiliatecms'); ?>">
                </div>
            </div>
            <input type="hidden" name="source_url" value="">
            <button type="submit" class="btn btn--primary btn--block newsletter-form__submit">
                <i class="bi bi-send-fill"></i>
                <span class="newsletter-form__btn-text"><?php echo esc_html($button_text); ?></span>
                <span class="newsletter-form__btn-loading" style="display:none;">
                    <i class="bi bi-arrow-repeat"></i>
                </span>
            </button>
            <div class="newsletter-form__success" style="display:none;">
                <i class="bi bi-check-circle-fill"></i>
                <span><?php echo esc_html($success_message); ?></span>
            </div>
        </form>
        <?php
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Get Deal Alerts', 'affiliatecms');
        $description = !empty($instance['description']) ? $instance['description'] : __('Subscribe to receive the best deals and product recommendations directly to your inbox.', 'affiliatecms');
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : __('Subscribe', 'affiliatecms');
        $placeholder = !empty($instance['placeholder']) ? $instance['placeholder'] : __('Enter your email', 'affiliatecms');
        $form_action = !empty($instance['form_action']) ? $instance['form_action'] : '';
        $success_message = !empty($instance['success_message']) ? $instance['success_message'] : __('Thank you for subscribing!', 'affiliatecms');
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
            <label for="<?php echo esc_attr($this->get_field_id('placeholder')); ?>"><?php esc_html_e('Input Placeholder:', 'affiliatecms'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('placeholder')); ?>" name="<?php echo esc_attr($this->get_field_name('placeholder')); ?>" type="text" value="<?php echo esc_attr($placeholder); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_text')); ?>"><?php esc_html_e('Button Text:', 'affiliatecms'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('button_text')); ?>" name="<?php echo esc_attr($this->get_field_name('button_text')); ?>" type="text" value="<?php echo esc_attr($button_text); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('form_action')); ?>"><?php esc_html_e('Form Action URL (optional):', 'affiliatecms'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('form_action')); ?>" name="<?php echo esc_attr($this->get_field_name('form_action')); ?>" type="url" value="<?php echo esc_attr($form_action); ?>">
            <small><?php esc_html_e('Leave empty for AJAX submission or enter Mailchimp/ConvertKit URL', 'affiliatecms'); ?></small>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('success_message')); ?>"><?php esc_html_e('Success Message:', 'affiliatecms'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('success_message')); ?>" name="<?php echo esc_attr($this->get_field_name('success_message')); ?>" type="text" value="<?php echo esc_attr($success_message); ?>">
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
        $instance['placeholder'] = (!empty($new_instance['placeholder'])) ? sanitize_text_field($new_instance['placeholder']) : '';
        $instance['form_action'] = (!empty($new_instance['form_action'])) ? esc_url_raw($new_instance['form_action']) : '';
        $instance['success_message'] = (!empty($new_instance['success_message'])) ? sanitize_text_field($new_instance['success_message']) : '';
        return $instance;
    }
}
