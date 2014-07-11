<?php
/**
* The base configurations of the WordPress.
*
* This file has the following configurations: MySQL settings, Table Prefix,
* Secret Keys, WordPress Language, and ABSPATH. You can find more information
* by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
* wp-config.php} Codex page. You can get the MySQL settings from your web host.
*
* This file is used by the wp-config.php creation script during the
* installation. You don't have to use the web site, you can just copy this file
* to "wp-config.php" and fill in the values.
*
* @package WordPress
*/
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'mattgermak_wordpress');

/** MySQL database username */
define('DB_USER', 'mattgermak');

/** MySQL database password */
define('DB_PASSWORD', 'EukiPx2eFpWJ7d');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
* Authentication Unique Keys and Salts.
*
* Change these to different unique phrases!
* You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
* You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
*
* @since 2.6.0
*/
define('AUTH_KEY',         'KIN)7^BCI{|WYT@El+$c3S2UIKytX[)hv8;>SzpA-[z8N5ac;R}58WWO#65W9{RY');
define('SECURE_AUTH_KEY',  'It%VXmoEkEiWhHL[sS95K2`Bj:cVG-0u+zkbs#27FYb-^r=n-v=#itS~*}Q&%JXS');
define('LOGGED_IN_KEY',    'GRds$_<N8^^*2pDOygbnJ@U6]4i!~-L1zbw,yjQsNiViAYKx!j;cu!lIV3ct5O33');
define('NONCE_KEY',        'cmBPQP1<He}cy4(:/|&JGFP0,I`#|{+rUauT]#G@|/TY4q5[? WYPksd:4:u~ZIc');
define('AUTH_SALT',        '8TAvM)*FTLUGbi[1,)/|4/K+7`6r@>Fp6OULHERS.*G,j+7_vqu|U!g{+QB6VU<W');
define('SECURE_AUTH_SALT', '+oY]r29c6/,eeZ|i}em?^!B|MSqfjfX X9NVTlr_$yu=W7eorE_$=T1#|y56*0#i');
define('LOGGED_IN_SALT',   'X*Sp^NX-$%|$:7KPXf$9Z/##t%X&U6-l@&-.$|#BSsOdMy_t3IIunjh5V`I#+z;.');
define('NONCE_SALT',       'GiF{<$CRj=P5mDG@.;eOq#J,N[zfWF[k4hEGP,gN0n2gF_|RHC-jrJfI5I;(>z(A');

/**#@-*/

/**
* WordPress Database Table prefix.
*
* You can have multiple installations in one database if you give each a unique
* prefix. Only numbers, letters, and underscores please!
*/
$table_prefix  = 'wp_';

/**
* WordPress Localized Language, defaults to English.
*
* Change this to localize WordPress. A corresponding MO file for the chosen
* language must be installed to wp-content/languages. For example, install
* de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
* language support.
*/
define('WPLANG', '');

/**
* For developers: WordPress debugging mode.
*
* Change this to true to enable the display of notices during development.
* It is strongly recommended that plugin and theme developers use WP_DEBUG
* in their development environments.
*/
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
