<?php /*
    Template Name: Archives (Do Not Use Manually)
*/ ?>

<?php /* Counts the posts, comments and categories on your blog */
    $numposts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish'");
    if (0 < $numposts) $numposts = number_format($numposts);

    $numcomms = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1'");
    if (0 < $numcomms) $numcomms = number_format($numcomms);

    $numcats = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->terms");
    if (0 < $numcats) $numcats = number_format($numcats);
?>


<?php get_header(); ?>

<div class="content">

    <?php include(TEMPLATEPATH . '/leftbar.php'); ?>

    <div id="primary">

        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <div class="item">

                <div class="pagetitle">
                    <h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title='<?php printf( __('Permanent Link to "%s"','k2_domain'), get_the_title()) ?>'><?php the_title(); ?></a></h2>
                    <?php edit_post_link(__('Edit','k2_domain'), '<span class="editlink">','</span>'); ?>
                </div>

                <div class="itemtext">

                    <p><?php printf( __('This is the frontpage of the %1$s archives. Currently the archives are spanning %2$s posts and %3$s comments, contained within the meager confines of %4$s categories. Through here, you will be able to move down into the archives by way of time or category. If you are looking for something specific, perhaps you should try the search on the sidebar.','k2_domain'), get_bloginfo('name'), $numposts, $numcomms, $numcats) ?></p>

                    <?php if (function_exists('af_ela_super_archive')) { ?>

                    <h3><?php _e('Live Archives','k2_domain'); ?></h3>
                    <p><?php printf( __('This is a \'live archive\', which allows you to \'dig\' into the %s repository in a fast an efficient way, without having to reload this page as you explore.','k2_domain'), get_bloginfo('name')) ?> </p>

                    <div id="livearchives">
                        <?php af_ela_super_archive('num_posts_by_cat=50&truncate_title_length=40&hide_pingbacks_and_trackbacks=1&num_entries=1&num_comments=1&number_text=<span>%</span>&comment_text=<span>%</span>&selected_text='.urlencode('')); ?>
                        <div style="clear: both;"></div>
                    </div>

                    <?php } ?>

                    <?php if (function_exists('UTW_ShowWeightedTagSetAlphabetical')) { ?>

                    <h3><?php _e('Tag Cloud','k2_domain'); ?></h3>
                    <p><?php printf( __('The following is a list of the tags used at %s, colored and \'weighed\' in relation to their relative usage.','k2_domain'), get_bloginfo('name')) ?></p>

                    <?php UTW_ShowWeightedTagSetAlphabetical("coloredsizedtagcloud"); } ?>

                </div>

            </div>

        <?php endwhile; endif; ?>

    </div>

    <hr />

    <div class="<?php /* Is Flexible Width Enabled? */ if (get_option('k2widthtype') == 0) echo flex; ?> secondary">

        <div class="sb-search"><h2><?php _e('Search','k2_domain'); ?></h2>
            <?php include (TEMPLATEPATH . '/searchform.php'); ?>
        </div>


        <div class="sb-about"><h2><?php _e('About','k2_domain'); ?></h2>
            <p><?php printf( __('You are currently at the root of the %s archives. From here there are several options for burrowing into the archives.','k2_domain'), get_bloginfo('name')) ?></p>
        </div>

        <div class="sb-latest"><h2><?php _e('Latest','k2_domain'); ?></h2>
            <ul>
                <?php wp_get_archives('type=postbypost&limit=10'); ?>
            </ul>
        </div>

        <div class="sb-months"><h2><?php _e('Archives','k2_domain'); ?></h2>
            <ul>
                <?php wp_get_archives('type=monthly'); ?>
            </ul>
        </div>


        <div class="sb-categories"><h2><?php _e('Categories','k2_domain'); ?></h2>
            <ul>
                <?php list_cats(0, '', 'name', 'asc', '', 1, 0, 1, 1, 1, 1, 0,'','','','','') ?>
            </ul>
        </div>

    </div>

    <?php // get_sidebar(); ?>

</div>

<?php get_footer();
