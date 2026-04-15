<?php
/**
 * The template for displaying comments
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

// Don't load directly
if (!defined('ABSPATH')) {
    exit;
}

// Password protected posts
if (post_password_required()) {
    return;
}
?>

<section id="comments" class="comments-area">
    <?php if (have_comments()) : ?>
        <div class="comments-header">
            <h2 class="comments-title">
                <?php
                $comments_number = get_comments_number();
                printf(
                    /* translators: 1: number of comments, 2: post title */
                    esc_html(_nx(
                        '%1$s Comment',
                        '%1$s Comments',
                        $comments_number,
                        'comments title',
                        'affiliatecms'
                    )),
                    number_format_i18n($comments_number)
                );
                ?>
            </h2>
        </div>

        <ol class="comment-list">
            <?php
            wp_list_comments([
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 60,
                'callback'    => 'acms_comment_callback',
            ]);
            ?>
        </ol>

        <?php
        the_comments_navigation([
            'prev_text' => '<i class="bi bi-arrow-left"></i> ' . __('Older Comments', 'affiliatecms'),
            'next_text' => __('Newer Comments', 'affiliatecms') . ' <i class="bi bi-arrow-right"></i>',
        ]);
        ?>

    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
        <p class="no-comments"><?php esc_html_e('Comments are closed.', 'affiliatecms'); ?></p>
    <?php endif; ?>

    <?php
    // Comment form
    comment_form([
        'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
        'title_reply' => __('Leave a Comment', 'affiliatecms'),
        'title_reply_after' => '</h3>',
        'class_form' => 'comment-form',
        'class_submit' => 'btn btn--primary',
        'submit_button' => '<button type="submit" id="%2$s" class="%3$s"><i class="bi bi-send-fill"></i> %4$s</button>',
        'submit_field' => '<div class="form-submit">%1$s %2$s</div>',
    ]);
    ?>
</section>
