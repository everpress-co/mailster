<?php

define( 'UPLOADS', 'wp-content/tests_uploads' );

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/Users/Xaver/Sites/dev.local/app/public/tests/phpunit';
}

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


_setup();


/**
 *
 */
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

			// 'deliverymethod' => 'simple',
			'simplemethod'            => 'mail',

			'remove_data'             => true,

		)
	);

	mailcatcher()->clearinbox();
}



/**
 *
 */
function _teardown() {
	deactivate_plugins( 'mailster/mailster.php' );

	if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
		define( 'WP_UNINSTALL_PLUGIN', 'mailster/mailster.php' );
	}

	include dirname( dirname( __FILE__ ) ) . '/uninstall.php';
}


/**
 *
 */
function _setUser() {
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
}


/**
 *
 *
 * @return unknown
 */
function mailcatcher( $clear = false ) {
	$class = MailCatcher::getInstance( $clear );

	return $class;
}


/**
 *
 */
class MailCatcher {


	static $instance;
	private $endpoint = 'http://127.0.0.1:1080';


	private function __construct( $clear = false ) {
		if ( $clear ) {
			$this->clearinbox();
		}
		$this->check();
	}



	/**
	 *
	 *
	 * @return unknown
	 */
	public static function getInstance( $clear = false ) {
		if ( ! self::$instance ) {
			self::$instance = new self( $clear );
		}

		return self::$instance;
	}





	private function do( $url, $method = 'get', $args = array() ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, strtoupper( $method ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$result = curl_exec( $ch );
		$e      = curl_error( $ch );
		curl_close( $ch );
		if ( $e ) {
			return false;
		}
		$json_result = json_decode( $result );
		if ( empty( $json_result ) ) {
			return $result;
		}

		return $json_result;
	}




	/**
	 *
	 *
	 * @param unknown $endpoint
	 * @param unknown $args     (optional)
	 * @return unknown
	 */
	public function check() {
		global $wp_version;

		$url = trailingslashit( $this->endpoint );

		$result = $this->do( $url );

		if ( false === $result ) {
			echo 'MailCatcher is NOT running => starting...' . "\n";
			exec( 'mailcatcher' );
		} else {
			echo 'MailCatcher is running' . "\n";
		}

		echo "WordPress $wp_version\n";
		echo 'Mailster ' . MAILSTER_VERSION . "\n";
		echo exec( 'mailcatcher --version' ) . "\n";
	}




	/**
	 *
	 *
	 * @param unknown $endpoint
	 * @param unknown $args     (optional)
	 * @return unknown
	 */
	public function get( $endpoint, $args = array() ) {
		$url = trailingslashit( $this->endpoint ) . $endpoint;

		return $this->do( $url, 'get', $args );
	}


	/**
	 *
	 *
	 * @param unknown $endpoint
	 * @param unknown $args     (optional)
	 * @return unknown
	 */
	public function delete( $endpoint, $args = array() ) {
		$url = trailingslashit( $this->endpoint ) . $endpoint;

		return $this->do( $url, 'delete', $args );
	}



	public function clearinbox() {
		$messages = $this->delete( 'messages' );
	}


	/**
	 *
	 *
	 * @param unknown $subject
	 * @return unknown
	 */
	public function get_by_subject( $subject ) {
		$messages = $this->get( 'messages' );

		if ( ! $messages ) {
			return false;
		}

		foreach ( $messages as $message ) {
			if ( $message->subject == $subject ) {
				$m         = $this->get( 'messages/' . $message->id . '.json' );
				$m->source = $this->get( 'messages/' . $message->id . '.source' );
				return $m;
			}
		}
	}


}


register_shutdown_function( '_teardown' );
