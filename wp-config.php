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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'legalsub' );

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
define( 'AUTH_KEY',         '~9|9~M=Wzy#xEj#yJVO_DlcRFK{t5Sw8*(d4M|[!>~z99AbM$7Eq/jHlRRd/gpQP' );
define( 'SECURE_AUTH_KEY',  's=nGe1K<(m_w]oF3M.N`v}=G0LJ&>F!dY8@Q)%3&107v6nt2Dr:TRk}brWK^wWy4' );
define( 'LOGGED_IN_KEY',    '9(/MXTy+5il4?H:KxicyUv6M*.olH0Ovl;OJ(#?W_UtCEDWL.>7k0*91@!=oW8!R' );
define( 'NONCE_KEY',        'h(~7W4uTR(bq?itSUi6WbI[og,@_bc7(Hih!qAH ^<nc,CwK]E*Mtd=2$T@9A;~L' );
define( 'AUTH_SALT',        'CG&[jDJCQv(ca`0rEO(xVtA!slP@Wx!ZKUv*@[TzPNm4B+06Uu0,^LevF:0]-rr8' );
define( 'SECURE_AUTH_SALT', '9^ydgJIQ7Y/-Fxh)@Yc_jd(lF:K0X8?O={0I4M75 vSsP3Zt,7 ;>]E+B,skdmuV' );
define( 'LOGGED_IN_SALT',   'lrP`TWc*BwZLI?P!PSGsAn.M*tOVL`8M-cxsrVzS@?cQG4A]Z?3/>-5b3PxNty)D' );
define( 'NONCE_SALT',       'zzC@W^iM:bp*4j~M},Bn_Xw@PYC>9I1,vsK&phL90I9~Sng|/TqAAQXsD9YeAK;_' );

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
