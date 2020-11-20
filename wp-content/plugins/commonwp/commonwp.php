<?php
/**
 * Plugin Name: commonWP
 * Plugin URI:  https://milandinic.com/wordpress/plugins/commonwp/
 * Description: Offload open source static assets to the free, public CDN.
 * Author:      Milan Dinić
 * Author URI:  https://milandinic.com/
 * Version:     1.1.0
 * Text Domain: commonwp
 * Domain Path: /languages/
 *
 * @package commonWP
 */

// Check minimum required PHP version.
if ( version_compare( phpversion(), '5.4.0', '<' ) ) {
	return;
}

/**
 * Autoloader for commonWP classes.
 *
 * @param string $class Name of the class.
 */
function commonwp_autoloader( $class ) {
	$pairs = [
		'WP_Temporary'                          => '/lib/wp-temporary/class-wp-temporary.php',
		'Temporary_Command'                     => '/lib/wp-temporary/cli/Temporary_Command.php',
		'dimadin\WP\Plugin\commonWP\Clean'      => '/inc/Clean.php',
		'dimadin\WP\Plugin\commonWP\Expiration' => '/inc/Expiration.php',
		'dimadin\WP\Plugin\commonWP\Lock'       => '/inc/Lock.php',
		'dimadin\WP\Plugin\commonWP\Main'       => '/inc/Main.php',
		'dimadin\WP\Plugin\commonWP\NPM'        => '/inc/NPM.php',
		'dimadin\WP\Plugin\commonWP\Paths'      => '/inc/Paths.php',
		'dimadin\WP\Plugin\commonWP\Privacy'    => '/inc/Privacy.php',
		'dimadin\WP\Plugin\commonWP\Process'    => '/inc/Process.php',
		'dimadin\WP\Plugin\commonWP\Queue'      => '/inc/Queue.php',
		'dimadin\WP\Plugin\commonWP\Rewrite'    => '/inc/Rewrite.php',
		'dimadin\WP\Plugin\commonWP\Singleton'  => '/inc/Singleton.php',
		'dimadin\WP\Plugin\commonWP\SRI'        => '/inc/SRI.php',
		'dimadin\WP\Plugin\commonWP\Store'      => '/inc/Store.php',
		'dimadin\WP\Plugin\commonWP\Utils'      => '/inc/Utils.php',
		'dimadin\WP\Plugin\commonWP\Versions'   => '/inc/Versions.php',
		'dimadin\WP\Plugin\commonWP\WPCLI'      => '/inc/WPCLI.php',
		'dimadin\WP\Library\Backdrop\Main'      => '/lib/backdrop/inc/Main.php',
		'dimadin\WP\Library\Backdrop\Server'    => '/lib/backdrop/inc/Server.php',
		'dimadin\WP\Library\Backdrop\Task'      => '/lib/backdrop/inc/Task.php',
	];

	if ( array_key_exists( $class, $pairs ) ) {
		include __DIR__ . $pairs[ $class ];
	}
}
spl_autoload_register( 'commonwp_autoloader' );

/**
 * Version of commonWP plugin.
 *
 * @since 1.0.0
 * @var string
 */
define( 'COMMONWP_VERSION', '1.1.0' );

/*
 * Initialize a plugin.
 *
 * Load class when all plugins are loaded
 * so that other plugins can overwrite it.
 */
add_action( 'plugins_loaded', [ 'dimadin\WP\Plugin\commonWP\Main', 'get_instance' ], 10 );
