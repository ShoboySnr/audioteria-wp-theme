<?php

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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

require 'vendor/autoload.php';

function fromenv( $key, $default = null ) {
	$value = getenv( $key );
	if ( $value === false ) {
		$value = $default;
	}

	return $value;
}

$url = parse_url( fromenv( 'DATABASE_URL', 'mysql://root:@localhost:3306/audioteria_wp' ) );

$host     = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$database = substr( $url["path"], 1 );

define( 'JWT_AUTH_SECRET_KEY', fromenv( 'AUTH_SECRET_TOKEN', 'h~|m3<5-VP4<?`Nm]D[qmkegvuzMyGJ2tOJz{D;@RzmjTWwpvk~+Y`}Q#&8-%h+@' ) );
define( 'JWT_AUTH_CORS_ENABLE', fromenv( 'AUTH_CORS_ENABLE', true ) );

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', fromenv( 'WORDPRESS_DB_NAME', $database ) );

/** Database username */
define( 'DB_USER', fromenv( 'WORDPRESS_DB_USER', $username ) );

/** Database password */
define( 'DB_PASSWORD', fromenv( 'WORDPRESS_DB_PASSWORD', $password ) );

/** Database hostname */
define( 'DB_HOST', fromenv( 'WORDPRESS_DB_HOST', $host ) );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', fromenv( 'WORDPRESS_DB_CHARSET', 'utf8mb4' ) );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', fromenv( 'WORDPRESS_DB_COLLATE', '' ) );

/** dummy content generator */
define( 'FS_METHOD', fromenv( 'WORDPRESS_FS_METHOD', 'direct' ) );

/** STYLES VERSION */
define( 'AUDIOTERIA_WP_THEME_VERSION', fromenv( 'AUDIOTERIA_WP_THEME_VERSION', '1.0.0' ) );

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
define( 'AUTH_KEY', fromenv( 'WORDPRESS_AUTH_KEY', 'GWF8-r%VJ1!YKCC*&n$;n.(..>54vLQA#e&n[nf(a9`Z]RA~w?L#t t4{_1 K) n' ) );
define( 'SECURE_AUTH_KEY', fromenv( 'WORDPRESS_SECURE_AUTH_KEY', '5xPrS%`p[[jGa$-r.$ap@-_o_C$+iR]HNe870^(O=(/`bkEG <^uneYi13]xy`^l' ) );
define( 'LOGGED_IN_KEY', fromenv( 'WORDPRESS_LOGGED_IN_KEY', '_-bS&w@tX+sK#^hQ189:?{#IR%1W*flN$<)fI.L1 Oe;XEAYx^2^[7!tuO{1_}Ef' ) );
define( 'NONCE_KEY', fromenv( 'WORDPRESS_NONCE_KEY', 'Drg)W 3B=I)AGj,(9_s@W/oqP+b;zjf#P2.PlwhPRuuy|{U=+M[m({.69+Svz!ng' ) );
define( 'AUTH_SALT', fromenv( 'WORDPRESS_AUTH_SALT', ',>.nAuiE$jFf_+ln&otg[:i>4TsLMoCNKc%X#(@>gmBpF,oIG+*2^OpdC}x.[*As' ) );
define( 'SECURE_AUTH_SALT', fromenv( 'WORDPRESS_SECURE_AUTH_SALT', '`U&>eb/i_8$QMVoNB>k+Bb|Bdt7>pxWh0_Upk*&S8>^cYRRgKs%}1>[DLe28>0DM' ) );
define( 'LOGGED_IN_SALT', fromenv( 'WORDPRESS_LOGGED_IN_SALT', 'ccf^4WiFCoysFR(PD~tgoi7>=PZ+?N{z!ju[DXpP7?SU=q0P*(Gz1<X@WuXAv}|x' ) );
define( 'NONCE_SALT', fromenv( 'WORDPRESS_NONCE_SALT', 'g|QHvzY4dkXTs:-:(CoD`W;Ex]U}Y#Nmwim`g6cSTUO,T!/2h}A,+J3hEaSkG-.r' ) );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = fromenv( 'WORDPRESS_TABLE_PREFIX', 'wp_' );

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
define( 'WP_DEBUG', (bool) fromenv( 'WORDPRESS_DEBUG', true ) );

/* Add any custom values between this line and the "stop editing" line. */
if (
	isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] )
	&& $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
) {
	$_SERVER['HTTPS'] = 'on';
}


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';