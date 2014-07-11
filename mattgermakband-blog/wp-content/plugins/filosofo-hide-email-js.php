<?php
/*Plugin Name: Filosofo Hide Email Addresses 
URI: http://www.ilfilosofo.com/blog/hide-email-js/
Description: Hides email addresses within posts and pages from spambots, using JavaScript.
Author: Austin Matzko
Version: 0.50
Author URI: http://www.ilfilosofo.com/blog
*/ 

/*

Instructions: upload this file to wp-content/plugins/ and activate it under "Plugins" in the WordPress
admin control panel.

This plugin hides email addresses of two forms:

1. Standing alone, surrounded by whitespace: example@example.com

2. Within a link, as "mailto": <a href="mailto:example@example.com">Your Name</a>

/*

/*  Copyright 2006  Austin Matzko  (email : if.website at gmail.com)

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

class filosofo_hide_email_js {

function create_key() {
	$site = get_option('home');
	$overall = substr( md5($site . 'hideemail' ), 0, 7);
	$name = substr( md5($site . 'name' ), 0, 7);
	$domain = substr( md5($site . 'domain' ), 0, 7);
	$tld = substr( md5($site . 'tld' ), 0, 7);
	$text = substr( md5($site . 'text' ), 0, 7);
	$filosofo_hide_email_js = array(
		'overall' => $overall,
		'name' => $name,
		'domain' => $domain,
		'tld' => $tld,
		'text' => $text
	);
	update_option('filosofo_hide_email_js',$filosofo_hide_email_js);
}

function find_email($content) {
	extract( (array) get_option('filosofo_hide_email_js'));
	$pattern = '#\s([A-Za-z0-9\-_\.]+?)@(([\w,-]*)\.(\w{2,3}))\s#U';
	$replace = ' <span class="' . $overall . '"><span class="' . $name . '">$1</span> (at) <span class="' 
		. $domain . '">$3</span> (dot) <span class="' . $tld . '">$4</span></span> ';
	$pattern2 = '#<a href=[\'|"]mailto:([A-Za-z0-9\-_\.]+?)@(([\w,-]*)\.(\w{2,3}))[\'|"](.*)?>(.*)</a>#U';
	$replace2 = '<span class="' . $overall . '"> (<span class="' . $text . '">$6</span>) <span class="' . $name . '">$1</span> (at) <span class="' 
		. $domain . '">$3</span> (dot) <span class="' . $tld . '">$4</span></span>';
	$content = preg_replace($pattern,$replace,$content); 	
	return preg_replace($pattern2,$replace2,$content);
}

function javascript() {
$keys = (array) get_option('filosofo_hide_email_js');
?>
<script type="text/javascript">
//<![CDATA[
function filosofo_addEvent(obj, evType, fn){
	if (obj.addEventListener) {
		obj.addEventListener(evType, fn, false); //false to make sure it happens during event bubbling, not capturing.
		return true;
	} 
	else if (obj.attachEvent) {
		var r = obj.attachEvent("on"+evType, fn);
		return r;
	} 
	else {
		return false;
	}
}
function filosofo_revealEmails() {
	var x = document.getElementsByTagName('span');
	for (var i=0;i<x.length;i++) {
		var address;
		var text = '';
		if (x[i].className == '<?php echo $keys['overall']; ?>') {
			var y = x[i].getElementsByTagName('span');
			for (var j=0;j<y.length;j++) {
				if (y[j].className == '<?php echo $keys['name']; ?>') {
					if (y[j].innerHTML !== null) address = y[j].innerHTML + '@';
				}
				if (y[j].className == '<?php echo $keys['domain']; ?>') {
					if (y[j].innerHTML !== null) address = address + y[j].innerHTML + '.';
				}
				if (y[j].className == '<?php echo $keys['tld']; ?>') {
					if (y[j].innerHTML !== null) address = address + y[j].innerHTML;
				}
				if (y[j].className == '<?php echo $keys['text']; ?>') {
					if (y[j].innerHTML !== null) text = y[j].innerHTML;
				}
			}
			if ((x[i].innerHTML !== null) && (address !== null)) {
				if ('' == text) text = address;
				x[i].innerHTML = '<a href=\'mailto:' + address + '\'>' + text + '</a>';
			}
		}
	}
}

filosofo_addEvent(window, 'load', filosofo_revealEmails);

//]]>
</script>
<?php
}

} // end class filosofo_hide_email_js

$filosofo_hej = new filosofo_hide_email_js();

add_action('wp_head', array(&$filosofo_hej, 'javascript'),1);
add_action('activate_' . basename(__FILE__),array(&$filosofo_hej,'create_key'));
add_action('the_content',array(&$filosofo_hej,'find_email'));

?>
