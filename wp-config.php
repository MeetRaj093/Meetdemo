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

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'meet_demo' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

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
define( 'AUTH_KEY',         '}Lu[5$phg~UqP81;7#insN^3 -h9sCo~J<[#J]O4kY2S[~$Y)Ut;XHu~y&0mQg2,' );
define( 'SECURE_AUTH_KEY',  'GxpP{*uhyUbW51q4(g-Md33<O$n4Bg)w&j>VVbMFG6vNm(~B(L}qNo:q@:Cs:x1X' );
define( 'LOGGED_IN_KEY',    ';y$i=~QV1,_EK$b=H*`q_+&c ?~K[%BIhen!Q$F}1nRGJrd`~5bi9Z&&K2_hjTNX' );
define( 'NONCE_KEY',        'C1zFh}4[6[Q;ze_di[~+3z6Of42&ZRV?DNdBc:p8~2>?uET==4a<hC7Z!tu7c!h0' );
define( 'AUTH_SALT',        'B;5BJaBu3ea_w}Fd;2KoKypO8!5{%iCpjH)E1?lA+~H71RuTWl~RknIGwh~ZHOJ+' );
define( 'SECURE_AUTH_SALT', 'Tg_U[#3T2<e%k (Vh8Ub6$$3PTnas;?m-RSBLhCQ%hq5N!NW](H17Qwb|A*m4k;R' );
define( 'LOGGED_IN_SALT',   'zFqP8169(!T[G%6e%2+1Fk~>TiLX^E.V^2fjKrV6~#vANSR49BqFE!@Q.8<V_0(Q' );
define( 'NONCE_SALT',       'e0d~lU.F(y|KX0v17a ->}<7B0gEFDsKedSMd*Pm|wFuc*a+A-mZ9OfJkP_xz_D9' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
// Enable WP_DEBUG mode
define( 'WP_DEBUG', false );

// Enable Debug logging to the /wp-content/debug.log file
define( 'WP_DEBUG_LOG', false );

// Disable display of errors and warnings
define( 'WP_DEBUG_DISPLAY', true );
@ini_set( 'display_errors', 0 );

// Use dev versions of core JS and CSS files (only needed if you are modifying these core files)
define( 'SCRIPT_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
