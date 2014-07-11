<!doctype html>
<html>

<head profile="http://gmpg.org/xfn/11">
    <title><?php
        wp_title(''); ?> <?php
        if (!(is_404())
            && (is_single()) or (is_page())
            or (is_archive())
        ) {
            ?> at <?php
        }
        ?> <?php
        bloginfo('name');
    ?></title>
    <meta charset="<?php bloginfo('charset'); ?>">

    <meta name="description" content="<?php bloginfo('description'); ?>" />

    <meta name="microid" content="dce0ab69d848c83bb58e50f7a297ea875e30ffa4" />

    <link rel="stylesheet" media="screen" href="<?php bloginfo('stylesheet_url'); ?>" />
    <?php
    /* User's Custom Style */
    if (get_option('k2scheme') != '') {
        ?>

        <link rel="stylesheet" media="screen" href="<?php k2info('scheme'); ?>" />

        <?php
    }
    ?>

    <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
    <link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
    <link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />

    <script src="<?php bloginfo('template_directory'); ?>/js/k2functions.js"></script>
    <script src="<?php bloginfo('template_directory'); ?>/js/prototype.js.php"></script>
    <script src="<?php bloginfo('template_directory'); ?>/js/effects.js.php"></script>
    <script src="<?php bloginfo('template_directory'); ?>/js/slider.js.php"></script>

    <!-- my additions -->
    <script src="<?php bloginfo('template_directory'); ?>/js/show-hide.js"></script>
    <script src="<?php bloginfo('template_directory'); ?>/js/external.js"></script>
    <!-- end -->

    <?php
    // Live Commenting
    if ((get_option('k2livecommenting') == 1)
        and ((is_page()
            or is_single()
        )
        and ('open' == $post-> comment_status)
        or ('comment' == $post-> comment_type) )
    ) {
        ?>

        <script src="<?php bloginfo('template_directory'); ?>/js/ajax_comments.js"></script>

        <?php
    }

    // LiveSearch
    if (get_option('k2livesearch') == 1) {
        ?>

        <script src="<?php bloginfo('template_directory'); ?>/js/livesearch.js.php"></script>

        <?php
    }

    // Rolling Archives
    if ((is_home()
        or is_paged())
        and (get_option('k2rollingarchives')) == 1
    ) {
        ?>

        <script src="<?php bloginfo('template_directory'); ?>/js/rollingarchives.js.php"></script>

        <?php
    }

    // Hide Comment Form
    if (!is_user_logged_in()
        and (is_page()
            or is_single()
        )
        and ($comment_author = $_COOKIE['comment_author_' . COOKIEHASH])
        and ('open' == $post->comment_status)
        or ('comment' == $post->comment_type)
    ) {
        ?>

        <script>
            Event.observe(window, 'load', HideUtils, false);
        </script>

        <?php
    }

    wp_get_archives('type=monthly&format=link');

    wp_head();
    ?>
</head>
<body class="k2<?php
    if (get_option('k2widthtype') == 0) {
        echo ' flex';
    }
    if (is_single()) {
        echo ' permalink';
    }
    ?>"<?php
    if (is_page()) {
        echo ' id="' . get_query_var('name') . '"';
    }
?>>
<div id="page">
    <div id="header">
        <a href="/"><?php bloginfo('name'); ?></a>
        <p class="description"><?php bloginfo('description'); ?></p>
    </div>

    <hr />
