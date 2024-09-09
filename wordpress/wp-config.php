<?php

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp' );

/** Database username */
define( 'DB_USER', 'wp' );

/** Database password */
define( 'DB_PASSWORD', 'secret' );

/** Database hostname */
define( 'DB_HOST', 'mysql' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define('WP_MEMORY_LIMIT', '256M');

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
define( 'AUTH_KEY',         'J)X7Fk+x.*F:=u//P<:T|wb]*MkbIMKZAh`,LlG.xwS0UhFH ]o40e6C:n{Q85Dy' );
define( 'SECURE_AUTH_KEY',  'M8}qe3IYF@peVtp-30~qCz0;)GAe88X >Yp(>)Lyb|!z,] m;R/j`vca<~?/M0G,' );
define( 'LOGGED_IN_KEY',    'NZM|Zj0uk426#;x<@<t!48AI{2[yx|QIVmlmiS1i]%<ZiNNfLLSOW):p[v6tpr3r' );
define( 'NONCE_KEY',        'G39/l3(ue+J{,`.Gn+$8u<EV*z:&)agKF;z]Vty5x$5.[l67.xDp||OW6)ZX&88r' );
define( 'AUTH_SALT',        'sSf=Y_Y3}>5UZa-y ]%je?veGJ-(I`x=>YB,1B_QIk>*i&Uhzac4%qqR4&N vd5Q' );
define( 'SECURE_AUTH_SALT', '#XjRwEhx@aRAuHhsN##z[}?*eoQz*ToGpS{Eq+G(Ij]T}uwcIRs9EzYn*3b1SyNB' );
define( 'LOGGED_IN_SALT',   '_Fd&Q3PUF7Sx<)RPH=~-!afs N-cD4]{Pxm9YJ+0Q&C(e4(PpCofeQG`rH_eQi3=' );
define( 'NONCE_SALT',       'aSA&H|rWfKHq}zi~~dTW5_j+1$>mT92A<JI__j8%l~j_:gQ5mCS{?N|hzt&QMS~L' );

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */
// adjust Redis host and port if necessary 
define( 'WP_REDIS_HOST', 'redis' );
define( 'WP_REDIS_PORT', 6379 );
define( 'WP_REDIS_DATABASE', 0 );


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
