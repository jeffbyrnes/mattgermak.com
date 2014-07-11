<?php // Do not delete these lines
    if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
        die (__('Please do not load this page directly. Thanks!','k2_domain'));

        if (!empty($post->post_password)) { // if there's a password
            if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
    ?>

        <p class="center"><?php _e('This post is password protected. Enter the password to view comments.','k2_domain'); ?></p>

<?php   return; } }


    /* Function for seperating comments from track- and pingbacks. */
    function k2_comment_type_detection($commenttxt = 'Comment', $trackbacktxt = 'Trackback', $pingbacktxt = 'Pingback') {
        global $comment;
        if (preg_match('|trackback|', $comment->comment_type))
            return $trackbacktxt;
        elseif (preg_match('|pingback|', $comment->comment_type))
            return $pingbacktxt;
        else
            return $commenttxt;
    }

    $templatedir = get_bloginfo('template_directory');
?>

<!-- You can start editing here. -->

<?php if (($comments) or ('open' == $post-> comment_status)) { $shownavigation = 'yes'; ?>

<hr />

<div class="comments">

    <h4><?php printf( __('%1$s %2$s to &#8220;%3$s&#8221;','k2_domain'), '<span id="comments">' . get_comments_number() . '</span>', ($post->comment_count == 1) ? __('Response','k2_domain'): __('Responses','k2_domain'), get_the_title() ) ?></h4>

    <div class="metalinks">
        <span class="commentsrsslink"><?php comments_rss_link(__('Feed for this Entry','k2_domain')); ?></span>
        <?php if ('open' == $post-> ping_status) { ?><span class="trackbacklink"><a href="<?php trackback_url() ?>" title="<?php _e('Copy this URI to trackback this entry.','k2_domain'); ?>"><?php _e('Trackback Address','k2_domain'); ?></a></span><?php } ?>
    </div>

    <ol id="commentlist">

    <?php if ($comments) { ?>

            <?php $count_pings = 1; foreach ($comments as $comment) {
                if (k2_comment_type_detection() == "Comment") { ?>

                <li class="<?php /* Style differently if comment author is blog author */ if ($comment->comment_author_email == get_the_author_email()) { echo 'authorcomment'; } ?> item" id="comment-<?php comment_ID() ?>">
                    <?php if (function_exists('gravatar')) { ?><a href="http://www.gravatar.com/" title="<?php _e('What is this?','k2_domain'); ?>"><img src="<?php gravatar("X", 32,  get_bloginfo('template_url')."/images/defaultgravatar.jpg"); ?>" class="gravatar" alt="<?php _e('Gravatar Icon','k2_domain'); ?>" /></a><?php } ?>
                    <a href="#comment-<?php comment_ID() ?>" class="counter" title="<?php _e('Permanent Link to this Comment','k2_domain'); ?>"><?php echo $count_pings; $count_pings++; ?></a>
                    <span class="commentauthor"><?php comment_author_link() ?></span>
                    <small class="commentmetadata">
                        <a href="#comment-<?php comment_ID() ?>" title="<?php if (function_exists('time_since')) { printf(__('%s ago.','k2_domain'), time_since(abs( strtotime($comment->comment_date . " GMT") ), time()) ); } else { echo __('Permanent Link to this Comment','k2_domain'); } ?>"><?php printf( __('%1$s at %2$s','k2_domain'), get_comment_date(__('M jS, Y','k2_domain')), get_comment_time()) ?></a>
                        <?php if ( $user_ID ) { edit_comment_link(__('Edit','k2_domain'),'<span class="commentseditlink">','</span>'); } ?>
                    </small>

                    <div class="itemtext">
                        <?php comment_text() ?>
                    </div>

                    <?php if ($comment->comment_approved == '0') : ?>
                    <p class="alert"><strong><?php _e('Your comment is awaiting moderation.','k2_domain'); ?></strong></p>
                    <?php endif; ?>

                </li>

            <?php } } /* end for each comment */ ?>

        </ol>

        <?php $comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post->ID' AND comment_approved = '1' AND comment_type!= '' ORDER BY comment_date"); ?>

        <?php if ($comments) { ?>

        <ol id="pinglist">
        <?php $count_pings = 1; foreach ($comments as $comment) {
            if (k2_comment_type_detection() != "Comment") { ?>
                <li class="item" id="comment-<?php comment_ID() ?>">
                    <?php if (function_exists('comment_favicon')) { ?><span class="favatar"><?php comment_favicon(); ?></span><?php } ?>
                    <a href="#comment-<?php comment_ID() ?>" title="<?php _e('Permanent Link to this Comment','k2_domain'); ?>" class="counter"><?php echo $count_pings; $count_pings++; ?></a>
                    <span class="commentauthor"><?php comment_author_link() ?></span>
                    <small class="commentmetadata"><span class="pingtype"><?php comment_type(); ?></span> <?php _e('on','k2_domain'); ?> <a href="#comment-<?php comment_ID() ?>" title="<?php if (function_exists('time_since')) { printf(__('%s ago.','k2_domain'), time_since(abs( strtotime($comment->comment_date . " GMT") ), time()) ); } else { echo __('Permanent Link to this Comment','k2_domain'); } ?>"><?php printf( __('%1$s at %2$s','k2_domain'), get_comment_date(__('M jS, Y','k2_domain')), get_comment_time()) ?></a> <?php if ( $user_ID ) { edit_comment_link(__('Edit','k2_domain'),'<span class="commentseditlink">','</span>'); } ?></small>
                </li>

            <?php } } /* end for each comment */ ?>

        </ol>
        <?php } ?>

    <?php } else { // this is displayed if there are no comments so far ?>

        <?php if ('open' == $post-> comment_status) { ?>
            <!-- If comments are open, but there are no comments. -->
                <li id="leavecomment"><?php _e('No Comments','k2_domain'); ?></li>

        <?php } else { // comments are closed ?>

            <!-- If comments are closed. -->

            <?php if (is_single) { // To hide comments entirely on Pages without comments ?>
                <li><?php _e('Comments are currently closed.','k2_domain'); ?></li>
            <?php } ?>

        <?php } ?>

        </ol>

    <?php } ?>

    <!-- Reply Form -->
    <?php if ('open' == $post-> comment_status) : ?>
    <div id="loading" style="display: none;">
        <?php _e('Posting Your Comment','k2_domain'); ?><br />
        <?php _e('Please Wait','k2_domain'); ?>
    </div>

        <h4><?php _e('Leave a Reply','k2_domain'); ?></h4>

        <?php if ( get_option('comment_registration') && !$user_ID ) : ?>

            <p><?php printf( __('You must <a href="%s">login</a> to post a comment.','k2_domain'), get_option('siteurl') . '/wp-login.php?redirect_to=' . get_permalink()) ?></p>

        <?php else : ?>

<?php /* Load Live Commenting if enabled in the K2 Options Panel */
    $k2lc = get_option('k2livecommenting'); if ($k2lc == 1) { ?>

        <form id="commentform" action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" onsubmit="new Ajax.Updater({success: 'commentlist'}, '<?php bloginfo('stylesheet_directory') ?>/comments-ajax.php', {asynchronous: true, evalScripts: true, insertion: Insertion.Bottom, onComplete: function(request){complete(request)}, onFailure: function(request){failure(request)}, onLoading: function(request){loading()}, parameters: Form.serialize(this)}); return false;">

    <?php } else { ?>

        <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

    <?php } ?>

    <div id="errors"></div>

        <?php if ( $user_ID ) { ?>

        <div class="metalinks"><?php printf( __('Logged in as %s.','k2_domain'), '<a href="' . get_option('siteurl') . '/wp-admin/profile.php">' . $user_identity . '</a>') ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account','k2_domain'); ?>"><?php _e('Logout','k2_domain'); ?> &raquo;</a></div>

        <?php } elseif ($comment_author != "") { ?>

            <p><small><?php printf( __('Welcome back <strong>%s</strong>','k2_domain'), $comment_author) ?>
            <span id="showinfo">(<a href="javascript:ShowUtils();"><?php _e('Change','k2_domain'); ?></a>)</span>
            <span id="hideinfo">(<a href="javascript:HideUtils();"><?php _e('Close','k2_domain'); ?></a>)</span></small></p>

        <?php } ?>
        <?php if ( !$user_ID ) { ?>
            <div id="authorinfo">
                <p><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
                <label for="author"><small><strong><?php _e('Name','k2_domain'); ?></strong> <?php if ($req) __('(required)','k2_domain'); ?></small></label></p>

                <p><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
                <label for="email"><small><strong><?php _e('Mail','k2_domain'); ?></strong> (<?php _e('will not be published','k2_domain'); ?>) <?php if ($req) __('(required)','k2_domain'); ?></small></label></p>

                <p><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
                <label for="url"><small><strong><?php _e('Website','k2_domain'); ?></strong></small></label></p>
            </div>
        <?php } ?>
            <!--<p><small><?php printf( __('<strong>XHTML:</strong> You can use these tags %s:','k2_domain'), allowed_tags()) ?></small></p>-->

            <p><textarea name="comment" id="comment" cols="100%" rows="10" tabindex="4"></textarea></p>

            <?php if (function_exists('show_subscription_checkbox')) { show_subscription_checkbox(); } ?>

            <p>
                <input name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit','k2_domain'); ?>" />
                <input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
                <br class="clear" />
            </p>

            <?php do_action('comment_form', $post->ID); ?>

            </form>

    <?php if ($shownavigation) { ?>
        <?php include (TEMPLATEPATH . '/navigation.php'); ?>
    <?php } ?>


<?php endif; // If registration required and not logged in ?>

<?php endif; // if you delete this the sky will fall on your head ?>
</div> <!-- Close .comments container -->
<?php }
