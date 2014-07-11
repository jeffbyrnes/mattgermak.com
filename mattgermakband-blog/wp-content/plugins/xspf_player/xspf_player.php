<?php
/*
Plugin Name: Xspf_player
Plugin URI: http://www.boriel.com/?page_id=12
Description: Provides a quick and simple way to put MP3 soundtracks in your wordpress site. Music can be either local or remote (http). <br /> Uses free flash <a href="http://musicplayer.sourceforge.net">XSPF music player</a>.
Version: 3.2
Author: Jose Rodriguez (a.k.a. Boriel)
Author URI: http://www.boriel.com
*/

/*  Copyright 2005  Jose Rodriguez (a.k.a. Boriel)  (email: boriel@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


// Avoid being called directly
if (eregi(basename(__FILE__),$_SERVER['PHP_SELF'])) {
	require_once('xspf_player_class.php');
	echo '<html><body>';
	echo 'Direct module access forbidden. <br />';
	echo 'XSPF Player extension v', xspf_player::_version(), ' for WordPress by <a href="http://www.boriel.com">Boriel</a> <br />';
	echo '</body></html>';
    exit;
}

// Defines edit_insert_button if not exist
if(!function_exists('edit_insert_button')) {
	//edit_insert_button: Inserts a button into the editor
	function edit_insert_button($caption, $js_onclick, $title = '')
	{
		?>
		if(toolbar)
		{
			var theButton = document.createElement('input');
			theButton.type = 'button';
			theButton.value = '<?php echo $caption; ?>';
			theButton.onclick = <?php echo $js_onclick; ?>;
			theButton.className = 'ed_button';
			theButton.title = "<?php echo $title; ?>";
			theButton.id = "<?php echo "ed_{$caption}"; ?>";
			toolbar.appendChild(theButton);
		}
		<?php
	}
}


// main() 
// This class is a first stage load. It won't load remaining 

require_once('xspf_player_class.php');
$xspf = new xspf_player();  // Initialize the plugin (database, etc...)
                            // It's safe to do this more than once.

?>
