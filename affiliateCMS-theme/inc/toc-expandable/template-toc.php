<?php
/**
 * TOC Template Functions
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render inline expandable TOC
 *
 * @param array $args Optional arguments
 */
function acms_render_toc_inline($args = []) {
    $toc = \AffiliateCMS\TOC\TOC::instance();

    if (!$toc->should_display()) {
        return;
    }

    $defaults = [
        'title'        => __('Table of Contents', 'affiliatecms'),
        'show_meta'    => true,
        'reading_time' => 0, // Will be calculated if 0
    ];
    $args = wp_parse_args($args, $defaults);

    $headings = $toc->get_numbered_headings();
    $section_count = $toc->get_section_count();
    $total_count = $toc->get_heading_count();
    $visible_count = $toc->get_visible_count();
    $has_hidden = $total_count > $visible_count;

    // Calculate reading time if not provided
    $reading_time = $args['reading_time'];
    if ($reading_time <= 0) {
        global $post;
        if ($post) {
            $word_count = str_word_count(strip_tags($post->post_content));
            $reading_time = max(1, ceil($word_count / 200)); // ~200 words per minute
        }
    }
    ?>
    <div class="toc-expandable" id="tocExpandable">
        <div class="toc-expandable__card" id="tocExpandableCard">

            <!-- Header -->
            <div class="toc-expandable__header">
                <div class="toc-expandable__header-left">
                    <div class="toc-expandable__icon">
                        <i class="bi bi-list-nested"></i>
                    </div>
                    <div class="toc-expandable__header-text">
                        <h4 class="toc-expandable__title"><?php echo esc_html($args['title']); ?></h4>
                        <?php if ($args['show_meta']) : ?>
                            <div class="toc-expandable__meta">
                                <span class="toc-expandable__meta-item">
                                    <i class="bi bi-bookmark"></i>
                                    <?php printf(_n('%d section', '%d sections', $section_count, 'affiliatecms'), $section_count); ?>
                                </span>
                                <?php if ($reading_time > 0) : ?>
                                    <span class="toc-expandable__meta-item">
                                        <i class="bi bi-clock"></i>
                                        <?php printf(_n('%d min read', '%d min read', $reading_time, 'affiliatecms'), $reading_time); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="toc-expandable__progress">
                <div class="toc-expandable__progress-bar" id="tocProgressBar"></div>
            </div>

            <!-- TOC List -->
            <div class="toc-expandable__body">
                <ul class="toc-expandable__list">
                    <?php foreach ($headings as $index => $heading) :
                        $is_h3 = $heading['level'] === 3;
                        $is_hidden = $toc->is_hidden_item($index);
                        $link_class = 'toc-expandable__link';
                        if ($is_h3) {
                            $link_class .= ' toc-expandable__link--h3';
                        }
                        if ($index === 0) {
                            $link_class .= ' is-active';
                        }
                        ?>
                        <li<?php echo $is_hidden ? ' class="toc-expandable__hidden"' : ''; ?>>
                            <a href="#<?php echo esc_attr($heading['id']); ?>"
                               class="<?php echo esc_attr($link_class); ?>"
                               data-section="<?php echo esc_attr($heading['id']); ?>">
                                <span class="toc-expandable__number"><?php echo esc_html($heading['number']); ?></span>
                                <span class="toc-expandable__text"><?php echo esc_html($heading['text']); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Expand Button -->
            <?php if ($has_hidden) : ?>
                <button class="toc-expandable__expand" id="tocExpandBtn" type="button">
                    <span class="toc-expandable__expand-text">
                        <?php printf(__('Show all %d sections', 'affiliatecms'), $total_count); ?>
                    </span>
                    <span class="toc-expandable__collapse-text"><?php _e('Show less', 'affiliatecms'); ?></span>
                    <i class="bi bi-chevron-down"></i>
                </button>
            <?php endif; ?>

        </div>
    </div>
    <?php
}

/**
 * Render floating action bar with TOC bubble
 *
 * @param array $args Optional arguments
 */
function acms_render_toc_bubble($args = []) {
    $toc = \AffiliateCMS\TOC\TOC::instance();

    if (!$toc->should_display()) {
        return;
    }

    $defaults = [
        'title'        => __('Contents', 'affiliatecms'),
    ];
    $args = wp_parse_args($args, $defaults);

    $headings = $toc->get_numbered_headings();
    ?>
    <div class="toc-action-bar" id="tocActionBar">

        <!-- TOC Bubble -->
        <div class="toc-bubble" id="tocBubble">
            <!-- Trigger Button -->
            <?php
            // Count only H2 headings for countdown
            $h2_count = 0;
            foreach ($headings as $heading) {
                if ($heading['level'] === 2) {
                    $h2_count++;
                }
            }
            $sections_remaining = max(0, $h2_count - 1); // Start at first section
            ?>
            <button class="toc-bubble__trigger" id="tocBubbleTrigger" type="button">
                <svg class="toc-bubble__progress" viewBox="0 0 64 64">
                    <circle class="toc-bubble__progress-bg" cx="32" cy="32" r="30"></circle>
                    <circle class="toc-bubble__progress-bar" id="tocBubbleProgressBar" cx="32" cy="32" r="30"></circle>
                </svg>
                <i class="bi bi-list-nested"></i>
            </button>
            <span class="toc-bubble__badge" id="tocBubbleBadge"><?php echo esc_html($sections_remaining); ?></span>

            <!-- Dropdown Panel -->
            <div class="toc-bubble__panel" id="tocBubblePanel">
                <!-- Header -->
                <div class="toc-bubble__header">
                    <div class="toc-bubble__header-left">
                        <i class="bi bi-list-nested toc-bubble__header-icon"></i>
                        <h4 class="toc-bubble__header-title"><?php echo esc_html($args['title']); ?></h4>
                    </div>
                    <button class="toc-bubble__close" id="tocBubbleClose" type="button">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- TOC List -->
                <ul class="toc-bubble__list">
                    <?php foreach ($headings as $index => $heading) :
                        $is_h3 = $heading['level'] === 3;
                        $link_class = 'toc-bubble__link';
                        if ($is_h3) {
                            $link_class .= ' toc-bubble__link--h3';
                        }
                        if ($index === 0) {
                            $link_class .= ' is-active';
                        }
                        ?>
                        <li>
                            <a href="#<?php echo esc_attr($heading['id']); ?>"
                               class="<?php echo esc_attr($link_class); ?>"
                               data-section="<?php echo esc_attr($heading['id']); ?>">
                                <span class="toc-bubble__number"><?php echo esc_html($heading['number']); ?></span>
                                <span><?php echo esc_html($heading['text']); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Footer -->
                <div class="toc-bubble__footer">
                    <span class="toc-bubble__progress-text" id="tocBubbleProgressText">0% <?php _e('completed', 'affiliatecms'); ?></span>
                    <button class="toc-bubble__back-top" id="tocBackTop" type="button">
                        <i class="bi bi-arrow-up"></i>
                        <?php _e('Back to top', 'affiliatecms'); ?>
                    </button>
                </div>
            </div>
        </div>

    </div>
    <?php
}
