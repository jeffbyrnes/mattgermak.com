<?php
/*  Copyright 2005  Jose Rodriguez (a.k.a. Boriel)  (email: boriel@gmail.com)
	
	CLASS definition for XSPF PLAYER

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


// XSPF Player class 
// Defines a local xspf_ namespace

if (!class_exists('xspf_player')):
class xspf_player {
	// -----------------------------------------------------------------------------------------------------
	// -- CONFIGURATION --
	// You can change this make some hacks on this plugin (usually not needed)

	// -----------------------------------------------------------------------------------------------------
	// xspf Main table name
	// -----------------------------------------------------------------------------------------------------
	function table() {
		$xspf_main_table = 'xspf_player';
		global $table_prefix;
		return $table_prefix . $xspf_main_table;
	}
	
	// -----------------------------------------------------------------------------------------------------
	// Returns database categories table name
	// -----------------------------------------------------------------------------------------------------
	function table_categories() {
		$xspf_table_categories = 'xspf_player_categories';
		global $table_prefix;
		return $table_prefix . $xspf_table_categories;
	}
	
	// -----------------------------------------------------------------------------------------------------
	// Returns database categories <--> tracks table name
	// -----------------------------------------------------------------------------------------------------
	function table_cat_rel() {
		$xspf_table_tracks_categories = 'xspf_player_tracks_categories';
		global $table_prefix;
		return $table_prefix . $xspf_table_tracks_categories;
	}
	
	// -- CONFIGURATION END --
	// You should not edit anything beyond this line
	// -----------------------------------------------------------------------------------------------------

	// -----------------------------------------------------------------------------------------------------
	// Returns xspf mayor and minor version
	// -----------------------------------------------------------------------------------------------------
	function _version() {
		return '3.2';
	}
	
	// -----------------------------------------------------------------------------------------------------
	// Returns whether versionA is less (<0), greater (>0) or equal (0) to versionB
	// -----------------------------------------------------------------------------------------------------
	function cmp_version($versionA, $versionB) {
		return strcmp($versionA, $versionB);  // This is enough
	}

	// -----------------------------------------------------------------------------------------------------
	// This defines the xspf player tag used for embeding the player into the post body
	// -----------------------------------------------------------------------------------------------------
	function tag() {
		return 'xspf';
	}

	// -----------------------------------------------------------------------------------------------------
	// Capture <xspf> tag and code and returns an array of chunks of code sorrounded by these tags
	// -----------------------------------------------------------------------------------------------------
	function tag_split($text) {
		$XSPF = xspf_player::tag();
		$ER = "(<$XSPF>.*<\\/$XSPF>|\[$XSPF\].*\[\\/$XSPF\])";

		return preg_split("/$ER/Us", $text, -1, PREG_SPLIT_DELIM_CAPTURE); // capture the tags as well as in between
	}

	// -----------------------------------------------------------------------------------------------------
	// Strips tags and retuns only the string in between (in $code). $result returns whether it matched or
	// not.
	// -----------------------------------------------------------------------------------------------------
	function tag_match($text, &$code) {
		$XSPF = xspf_player::tag();
		$ER = "[<\\[]" . $XSPF . "[>\\]](.*)[<\\[]\\/" .$XSPF . "[>\\]]";
		$result = preg_match("/^$ER/Us", $text, $code); // If it's a phpcode

		return $result;
	}

	// -----------------------------------------------------------------------------------------------------
	// Removes escaped characters \' => ' , \" => ", \\ => \
	// -----------------------------------------------------------------------------------------------------
	function unescape($str) {
		return str_replace(array('\\\\', "\\'", '\\"'), array('\\', "'", '"'), $str);
	}
	
	// -----------------------------------------------------------------------------------------------------
	// Output all what cames into header here
	// -----------------------------------------------------------------------------------------------------
	function header($str) {
	?>
		<script type="text/javascript">
			<!--
			function MP_ResizeContainer(id,w,h){
				var container = document.getElementById(id)
				container.style.width = w+"px"
				container.style.height = h+"px"
			}	
			-->
		</script>
	<?php
	}	

	// -----------------------------------------------------------------------------------------------------
	//  Saves statically the value of arr, or returns it if arr is FALSE
	// -----------------------------------------------------------------------------------------------------	
	function saver($arr = FALSE) {
		static $res = array();
		
		if ($arr != FALSE)
			$res = $arr;
		
		return $res;
	}

	// -----------------------------------------------------------------------------------------------------
	// Constructor. 
	// This should be called when the plugin is installed.
	// It will create the wp_xspf_tracks table into the wp database
	// -----------------------------------------------------------------------------------------------------
	function xspf_player() {
		global $wpdb;
		global $table_prefix;
	
		add_action('admin_menu', array('xspf_player', 'add_pages'));
		add_filter('admin_head', array('xspf_player', 'header')); // Adds the mode 4 menu for preview
		add_filter('wp_head', array('xspf_player', 'header'));	// Adds the mode 4 menu for preview
	
		add_filter('admin_footer', array('xspf_player', 'callback_xspf')); // Inserts XSPF button
		
		add_filter('the_content', array('xspf_player', 'inline_insert'), 2);   // Allows xspf_player inline insertion
		add_filter('the_content', array('xspf_player', 'inline_insert_after'), 100); // Allows xspf_player inline insertion		
		
		add_filter('content_save_pre', array('xspf_player', 'inline_insert_edit_before'), 2); // Hides xspf_call before wp balancing tags
		add_filter('content_save_pre', array('xspf_player', 'inline_insert_edit_after'), 100); // Recovers xspf_call before saving

		if (($version = get_option('xspf_player_version')) == '') {
			$version = '0.0';
		}

		if (xspf_player::cmp_version(xspf_player::_version(), $version) <= 0) { // already updated to current version
			return;
		}

        if (!xspf_player::cmp_version($version, '0.0')) { // If is first time, clear database
            $query = 'DROP TABLE IF EXISTS `' . xspf_player::table_cat_rel() . '`';
            $wpdb->query($query);
            $query = 'DROP TABLE IF EXISTS `' . xspf_player::table_categories() . '`';
            $wpdb->query($query);
            $query = 'DROP TABLE IF EXISTS `' . xspf_player::table() . '`';
            $wpdb->query($query);
        }

		// create table if not done already
		$query = 'CREATE TABLE IF NOT EXISTS `' . xspf_player::table() . "` (`id` int(11) NOT NULL auto_increment, " 
			. "`artist` tinytext, `title` tinytext, `url` varchar(128) UNIQUE NOT NULL, `imageurl` tinytext, `infourl` tinytext, PRIMARY KEY(`id`))";
		$wpdb->query($query);
		
		$query = 'CREATE TABLE IF NOT EXISTS `' . xspf_player::table_categories() . "`(`id` int(11) NOT NULL auto_increment, `name` varchar(64) UNIQUE NOT NULL, "
			. "`description` varchar(128) DEFAULT '', PRIMARY KEY(`id`))";
		$wpdb->query($query);
		
		$query = 'CREATE TABLE IF NOT EXISTS `' . xspf_player::table_cat_rel() . '` (`idtrack` int(11) NOT NULL, `idcat` int(11) NOT NULL, UNIQUE (idtrack, idcat), '
			. 'FOREIGN KEY (`idtrack`) REFERENCES `' . xspf_player::table() . '`(`id`) ON DELETE CASCADE ON UPDATE CASCADE, '
			. 'FOREIGN KEY (`idcat`) REFERENCES `' . xspf_player::table_categories() . '`(`id`) ON DELETE CASCADE ON UPDATE CASCADE)';
		$wpdb->query($query);
			
		// Updates to version 1.3
		if (xspf_player::cmp_version($version, '1.3') < 0) { // Need to update database to 1.3?
			$wpdb->query('ALTER TABLE `' . xspf_player::table_categories() . '` ADD COLUMN `random` INT(1)');
		}
		
		// Updates to version 1.7
		if (xspf_player::cmp_version($version, '1.7') < 0) { // Need to update database to 1.7?
			$wpdb->query('ALTER TABLE `' . xspf_player::table_categories() . '` ADD COLUMN `autostart` INT(1)');
			$wpdb->query('ALTER TABLE `' . xspf_player::table_categories() . '` ADD COLUMN `order` TEXT');
		}
								
		update_option('xspf_player_version', xspf_player::_version()); // Store current version
	}
		

	// -----------------------------------------------------------------------------------------------------
	// This will insert the button "XSPF" whenever the Edit Post window is displayed
	// -----------------------------------------------------------------------------------------------------		
	function callback_xspf()
	{
		global $userdata;
		get_currentuserinfo();
		//if (!isset($userdata->user_level) || ($userdata->user_level < 9)) return; // Must be admin to use this
	
		$XSPF = xspf_player::tag();
		$tag = "[$XSPF]_start()[/$XSPF]";
		$plugin_url = get_settings('siteurl') . '/wp-content/' . basename(__FILE__, ".php") . '/';
		$url = $_SERVER['REQUEST_URI'];
		if(strpos($url, 'post.php') || 
		   strpos($url, 'page-new.php') ||
		   strpos($url, 'post-new.php')) // New for WP 2.1
		{
	?>
	<script language="JavaScript" type="text/javascript"><!--
	var toolbar = document.getElementById("ed_toolbar");
	<?php
			edit_insert_button("Xspf", "insert_tag", "Xspf Player");
	?>

	function insert_tag()
	{
		var edCanvas = document.getElementById("content");
		edInsertContent(edCanvas, '<?php echo $tag; ?>');
	}

	//--></script>

	<?php
		}
	}

	
	// -----------------------------------------------------------------------------------------------------
	// Shows a song data edition form body (used either for adding or updating song data)
	// Only form body (neither <form> nor <submit> tags.
	// -----------------------------------------------------------------------------------------------------
	function song_form_body($trackid = FALSE) {
		global $wpdb;
		
		if ($trackid === FALSE) {
			$artist = $title = $imageurl =  $url = $infourl = '';
		} else {
			$row = $wpdb->get_row('SELECT * FROM ' . xspf_player::table() . " WHERE id = $trackid");
			foreach ($row as $key => $value) {  // Expands SQL result to variables
				$$key = $value;
			}
		}
        ?>
	 <table width="100%" cellspacing="2" cellpadding="5" class="editform">
	  <tr>
	   <th>MP3 Track URL:</th>
	   <td>
		<input type="text" name="url" size="64" value="<?php echo $url; ?>" /> <br />
					Enter the absolute URL of the MP3 file. <strong>Required.</strong>
	   </td>
	  </tr>
	  <tr>
	   <th>Artist:</th>
	   <td>
			<input type="text" name="artist" value="<?php echo $artist; ?>" /> <br />
			Artist or Group name.
	   </td>
	  </tr>
	  <tr>
	   <th>Title:</th>
	   <td>
			<input type="text" name="title" value="<?php echo $title; ?>" /> <br />
			Track Title.
	   </td>
	  </tr>
	  <tr>
	   <th>Image URL:</th>
	   <td>
		   <input type="text" name="imageurl" size="64" value="<?php echo $imageurl; ?>" /> <br />
		   Little thumbnail image-like url (e.g. cd-cover). See XSPF player web.
	   </td>
	  </tr>
	  <tr>
	   <th>Artist URL:</th>
	   <td>
		   <input type="text" name="infourl" size="64" value="<?php echo $infourl; ?>" /> <br />
		   Artist or track info URL.
	   </td>
	  </tr> <?php
        if ($trackid != FALSE) {
            printf("<input type=\"hidden\" name=\"idtrack\" value=\"$trackid\" />\n");
        } ?>
	 </table>
	<?php
	}
	
	
	// -----------------------------------------------------------------------------------------------------
	// Shows song data table for edition. This function allows offset and limit for pagination.
	// -----------------------------------------------------------------------------------------------------
	function show_songs_table($from = '0', $limit = '10', $idtrack = FALSE) {
		global $wpdb;
					
		$numtracks = $wpdb->get_var("SELECT COUNT(*) FROM `" . xspf_player::table());
		$totalpages = ceil($numtracks / $limit);
		$pagenum = ceil(($from + 1) / $limit);
		$numrows = $wpdb->query("SELECT * FROM `" . xspf_player::table() . "` LIMIT $from, $limit");
		if ($numrows) {
		?>
		<fieldset id="tracks_div">
        <legend>Track List</legend>
		 <table><tr><td width="10%">
		<?php
		   if ($totalpages > 1)
				echo __('Page'), ' ', $pagenum, ' ', __('of'), ' ', $totalpages; ?>
			&nbsp;&nbsp;
			</td>
		<?php
			if ($from > 0) { ?><td>
		<form action="" name="previous_page" method="post">
		 <input type="hidden" name="xspf_mgmt" value="gotopage" />
		 <input type="hidden" name="offset" value="<?php if ($from > $limit) {
		 													echo $from - $limit;
														 } 	else echo 0; ?>" />
		 <input class="submit" type="submit" name="action" value="&laquo; <?php _e('Back'); ?>" />
		</form></td>
		<?php
		    } else
				echo "<td>&nbsp;</td>\n";
			
			if ($from + $limit < $numtracks) { ?><td>
		<form action="" name="next_page" method="post">
		 <input type="hidden" name="xspf_mgmt" value="gotopage" />
		 <input type="hidden" name="offset" value="<?php if ($from + $limit < $numtracks) {
		 													echo $from + $limit;
														 } 	else echo $numtracks - $limit; ?>" />		 
		 <input class="submit" type="submit" name="action" value="<?php _e('Next'); ?> &raquo;" />
		</form></td>
        <?php
		    } else
				echo "<td>&nbsp;</td>\n";
	    ?>
		  <td width="100%" align="right">
		<form action="" name="switch_mode" method="post">
		 <input type="hidden" name="xspf_mgmt" value="switch_to_cats" />
		 <input class="submit" type="submit" name="action" value="<?php _e('Edit categories'); ?> &raquo;" />
		</form>
		  </td>
		 </tr>
		</table>
		<table width="100%" cellpadding="3" cellspacing="3"> 
		 <tr>
		  <th scope="col">ID</th>
		  <th scope="col">Title</th>
		  <th scope="col">Author</th>
		  <th scope="col">Image</th>
		  <th></th>
		  <th></th>
		 </tr>
		<?php 
			$results = $wpdb->get_results("SELECT * FROM `" . xspf_player::table() . "` LIMIT $from, $limit", ARRAY_A);
			foreach ($results as $row) { ?>
				<tr class="alternate" <?php if ($idtrack == $row['id']) echo 'style="background-color: #FFAA88"'; ?>>
				 <th scope="row">
				   <?php echo $row['id']; ?>
				 </th>
				 <td>
				  <a href="<?php echo $row['url']; ?>"><?php 
				  			if ($row['title'] != '') { 
								echo $row['title'];
							} else {
								echo $row['url'];
							} ?></a>
				 </td>
				 <td>
				 <?php if ($row['infourl'] != '') {
							printf('<a href="%s">%s</a>', $row['infourl'], $row['artist']);
					   } else {
							print $row['artist'];
					   }
				 ?>
				 </td>
				 <td>
				 <?php if ($row['imageurl'] != '') {
							printf('<img align="center" src="%s" width="20" height="20" />', $row['imageurl']);
					   }				 	
				 ?>
				 </td>
				 <td>
				  <form name="track_<?php echo $row['id'];?>" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" />
					 <input type="hidden" name="idtrack" value="<?php echo $row['id']; ?>" />
					 <input type="hidden" name="url" value="<?php echo $row['url'];?>" />
					 <input type="hidden" name="artist" value="<?php echo $row['artist'];?>" />
					 <input type="hidden" name="title" value="<?php echo $row['title'];?>" />
					 <input type="hidden" name="imageurl" value="<?php echo $row['imageurl'];?>" />
					 <input type="hidden" name="infourl" value="<?php echo $row['infourl'];?>" />
					 <input type="hidden" name="xspf_mgmt" value="edit" />
					 <input type="hidden" name="offset" value="<?php echo $from; ?>" />
				     <input type="submit" name="action" value="<?php _e('Edit'); ?>" />
				  </form>
				 </td>
				 <td>
				  <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" />
					 <input type="hidden" name="idtrack" value="<?php echo $row['id']; ?>" />				   
					 <input type="hidden" name="xspf_mgmt" value="delete" />					 
				     <input type="submit" name="action" value="<?php _e('Delete'); ?>" 
					onclick="return confirm('You are about to delete this track info (you must remove the file manually)!\n  \'OK\' to delete, \'Cancel\' to stop.')" />
				 </form>					
				 </td>
				</tr>
		<?php
			} // foreach
		?>
		</table>
		</fieldset>
		<?php
		}  // if ($numrows)
	} // private function show_songs_table
	

	// -----------------------------------------------------------------------------------------------------
	// Show categories table for management
	// -----------------------------------------------------------------------------------------------------
	function show_categories_table() {
		global $wpdb;
		
		?>
		<fieldset id="categories_div">
         <legend>Categories List</legend>
		<div align="right">
		<form action="" name="switch_mode" method="post">
		 <input type="hidden" name="xspf_mgmt" value="switch_to_tracks" />
		 <input class="submit" type="submit" name="action" value="<?php _e('Edit Tracks'); ?> &raquo;" />
		</form>
		</div>
		<?php
		
		$query = 'SELECT * FROM `' . xspf_player::table_categories() . '` ORDER BY `name` ASC';
		if ($wpdb->query($query)) { // There's at less 1 category
			?><div class="wrap" style="overflow: auto"><?php
			$result = $wpdb->get_results(NULL, ARRAY_A);
		?><script type="text/javascript">
		<!--
		
		function getCatFormElemId(name) {
				 for (var e = 0; e < document.forms.length; e++)
				     for (var i = 0; i < document.forms[e].elements.length; i++)
						if (document.forms[e].elements[i].name == name)
							return i;
						
				 return -1;
			  }

		function randCheck(formname, name, value) {
		   e = getCatFormElemId(name);
		   document.forms[formname].elements[e].disabled = value;
		   
		   if (value)
		   		document.forms[formname].elements[e].style.color = '#AAAAAA';
		   else
		   		document.forms[formname].elements[e].style.color = '#000000';
		}
		
		function setOrderVal(formname) {
			document.forms[formname].elements['xspf_mgmt'].value = 'order';
		}
		-->
		  </script>
		  <table width="100%" cellspacing="2" cellpadding="2" class="editform">
           <tr>
            <th>Id</th><th>Name</th><th>Order</th><th>Shuffle</th><th>Autostart</th><th>Description</th><th></th><th></th>
           </tr>
            <?php
			foreach ($result as $row) {?>
				<tr class="alternate">
				   <th align="right"><?php echo $row['id']; ?>&nbsp;</th>
 			       <form action="" name="updatecat_<?php echo $row['id']?>" method="post">								   				   
				    <td align="right">
				       <input type="hidden" name="idcat" value="<?php echo $row['id']; ?>" />   
				       <input type="text" value="<?php echo $row['name']; ?>" name="cat_name" />
					</td>
					<td align="center"><input type="submit" name="action_<?php echo $row['id']; ?>" value="<?php _e('Order');?>" 
					    onClick="setOrderVal('updatecat_<?php echo $row['id']; ?>')" /></td>
				    <td align="center"><input type="checkbox" value="1" <?php 
				   					if ($row['random'] == '1') { 
				   						echo 'checked="checked"';
									}?> name="rand" onClick="randCheck('updatecat_<?php echo $row['id']; ?>', 'action_<?php echo $row['id']; ?>', this.checked)" /></td>
					<td align="center"><input type="checkbox" value="1" <?php 
				   					if ($row['autostart'] == '1') { 
				   						echo 'checked="checked"';
									}?> name="autostart" /></td>
				    <td align="center"><input type="text" size="64" value="<?php echo $row['description']; ?>" name="desc" /></td>
				    <td><input type="submit" value="<?php _e('Update'); ?>" name="action" />
					   <input type="hidden" name="xspf_mgmt" value="update_cat" />				   
				       </form>
				    </td>
				    <td>
				   	 <form action="" name="deletecat" method="post">
				      <input type="hidden" name="idcat" value="<?php echo $row['id']; ?>" />
					  <input type="hidden" name="xspf_mgmt" value="delete_cat" />
				      <input type="submit" value="<?php _e('Delete'); ?>" name="action" onClick="return confirm('You are about to delete this category.\n  \'OK\' to delete, \'Cancel\' to stop.')" />
				     </form>				   				   
				    </td>
				</tr>
		<?php
			}
			print "</table>\n</div>\n</fieldset>\n";
		}
		?>
		<form action="" method="post">
            <table>
              <tr>
               <td><input type="text" name="new_cat" size="30" /></td>
               <td class="submit">
			    <input type="submit" class="submit" name="action" value="<?php _e('Add Category'); ?>&raquo;" />
			   </td>
              </tr>
			 <input type="hidden" name="xspf_mgmt" value="add_category" />
			</table>
		</form>
		<?php
	}

	
	// -----------------------------------------------------------------------------------------------------
	// Add options into management menus
	// -----------------------------------------------------------------------------------------------------
	function add_pages() {
		add_management_page('XSPF Player Plugin Management', 'XSPF Player', 8, __FILE__, array('xspf_player', 'management'));
		add_options_page('XSPF Player Plugin Options', 'XSPF Player', 8, __FILE__, array('xspf_player', 'options'));
	}

	// -----------------------------------------------------------------------------------------------------
	// Returns servername according to worpress URI
	// -----------------------------------------------------------------------------------------------------
	function server_name() {
		$urlinfo = parse_url(get_option('siteurl'));
		return $urlinfo['host'];
	}

	// -----------------------------------------------------------------------------------------------------
	// Shows plugin options
	// -----------------------------------------------------------------------------------------------------
	function options() {
		global $wpdb;
		
		if ($_POST['xspf_setup'] === 'sent') {  // Process pending SETUP information
			update_option('xspf_player_url', $wpdb->escape($_POST['player_url']));
			update_option('xspf_player_autostart', $wpdb->escape($_POST['player_autostart']));
			update_option('xspf_player_repeat', $wpdb->escape($_POST['player_repeat']));
			update_option('xspf_player_width', $wpdb->escape($_POST['width']));
			update_option('xspf_player_height', $wpdb->escape($_POST['height']));
			update_option('xspf_player_mode', $wpdb->escape($_POST['player_mode']));
			update_option('xspf_player_bgcolor', $wpdb->escape($_POST['bgcolor']));
			update_option('xspf_player_fgcolor', $wpdb->escape($_POST['fgcolor']));			
		}
		
		//$uri = str_replace('&updated=true', '', $_SERVER['REQUEST_URI']);
		$uri = get_option('siteurl') . '/wp-admin/options-general.php?page=xspf_player/xspf_player_class.php';
		$__base_url = $img_url = get_option('siteurl') . '/wp-content/plugins/xspf_player/';
		$__url = $__base_url . 'xspf_player_slim.swf';	
		if (($_mode = get_option('xspf_player_mode')) == '') {
			$_mode = '2'; // Default Player Mode
		}
		
		$mode1 = ($_mode == '1')? 'selected': '';
		$mode2 = ($_mode == '2')? 'selected': '';		
		$mode3 = ($_mode == '3')? 'selected': '';
		$mode4 = ($_mode == '4')? 'selected': '';		
	?>	   
	   <div class="wrap">
		<h2>XSPF Player plugin Setup</h2>
		<script type="text/javascript">
		 <!--
		 function setPlayerMode(mode) {
			document.forms['xspf_prefs'].elements['player_url'].disabled = true;					
			document.forms['xspf_prefs'].elements['player_url'].style.color = '#999999';
		 
		    switch(mode) {
			    case '0':
				case 0:
					document.forms['xspf_prefs'].elements['width'].disabled = false;
					document.forms['xspf_prefs'].elements['height'].disabled = false;
					document.forms['xspf_prefs'].elements['player_url'].disabled = false;					
					document.forms['xspf_prefs'].elements['player_url'].style.color = '#000000';				
				break;
				
				case '1':
				case 1:
					document.forms['xspf_prefs'].elements['player_url'].value = '<?php 
						echo $img_url, 'musicplayer.swf'; ?>';
					document.forms['xspf_prefs'].elements['width'].disabled = true;
					document.forms['xspf_prefs'].elements['width'].style.color = '#999999';					
					document.forms['xspf_prefs'].elements['height'].disabled = true;
					document.forms['xspf_prefs'].elements['height'].style.color = '#999999';
				break;
				
				case '2':
				case 2:
					document.forms['xspf_prefs'].elements['player_url'].value = '<?php 
						echo $img_url, 'xspf_player_slim.swf'; ?>';
					document.forms['xspf_prefs'].elements['width'].disabled = false;
					document.forms['xspf_prefs'].elements['width'].style.color = '#000000';					
					document.forms['xspf_prefs'].elements['height'].disabled = true;
					document.forms['xspf_prefs'].elements['height'].style.color = '#999999';
				break;

				case '3':
				case 3:
					document.forms['xspf_prefs'].elements['player_url'].value = '<?php 
						echo $img_url, 'xspf_player.swf'; ?>';
					document.forms['xspf_prefs'].elements['width'].disabled = false;
					document.forms['xspf_prefs'].elements['height'].disabled = false;
					document.forms['xspf_prefs'].elements['width'].style.color = '#000000';					
					document.forms['xspf_prefs'].elements['height'].style.color = '#000000';				
				break;
				
				case '4':
				case 4:
					document.forms['xspf_prefs'].elements['player_url'].value = '<?php 
						echo $img_url, 'musicplayer_menu_f6.swf'; ?>';
					document.forms['xspf_prefs'].elements['width'].disabled = false;
					document.forms['xspf_prefs'].elements['height'].disabled = false;
					document.forms['xspf_prefs'].elements['width'].style.color = '#000000';					
					document.forms['xspf_prefs'].elements['height'].style.color = '#000000';				
				break;				
			}
		}
		 -->
		</script>
		<form action="<?php echo $uri; ?>&updated=true" method="post" name="xspf_prefs">
		 <input type="hidden" name="xspf_setup" value="sent" />	   
		 <table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
		  <tr>
		   <th>Autostart:</th>
		   <td>
			<input type="checkbox" <?php if (get_option('xspf_player_autostart') == 1) {
												echo 'checked';
										  }?> value="1" name="player_autostart" /> If checked, tracks will start playing automatically.
		   </td>
		  </tr>
		  <tr>
		   <th>Repeat:</th>
		   <td>
			<input type="checkbox" <?php if (get_option('xspf_player_repeat') == 1) {
												echo 'checked';
										  }?> value="1" name="player_repeat" /> Check to repeat the playlist after the end of the last song.
		   </td>
		  </tr>
		  <tr>
		   <td colspan="2">
		    <table align="center">
			 <tr>
		   	<th>Width:</th>
		   	<td><input type="text" value="<?php $width = get_option('xspf_player_width');
		   										if ('' == $width) {
													$width = '176';  // Default width
												}
												echo $width;
		   								 ?>" name="width" /></td>
			<th>Height:</th>
			<td>
		    <input type="text" value="<?php $height = get_option('xspf_player_height');
		   										if ('' == $height) {
													$height = '15';  // Default height
												}
												echo $height;
		   								 ?>" name="height" /></td>		   
		 
			 </tr>
		    </table>
		  </td>
		  </tr>
		  <tr>
		   <th>
		   Background Color:
		   </th>
		   <td>
		    #<input type="text" name="bgcolor" value="<?php echo get_option('xspf_player_bgcolor'); ?>" size="7" maxlength="6" />
			<script language="JavaScript" type="text/javascript" src="<?php echo $__base_url; ?>picker.js"></script>			
			<script type="text/javascript"><!--
			function bg_colorpicker()
			{
				TCP.popup('<?php echo $__base_url; ?>picker.html', document.forms['xspf_prefs'].elements['bgcolor']);
			}
			//--></script>
			<input name="bg_color_picker" type="button" onClick="bg_colorpicker()" value="<?php _e('Color'); ?>"> <br />
			Leave empty for transparent background.
		   </td>
		  </tr>
		  <tr>
		   <th>
		   Foreground Color:
		   </th>
		   <td>
		    #<input type="text" name="fgcolor" value="<?php echo get_option('xspf_player_fgcolor'); ?>" size="7" maxlength="6" />
			<script type="text/javascript"><!--
			function fg_colorpicker()
			{
				TCP.popup('<?php echo $__base_url; ?>picker.html', document.forms['xspf_prefs'].elements['fgcolor']);
			}
			//--></script>
			<input name="fg_color_picker" type="button" onClick="fg_colorpicker()" value="<?php _e('Color'); ?>"> <br />
		   </td>
		  </tr>		  
		  <tr>
		   <th><?php _e('Player Mode'); ?>:</th>
		   <td>
		   <fieldset>
		   <table>
		   <tr>
		   <td valign="top">
	<script language="JavaScript" type="text/javascript">
		function cambio() {
			box = document.forms['xspf_prefs'].elements['player_mode'];
			valor = box.options[box.selectedIndex].value;
			switch (valor) {
				case "0":
					document.getElementById('bt').style.visibility = "hidden";
					document.getElementById('bt2').style.visibility = "hidden";
					document.getElementById('sl').style.visibility = "hidden";
					document.getElementById('wl').style.visibility = "hidden";
					document.getElementById('ur').style.visibility = "visible";
					break;
			
				case "1":
					document.getElementById('bt').style.visibility = "visible";
					document.getElementById('bt2').style.visibility = "hidden";
					document.getElementById('sl').style.visibility = "hidden";
					document.getElementById('wl').style.visibility = "hidden";
					document.getElementById('ur').style.visibility = "hidden";
					break;

				case "2":
					document.getElementById('bt').style.visibility = "hidden";
					document.getElementById('bt2').style.visibility = "hidden";					
					document.getElementById('sl').style.visibility = "visible";
					document.getElementById('wl').style.visibility = "hidden";
					document.getElementById('ur').style.visibility = "hidden";
					break;

				case "3":
					document.getElementById('bt').style.visibility = "hidden";
					document.getElementById('bt2').style.visibility = "hidden";
					document.getElementById('sl').style.visibility = "hidden";
					document.getElementById('wl').style.visibility = "visible";
					document.getElementById('ur').style.visibility = "hidden";
					break;

				case "4":
					document.getElementById('bt').style.visibility = "hidden";
					document.getElementById('bt2').style.visibility = "visible";
					document.getElementById('sl').style.visibility = "hidden";
					document.getElementById('wl').style.visibility = "hidden";
					document.getElementById('ur').style.visibility = "hidden";					
					break;					
			}
			
			setPlayerMode(valor);
		}
	</script>
	<select name="player_mode" onchange="cambio()">
	 <option value="0" <?php echo $mode0; ?>>User - URL</option>
	 <option value="1" <?php echo $mode1; ?>>Button</option>
	 <option value="2" <?php echo $mode2; ?>>Slim</option>
	 <option value="3" <?php echo $mode3; ?>>Wide List</option>
	 <option value="4" <?php echo $mode4; ?>>Button - Menu</option>
	</select>
	 </td>
	 <td>
		<div id="bt"  style="z-index: 1; position: relative; top:   0px; height: 20px; visibility: hidden"><img src="<?php echo $img_url, 'xspf_player_button.png'; ?>" /></div>
		<div id="sl"  style="z-index: 2; position: relative; top: -15px; height: 20px; visibility: hidden"><img src="<?php echo $img_url, 'xspf_player_slim.png'; ?>" /></div>
		<div id="wl"  style="z-index: 3; position: relative; top: -30px; height: 200px; visibility: hidden"><img src="<?php echo $img_url, 'xspf_player.png'; ?>" /><br />
			If you want the image to appear, you'll have to choose a <b>width</b> size in the above box.
		</div>
		<div id="bt2" style="z-index: 4; position: relative; top: -245px; height: 10px; visibility: hidden"><img src="<?php echo $img_url, 'xspf_player_button_2.png'; ?>" /><br />
			Warning: You (and your users) will need Firefox 1.5 under linux for this mode to work.
		</div>
		<div id="ur"  style="z-index: 5; position: relative; top: -255px; height: 10px; visibility: hidden">
		    <br /><br /><br />
		    This mode is for <b>advanced users</b>. You can choose the player URL in the box below (or even a completely different flash player,
			provided it understands the XSPF XML specification and the XSPF player parameters).
	  </td>
	 </tr>
	</table>
	</fieldset>
		   </td>
		  </tr>
		  <tr>
		   <th align="right" valign="top">Preview:</th>
		   <td valign="top" align="left"><?php xspf_player::start(); ?></td>
		  </tr>
	<script type="text/javascript">
	<!--
		cambio();
	-->
	</script>	  
		  <tr>
		   <th>XSPF Flash Player URL:</th>
		   <td>
			<input type="text" name="player_url" value="<?php $player_url = get_option('xspf_player_url');
														if ($player_url == '') {
															$player_url = $__url;
														}
														echo $player_url;													
												 ?>" size="100" /> <br />
			Enter the URL of your XSPF flash player. <b>Leave this as is, unless the player is not working.</b><br />
			Please, use your webserver. It <b>MUST</b> start with the prefix <u><?php echo 'http://' . xspf_player::server_name(); ?></u>.<br />You can download the player from <a href="http://musicplayer.sourceforge.net">http://musicplayer.sourceforge.net</a>
		   </td>
		  </tr>		  
		 </table>
		 <p class="submit">
		  <input type="submit" name="action" class="submit" value="<?php _e('Update');?> &raquo;" onClick="setPlayerMode('0');" />
		 </p>
		</form>
	   </div>
	   <script type="text/javascript">
	   <!-- 
	      setPlayerMode(<?php echo $_mode; ?>);
		-->
	   </script>
<?php
	} /* public function options() */
	
	
	// -----------------------------------------------------------------------------------------------------
	// Show a Box for categories for a song
	// -----------------------------------------------------------------------------------------------------
	function show_categories_mgmt($idtrack = FALSE) {
		global $wpdb;
		
		?>
		<fieldset id="track_categories">
		 <legend>Track Categories</legend>
		 <?php
			$query = 'SELECT * FROM `' . xspf_player::table_categories() . '` ORDER BY `name` ASC';
			if (($result = $wpdb->get_results($query, ARRAY_A))) { // There's at less 1 category
				$query = 'SELECT a.`id` FROM `' . xspf_player::table_categories() . '` a INNER JOIN `'
					. xspf_player::table_cat_rel() . "` b ON (a.`id` = b.`idcat`) WHERE b.`idtrack` = '$idtrack'";

				if (($cat_rels = $wpdb->get_col($query)) == NULL) {
					$cat_rels = array();
				}

				foreach ($result as $row) { ?>
					<label for="category-<?php echo $row['id'];?>" class="selectit">
					 <input value="1" type="checkbox" name="post_category[<?php echo $row['id']; ?>]" <?php
						if (in_array($row['id'], $cat_rels)) {
								echo 'checked="checked"';
						} ?> /> <?php echo $row['name']; ?>
					</label><?php
				}
			}
		 ?>
		</fieldset>
		<?php
	}
	
	// -----------------------------------------------------------------------------------------------------
	// Show track list and an form to edit song
	//  $lengend => Title of the form (Edit Track, New Track... etc)
	//  $action => Whether it's a new song (Add) or a track update (Update)
	// -----------------------------------------------------------------------------------------------------
	function show_management($legend, $action, $button_label, $idtrack = FALSE, $offset = 0) {
		xspf_player::show_songs_table($offset, 10, $idtrack); ?>
		<script type="text/javascript">
		<!--
		 function CancelSongEditing() {
		 	document.forms['xspf_mgmt'].elements['xspf_mgmt'].value = 'cancel_track_edit';
		 	return true;
		 }
		-->
		</script>
		<form action="" method="post" name="xspf_mgmt">
		 <table width="100%">
		  <tr><td width="90%">
		 <fieldset id="track_form">
		  <legend><?php echo $legend; ?></legend>
		    <?php if ($idtrack != FALSE) {
				echo '<input type="hidden" name="idtrack" value="', $idtrack, '" />', "\n";
				echo '<input type="hidden" name="offset" value="', $offset, '" />', "\n";
			} ?>
			<input type="hidden" name="xspf_mgmt" value="<?php echo $action; ?>" />
		 <?php xspf_player::song_form_body($idtrack); ?>
		 <p class="submit">
		  <input type="submit" name="action_cancel" value="<?php _e('Cancel'); ?>" onClick="CancelSongEditing();" />
		  <input type="submit" name="action" value="<?php echo $button_label; ?> &raquo;" />
		 </p>
		 </fieldset></td>
		 <td>
		 <?php xspf_player::show_categories_mgmt($idtrack); ?>
		 </td>
		 </tr>
		 </table>
		</form>
		<?php
	}
	
	// -----------------------------------------------------------------------------------------------------
	// Show a little form to specify the order song of a category
	// -----------------------------------------------------------------------------------------------------
	function show_categories_order_mgmt($idcat) {
		global $wpdb;
		
		$idcat = $wpdb->escape($idcat);
		$cat_name = $wpdb->get_var('SELECT `name`, `order` FROM `' . xspf_player::table_categories() . "` WHERE `id` = $idcat");
		$order = $wpdb->get_var(NULL, 1);
		?>
		<fieldset id="track_categories">
		 <legend>Track Order for Category <i><?php echo $cat_name; ?></i></legend>
		 <table><tr>
		  <td>
		  <form action="" method="post" name="track_order">
		   <textarea name="order" cols="72" rows="30"><?php echo $order; ?></textarea>
		   <br />
		   <?php _e('Put some <i>comma separated</i> track numbers above (see example below).'); ?>
		   <br />
		   <?php _e('A track Id can appear more than once. Ranges are allowed using "-".'); ?>
		   <br />
		   <?php _e('Example');?>: <b>1-3, 4, 4, 6, 10</b> <input type="submit" name="action" value="<?php _e('Update'); ?>" />
		   <input type="hidden" name="idcat" value="<?php echo $idcat; ?>" />
		   <input type="hidden" name="update" value="true" />
		   <input type="hidden" name="xspf_mgmt" value="update_cat_order" />		   
		  </form>
		   </td>
		   <td valign="top">
		   Songs into this category: <br />
		   <?php 
		     $song_list = $wpdb->get_results("SELECT * FROM `" . xspf_player::table() . "` a INNER JOIN `" . xspf_player::table_cat_rel()
			 		. "` b ON (a.`id` = b.`idtrack`) AND b.`idcat` = $idcat ORDER BY a.`title` ASC" , ARRAY_A);
					
			 if (count($song_list)) {
			    echo "<table>\n";
		     	foreach($song_list as $song) {
					$title = $song['title'] . ' - ' . $song['artist'];
					if ($title === ' - ') {
						$title = $song['url'];
					}
			 		printf("<tr><td><b>%s</b></td><td><a href=\"%s\">%s</a></td></tr>\n", $song['id'], $song['url'], $title);
			 	}
				echo "</table>\n";
			 }
		   ?>
		   </td>
		  </tr>
		</table>		  
		</fieldset>
		<?php
	}
	

	// -----------------------------------------------------------------------------------------------------
	// Parses order list and convert it to formated string
	// -----------------------------------------------------------------------------------------------------	
	function parse_orderlist($order, $idcat) {
		$order = str_replace(array("\n", ' '), array('', ''), $order);
		$list = split(',', $order);
		$result = '';
		$sep = '';
		
		foreach ($list as $line) {
			$line = trim($line);
			if (strcspn($line, '0123456789-'))
				return $result; // Invalid order str

			$range = split('-', $line);
			if (count($range) > 2)
				return $result;  // Invalid order str
				
			$a = intval($range[0]);
			if (count($range) > 1) {
				$b = intval($range[1]);
			} else {
				$b = $a;
			}
			
			$range = $a;
			
			if ($b < $a)
				return $result; // Invalid order str
				
			if ($b > $a)
				$range .= "-$b";
				
			$result .= $sep . $range;
			$sep = ", ";
		}
		
		return $result;
	}
	
	
	// -----------------------------------------------------------------------------------------------------
	// Tracks & Categories management page
	// -----------------------------------------------------------------------------------------------------
	function management() {
		global $wpdb;
	    
		?>
	   <div class="wrap">
		<h2>XSPF Player Plugin Management</h2>
	<?php
		switch ($_POST['xspf_mgmt']) {
			case 'delete':   // deleting a new track
				$wpdb->query('DELETE FROM `' . xspf_player::table_cat_rel() . '` WHERE `idtrack` = ' . intval($_POST['idtrack']));
				$wpdb->query('DELETE FROM `' . xspf_player::table() . '` WHERE `id` = ' . intval($_POST['idtrack']));
				xspf_player::show_management(__('Add New Track'), 'add', __('Add New Track'));				
				break;	
				
			case 'edit': // Editing old values
				xspf_player::show_management(__('Update Track'), 'update', __('Update Track Info'), intval($_POST['idtrack']), intval($_POST['offset']));	
				break;
				
			case 'update': // Update Track Info
                $id = $wpdb->escape(intval(trim($_POST['idtrack'])));
                $wpdb->query('DELETE FROM `' . xspf_player::table_cat_rel() . "` WHERE `idtrack` = '$id'"); // Delete all relations
                if (is_array($_POST['post_category'])) {  //post_category is defined an is an array
                    foreach ($_POST['post_category'] as $idcat => $valor) {
                        if ($valor == 1) { // Category set?
                            $wpdb->query('INSERT INTO `' . xspf_player::table_cat_rel() . "`(`idtrack`, `idcat`) VALUES('$id', '$idcat')");
                        }
                    }
                }
                
				$url = trim($wpdb->escape(trim($_POST['url'])));
				if ($url === '') {  // Empty tracks url not allowed
					xspf_player::show_management(__('Add New Track'), 'add', __('Add New Track'), FALSE, intval($_POST['offset']));								
					break;
				}
				$imageurl = $wpdb->escape(trim($_POST['imageurl']));
				$infourl = $wpdb->escape(trim($_POST['infourl']));
				$artist = $wpdb->escape(xspf_player::unescape($_POST['artist']));
				$title = $wpdb->escape(xspf_player::unescape($_POST['title']));
				$wpdb->query('UPDATE `' . xspf_player::table() . "` SET `artist` = '$artist', `title` = '$title', `imageurl` = '$imageurl', `url` = '$url', `infourl` = '$infourl'"
                    . " WHERE `id` = '$id'");
				xspf_player::show_management(__('Add New Track'), 'add', __('Add New Track'), FALSE, intval($_POST['offset']));
				break;

			case 'add':   // adding a new track
				$url = trim($wpdb->escape(trim($_POST['url'])));
				if ($url === '') {  // Empty tracks url not allowed
					xspf_player::show_management(__('Add New Track'), 'add', __('Add New Track'));				
					break;
				}
			
				$imageurl = $wpdb->escape(trim($_POST['imageurl']));
				$infourl = $wpdb->escape(trim($_POST['infourl']));
				$artist = $wpdb->escape(xspf_player::unescape($_POST['artist']));
				$title = $wpdb->escape(xspf_player::unescape($_POST['title']));

				$wpdb->query('INSERT INTO `' . xspf_player::table() . "`(`artist`, `title`, `imageurl`, `url`, `infourl`) VALUES ('$artist', '$title', '$imageurl', '$url', '$infourl')");
				$id = $wpdb->get_var('SELECT `id` FROM `' . xspf_player::table() . "` WHERE `url`= '$url'");
                $wpdb->query('DELETE FROM `' . xspf_player::table_cat_rel() . "` WHERE `idtrack` = '$id'"); // Delete al relations (should not be needed)
                if (is_array($_POST['post_category'])) {  //post_category is defined an is an array
                    foreach ($_POST['post_category'] as $idcat => $valor) {
                        if ($valor == 1) { // Category set?
                            $wpdb->query('INSERT INTO `' . xspf_player::table_cat_rel() . "`(`idtrack`, `idcat`) VALUES('$id', '$idcat')");
                        }
                    }
                }
				xspf_player::show_management(__('Add New Track'), 'add', __('Add New Track'));
				break;
				
			case 'switch_to_tracks':
				xspf_player::show_management(__('Add New Track'), 'add', __('Add New Track'));
				break;
				
			case 'cancel_track_edit':
				xspf_player::show_management(__('Add New Track'), 'add', __('Add New Track'), FALSE, intval($_POST['offset']));
				break;
				
			case 'switch_to_cats':
				xspf_player::show_categories_table();
				break;
				
			case 'delete_cat':
				// This should be done automatically by MySQL database (DELETE CASCADE) but depends on table types and versions...
            	$wpdb->query('DELETE FROM `' . xspf_player::table_cat_rel() ."` WHERE idtrack = '" . $wpdb->escape($_POST['idcat']) . "'");       
            	// Delete category from categories table
            	$wpdb->query('DELETE FROM `' . xspf_player::table_categories() ."` WHERE id = '" . $wpdb->escape($_POST['idcat']) . "'");
				xspf_player::show_categories_table();
				break;
				
			case 'update_cat':
				$cat = $wpdb->escape(trim(xspf_player::unescape($_POST['cat_name'])));
				$rand = $wpdb->escape($_POST['rand']);
				$autostart = $wpdb->escape($_POST['autostart']);
				$result = $wpdb->get_var('SELECT `id` FROM `' . xspf_player::table_categories() . "` WHERE `name` = '$cat'");
				if (is_null($result) || ($result == $_POST['idcat'])) {
					$wpdb->query('UPDATE `' . xspf_player::table_categories() . "` SET `name` = '$cat', `description` = '"
						. $wpdb->escape(xspf_player::unescape($_POST['desc'])) . "', `random` = '$rand', `autostart`= '$autostart' WHERE id = '" . $wpdb->escape($_POST['idcat']) . "'");
				}
				
				xspf_player::show_categories_table();
				break;
				
			case 'update_cat_order':  // Update category order
				$idcat = intval($_POST['idcat']);
				$order = $_POST['order'];
				$order = xspf_player::parse_orderlist($order, $idcat);
				$wpdb->query('UPDATE `' . xspf_player::table_categories() . "` SET `order` = '$order' WHERE id = $idcat"); 
				xspf_player::show_categories_table();
				break;
				
			case 'add_category':
				$cat = $wpdb->escape(trim(xspf_player::unescape($_POST['new_cat'])));
				$rand = $wpdb->escape($_POST['rand']);
				if ($cat != '') { // Not empty string and...
		        	if (!$wpdb->query('SELECT * FROM `' . xspf_player::table_categories() . "` WHERE `name` = '$cat'")) {  // Not already inserted
				        $wpdb->query('INSERT INTO `' . xspf_player::table_categories() . "`(`name`, `random`) VALUES ('$cat', '$rand')");
                  	}
				}

				xspf_player::show_categories_table();
				break;
				
			case 'order':  // Display the category song-order list
				xspf_player::show_categories_order_mgmt($_POST['idcat']);
				break;
				
			case 'gotopage':  // Display the song list to the next, previos page as selected by the user
				xspf_player::show_management(__('Add New Track'), 'add', __('Add New Track'), FALSE, $_POST['offset']);
				break;
				
			default:
				xspf_player::show_management(__('Add New Track'), 'add', __('Add New Track'));
		}
	?></div><?php
	} /* public function management() */
	

	//----------------------------------------------------------
	// Get the a songs list ordered according to the $order str
	// $order str is defined in the categories panel. 
	// Sample value for songs $order '1,2-5,7,7'
	//----------------------------------------------------------
	function order_list($query, $order) {
		global $wpdb;
		
		$order = str_replace(array("\n", ' '), array('', ''), $order);
		$list = split(',', $order);
		$result = array();
		
		foreach ($list as $line) {
			$line = trim($line);
			if (strcspn($line, '0123456789-'))
				return $result; // Invalid order str
	
			$range = split('-', $line);
			if (count($range) > 2)
				return $result;  // Invalid order str
				
			$a = intval($range[0]);
			if (count($range) > 1) {
				$b = intval($range[1]);
			} else {
				$b = $a;
			}
			
			$range = $a;			
			if ($b < $a)
				return $result; // Invalid order str
				
			$query_ = $query;
				
			if ($b > $a)
				$query_ .= "(a.`id` >= $a) AND (a.`id` <= $b) ORDER BY `title`, `artist` ASC";
			else
				$query_ .= "(a.`id` = $a)";				
				
			if (($res_tmp = $wpdb->get_results($query_, ARRAY_A))) {
				$result = array_merge($result, $res_tmp);
			}
		}
		 
		return $result;
	}
	
	//----------------------------------------------------------
	// Translate characters &, <, > to entities
	//----------------------------------------------------------
	function entities($str) {
		global $charset;
		
		if (version_compare(phpversion(), '5.0.0') < 0) {
			return htmlspecialchars($str);
		}
		
		return htmlspecialchars(html_entity_decode($str, ENT_COMPAT, $charset));
	}
	
	
	//----------------------------------------------------------
	// Output player params
	//----------------------------------------------------------
	function echo_player_params($url, $playlist_url, $category, $random, $autostart, $params = '') {
			echo  $url . '?autoload=true&amp;';
			
			if ($params != '') {
				echo $params, '&amp;';
			}
			
			if ($random == 1) {
				echo 'shuffle=true&amp;';
			}
			
		 	if ($autostart == 1) {
				echo 'autoplay=true&amp;';
			}
			
			if (get_option('xspf_player_repeat') == 1) {
				echo 'repeat_playlist=true&amp;';
			}
			
			echo 'playlist_url=', urlencode($playlist_url); 
			
			if ($category != FALSE) {
				if (strchr($playlist_url, '?') == FALSE)
					echo '?';
				else
					echo '&amp;';
					
				echo urlencode("cat=$category");
			}
		}
	

	//----------------------------------------------------------
	// Recognizes <xpsf></xspf> tags and inserts the player 
	// into the post body
	//----------------------------------------------------------
	function inline_insert($post_text) {
		$phpexec_textarr = xspf_player::tag_split($post_text); // capture the tags as well as in between
		$phpexec_stop = count($phpexec_textarr); // loop stuff

		$phpexec_output = '';
		$XSPF = xspf_player::tag();
		
		$sav = xspf_player::saver();
		if (!isset($sav['tag_count'])) {
			$sav['tag_count'] = 0;
			$sav['tag_save'] = array();
		}

		for ($phpexec_i = 0; $phpexec_i < $phpexec_stop; $phpexec_i++) {
			$phpexec_content = $phpexec_textarr[$phpexec_i];
			if (xspf_player::tag_match($phpexec_content, $phpexec_code)) { // If it's a xspf player inline	
				$phpexec_php = $phpexec_code[1];
				ob_start();
				eval('xspf_player::' . $phpexec_php . ';');
				$sav[strval($sav['tag_count'])] = ob_get_clean();
				$phpexec_output .= "[$XSPF]" . $sav['tag_count'] . "[/$XSPF]";  // Hides it to avoid being changed by wptexturize, etc..
				$sav['tag_count'] += 1;
			} else {
				$phpexec_output .= $phpexec_content;
			}
		}

		xspf_player::saver($sav);
		return $phpexec_output;
	}
	
	
	//----------------------------------------------------------
	// Unmask code after passed nasty balace-tags
	//----------------------------------------------------------
	function inline_insert_after($text)
	{
		$textarr = xspf_player::tag_split($text); // capture the tags as well as in between
		$stop = count($textarr);// loop stuff
		$output = '';
		$sav = xspf_player::saver();
		
		for ($phpexec_i = 0; $phpexec_i < $stop; $phpexec_i++) {
			$content = $textarr[$phpexec_i];

			if (xspf_player::tag_match($content, $code)) { // If it's a phpcode
				$content = $sav[$code[1]];
			}
			$output .= $content;
		}
		
		return $output;
	}
	
	
	//----------------------------------------------------------
	// Mask code before it's saved
	//----------------------------------------------------------
	function inline_insert_edit_before($text)
	{
		global $userdata;
		get_currentuserinfo();
		$allowed = (isset($userdata->user_level) && ($userdata->user_level >= 9)); // Only admin is allowed
		$XSPF = xspf_player::tag();
		$textarr = xspf_player::tag_split($text); // capture the tags as well as in between
		$stop = count($textarr);// loop stuff

		$output = '';	
		for ($phpexec_i = 0; $phpexec_i < $stop; $phpexec_i++) {
			$content = $textarr[$phpexec_i];
			if (xspf_player::tag_match($content, $code)) { // If it's a phpcode
				if ($allowed) {
					$content = "[$XSPF]" . base64_encode($code[1]) . "[/$XSPF]";
				} else {
					$content = $code[1]; // strips <xspf> tag for untrusted editor users
				}
			}
			
		$output .= $content;
		}
	
		return $output;
	}
	
	
	//----------------------------------------------------------
	// Unmask code after passed nasty balace-tags
	//----------------------------------------------------------
	function inline_insert_edit_after($text)
	{
		global $userdata;
		get_currentuserinfo();
		$allowed = (isset($userdata->user_level) && ($userdata->user_level >= 9)); // Only admin is allowed
	
		$textarr = xspf_player::tag_split($text); // capture the tags as well as in between
		$stop = count($textarr);// loop stuff
		$output = '';
		$XSPF = xspf_player::tag();
		
		for ($phpexec_i = 0; $phpexec_i < $stop; $phpexec_i++) {
			$content = $textarr[$phpexec_i];
			if (xspf_player::tag_match($content, $code)) { // If it's a phpcode
				if ($allowed) {
					$content = "[$XSPF]" . base64_decode($code[1]) . "[/$XSPF]";
				} else {
					$content = $code[1]; // This should never happen
				}
			}
			$output .= $content;
		}
		
		return $output;
	}
	
	
	// -----------------------------------------------------------------------------------------------------
	// This function puts the player on your web page selecting songs by category (Default: ALL SONGS)
	// You can use the 
	// -----------------------------------------------------------------------------------------------------
	function start($category = FALSE, $args = '', $inline = FALSE) {
        global $wpdb;
		static $instance = '0';  // Increases for every instance since multiple instances are allowed
		
        if ($category != FALSE) {
            $category = $wpdb->get_var('SELECT `id`, `random`, `autostart` FROM `' . xspf_player::table_categories() . "` WHERE `name` = '$category'");
            if (is_null($category)) {  // If the category does not exists, "ALL" is selected (using FALSE)
                $category = FALSE;
				$random = 0;
				$autostart = get_option('xspf_player_autostart');
            } else {
				$random = $wpdb->get_var(NULL, 1);
				$autostart = $wpdb->get_var(NULL, 2);
			}
        } else {
			$autostart = get_option('xspf_player_autostart');
		}
		
		if (($width = get_option('xspf_player_width')) == '') {
			$width = 176; // Default Width
		}
		
		if (($height = get_option('xspf_player_height')) == '') {
			$height = 176; // Default Height
		}
        
		$playlist_url = get_option('siteurl') . '/wp-content/plugins/xspf_player/playlist.php';
		$order = '';	
		$bgcolor = get_option('xspf_player_bgcolor');
		$fgcolor = get_option('xspf_player_fgcolor');
				
		$mode = get_option('xspf_player_mode');
		if ($mode == '')  // Not defined
			$mode = '1';

		if ($mode == '1')
			$width = $height = 17;
			
		if ($mode == '2')
			$height = 15;

		$sep = '';    // Separator for params (initially '', but later becomes '&amp;'
		$params = ''; // List of params parsed: 'param1=value&amp;param2=value&amp;...'
		$url = get_option('siteurl') . '/wp-content/plugins/xspf_player/';		
		switch ($mode) {
			case '0':
				$url = get_option('xspf_player_url');  // Override $url values, and use user settings
				break;
				
			case '1':
				$url .= 'musicplayer.swf';
				break;
				
			case '2':
				$url .= 'xspf_player_slim.swf';
				break;
				
			case '3':
				$url .= 'xspf_player.swf';
				break;
				
			case '4':
				$url .= 'musicplayer_menu_f6.swf';
				$params .= $sep . 'container_id=xspf_player' . $instance;
				$sep = '&amp;';
				break;
		}
		
		parse_str($args, $tmpvars);  
		foreach ($tmpvars as $varname => $varval) {
			$$varname = $varval;
		}
				
		if ($mode != get_option('xspf_player_mode')) { // Player mode has changed?
			$url = get_option('siteurl') . '/wp-content/plugins/xspf_player/';
			switch ($mode) {
				case '0':
					$url = get_option('xspf_player_url');  // Override $url values, and use user settings
					break;
			
				case '1':
					$url .= 'musicplayer.swf';
					if (!isset($tmpvars['height'])) { // user didn't redefine height, use default button's
						$height = 17;
					}

					if (!isset($tmpvars['width'])) { // user didn't redefine height, use default button's
						$width = 17;
					}
					
					break;
					
				case '2':
					$url .= 'xspf_player_slim.swf';
					if (!isset($tmpvars['height'])) { // user didn't redefine height, use default slim's
						$height = 15;
					}
					break;
					
				case '3':
					$url .= 'xspf_player.swf';
					break;
					
				case '4':
					$url .= 'musicplayer_menu_f6.swf';
					$params .= $sep . 'container_id=xspf_player' . $instance;
					$sep = '&amp;';
					break;
			}
		}
		
		if ($order != '')
			$playlist_url .= '?order=' . $order;

		if ($fgcolor != '') {
			$params .= $sep . 'b_fgcolor=' . $fgcolor;
			$sep = '&amp;';
		}
		
		if ($bgcolor != '') {
			$params .= $sep . 'b_bgcolor=' . $bgcolor;
			$sep = '&amp;';			 
		}
		
		if ('4' == $mode) {
			$params .= "&amp;button_width=17&amp;button_height=17&amp;menu_width=$width&amp;menu_height=$height";
			$width = $height = '100%';
		}
		?>
 	 <!-- Wordpress XSPF_Player Plugin v. <?php echo xspf_player::_version(); ?>, by Boriel :-) -->
	 <?php
		if ((4 == $mode) && !$inline) {
	   		echo '<div id="xspf_player_margin', $instance, '" style="width:17px; height:17px; position:relative;">', "\n";
		}
	 ?>
	  <span id="xspf_player<?php echo $instance; ?>"<?php
		if (4 == $mode) echo ' style="width:17px; height:17px; position:absolute;"'; ?>>
		<object	type="application/x-shockwave-flash" data="<?php xspf_player::echo_player_params($url, $playlist_url, $category, $random, $autostart, $params); ?>" 
		   <?php echo 'width="', $width, '" height="', $height, '"'; 
		   ?> codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0">
			<param name="movie" value="<?php xspf_player::echo_player_params($url, $playlist_url, $category, $random, $autostart, $params); ?>" />
			<param name="allowScriptAccess" value="sameDomain" />
		 	<param name="quality" value="high" />
			<?php 
		   		if (('4' != $mode) && ('1' != $mode) && ($bgcolor != '')) {
		   			echo '<param name="bgcolor" value="#', $bgcolor, '" />' . "\n";
				} else {
					echo '<param name="wmode" value="transparent" />' . "\n";			
				}
			?>
		</object>
	   </span> 
	    <?php
		if ((4 == $mode) && !$inline) {
			echo '</div>', "\n";
		}
		$instance++;
	}  /* public function start() */
	
	// -----------------------------------------------------------------------------------------------------
	// This function puts the player on your web page selecting songs by category (Default: ALL SONGS)
	// You can use the 
	// -----------------------------------------------------------------------------------------------------
	function _start($category = FALSE, $args = '') {
		xspf_player::start($category, $args, TRUE);
	}
}
endif;

?>
