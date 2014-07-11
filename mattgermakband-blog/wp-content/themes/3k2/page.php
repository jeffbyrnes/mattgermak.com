<?php get_header(); ?>

<div class="content">

    <?php include(TEMPLATEPATH . '/leftbar.php'); ?>

    <div id="primary">

    <?php if (have_posts()) { while (have_posts()) { the_post(); ?>

        <div class="item">

            <div class="pagetitle">
                <!-- page titles are redundant ><h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title='<?php printf(__('Permanent Link to "%s"','k2_domain'), get_the_title()) ?>'><?php the_title(); ?></a></h2> -->
                <?php edit_post_link(__('Edit','k2_domain'), '<span class="editlink">','</span>'); ?>
            </div>

            <?php the_content(); ?>

            <?php link_pages('<p><strong>'.__('Pages:','k2_domain').'</strong> ', '</p>', __('number','k2_domain')); ?>

        </div>

        <?php } ?>
        <?php /* If there is nothing to loop */  } else { $notfound = '1'; /* So we can tell the sidebar what to do */ ?>

            <div class="center">
                <h2><?php _e('Not Found','k2_domain'); ?></h2>
            </div>

            <div class="item">
            <div class="itemtext">
                <p><?php _e('Oh no! You\'re looking for something which just isn\'t here! Fear not however, errors are to be expected, and luckily there are tools on the sidebar for you to use in your search for what you need.','k2_domain'); ?></p>
            </div>
            </div>

        <?php /* End Loop Init */ } ?>

    </div>

    <?php get_sidebar(); ?>

</div>

<?php get_footer();
