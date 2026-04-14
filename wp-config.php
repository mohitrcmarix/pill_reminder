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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'pill-reminder' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'Admin@123#@!' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define('FS_METHOD','direct');

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
define( 'AUTH_KEY',          '|}`:+2*.d6X;|;5Ub3H,4js}Y@bqy,SG=vDMZ1NdT}pr7 fI9,HU_F-C_Qtw 8Op' );
define( 'SECURE_AUTH_KEY',   '<<F~Fdz/ wuT=R[D~BUFmwdYF[~!}$`]jH1*2&Ax&?XGJ&T<@Ys%[^Ou-KQ*CHsp' );
define( 'LOGGED_IN_KEY',     'nBu+EOj0tD#%Ap,.lardk7`0bL8UJ0=0$x^(nZj3q)jz8`kQ3 wz.Tb3mB5SkMMp' );
define( 'NONCE_KEY',         '|MTp^Mtf4jNc3C2ISLlykFx PxmidqcbHFRaGFoHQwO2d+Y#}E+~<cXm}L~ L~0G' );
define( 'AUTH_SALT',         'KSDn!W1KU$Hr:]A]l)Sh-QT^hkK&j&~jor/3%`yUM:HXvNR`yB*Mr!_1UB()dTQS' );
define( 'SECURE_AUTH_SALT',  '!L=GQ%oz;@hK{Hwb@,7bI0#-}zQ:6#km-w.&5jd;$4pYrye&w}|C=qcb0C.f7k-w' );
define( 'LOGGED_IN_SALT',    '_ZR|#{U.<XlDxR_!W(!U2h]s$B?:EV(5^<zGWp@#_=hkBqWh auI]|6$t*`b3(=z' );
define( 'NONCE_SALT',        'Q~&^UxdJxt{6LjXe7[/iuX6+j}fq4_z(tdOw08pHaZ@f%RAi+SDh(Ud`UTE=yvgX' );
define( 'WP_CACHE_KEY_SALT', '[mndS=,O@MS;~og_1,5>99C|J2.4KB0J<t9XI&f]fwY@y T>&6i>?81Tv[PFSh%b' );


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
// Enable Debugging
define( 'WP_DEBUG', true );

// Log errors to a file (/wp-content/debug.log)
define( 'WP_DEBUG_LOG', true );

// Stop errors from showing on the front-end (keeps site looking clean)
define( 'WP_DEBUG_DISPLAY', false);
// define('DISABLE_WP_CRON', true);


// Optional: Force PHP to show errors if the server is hiding them
@ini_set( 'display_errors', 0 );


/* Add any custom values between this line and the "stop editing" line. */

define( 'WP_MEMORY_LIMIT', '1024M' );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
