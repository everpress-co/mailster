<?php
/**
 * PHPUnit bootstrap file for setting up WordPress testing.
 *
 * For integration tests, not unit tests.
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?";
	exit( 1 );
}

require_once dirname( dirname( __FILE__ ) ) . '/vendor/autoload.php';


// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/mailster.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';



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

			'deliverymethod'          => 'smtp',

			'smtp_host'               => '127.0.0.1',
			'smtp_port'               => 1025,
			'smtp_timeout'            => 10,
			'smtp_secure'             => false,
			'smtp_auth'               => false,
			'smtp_user'               => '',
			'smtp_pwd'                => '',

			// 'deliverymethod'          => 'simple',
			'simplemethod'            => 'mail',

			'remove_data'             => true,

			'disable_cache'           => true,

		)
	);

	// mailcatcher()->clearinbox();
}
