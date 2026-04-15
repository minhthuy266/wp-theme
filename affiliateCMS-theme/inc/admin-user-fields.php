<?php
/**
 * Custom User Profile Fields
 * Adds expertise, social links and other meta fields to user profiles
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Available expertise options - grouped by category
 */
function acms_get_expertise_options() {
    return [
        // Technology
        'tech-reviews'  => ['icon' => 'bi-laptop', 'label' => __('Tech Reviews', 'affiliatecms'), 'group' => 'Technology'],
        'smartphones'   => ['icon' => 'bi-phone', 'label' => __('Smartphones', 'affiliatecms'), 'group' => 'Technology'],
        'laptops'       => ['icon' => 'bi-laptop', 'label' => __('Laptops & PCs', 'affiliatecms'), 'group' => 'Technology'],
        'audio'         => ['icon' => 'bi-headphones', 'label' => __('Audio & Sound', 'affiliatecms'), 'group' => 'Technology'],
        'cameras'       => ['icon' => 'bi-camera', 'label' => __('Cameras & Photo', 'affiliatecms'), 'group' => 'Technology'],
        'gaming'        => ['icon' => 'bi-controller', 'label' => __('Gaming', 'affiliatecms'), 'group' => 'Technology'],
        'smart-home'    => ['icon' => 'bi-house-fill', 'label' => __('Smart Home', 'affiliatecms'), 'group' => 'Technology'],
        'wearables'     => ['icon' => 'bi-smartwatch', 'label' => __('Wearables', 'affiliatecms'), 'group' => 'Technology'],
        'software'      => ['icon' => 'bi-code-slash', 'label' => __('Software', 'affiliatecms'), 'group' => 'Technology'],
        'ai'            => ['icon' => 'bi-robot', 'label' => __('AI & ML', 'affiliatecms'), 'group' => 'Technology'],

        // Lifestyle
        'fashion'       => ['icon' => 'bi-bag', 'label' => __('Fashion', 'affiliatecms'), 'group' => 'Lifestyle'],
        'beauty'        => ['icon' => 'bi-droplet', 'label' => __('Beauty & Skincare', 'affiliatecms'), 'group' => 'Lifestyle'],
        'health'        => ['icon' => 'bi-heart-pulse', 'label' => __('Health & Wellness', 'affiliatecms'), 'group' => 'Lifestyle'],
        'fitness'       => ['icon' => 'bi-bicycle', 'label' => __('Fitness', 'affiliatecms'), 'group' => 'Lifestyle'],
        'food'          => ['icon' => 'bi-cup-hot', 'label' => __('Food & Cooking', 'affiliatecms'), 'group' => 'Lifestyle'],
        'travel'        => ['icon' => 'bi-airplane', 'label' => __('Travel', 'affiliatecms'), 'group' => 'Lifestyle'],
        'home-decor'    => ['icon' => 'bi-lamp', 'label' => __('Home & Decor', 'affiliatecms'), 'group' => 'Lifestyle'],
        'parenting'     => ['icon' => 'bi-people', 'label' => __('Parenting', 'affiliatecms'), 'group' => 'Lifestyle'],
        'pets'          => ['icon' => 'bi-piggy-bank', 'label' => __('Pets', 'affiliatecms'), 'group' => 'Lifestyle'],

        // Business & Finance
        'business'      => ['icon' => 'bi-briefcase', 'label' => __('Business', 'affiliatecms'), 'group' => 'Business'],
        'finance'       => ['icon' => 'bi-currency-dollar', 'label' => __('Finance', 'affiliatecms'), 'group' => 'Business'],
        'investing'     => ['icon' => 'bi-graph-up', 'label' => __('Investing', 'affiliatecms'), 'group' => 'Business'],
        'crypto'        => ['icon' => 'bi-currency-bitcoin', 'label' => __('Cryptocurrency', 'affiliatecms'), 'group' => 'Business'],
        'marketing'     => ['icon' => 'bi-megaphone', 'label' => __('Marketing', 'affiliatecms'), 'group' => 'Business'],
        'ecommerce'     => ['icon' => 'bi-cart', 'label' => __('E-commerce', 'affiliatecms'), 'group' => 'Business'],

        // Entertainment
        'movies'        => ['icon' => 'bi-film', 'label' => __('Movies & TV', 'affiliatecms'), 'group' => 'Entertainment'],
        'music'         => ['icon' => 'bi-music-note-beamed', 'label' => __('Music', 'affiliatecms'), 'group' => 'Entertainment'],
        'books'         => ['icon' => 'bi-book', 'label' => __('Books', 'affiliatecms'), 'group' => 'Entertainment'],
        'sports'        => ['icon' => 'bi-trophy', 'label' => __('Sports', 'affiliatecms'), 'group' => 'Entertainment'],

        // Auto & Outdoor
        'automotive'    => ['icon' => 'bi-car-front', 'label' => __('Automotive', 'affiliatecms'), 'group' => 'Auto & Outdoor'],
        'ev'            => ['icon' => 'bi-ev-front', 'label' => __('Electric Vehicles', 'affiliatecms'), 'group' => 'Auto & Outdoor'],
        'outdoor'       => ['icon' => 'bi-tree', 'label' => __('Outdoor & Camping', 'affiliatecms'), 'group' => 'Auto & Outdoor'],
        'drones'        => ['icon' => 'bi-broadcast', 'label' => __('Drones', 'affiliatecms'), 'group' => 'Auto & Outdoor'],

        // Education
        'education'     => ['icon' => 'bi-mortarboard', 'label' => __('Education', 'affiliatecms'), 'group' => 'Education'],
        'online-courses'=> ['icon' => 'bi-play-circle', 'label' => __('Online Courses', 'affiliatecms'), 'group' => 'Education'],
        'productivity'  => ['icon' => 'bi-lightning', 'label' => __('Productivity', 'affiliatecms'), 'group' => 'Education'],
    ];
}

