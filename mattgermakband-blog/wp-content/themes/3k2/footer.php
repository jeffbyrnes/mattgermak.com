    <br class="clear" />
</div> <!-- Close Page -->

<hr />

<div id="footer">
    <small>
        © 2006–<?php
            echo date('Y') . " ";
            bloginfo('name');
        ?><br />

        <a style="padding: 0px" href="http://www.davereederdesign.com/work/web/thegig/" title="The Gig template" rel="external">The Gig</a> + <a href="http://aydin.net/blog/2006/03/23/three-column-k2-theme-for-wordpress-3k2/" title="3K2" rel="external">3K2 <?php if (function_exists('k2info')) { k2info(version); } ?></a> modified by <a href="http://thejeffbyrnes.com/" rel="external">Jeff Byrnes</a><br />

        <!-- <?php echo $wpdb->num_queries; ?> queries. <?php timer_stop(1); ?> seconds. -->
    </small>
</div>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-43360528-1', 'mattgermak.com');
  ga('send', 'pageview');

</script>

<?php wp_footer(); ?>

</body>
</html>
