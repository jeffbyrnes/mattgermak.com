<?php
/**
 * This is the loop, which fetches entries from your database. It is used in some
 * form on most of the K2 pages. Because of that, to make editing everything in one go
 * easier, it has been placed in its own file, which is then included where needed.
 */

// Prepare Rolling Archives
if ($_GET["rollingarchives"] == '1'
    or $_GET["livesearch"] == '1'
) {
    require("../../../wp-blog-header.php");
}

// Prepare Loop
if (have_posts()) {
    // Headlines for archives
    if (!is_single()
        && !is_home()
        or is_paged()
    ) {
        ?>

    <div class="pagetitle">
        <h2>
        <?php
        if (is_category()) {
            if (the_category_ID(false) != $k2asidescategory) {
                printf(
                    __('Archive for the \'%s\' Category', 'k2_domain'),
                    single_cat_title('', false)
                );
                ?> <?php
            } else {
                echo single_cat_title();
            }
        } elseif (is_day()) {
            printf(
                __('Archive for %s', 'k2_domain'),
                get_the_time(__('F jS, Y', 'k2_domain'))
            );
        } elseif (is_month()) {
            printf(
                __('Archive for %s', 'k2_domain'),
                get_the_time(__('F, Y', 'k2_domain'))
            );
        } elseif (is_year()) {
            printf(
                __('Archive for %s', 'k2_domain'),
                get_the_time(__('Y', 'k2_domain'))
            );
        } elseif (is_search()) {
            printf(
                __('Search Results for \'%s\'', 'k2_domain'),
                $s
            );
        } elseif (is_author()) {
            $post = $wp_query->post;

            $the_author = $wpdb->get_var(
                "SELECT meta_value
                FROM $wpdb->usermeta
                WHERE user_id = '{$post->post_author}'
                AND meta_key  = 'nickname'"
            );

            printf(
                __('Author Archive for %s', 'k2_domain'),
                $the_author
            );
        } elseif (is_paged()) {
            printf(
                __('Archive Page %s', 'k2_domain'),
                $paged
            );
        } ?>
        </h2>
    </div>

        <?php
    }

    if (get_option('k2rollingarchives') == 1
        and $_GET["rollingarchives"] != 1
        and $_GET["livesearch"] != 1
        and is_home()
    ) {
        include (TEMPLATEPATH . '/rollingarchive.php');
    }

    if (get_option('k2rollingarchives') == 0
        and !is_single()
        and !is_home()
        and is_paged()
    ) {
        include (TEMPLATEPATH . '/navigation.php');
    }

    if ($_GET["rollingarchives"] != '1'
        and $_GET["livesearch"] != '1'
    ) {
        ?>

        <div id="dynamiccontent"></div>

        <?php
    }
    ?>

    <div id="primarycontent">
        <div>
            <h2>Music player</h2>
            <span class="chronodata">Last Updated: 2006.06.09</span>

            <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="400" height="15">
                <param name="movie" value="/xspf_player_slim.swf?playlist_url=/xspf%5Fplaylist%2Exspf&amp;autoload=true" />
                <param name="quality" value="high" />
                <param name="bgcolor" value="#e6e6e6" />

                <!--[if !IE]> <-->
                    <object data="/xspf_player_slim.swf?playlist_url=/xspf%5Fplaylist%2Exspf&amp;autoload=true" width="400" height="15" type="application/x-shockwave-flash">
                        <param name="quality" value="high" />
                        <param name="bgcolor" value="#e6e6e6" />
                        <param name="pluginurl" value="http://www.macromedia.com/go/getflashplayer" />
                        If you're seeing this, you need to install the latest Flash Player.
                    </object>
                <!--> <![endif]-->
            </object>

    <?php
    // Start The Loop
    while (have_posts()) {
        the_post();

        /* Permalink navigation has to be inside the loop */
        if (is_single()) {
            include (TEMPLATEPATH . '/navigation.php');
        }

        /* Asides -- Pick a category to be an 'aside' in the K2 options panel */
        /* On archive pages, show asides inline no matter what */
        if (is_archive()
            or is_search()
            or is_single()
        ) {
            $k2asidescheck = '0';
        } else {
            $k2asidescheck = get_option('k2asidesposition');
        }

        $k2asides = get_option('k2asidescategory');
        ?>

        <div id="post-<?php the_ID(); ?>" class="item entry">
            <div class="itemhead">
                <h3><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h3>

                <small class="metadata">
                    <span class="chronodata">Published <?php

                        // If 'Dunstan's Time Since' plugin is installed use it;
                        // else use default.
                        if (function_exists('time_since')) {
                            echo time_since(
                                abs(strtotime($post->post_date_gmt . " GMT")),
                                time());
                                gt;
                                ?> ago<?php
                        } else {
                            the_time('F jS, Y');
                        }

                        edit_post_link(
                            __('Edit', 'k2_domain'),
                            '<span class="editlink">','</span>'
                        );
                    ?>
                </small>
            </div>

            <div class="itemtext">
                <?php
                if (is_archive()
                    or is_search()
                ) {
                    the_excerpt();
                } else {
                    the_content(__('Continue reading', 'k2_domain') . " '" . the_title('', '', false) . "'");
                }

                link_pages(
                    '<p><strong>' . __('Pages:', 'k2_domain') . '</strong> ',
                    '</p>',
                    __('number', 'k2_domain')
                );
                ?>
            </div>
        </div>

        <?php
    } /* End The Loop */ ?>

    </div></div>

    <?php
    /* Insert Paged Navigation */
    if (!is_single()
        && get_option('k2rollingarchives') != 1
    ) {
        include (TEMPLATEPATH . '/navigation.php');
    }
    ?>

    <?php
    // If there is nothing to loop
} else {
    $notfound = '1';
    // So we can tell the sidebar what to do
    ?>

        <div class="center">
            <h2><?php _e('Not Found', 'k2_domain'); ?></h2>
        </div>

        <div class="item">
            <div class="itemtext">
                <p><?php _e('Oh no! You\'re looking for something which just isn\'t here! Fear not however, errors are to be expected, and luckily there are tools on the sidebar for you to use in your search for what you need.','k2_domain'); ?></p>
            </div>
        </div>

    <?php
    // End Loop Init
}
