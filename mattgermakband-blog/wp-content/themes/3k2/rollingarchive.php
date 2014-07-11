<?php $pagecount = ceil($wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish'") / get_settings('posts_per_page')); ?>

<div id="rollingarchives">
    <div id="rollnavigation">
        <a href="#" id="rollprevious"><span>&laquo;</span> <?php _e('Older','k2_domain'); ?></a>
        <a href="#" id="rollhome"><img src="<?php bloginfo('template_directory'); ?>/images/house.png" alt="Home" /></a>

        <div id="pagetrack"><div id="pagetrackend"><div id="pagehandle"></div></div></div>

        <script type="text/javascript" language="javascript">
        // <![CDATA[
            var PageSlider = new Control.Slider('pagehandle','pagetrack', {
                sliderValue: 1,
                range: $R(<?php echo $pagecount; ?>, 1),
                values: [<?php for ($i = 1; $i < $pagecount; $i++) {echo $i.", ";}; echo $i; ?>],
                onSlide: function(v) {$('rollpages').innerHTML = 'Page '+v+' of '+<?php echo $pagecount; ?>},
                onChange: function(v) {rollGotoPage(v)}
            });
        // ]]>
        </script>

        <span id="rollload"><?php _e('Loading','k2_domain'); ?></span>
        <span id="rollpages"></span>

        <a href="#" id="rollnext"><?php _e('Newer','k2_domain'); ?> <span>&raquo;</span></a>
    </div>

    <div id="rollnotices"></div>
</div>



<div id="rollingcontent"></div>
