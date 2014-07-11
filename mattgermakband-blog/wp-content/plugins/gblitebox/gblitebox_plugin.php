<?php
/*
Plugin Name: Litebox Plugin
Plugin URI: http://www.gavinbarker.co.uk/gblitebox
Description: <a href="http://www.doknowevil.net/litebox/">Litebox</a> is a modified version of Lightbox v2.0 created with one thing in mind, size reduction. Litebox utilizes the 3kb javascript library moo.fx in association with prototype.lite.
Version: 1.0
Author: Gavin Barker
Author URI: http://www.gavinbarker.co.uk
*/
//Function to write out the required liteboxbox files to pages
function insert_litebox_styles() {
/* litebox_path is used to tell the script where the plugin is installed to relative to your wordpress root.
If you change the plugin location for any reason then be sure to reflect the change in here to make it work!
*/
$litebox_path =  get_settings('siteurl')."/wp-content/plugins/gblitebox/";

$liteboxscript = "
<!-- begin litebox scripts -->
<link rel=\"stylesheet\" href=\"".$litebox_path."css/lightbox.css\" type=\"text/css\" media=\"screen\" />
<script type=\"text/javascript\" src=\"".$litebox_path."js/moo.fx.js\"></script>
<script type=\"text/javascript\" src=\"".$litebox_path."js/litebox-1.0.js\"></script>
<script type=\"text/javascript\">window.onload = initLightbox;</script>
<!-- end litebox scripts -->\n";
echo($liteboxscript);
}
/* Output $liteboxscript into header of pages: */
add_action('wp_head', 'insert_litebox_styles');
?>
