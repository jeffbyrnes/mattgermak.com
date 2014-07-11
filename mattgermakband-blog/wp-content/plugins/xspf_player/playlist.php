<?php 
error_reporting(E_ALL);
header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n";
echo '<!-- ';  // Comment some headers introduced by some free web hosts
require_once('../../../wp-config.php');
require_once('../../../wp-settings.php');
require_once('xspf_player_class.php');
echo ' -->', "\n";

$charset = get_option('blog_charset');
?>
<playlist version="0" xmlns = "http://xspf.org/ns/0/">
  <title>XSPF Player</title>
  <annotation>http://musicplayer.sourceforge.net</annotation>
 <trackList>
<?php

$query = 'SELECT * FROM `' . xspf_player::table() . '` a';
if (isset($_GET['cat']) || isset($_GET['order'])) {

	if (isset($_GET['cat'])) {
		$query .= ' INNER JOIN `' . xspf_player::table_cat_rel() . '` b '
			. "ON (a.`id` = b.`idtrack`) WHERE `idcat` = '" . $wpdb->escape($_GET['cat']) . "'";
		
		$random = $wpdb->get_var('SELECT `random`, `order` FROM `' . xspf_player::table_categories() . "` WHERE `id` = '"
			. $wpdb->escape($_GET['cat']) . "'");
			
		if (!$random)
			$order = $wpdb->get_var(NULL, 1);
	}
	
	if (isset($_GET['order']))
		$order = $_GET['order'];
		
	if ($order != '') {
		if (isset($_GET['cat']))
			$query .= ' AND ';
		else
		    $query .= ' WHERE ';
			
		$tracks = xspf_player::order_list($query, $order);
	}
		
} else {
	$random = '0';
}

$query .= ' ORDER BY `title` ASC';
	
if (isset($tracks) || ($tracks = $wpdb->get_results($query, ARRAY_A))) {
	// option "shuffle = true" not always working into xspf. Do it our own way...
	if ($random == 1) { // shuffle tracks?
		list($usec, $sec) = explode(' ', microtime());
		mt_srand((float) $sec + ((float) $usec * 100000));
		$nrows = count($tracks);
		for ($i = 0; $i < $nrows; $i++) {
			$j = mt_rand(0, $nrows - 1);  // pick j at random
			$row = $tracks[$i]; // swap i, j
			$tracks[$i] = $tracks[$j];
			$tracks[$j] = $row;
		}
	}

	foreach ($tracks as $row) {
		echo "   <track>\n";
		printf("    <location>%s</location>\n",  xspf_player::entities($row['url']));
		
		if (($row['artist'] != '') || ($row['title'] != '')) {
			printf("    <annotation>%s - %s</annotation>\n",  xspf_player::entities($row['artist']),  xspf_player::entities($row['title']));
		}
		
		if ($row['imageurl'] != '') {
			printf("    <image>%s</image>\n",  xspf_player::entities($row['imageurl']));
		}
		
		if ($row['infourl'] != '') {
			printf("    <info>%s</info>\n",  xspf_player::entities($row['infourl']));
		}	
		echo "   </track>\n";
	}
}
?>
 </trackList>
</playlist>
