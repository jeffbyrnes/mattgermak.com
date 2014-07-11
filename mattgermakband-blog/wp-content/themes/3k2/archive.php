<?php get_header(); ?>

<div class="content">

    <?php include(TEMPLATEPATH . '/leftbar.php'); ?>

    <div id="primary">

        <?php include (TEMPLATEPATH . '/theloop.php'); ?>

    </div>

    <?php get_sidebar(); ?>

</div>

<?php get_footer();