/**
 * Get expertise options grouped
 */
function acms_get_expertise_grouped() {
    $options = acms_get_expertise_options();
    $grouped = [];

    foreach ($options as $key => $option) {
        $group = $option['group'] ?? 'Other';
        if (!isset($grouped[$group])) {
            $grouped[$group] = [];
        }
        $grouped[$group][$key] = $option;
    }

    return $grouped;
}

/**
 * Add custom fields to user profile - HIGH PRIORITY to show before Rank Math
 */
function acms_user_profile_fields($user) {
    // Get saved values
    $expertise = get_user_meta($user->ID, 'acms_expertise', true);
    $expertise = is_array($expertise) ? $expertise : [];
    $custom_socials = get_user_meta($user->ID, 'acms_custom_socials', true);

    // Standard social fields for Rank Math compatibility
    $twitter = get_user_meta($user->ID, 'twitter', true);
    $facebook = get_user_meta($user->ID, 'facebook', true);
    $instagram = get_user_meta($user->ID, 'instagram', true);
    $linkedin = get_user_meta($user->ID, 'linkedin', true);
    $youtube = get_user_meta($user->ID, 'youtube', true);

    // Get grouped options
    $grouped_options = acms_get_expertise_grouped();
    ?>

    <h2 style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #2271b1;"><?php esc_html_e('AffiliateCMS - Author Profile', 'affiliatecms'); ?></h2>

    <table class="form-table" role="presentation">
        <!-- Expertise Section with Collapsible Groups -->
        <tr>
            <th>
                <label><?php esc_html_e('Expertise Areas', 'affiliatecms'); ?></label>
            </th>
            <td>
                <div class="acms-expertise-groups" style="max-width: 700px;">
                    <?php foreach ($grouped_options as $group => $options) :
                        // Check if any option in this group is selected
                        $group_has_selected = false;
                        foreach ($options as $key => $option) {
                            if (in_array($key, $expertise)) {
                                $group_has_selected = true;
                                break;
                            }
                        }
                        $group_id = sanitize_title($group);
                    ?>
                        <div class="acms-expertise-group" style="margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; background: #fff;">
                            <button type="button"
                                    class="acms-expertise-toggle"
                                    data-target="<?php echo esc_attr($group_id); ?>"
                                    style="width: 100%; padding: 10px 15px; background: #f9f9f9; border: none; cursor: pointer; display: flex; align-items: center; justify-content: space-between; font-weight: 600; font-size: 13px;">
                                <span>
                                    <?php echo esc_html($group); ?>
                                    <?php if ($group_has_selected) : ?>
                                        <span style="color: #2271b1; font-weight: normal; margin-left: 5px;">
                                            (<?php
                                            $count = 0;
                                            foreach ($options as $key => $option) {
                                                if (in_array($key, $expertise)) $count++;
                                            }
                                            echo $count . ' selected';
                                            ?>)
                                        </span>
                                    <?php endif; ?>
                                </span>
                                <span class="dashicons dashicons-arrow-down-alt2" style="transition: transform 0.2s;"></span>
                            </button>
                            <div id="acms-group-<?php echo esc_attr($group_id); ?>"
                                 class="acms-expertise-options"
                                 style="display: <?php echo $group_has_selected ? 'block' : 'none'; ?>; padding: 10px 15px; border-top: 1px solid #eee;">
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 8px;">
                                    <?php foreach ($options as $key => $option) : ?>
                                        <label style="display: flex; align-items: center; gap: 6px; padding: 5px 8px; background: #f5f5f5; border-radius: 4px; cursor: pointer; font-size: 13px;">
                                            <input type="checkbox"
                                                   name="acms_expertise[]"
                                                   value="<?php echo esc_attr($key); ?>"
                                                   <?php checked(in_array($key, $expertise)); ?>>
                                            <i class="<?php echo esc_attr($option['icon']); ?>" style="color: #2271b1; font-size: 14px;"></i>
                                            <span><?php echo esc_html($option['label']); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="description" style="margin-top: 10px;"><?php esc_html_e('Click group headers to expand/collapse. Selected items will be displayed on your author page.', 'affiliatecms'); ?></p>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.acms-expertise-toggle').forEach(function(btn) {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            var target = document.getElementById('acms-group-' + this.dataset.target);
                            var icon = this.querySelector('.dashicons');
                            if (target.style.display === 'none') {
                                target.style.display = 'block';
                                icon.style.transform = 'rotate(180deg)';
                            } else {
                                target.style.display = 'none';
                                icon.style.transform = 'rotate(0deg)';
                            }
                        });
                    });
                });
                </script>
            </td>
        </tr>

        <!-- Standard Social Links (Rank Math compatible) -->
        <tr>
            <th colspan="2">
                <h3 style="margin: 0; padding-top: 20px; border-top: 1px solid #ddd;"><?php esc_html_e('Social Profiles', 'affiliatecms'); ?></h3>
                <p class="description" style="font-weight: normal;"><?php esc_html_e('Used for SEO schema (Rank Math compatible) and displayed on author page', 'affiliatecms'); ?></p>
            </th>
        </tr>

        <tr>
            <th><label for="twitter"><?php esc_html_e('Twitter/X', 'affiliatecms'); ?></label></th>
            <td>
                <input type="text" name="twitter" id="twitter" value="<?php echo esc_attr($twitter); ?>" class="regular-text" placeholder="username (without @)">
            </td>
        </tr>

        <tr>
            <th><label for="facebook"><?php esc_html_e('Facebook', 'affiliatecms'); ?></label></th>
            <td>
                <input type="url" name="facebook" id="facebook" value="<?php echo esc_attr($facebook); ?>" class="regular-text" placeholder="https://facebook.com/yourprofile">
            </td>
        </tr>

        <tr>
            <th><label for="instagram"><?php esc_html_e('Instagram', 'affiliatecms'); ?></label></th>
            <td>
                <input type="text" name="instagram" id="instagram" value="<?php echo esc_attr($instagram); ?>" class="regular-text" placeholder="username">
            </td>
        </tr>

        <tr>
            <th><label for="linkedin"><?php esc_html_e('LinkedIn', 'affiliatecms'); ?></label></th>
            <td>
                <input type="url" name="linkedin" id="linkedin" value="<?php echo esc_attr($linkedin); ?>" class="regular-text" placeholder="https://linkedin.com/in/yourprofile">
            </td>
        </tr>

        <tr>
            <th><label for="youtube"><?php esc_html_e('YouTube', 'affiliatecms'); ?></label></th>
            <td>
                <input type="url" name="youtube" id="youtube" value="<?php echo esc_attr($youtube); ?>" class="regular-text" placeholder="https://youtube.com/@yourchannel">
            </td>
        </tr>

        <!-- Custom Social Links -->
        <tr>
            <th>
                <label for="acms_custom_socials"><?php esc_html_e('Additional Links', 'affiliatecms'); ?></label>
            </th>
            <td>
                <textarea name="acms_custom_socials" id="acms_custom_socials" rows="3" class="large-text code" placeholder="https://discord.gg/xxx|bi-discord|Discord"><?php echo esc_textarea($custom_socials); ?></textarea>
                <p class="description">
                    <?php esc_html_e('Format: URL|icon-class|Label (one per line)', 'affiliatecms'); ?> |
                    <a href="https://icons.getbootstrap.com/" target="_blank"><?php esc_html_e('Browse Icons', 'affiliatecms'); ?></a>
                </p>
            </td>
        </tr>
    </table>

    <?php
}
// Priority 5 to show BEFORE Rank Math (which uses default priority 10)
add_action('show_user_profile', 'acms_user_profile_fields', 5);
add_action('edit_user_profile', 'acms_user_profile_fields', 5);

