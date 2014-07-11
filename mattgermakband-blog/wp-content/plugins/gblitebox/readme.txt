-------------------------------------------------------
LiteBox Plugin for Wordpress v1.0 by Gavin Barker
Downloaded from http://www.gavinbarker.co.uk/gblitebox
-------------------------------------------------------

Installation is simple and depends on where your plugin folder is located. In either case, see examples at the bottom on how to display your links in a LiteBox.

If your plugins folder is located at /wp-content/plugins/ then...

1) Upload the gblitebox folder to your Wordpress plugins folder
2) Activate the plugin from your Admin panel

If your plugins folder is NOT located at /wp-content/plugins/ then...

1) Open the /gblitebox/gblitebox_plugin.php file and edit the path to your plugin folder - it's easy to spot
2) Open the /gblitebox/js/litebox-1.0.js file and on lines 23 & 24 edit the path to the loading animation gif file and close file
3) Upload the gblitebox folder to your Wordpress plugins folder
4) Activate the plugin from your Admin panel



EXAMPLE USAGE

EXAMPLE ONE - SINGLE IMAGE USE
We simply need to put in a rel="lightbox" in your link. Also, the 'title' is what is displayed under the image
<a href="full.jpg" rel="lightbox" title="Your caption in here"><img src="thumb.jpg" width="50" height="50" alt="Usual alt text" /></a>


EXAMPLE TWO - AN IMAGE SET or GALLERY
We simply change the rel slightly to group images. Set this to anything you like but make it the same for all the images you want in a certain gallery. Our example uses [galname].

<a href="fullsize1.jpg" title="Caption for image 1" rel="lightbox[galname]"><img src="thumbnail1.jpg" alt="Picture 1" /></a>
<a href="fullsize2.jpg" title="Caption for image 2" rel="lightbox[galname]"><img src="thumbnail2.jpg" alt="Picture 2" /></a>
<a href="fullsize3.jpg" title="Caption for image 3" rel="lightbox[galname]"><img src="thumbnail3.jpg" alt="Picture 3" /></a>
<a href="fullsize4.jpg" title="Caption for image 4" rel="lightbox[galname]"><img src="thumbnail4.jpg" alt="Picture 4" /></a>


For more help on using LiteBox, please visit the authors site at http://www.doknowevil.net/litebox/

NOTE: This version will not work alongside our Thickbox v1.1 script.