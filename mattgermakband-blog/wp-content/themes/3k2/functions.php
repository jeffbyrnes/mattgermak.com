<?php
/* Current revision of 3K2 */
$current = '1.01';

load_theme_textdomain('k2_domain');

/* blast you red baron! */
/* initialise the k2 system */

require('options/app/archive.php');
require('options/app/options.php');
require('options/app/update.php');
require('options/app/info.php');

// if we can't find k2 installed lets go ahead and install all the options that run K2.  This should run only one more time for all our existing users, then they will just be getting the upgrade function if it exists.
if (!get_option('k2installed')) {
    installk2::installer();
}

// Here we handle upgrading our users with new options and such.  If k2installed is in the DB but the version they are running is lower than our current version, trigger this event.
elseif (get_option('k2installed') < $current) {
/* Do something! */
//add_option('k2upgrade-test', 'this is the text', 'Just testing', $autoload);
}

// Let's add some support for WordPress Widgets
if (function_exists('register_sidebars')) register_sidebars(2, array('before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>'));

// Let's add the options page.
add_action ('admin_menu', 'k2menu');

$k2loc = '../themes/'.basename(dirname($file));

function k2menu() {
    add_submenu_page('themes.php', __('3K2 Options','k2_domain'), __('3K2 Options','k2_domain'), 5, $k2loc . 'functions.php', 'menu');
}

function menu() {
    load_plugin_textdomain('k2options');
    //this begins the admin page

    include( 'options/display/form.php' );

} // this ends the admin page
