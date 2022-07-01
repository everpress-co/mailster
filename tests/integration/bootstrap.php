<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Wordpress_Seo
 */

use Yoast\WPTestUtils\WPIntegration;

require_once dirname( dirname( __DIR__ ) ) . '/vendor/yoast/wp-test-utils/src/WPIntegration/bootstrap-functions.php';


/* *****[ Wire in the integration ]***** */

$_tests_dir = WPIntegration\get_path_to_wp_test_dir();

// Give access to tests_add_filter() function.
require_once $_tests_dir . 'includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require dirname( dirname( __DIR__ ) ) . '/mailster.php';
}

/**
 * Filter the plugins URL to pretend the plugin is installed in the test environment.
 *
 * @param string $url    The complete URL to the plugins directory including scheme and path.
 * @param string $path   Path relative to the URL to the plugins directory. Blank string
 *                       if no path is specified.
 * @param string $plugin The plugin file path to be relative to. Blank string if no plugin
 *                       is specified.
 *
 * @return string
 */
function _plugins_url( $url, $path, $plugin ) {
	$plugin_dir = dirname( dirname( __DIR__ ) );
	if ( $plugin === $plugin_dir . '/mailster.php' ) {
		$url = str_replace( dirname( $plugin_dir ), '', $url );
	}

	return $url;
}

function _setup() {

	global $wpdb;

	if ( ! $wpdb->has_connected ) {
		die( 'no database' );
	}

	global $current_user;
	$current_user = new WP_User( 1 );
	$current_user->set_role( 'administrator' );
	wp_update_user(
		array(
			'ID'         => 1,
			'first_name' => 'Admin',
			'last_name'  => 'User',
		)
	);

	global $wp_rewrite, $wp_query;
	$GLOBALS['wp_rewrite']->init();
	$GLOBALS['wp_rewrite']->set_permalink_structure( '/%postname%/' );
	flush_rewrite_rules( false );

	activate_plugin( 'mailster/mailster.php' );
	mailster()->activate();

	mailster_update_option(
		array(

			'system_mail'             => 1,

			'subscriber_notification' => false,
			'homepage'                => 3,
			'antiflood'               => 0,

			// 'deliverymethod'          => 'smtp',

			// 'smtp_host'               => '127.0.0.1',
			// 'smtp_port'               => 1025,
			// 'smtp_timeout'            => 10,
			// 'smtp_secure'             => false,
			// 'smtp_auth'               => false,
			// 'smtp_user'               => '',
			// 'smtp_pwd'                => '',

			'remove_data'             => true,
			'disable_cache'           => true,

		)
	);	

	// run this code once
}

// Add plugin to active mu-plugins - to make sure it gets loaded.
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Overwrite the plugin URL to not include the full path.
tests_add_filter( 'plugins_url', '_plugins_url', 10, 3 );

// load the setup code
tests_add_filter( 'init', '_setup' );


WPIntegration\bootstrap_it();
