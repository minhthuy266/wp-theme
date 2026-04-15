<?php
/**
 * Template Part: Comments Section - Featured Style (Hybrid)
 *
 * Uses real rating data from database with progressive enhancement.
 * Server-side renders initial comments for SEO, JS enhances UX.
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get rating data from database
$rating_data = acms_get_post_rating();
$rating = $rating_data['average'];
$rating_count = $rating_data['count'];
$recommend_pct = $rating_data['recommend'];
$distribution = $rating_data['distribution'];

// Calculate distribution percentages for progress bar
$total_reviews = array_sum($distribution);
$dist_pct = [];
if ($total_reviews > 0) {
    foreach ($distribution as $star => $count) {
        $dist_pct[$star] = round(($count / $total_reviews) * 100);
    }
}

// Get total comment count (including non-rated)
$comment_count = acms_get_comment_count();

// Get comments for display
$comments_per_page = get_option('comments_per_page', 10);

// Get ALL approved comments as flat array (not threaded)
$all_comments = get_comments([
    'post_id' => get_the_ID(),
    'status'  => 'approve',
    'type'    => 'comment',
    'orderby' => 'comment_date_gmt',
    'order'   => 'DESC',
]);

// Separate top-level and replies
$top_level_comments = [];
$replies_by_parent = [];

foreach ($all_comments as $comment) {
    if ($comment->comment_parent == 0) {
        $top_level_comments[] = $comment;
    } else {
        // Group replies by their root parent
        $root_parent = $comment->comment_parent;
        $temp_parent = get_comment($root_parent);
        while ($temp_parent && $temp_parent->comment_parent > 0) {
            $root_parent = $temp_parent->comment_parent;
            $temp_parent = get_comment($root_parent);
        }
        if (!isset($replies_by_parent[$root_parent])) {
            $replies_by_parent[$root_parent] = [];
        }
        $replies_by_parent[$root_parent][] = $comment;
    }
}

// Find Top Review: top-level comment with most likes
// Priority: 1. Most likes (min 1), 2. Fallback to highest rating (5 stars) if no likes
$top_review_comment = null;
$top_review_id = null;
$min_likes_for_top = 1;

if (!empty($top_level_comments)) {
    $max_likes = 0;
    $fallback_comment = null;
    $max_rating = 0;

    foreach ($top_level_comments as $tlc) {
        $likes = acms_get_comment_likes($tlc->comment_ID);
        $rating = intval(get_comment_meta($tlc->comment_ID, '_acms_rating', true));

        // Primary: most likes
        if ($likes >= $min_likes_for_top && $likes > $max_likes) {
            $max_likes = $likes;
            $top_review_comment = $tlc;
            $top_review_id = $tlc->comment_ID;
        }

        // Fallback: highest rating (5 stars preferred)
        if ($rating > $max_rating) {
            $max_rating = $rating;
            $fallback_comment = $tlc;
        }
    }

    // Use fallback if no comment has likes and fallback has 5-star rating
    if (!$top_review_comment && $fallback_comment && $max_rating >= 5) {
        $top_review_comment = $fallback_comment;
        $top_review_id = $fallback_comment->comment_ID;
    }
}

// Paginate top-level comments (excluding top review from regular list)
$paginated_top_level = array_slice($top_level_comments, 0, $comments_per_page);

// Build final comments array: paginated top-level + their replies
$comments = [];
foreach ($paginated_top_level as $top_comment) {
    // Skip top review comment as it will be displayed separately
    if ($top_review_id && $top_comment->comment_ID == $top_review_id) {
        continue;
    }
    $comments[] = $top_comment;
    if (isset($replies_by_parent[$top_comment->comment_ID])) {
        foreach ($replies_by_parent[$top_comment->comment_ID] as $reply) {
            $comments[] = $reply;
        }
    }
}
?>

<!-- Section Divider -->
<div class="section-divider">
    <div class="container">
        <div class="section-divider__inner">
            <span class="section-divider__line"></span>
            <span class="section-divider__icon">
                <i class="bi bi-chat-square-heart"></i>
            </span>
            <span class="section-divider__line"></span>
        </div>
    </div>
</div>

<section class="post-comments" id="comments">
    <div class="container">
        <!-- Rating Showcase - Only show if there are ratings -->
        <?php if ($rating_count > 0) : ?>
        <div class="comment-showcase comment-showcase--centered">
            <div class="comment-showcase__badge">
                <i class="bi bi-trophy-fill"></i>
                <?php esc_html_e('Highly Rated', 'affiliatecms'); ?>
            </div>
            <div class="comment-showcase__score-ring">
                <span class="comment-showcase__score"><?php echo esc_html(number_format($rating, 1)); ?></span>
            </div>
            <div class="comment-showcase__stars">
                <?php echo acms_star_rating($rating); ?>
            </div>
            <div class="comment-showcase__meta">
                <span class="comment-showcase__count">
                    <?php
                    printf(
                        /* translators: %s: number of reviews */
                        esc_html(_n('%s review', '%s reviews', $rating_count, 'affiliatecms')),
                        number_format_i18n($rating_count)
                    );
                    ?>
                </span>
                <?php if ($recommend_pct > 0) : ?>
                <span class="comment-showcase__separator">•</span>
                <span class="comment-showcase__recommend">
                    <i class="bi bi-hand-thumbs-up-fill"></i>
                    <?php echo esc_html($recommend_pct); ?>% <?php esc_html_e('recommend', 'affiliatecms'); ?>
                </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Expandable Comment Form -->
        <?php if (comments_open()) : ?>
        <div class="comment-form comment-form--featured" data-expanded="false">
            <div class="comment-form__compact" data-action="expand-form">
                <div class="comment-form__compact-top">
                    <div class="comment-form__compact-icon">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <div class="comment-form__compact-text">
                        <?php esc_html_e('Share your experience with this product...', 'affiliatecms'); ?>
                    </div>
                </div>
                <button type="button" class="comment-form__compact-btn">
                    <i class="bi bi-pencil-square"></i>
                    <?php esc_html_e('Write Review', 'affiliatecms'); ?>
                </button>
            </div>
            <div class="comment-form__expanded">
                <div class="comment-form__close" data-action="collapse-form">
                    <i class="bi bi-x-lg"></i>
                </div>
                <h3 class="comment-form__title"><?php esc_html_e('Write Your Review', 'affiliatecms'); ?></h3>
                <?php
                $commenter = wp_get_current_commenter();
                $req = get_option('require_name_email');
                $aria_req = ($req ? " aria-required='true'" : '');
                $current_user = wp_get_current_user();
                $is_logged_in = is_user_logged_in();

                // Build fields array based on login status
                if ($is_logged_in) {
                    // Logged in user - show user info, no input fields needed
                    $fields = [
                        'logged_in_as' => '
                            <div class="comment-form__logged-in">
                                <div class="comment-form__user-info">
                                    ' . get_avatar($current_user->ID, 32, '', '', ['class' => 'comment-form__user-avatar']) . '
                                    <span class="comment-form__user-name">' . esc_html($current_user->display_name) . '</span>
                                    <a href="' . esc_url(wp_logout_url(get_permalink())) . '" class="comment-form__logout">' . esc_html__('Log out', 'affiliatecms') . '</a>
                                </div>
                            </div>',
                    ];
                    $submit_button = '<button type="submit" class="comment-form__submit comment-form__submit--full"><i class="bi bi-send-fill"></i> ' . esc_html__('Submit Review', 'affiliatecms') . '</button>';
                } else {
                    // Guest - show full form with name/email fields
                    // Note: JS will hide .comment-form__guest-fields if guest has saved info in localStorage
                    // Submit button is OUTSIDE guest-fields so it remains visible when fields are hidden
                    $fields = [
                        'cookies' => '
                            <div class="comment-form__cookies">
                                <label class="comment-form__checkbox">
                                    <input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" checked="checked">
                                    <span class="comment-form__checkbox-mark"></span>
                                    <span class="comment-form__checkbox-text">' . esc_html__('Save my name and email for next time', 'affiliatecms') . '</span>
                                </label>
                            </div>',
                        'author' => '
                            <div class="comment-form__guest-fields">
                                <div class="comment-form__field comment-form__field--icon">
                                    <i class="bi bi-person"></i>
                                    <input id="author" name="author" type="text" class="comment-form__input" value="' . esc_attr($commenter['comment_author']) . '" placeholder="' . esc_attr__('Your name', 'affiliatecms') . '"' . $aria_req . '>
                                </div>',
                        'email' => '
                                <div class="comment-form__field comment-form__field--icon">
                                    <i class="bi bi-envelope"></i>
                                    <input id="email" name="email" type="email" class="comment-form__input" value="' . esc_attr($commenter['comment_author_email']) . '" placeholder="' . esc_attr__('Your email', 'affiliatecms') . '"' . $aria_req . '>
                                </div>
                            </div>',
                        'url' => '',
                    ];
                    $submit_button = '<button type="submit" class="comment-form__submit"><i class="bi bi-send-fill"></i> ' . esc_html__('Submit', 'affiliatecms') . '</button>';
                }

                comment_form([
                    'title_reply'        => '',
                    'title_reply_before' => '',
                    'title_reply_after'  => '',
                    'class_form'         => 'comment-form__form',
                    'logged_in_as'       => '', // We handle this in fields
                    'comment_field'      => '
                        <div class="comment-form__rating-select">
                            <label class="comment-form__label">' . esc_html__('How would you rate this product?', 'affiliatecms') . '</label>
                            <div class="star-rating star-rating--xl" data-rating="0">
                                <button type="button" class="star-rating__star" data-value="1"><i class="bi bi-star"></i></button>
                                <button type="button" class="star-rating__star" data-value="2"><i class="bi bi-star"></i></button>
                                <button type="button" class="star-rating__star" data-value="3"><i class="bi bi-star"></i></button>
                                <button type="button" class="star-rating__star" data-value="4"><i class="bi bi-star"></i></button>
                                <button type="button" class="star-rating__star" data-value="5"><i class="bi bi-star"></i></button>
                            </div>
                        </div>
                        <div class="comment-form__field">
                            <textarea id="comment" name="comment" class="comment-form__textarea" placeholder="' . esc_attr__('What did you like or dislike about this product?', 'affiliatecms') . '" rows="4" required></textarea>
                        </div>',
                    'fields'             => $fields,
                    'submit_button'      => $submit_button,
                    'comment_notes_before' => '',
                    'comment_notes_after'  => '',
                ]);
                ?>
            </div>
        </div>
        <?php else : ?>
        <p class="comments-closed"><?php esc_html_e('Comments are closed.', 'affiliatecms'); ?></p>
        <?php endif; ?>

        <!-- Comment List -->
        <?php if ($comment_count > 0) : ?>
        <div class="comment-list comment-list--featured" id="comment-list" data-post-id="<?php the_ID(); ?>">
            <div class="comment-list__header">
                <div class="comment-list__header-left">
                    <span class="comment-list__count"><?php echo esc_html($comment_count); ?></span>
                    <span class="comment-list__label"><?php esc_html_e('COMMENTS', 'affiliatecms'); ?></span>
                </div>
                <div class="comment-list__header-right">
                    <button class="comment-list__reset is-hidden" title="<?php esc_attr_e('Reset to default', 'affiliatecms'); ?>">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    <div class="comment-list__sort">
                        <button class="comment-list__sort-toggle" aria-expanded="false" aria-haspopup="listbox">
                            <span class="comment-list__sort-value"><?php esc_html_e('Newest', 'affiliatecms'); ?></span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="comment-list__sort-dropdown" role="listbox">
                            <button class="comment-list__sort-option is-active" data-value="newest" role="option">
                                <i class="bi bi-sort-down"></i> <?php esc_html_e('Newest First', 'affiliatecms'); ?>
                            </button>
                            <button class="comment-list__sort-option" data-value="oldest" role="option">
                                <i class="bi bi-sort-up"></i> <?php esc_html_e('Oldest First', 'affiliatecms'); ?>
                            </button>
                            <button class="comment-list__sort-option" data-value="rating" role="option">
                                <i class="bi bi-trophy"></i> <?php esc_html_e('Top Rated', 'affiliatecms'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($total_reviews > 0) : ?>
            <!-- Rating Distribution Progress Bar -->
            <div class="comment-list__progress">
                <div class="comment-list__progress-bar">
                    <?php if (!empty($dist_pct[5]) && $dist_pct[5] > 0) : ?>
                    <span class="comment-list__progress-segment comment-list__progress-segment--green"
                          style="width: <?php echo esc_attr($dist_pct[5]); ?>%"
                          title="<?php printf(esc_attr__('5 stars: %d', 'affiliatecms'), $distribution[5]); ?>"></span>
                    <?php endif; ?>
                    <?php if (!empty($dist_pct[4]) && $dist_pct[4] > 0) : ?>
                    <span class="comment-list__progress-segment comment-list__progress-segment--lime"
                          style="width: <?php echo esc_attr($dist_pct[4]); ?>%"
                          title="<?php printf(esc_attr__('4 stars: %d', 'affiliatecms'), $distribution[4]); ?>"></span>
                    <?php endif; ?>
                    <?php if (!empty($dist_pct[3]) && $dist_pct[3] > 0) : ?>
                    <span class="comment-list__progress-segment comment-list__progress-segment--yellow"
                          style="width: <?php echo esc_attr($dist_pct[3]); ?>%"
                          title="<?php printf(esc_attr__('3 stars: %d', 'affiliatecms'), $distribution[3]); ?>"></span>
                    <?php endif; ?>
                    <?php if (!empty($dist_pct[2]) && $dist_pct[2] > 0) : ?>
                    <span class="comment-list__progress-segment comment-list__progress-segment--orange"
                          style="width: <?php echo esc_attr($dist_pct[2]); ?>%"
                          title="<?php printf(esc_attr__('2 stars: %d', 'affiliatecms'), $distribution[2]); ?>"></span>
                    <?php endif; ?>
                    <?php if (!empty($dist_pct[1]) && $dist_pct[1] > 0) : ?>
                    <span class="comment-list__progress-segment comment-list__progress-segment--red"
                          style="width: <?php echo esc_attr($dist_pct[1]); ?>%"
                          title="<?php printf(esc_attr__('1 star: %d', 'affiliatecms'), $distribution[1]); ?>"></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php
            // Render Top Review (highlighted) if exists
            if ($top_review_comment) :
                $tr_author = get_comment_author($top_review_comment);
                $tr_initial = strtoupper(mb_substr($tr_author, 0, 2));
                $tr_color = acms_get_avatar_color($top_review_comment->comment_author_email);
                $tr_rating = get_comment_meta($top_review_comment->comment_ID, '_acms_rating', true);
                $tr_rating_int = $tr_rating ? intval($tr_rating) : 0;
                $tr_sentiment = $tr_rating_int > 0 ? acms_get_rating_sentiment($tr_rating_int) : null;
                $tr_likes = acms_get_comment_likes($top_review_comment->comment_ID);
            ?>
            <article class="comment comment--featured comment--highlighted" id="comment-<?php echo esc_attr($top_review_comment->comment_ID); ?>">
                <div class="comment__badge-featured">
                    <i class="bi bi-award-fill"></i>
                    <?php esc_html_e('Top Review', 'affiliatecms'); ?>
                </div>
                <div class="comment__main">
                    <div class="comment__top">
                        <div class="comment__avatar comment__avatar--initial"
                             data-initial="<?php echo esc_attr($tr_initial); ?>"
                             style="--avatar-bg: <?php echo esc_attr($tr_color); ?>">
                            <?php echo esc_html($tr_initial); ?>
                        </div>
                        <div class="comment__info">
                            <div class="comment__header">
                                <span class="comment__author"><?php echo esc_html($tr_author); ?></span>
                                <?php if ($top_review_comment->user_id) : ?>
                                <span class="comment__verified" title="<?php esc_attr_e('Verified User', 'affiliatecms'); ?>">
                                    <i class="bi bi-patch-check-fill"></i>
                                </span>
                                <?php endif; ?>
                                <time class="comment__date" datetime="<?php echo esc_attr(get_comment_date('c', $top_review_comment)); ?>">
                                    <?php echo esc_html(get_comment_date('M j, Y', $top_review_comment)); ?>
                                </time>
                            </div>
                            <?php if ($tr_rating_int > 0) : ?>
                            <div class="comment__rating-inline">
                                <div class="comment__stars">
                                    <?php echo acms_star_rating(floatval($tr_rating)); ?>
                                </div>
                                <?php if ($tr_sentiment) : ?>
                                <span class="comment__rating-text comment__rating-text--<?php echo esc_attr($tr_sentiment['class']); ?>">
                                    <?php echo esc_html($tr_sentiment['emoji'] . ' ' . $tr_sentiment['text']); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="comment__actions comment__actions--top">
                            <button class="comment__action comment__action--like" data-comment-id="<?php echo esc_attr($top_review_comment->comment_ID); ?>">
                                <i class="bi bi-hand-thumbs-up"></i>
                                <span class="comment__like-count"><?php echo esc_html(acms_format_reaction_count($tr_likes)); ?></span>
                            </button>
                            <?php if (comments_open()) : ?>
                            <button class="comment__action" data-action="toggle-reply">
                                <i class="bi bi-reply"></i> <?php esc_html_e('Reply', 'affiliatecms'); ?>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="comment__body">
                        <?php echo apply_filters('comment_text', $top_review_comment->comment_content, $top_review_comment); ?>
                    </div>
                    <div class="comment__footer">
                        <div class="comment__actions comment__actions--bottom">
                            <button class="comment__action comment__action--like" data-comment-id="<?php echo esc_attr($top_review_comment->comment_ID); ?>">
                                <i class="bi bi-hand-thumbs-up"></i>
                                <?php esc_html_e('Helpful', 'affiliatecms'); ?> (<?php echo esc_html($tr_likes); ?>)
                            </button>
                            <?php if (comments_open()) : ?>
                            <button class="comment__action" data-action="toggle-reply">
                                <i class="bi bi-reply"></i> <?php esc_html_e('Reply', 'affiliatecms'); ?>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php
                    if (comments_open()) :
                        $reply_user = wp_get_current_user();
                        $reply_logged_in = is_user_logged_in();
                    ?>
                    <!-- Inline Reply Form for Top Review -->
                    <div class="comment__reply-form" data-parent-id="<?php echo esc_attr($top_review_comment->comment_ID); ?>">
                        <div class="comment__reply-form-header">
                            <div class="comment__reply-form-avatar">
                                <?php if ($reply_logged_in) : ?>
                                    <?php echo get_avatar($reply_user->ID, 24, '', '', ['class' => 'comment__reply-form-user-avatar']); ?>
                                <?php else : ?>
                                    <i class="bi bi-person"></i>
                                <?php endif; ?>
                            </div>
                            <div class="comment__reply-form-context">
                                <?php if ($reply_logged_in) : ?>
                                    <strong><?php echo esc_html($reply_user->display_name); ?></strong>
                                    <?php esc_html_e('replying to', 'affiliatecms'); ?>
                                    <strong><?php echo esc_html($tr_author); ?></strong>
                                <?php else : ?>
                                    <?php printf(esc_html__('Replying to %s', 'affiliatecms'), '<strong>' . esc_html($tr_author) . '</strong>'); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="comment__reply-form-body">
                            <textarea name="comment" class="comment__reply-form-textarea" placeholder="<?php esc_attr_e('Write your reply...', 'affiliatecms'); ?>" rows="2" required></textarea>
                        </div>
                        <div class="comment__reply-form-footer">
                            <?php if (!$reply_logged_in) : ?>
                            <div class="comment__reply-form-guest">
                                <input type="text" name="author" class="comment__reply-form-input" placeholder="<?php esc_attr_e('Your name', 'affiliatecms'); ?>" required>
                                <input type="email" name="email" class="comment__reply-form-input" placeholder="<?php esc_attr_e('Your email', 'affiliatecms'); ?>" required>
                            </div>
                            <?php endif; ?>
                            <button type="button" class="comment__reply-form-submit<?php echo $reply_logged_in ? ' comment__reply-form-submit--full' : ''; ?>" data-action="submit-reply">
                                <i class="bi bi-send-fill"></i>
                                <?php esc_html_e('Post Reply', 'affiliatecms'); ?>
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php
                // Render replies for top review
                if (isset($replies_by_parent[$top_review_id]) && !empty($replies_by_parent[$top_review_id])) :
                ?>
                <div class="comment__replies">
                    <?php
                    foreach ($replies_by_parent[$top_review_id] as $reply) {
                        acms_comment_callback($reply, ['style' => 'div', 'max_depth' => get_option('thread_comments_depth', 5)], 1);
                        echo '</div>'; // Close the reply div
                    }
                    ?>
                </div>
                <?php endif; ?>
            </article>
            <?php endif; ?>

            <ul class="comment-list__items">
                <?php
                wp_list_comments([
                    'style'             => 'ul',
                    'short_ping'        => true,
                    'callback'          => 'acms_comment_callback',
                    'max_depth'         => get_option('thread_comments_depth', 5),
                    'reverse_top_level' => true, // Newest first
                ], $comments);
                ?>
            </ul>

            <?php
            // Show Load More button if there are more comments than displayed
            if ($comment_count > $comments_per_page) :
            ?>
            <div class="comment-list__more">
                <button type="button" class="comment-list__more-btn" data-page="1">
                    <i class="bi bi-arrow-down-circle"></i>
                    <?php esc_html_e('Load More Comments', 'affiliatecms'); ?>
                </button>
            </div>
            <?php endif; ?>

            <?php
            // Native pagination as fallback (hidden when JS is enabled)
            ?>
            <noscript>
                <?php the_comments_pagination([
                    'prev_text' => '<i class="bi bi-chevron-left"></i>',
                    'next_text' => '<i class="bi bi-chevron-right"></i>',
                ]); ?>
            </noscript>
        </div>
        <?php else : ?>
        <div class="comment-list comment-list--empty">
            <div class="comment-list__empty-icon">
                <i class="bi bi-chat-square-text"></i>
            </div>
            <p class="comment-list__empty-text">
                <?php esc_html_e('No reviews yet. Be the first to share your experience!', 'affiliatecms'); ?>
            </p>
        </div>
        <?php endif; ?>
    </div>
</section>
