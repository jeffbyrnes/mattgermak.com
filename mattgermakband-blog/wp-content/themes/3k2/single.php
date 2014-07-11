<?php get_header(); ?>

<div class="content">

    <?php include(TEMPLATEPATH . '/leftbar.php'); ?>

    <div id="primary">

        <?php include (TEMPLATEPATH . '/theloop.php'); ?>
        <?php comments_template(); ?>

    </div>

    <?php get_sidebar(); ?>

</div>

<?php get_footer();