/**
 * Save custom user profile fields
 */
function acms_save_user_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    // Save expertise
    $expertise = isset($_POST['acms_expertise']) ? array_map('sanitize_text_field', $_POST['acms_expertise']) : [];
    update_user_meta($user_id, 'acms_expertise', $expertise);

    // Save standard social fields
    $social_fields = ['twitter', 'facebook', 'instagram', 'linkedin', 'youtube'];
    foreach ($social_fields as $field) {
        if (isset($_POST[$field])) {
            update_user_meta($user_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    // Save custom socials
    if (isset($_POST['acms_custom_socials'])) {
        update_user_meta($user_id, 'acms_custom_socials', sanitize_textarea_field($_POST['acms_custom_socials']));
    }
}
add_action('personal_options_update', 'acms_save_user_profile_fields');
add_action('edit_user_profile_update', 'acms_save_user_profile_fields');

/**
 * Get user expertise as array of formatted items
 */
function acms_get_user_expertise($user_id) {
    $saved = get_user_meta($user_id, 'acms_expertise', true);
    if (empty($saved) || !is_array($saved)) {
        return [];
    }

    $options = acms_get_expertise_options();
    $result = [];

    foreach ($saved as $key) {
        if (isset($options[$key])) {
            $result[] = [
                'key'   => $key,
                'icon'  => $options[$key]['icon'],
                'label' => $options[$key]['label'],
            ];
        }
    }

    return $result;
}

/**
 * Get user social links (standard + custom)
 */
function acms_get_user_socials($user_id) {
    $socials = [];

    // Standard social links
    $standard = [
        'facebook'  => ['icon' => 'bi-facebook', 'label' => 'Facebook'],
        'twitter'   => ['icon' => 'bi-twitter-x', 'label' => 'Twitter'],
        'instagram' => ['icon' => 'bi-instagram', 'label' => 'Instagram'],
        'linkedin'  => ['icon' => 'bi-linkedin', 'label' => 'LinkedIn'],
        'youtube'   => ['icon' => 'bi-youtube', 'label' => 'YouTube'],
    ];

    foreach ($standard as $key => $data) {
        $value = get_user_meta($user_id, $key, true);
        if (!empty($value)) {
            // Build full URL for username-based fields
            if ($key === 'twitter') {
                $url = 'https://x.com/' . ltrim($value, '@');
            } elseif ($key === 'instagram') {
                $url = 'https://instagram.com/' . $value;
            } else {
                $url = $value;
            }

            $socials[] = [
                'url'   => $url,
                'icon'  => $data['icon'],
                'label' => $data['label'],
            ];
        }
    }

    // Custom social links
    $custom = get_user_meta($user_id, 'acms_custom_socials', true);
    if (!empty($custom)) {
        $lines = explode("\n", $custom);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = explode('|', $line);
            if (count($parts) >= 3) {
                $socials[] = [
                    'url'   => esc_url(trim($parts[0])),
                    'icon'  => sanitize_html_class(trim($parts[1])),
                    'label' => sanitize_text_field(trim($parts[2])),
                ];
            }
        }
    }

    return $socials;
}

/**
 * Add user social data to Rank Math schema
 */
add_filter('rank_math/json_ld', function($data, $jsonld) {
    if (is_author() && isset($data['ProfilePage'])) {
        $author_id = get_queried_object_id();
        $socials = acms_get_user_socials($author_id);

        if (!empty($socials)) {
            $same_as = [];
            foreach ($socials as $social) {
                $same_as[] = $social['url'];
            }

            if (isset($data['ProfilePage']['mainEntity'])) {
                $data['ProfilePage']['mainEntity']['sameAs'] = $same_as;
            }
        }
    }
    return $data;
}, 20, 2);

/**
 * Enqueue admin styles for user profile
 */
function acms_user_profile_admin_styles($hook) {
    if ($hook !== 'profile.php' && $hook !== 'user-edit.php') {
        return;
    }

    // Add Bootstrap Icons for the admin
    wp_enqueue_style(
        'bootstrap-icons',
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
        [],
        '1.11.3'
    );
}
add_action('admin_enqueue_scripts', 'acms_user_profile_admin_styles');
