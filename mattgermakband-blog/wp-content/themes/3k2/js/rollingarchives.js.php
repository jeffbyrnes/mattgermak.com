<?php
	require("../../../../wp-blog-header.php");

	// check to see if the user has enabled gzip compression in the WordPress admin panel
	if ( !get_settings('gzipcompression') && !ini_get('zlib.output_compression') ) {
		ob_start('ob_gzhandler');
	}

	// The headers below tell the browser to cache the file and also tell the browser it is JavaScript.
	header("Cache-Control: public");
	header("Pragma: cache");

	$offset = 60*60*24*60;
	$ExpStr = "Expires: ".gmdate("D, d M Y H:i:s",time() + $offset)." GMT";
	$LmStr = "Last-Modified: ".gmdate("D, d M Y H:i:s",filemtime(__FILE__))." GMT";

	header($ExpStr);
	header($LmStr);
	header('Content-Type: text/javascript; charset: UTF-8');

?>

// Let's figure out how busy you've been...
var pagecount = Math.ceil(<?php echo $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish'"); ?> / <?php echo get_settings('posts_per_page'); ?>);

function rollArchive(direction) {
	if (direction == 1 || direction == -1) {
		pagenumber += direction;
		new Effect.Appear('rollload', {duration: .2});
	} else if (direction == 'home') {
		pagenumber = 1;
	}

	checkRollingElements();

	if (pagenumber > 1) {
		new Ajax.Updater({success: 'dynamiccontent'}, '<?php bloginfo('template_url'); ?>/theloop.php', {method: 'get', parameters: 'paged='+pagenumber+'&rollingarchives=1', onSuccess: rollSuccess, onFailure: rollError});
		$('primarycontent').style.display = 'none';
	} else {
		$('dynamiccontent').innerHTML = null;			// Remove rolling archives
		$('primarycontent').style.display = 'block';	// Bring back the frontpage
		rollRemoveLoad();
	}
}


function rollGotoPage(gotopage) {
	pagenumber = (gotopage - 1);
	rollArchive(1);
}


function rollSuccess() {
	rollRemoveLoad();
	if (pagenumber > 1) {								// If we've moved into the archives, 
		setCookie('rollpage', pagenumber);				// set a cookie so we can return to that page.
	} else if (pagenumber = 1) {
		deleteCookie('rollpage');
	}
}


function rollError() {
	$('rollnotices').innerHTML = 'Error! <a href="javascript:initRollingArchives()">Reset</a>';
}


function rollRemoveLoad() {
	new Effect.Fade('rollload', {duration: .2});
}


// Needs to be run when a direction is picked, but not when you click the link the notice provides. FIX IT
function rollRemoveNotices() { 
	new Effect.Fade($('rollnotices'));
	$('rollnotices').innerHTML = null;
}


function checkRollingElements() {
	if (pagenumber == 1) {
		$('rollprevious').className = null;
		$('rollprevious').onclick = function() { PageSlider.setValueBy(1); return false; };
		$('rollnext').className = 'inactive';
		$('rollnext').onclick = null;
		$('rollhome').className = 'inactive';
		$('rollhome').onclick = null;
	} else if (pagenumber > 1) {
		$('rollnext').className = null;
		$('rollnext').onclick = function() { PageSlider.setValueBy(-1); return false; };
		$('rollhome').className = null;
		$('rollhome').onclick = function() { rollArchive('home'); };
	}
	
	if (pagenumber >= pagecount) {
		$('rollprevious').className = 'inactive';
		$('rollprevious').onclick = null;
	} else {
		$('rollprevious').className = null;
		$('rollprevious').onclick = function() { PageSlider.setValueBy(1); return false; };
	}

	$('rollpages').innerHTML = 'Page '+pagenumber+' of '+pagecount;  // Insert page count
}


function initRollingArchives() {
	pagenumber = 1;

 	checkRollingElements();
	rollRemoveLoad();

	/*if (getCookie('rollpage') != null) {
		$('rollnotices').innerHTML = 'This session you were last seen on <a href="javascript:rollGotoPage('+getCookie('rollpage')+');">page '+getCookie('rollpage')+'</a>. <img src="<?php bloginfo('template_url'); ?>/images/transparent.gif" alt="Reset" onclick="Effect.Fade($(\'rollnotices\')); deleteCookie(\'rollpage\');" />';
		new Effect.Highlight('rollnotices');
	} else {
		$('rollnotices').style.display = 'none';
	}*/

	$('rollnotices').style.display = 'none';
	
	if (pagecount > 1) {
		$('rollprevious').onclick = function() { PageSlider.setValueBy(1); return false; };
	} else {
		$('rollingarchives').style.display = 'none';
	}
	
	$('rollingarchives').style.display = null;  // Show Rolling Archives
}


// Initialize the Rolling Archives
Event.observe(window, 'load', initRollingArchives, false);
