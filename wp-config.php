<?php
define( 'WP_CACHE', true );

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'xrrY3E1NJGFdkF' );

/** Database username */
define( 'DB_USER', 'xrrY3E1NJGFdkF' );

/** Database password */
define( 'DB_PASSWORD', 'y2cIJFDrIMifYc' );

/** Database hostname */
define( 'DB_HOST', 'localhost:3306' );

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'f2|rP#g<Zq^[BMGnMJn%Ex,o-XPmAiBFPQv%m,4~x(HUQBi=nTy^Bu=MA(2yIhQ>' );
define( 'SECURE_AUTH_KEY',   'yY!=oC*r|SYKLk^Rvycpu8)KY6wWHe~? ,# EPnPttrTeLrw:+qUjhgB|yX=`+El' );
define( 'LOGGED_IN_KEY',     '1/Q({WBGR0nuZ!ANeQ;L{LN]qC$R%2e*x]_I[.0kc6*}>!^Z9bNht??i/}h}BC$1' );
define( 'NONCE_KEY',         'QRos3^Dwn.edi%bJ-q)0pKr..-,AFW,M5[G{ySsM^;TcQW/<D~^$G@;@Y;7jccV#' );
define( 'AUTH_SALT',         '%ySb`x)NI_(lpcksse<I1D&@!=)#H?Ix%s=E`?x(PiUhgi+b4-(o8d1ei?JMDM<t' );
define( 'SECURE_AUTH_SALT',  'I1AHrN5w/W7/p;/bQRgW,2)%fd7*qyrH;@/O36wT}9;?H{!$b|v]?@ow342y-5cY' );
define( 'LOGGED_IN_SALT',    'B(c*No,FJ8JQi7Gh(cf0WSWK5B$pM@3]=!bA0e}fOp{g}`![xn2}g)f{,PFK8R!=' );
define( 'NONCE_SALT',        '+6,G@h2/maqa+LHSZi(QPR[3~uwGszPt=9BXL;7t3SY}x{BN76zIqj2zZ,k_7[b_' );
define( 'WP_CACHE_KEY_SALT', ',rz@67I?pu~+lCDCFoxp|: }ckjB}5kt}pTCQLqPS!SdGp3mNiVIX&-$M:r>PHLW' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';



/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
