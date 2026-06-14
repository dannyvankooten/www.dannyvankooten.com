<?php

// Prevent direct file access
defined('ABSPATH') or exit;

/**
 * @param mixed $approved One of 1, 0, 'spam', 'trash', WP_Error
 * @param array $commentdata {
 *     Comment data.
 *
 *     @type string $comment_author       The name of the comment author.
 *     @type string $comment_author_email The comment author email address.
 *     @type string $comment_author_url   The comment author URL.
 *     @type string $comment_content      The content of the comment.
 *     @type string $comment_date         The date the comment was submitted. Default is the current time.
 *     @type string $comment_date_gmt     The date the comment was submitted in the GMT timezone.
 *                                        Default is `$comment_date` in the GMT timezone.
 *     @type string $comment_type         Comment type. Default 'comment'.
 *     @type int    $comment_parent       The ID of this comment's parent, if any. Default 0.
 *     @type int    $comment_post_ID      The ID of the post that relates to the comment.
 *     @type int    $user_id              The ID of the user who submitted the comment. Default 0.
 *     @type int    $user_ID              Kept for backward-compatibility. Use `$user_id` instead.
 *     @type string $comment_agent        Comment author user agent. Default is the value of 'HTTP_USER_AGENT'
 *                                        in the `$_SERVER` superglobal sent in the original request.
 *     @type string $comment_author_IP    Comment author IP address in IPv4 format. Default is the value of
 *                                        'REMOTE_ADDR' in the `$_SERVER` superglobal sent in the original request.
 * }
 */

add_filter('pre_comment_approved', static function ($approved, array $commentdata) {
    if (is_wp_error($approved) || $approved === 'spam' || $approved === 'trash') {
        return $approved;
    }

    if (
        // all comments without HTTP User-Agent header: spam
        empty($commentdata['comment_agent'])

        // no author name: spam
        || empty($commentdata['comment_author'])

        // if email looks like "first_last@yahoo.com" and contains an URL, probably spam
        || (strlen($commentdata['comment_author_email']) > 0 && preg_match('/^\w+_\w+@(yahoo|gmail|hotmail)\.com$/', $commentdata['comment_author_email']) && strlen($commentdata['comment_author_url']) > 0)

        // if comment contains a russian character
        || (function_exists('mb_strpos') && mb_strpos($commentdata['comment_content'], "н") !== false)

        // if URL is given and does not contain at least one dot
        || (strlen($commentdata['comment_author_url']) > 0 && !str_contains($commentdata['comment_author_url'], '.'))

        // if URL hostname is example.com
        || parse_url($commentdata['comment_author_url'], PHP_URL_HOST) === 'example.com'

        // if comment text does not contain at least one space
        || !str_contains($commentdata['comment_content'], ' ')

        // if comment author or message consists of only digits
        || preg_match('/^\d+$/', $commentdata['comment_author'])
        || preg_match('/^\d+$/', $commentdata['comment_content'])

        // if comment contains "buy" and a hyperlink
        || (str_contains($commentdata['comment_content'], "buy") && str_contains($commentdata['comment_content'], '<a '))

        // if comment starts with certain pattern
        || (preg_match('/^(Hi|Hey there|Hello there|Hi there|Hello)! I just /', $commentdata['comment_content']))
    ) {
        return new WP_Error('Sorry, your comment was flagged as spam.');
    }

    return $approved;
}, 10, 2);
